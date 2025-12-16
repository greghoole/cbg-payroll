<?php

namespace App\Console\Commands;

use App\Models\Charge;
use App\Models\Client;
use App\Models\Coach;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommissionsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:commissions-csv {file=data/TCS - Commissions - Payments.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import commission data from CSV and assign coaches to clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading CSV file: {$filePath}");
        
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Could not open file: {$filePath}");
            return 1;
        }

        // Skip header row
        $header = fgetcsv($handle);
        if (!$header) {
            $this->error("Could not read header row");
            fclose($handle);
            return 1;
        }

        $processed = 0;
        $skipped = 0;
        $errors = 0;
        $assigned = 0;
        $alreadyAssigned = 0;
        $chargesUpdated = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                // Column indices:
                // 0: Date
                // 1: NET CASH (Column B)
                // 2: PROGRAM
                // 3: EMAIL ADDRESS (Column D)
                // 4: CLIENT NAME
                // 5: AMOUNT CHARGED (Column F)
                // 6: COACH (Column G)
                // 7: COMM. % (Column H)
                // 12: CUSTOMER ID

                if (count($row) < 7) {
                    $skipped++;
                    continue;
                }

                $email = trim($row[3] ?? '');
                $coachName = trim($row[6] ?? '');
                $commissionPercentage = $this->parsePercentage($row[7] ?? '');

                // Skip if no coach
                if (empty($coachName)) {
                    $skipped++;
                    continue;
                }

                // Skip if no email
                if (empty($email)) {
                    $skipped++;
                    continue;
                }

                // Find client by email
                $client = Client::where('email', $email)->first();
                if (!$client) {
                    $this->warn("Client not found for email: {$email}");
                    $errors++;
                    continue;
                }

                // Find coach by name
                $coach = Coach::where('name', $coachName)->first();
                if (!$coach) {
                    $this->warn("Coach not found: {$coachName}");
                    $errors++;
                    continue;
                }

                // Update client's coach if needed
                if ($client->coach_id !== $coach->id) {
                    $client->coach_id = $coach->id;
                    $client->save();
                    $this->info("Assigned coach {$coachName} to client {$email}");
                    $assigned++;
                    $wasNewAssignment = true;
                } else {
                    $alreadyAssigned++;
                }

                // If there's an existing relationship, apply commission to charges without commission
                if ($client->coach_id === $coach->id) {
                    // Determine which commission percentage to use
                    $commissionToApply = null;
                    
                    // Priority 1: Use commission from CSV if provided
                    if ($commissionPercentage !== null) {
                        $commissionToApply = $commissionPercentage;
                    } else {
                        // Priority 2: Find existing commission from prior charges for this client
                        $existingCharge = Charge::where('client_id', $client->id)
                            ->whereNotNull('commission_percentage')
                            ->orderBy('date', 'desc')
                            ->first();
                        
                        if ($existingCharge) {
                            $commissionToApply = $existingCharge->commission_percentage;
                        }
                    }
                    
                    // Apply commission to charges without commission percentage
                    if ($commissionToApply !== null) {
                        $chargesToUpdate = Charge::where('client_id', $client->id)
                            ->whereNull('commission_percentage')
                            ->get();
                        
                        $updatedCount = 0;
                        foreach ($chargesToUpdate as $charge) {
                            $charge->commission_percentage = $commissionToApply;
                            // Payout will be auto-calculated by the model's boot method
                            $charge->save();
                            $updatedCount++;
                        }
                        
                        if ($updatedCount > 0) {
                            $chargesUpdated += $updatedCount;
                            $this->info("  Applied {$commissionToApply}% commission to {$updatedCount} charge(s) for client {$email}");
                        }
                    }
                }

                $processed++;
            }

            DB::commit();

            $this->info("\n=== Import Summary ===");
            $this->info("Processed: {$processed}");
            $this->info("New coach assignments: {$assigned}");
            $this->info("Already assigned: {$alreadyAssigned}");
            $this->info("Charges updated with commission: {$chargesUpdated}");
            $this->info("Skipped (no coach/email): {$skipped}");
            $this->info("Errors: {$errors}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error processing CSV: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        } finally {
            fclose($handle);
        }

        return 0;
    }

    /**
     * Parse percentage string (e.g., "18%" or "28.30%") to float
     */
    private function parsePercentage(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        // Remove % sign
        $cleaned = str_replace('%', '', trim($value));
        
        if (empty($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }
}
