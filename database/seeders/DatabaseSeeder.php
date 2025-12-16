<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_admin' => true,
            ]
        );
        
        // Ensure existing admin user is marked as admin
        if (!$admin->is_admin) {
            $admin->update(['is_admin' => true]);
        }

        $this->call([
            CoachSeeder::class,
            AppointmentSetterSeeder::class,
            CloserSeeder::class,
            ChargeSeeder::class,
            RefundSeeder::class,
        ]);
    }
}
