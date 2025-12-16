<?php

namespace Database\Seeders;

use App\Models\Charge;
use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChargeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('data/Stripe Charges - CASH IN.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Could not open CSV file: {$csvPath}");
            return;
        }

        // Read header row
        $header = fgetcsv($file);
        if (!$header) {
            $this->command->error("Could not read header from CSV file");
            fclose($file);
            return;
        }

        $this->command->info("Starting to import charges...");
        $imported = 0;
        $skipped = [];
        $errors = 0;
        $rowNumber = 1; // Start at 1 (header is row 0)

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                
                if (count($row) < count($header)) {
                    $skipped[] = [
                        'row' => $rowNumber,
                        'reason' => 'Insufficient columns in row',
                        'data' => implode(', ', array_slice($row, 0, 5)) . '...',
                    ];
                    continue;
                }

                $data = array_combine($header, $row);

                // Skip if essential data is missing
                $missingFields = [];
                if (empty($data['EMAIL ADDRESS'])) $missingFields[] = 'EMAIL ADDRESS';
                if (empty($data['TRANSACTION ID'])) $missingFields[] = 'TRANSACTION ID';
                if (empty($data['ID'])) $missingFields[] = 'ID';
                
                if (!empty($missingFields)) {
                    $skipped[] = [
                        'row' => $rowNumber,
                        'reason' => 'Missing required fields: ' . implode(', ', $missingFields),
                        'email' => $data['EMAIL ADDRESS'] ?? 'N/A',
                        'transaction_id' => $data['TRANSACTION ID'] ?? 'N/A',
                        'charge_id' => $data['ID'] ?? 'N/A',
                        'client_name' => $data['CLIENT NAME'] ?? 'N/A',
                    ];
                    continue;
                }

                try {
                    // Find or create client
                    $client = Client::firstOrCreate(
                        ['email' => trim($data['EMAIL ADDRESS'])],
                        [
                            'name' => trim($data['CLIENT NAME'] ?? ''),
                            'stripe_customer_id' => !empty($data['CLIENT STRIPE ID']) ? trim($data['CLIENT STRIPE ID']) : null,
                            'country' => !empty($data['COUNTRY']) ? trim($data['COUNTRY']) : null,
                        ]
                    );

                    // Update client if stripe_customer_id is missing but we have it
                    if (empty($client->stripe_customer_id) && !empty($data['CLIENT STRIPE ID'])) {
                        $client->update(['stripe_customer_id' => trim($data['CLIENT STRIPE ID'])]);
                    }

                    // Parse date
                    $date = $this->parseDate($data['DATE'] ?? '');
                    if (!$date) {
                        $skipped[] = [
                            'row' => $rowNumber,
                            'reason' => 'Invalid or missing date',
                            'date_value' => $data['DATE'] ?? 'N/A',
                            'email' => $data['EMAIL ADDRESS'] ?? 'N/A',
                            'transaction_id' => $data['TRANSACTION ID'] ?? 'N/A',
                            'client_name' => $data['CLIENT NAME'] ?? 'N/A',
                        ];
                        continue;
                    }

                    // Parse amounts (remove $ and commas)
                    $net = $this->parseAmount($data['NET'] ?? '0');
                    $amountCharged = $this->parseAmount($data['AMOUNT CHARGED'] ?? '0');

                    // Parse boolean
                    $billingIncluded = $this->parseBoolean($data['BILLING INFORMATION IS INCLUDED?'] ?? '');

                    // Check if charge already exists
                    $transactionId = trim($data['TRANSACTION ID']);
                    $existingCharge = Charge::where('stripe_transaction_id', $transactionId)->first();
                    
                    if ($existingCharge) {
                        $skipped[] = [
                            'row' => $rowNumber,
                            'reason' => 'Duplicate transaction ID (already exists)',
                            'transaction_id' => $transactionId,
                            'email' => $data['EMAIL ADDRESS'] ?? 'N/A',
                            'client_name' => $data['CLIENT NAME'] ?? 'N/A',
                            'existing_charge_id' => $existingCharge->id,
                        ];
                        continue;
                    }

                    // Create charge
                    Charge::create([
                        'client_id' => $client->id,
                        'date' => $date,
                        'net' => $net,
                        'amount_charged' => $amountCharged,
                        'program' => !empty($data['PROGRAM']) ? trim($data['PROGRAM']) : null,
                        'stripe_url' => !empty($data['STRIPE URL']) ? trim($data['STRIPE URL']) : null,
                        'stripe_transaction_id' => trim($data['TRANSACTION ID']),
                        'stripe_charge_id' => trim($data['ID']),
                        'billing_information_included' => $billingIncluded,
                        'country' => !empty($data['COUNTRY']) ? trim($data['COUNTRY']) : null,
                    ]);

                    $imported++;
                    
                    if ($imported % 100 === 0) {
                        $this->command->info("Imported {$imported} charges...");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning("Error importing charge: " . $e->getMessage(), ['data' => $data]);
                    continue;
                }
            }

            DB::commit();
            $this->command->info("Successfully imported {$imported} charges.");
            
            if (count($skipped) > 0) {
                $this->command->warn("\nSkipped " . count($skipped) . " rows:");
                $this->command->line("");
                
                // Group by reason
                $byReason = [];
                foreach ($skipped as $skip) {
                    $reason = $skip['reason'];
                    if (!isset($byReason[$reason])) {
                        $byReason[$reason] = [];
                    }
                    $byReason[$reason][] = $skip;
                }
                
                foreach ($byReason as $reason => $skips) {
                    $this->command->warn("  {$reason} (" . count($skips) . " rows):");
                    foreach ($skips as $skip) {
                        $details = [];
                        if (isset($skip['transaction_id'])) $details[] = "Transaction ID: {$skip['transaction_id']}";
                        if (isset($skip['email'])) $details[] = "Email: {$skip['email']}";
                        if (isset($skip['client_name'])) $details[] = "Client: {$skip['client_name']}";
                        if (isset($skip['date_value'])) $details[] = "Date: {$skip['date_value']}";
                        if (isset($skip['charge_id'])) $details[] = "Charge ID: {$skip['charge_id']}";
                        if (isset($skip['existing_charge_id'])) $details[] = "Existing Charge DB ID: {$skip['existing_charge_id']}";
                        
                        $detailStr = !empty($details) ? ' - ' . implode(', ', $details) : '';
                        $this->command->line("    Row {$skip['row']}{$detailStr}");
                    }
                    $this->command->line("");
                }
                
                // Optionally write to a log file
                $logPath = storage_path('logs/skipped_charges_' . date('Y-m-d_His') . '.json');
                file_put_contents($logPath, json_encode($skipped, JSON_PRETTY_PRINT));
                $this->command->info("Detailed skip log saved to: {$logPath}");
            }
            
            if ($errors > 0) {
                $this->command->error("Encountered {$errors} errors during import.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Failed to import charges: " . $e->getMessage());
            throw $e;
        } finally {
            fclose($file);
        }
    }

    /**
     * Parse date string to Carbon date
     */
    private function parseDate(string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try to parse various date formats
            $date = \Carbon\Carbon::parse(trim($dateString));
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse amount string (remove $ and commas)
     */
    private function parseAmount(string $amountString): float
    {
        if (empty($amountString)) {
            return 0.0;
        }

        // Remove $ and commas, then convert to float
        $cleaned = preg_replace('/[\$,]/', '', trim($amountString));
        return (float) $cleaned;
    }

    /**
     * Parse boolean from various formats
     */
    private function parseBoolean(string $value): bool
    {
        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'y', 'true']);
    }
}

