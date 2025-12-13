<?php

namespace Database\Seeders;

use App\Models\Coach;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoachSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = [
            ['name' => 'Justyna Dapuzzo', 'email' => 'justyna.dapuzzo@example.com'],
            ['name' => 'Amanda Jeffrey', 'email' => 'amanda.jeffrey@example.com'],
            ['name' => 'Kelly Paulson', 'email' => 'kelly.paulson@example.com'],
            ['name' => 'Gab Bolin', 'email' => 'gab.bolin@example.com'],
            ['name' => 'Paige Aller', 'email' => 'paige.aller@example.com'],
            ['name' => 'Kali Butler', 'email' => 'kali.butler@example.com'],
            ['name' => 'Annalise Moore', 'email' => 'annalise.moore@example.com'],
            ['name' => 'Elizabeth Tiseo', 'email' => 'elizabeth.tiseo@example.com'],
            ['name' => 'Brooke Haas', 'email' => 'brooke.haas@example.com'],
            ['name' => 'Liz Wolfgang', 'email' => 'liz.wolfgang@example.com'],
            ['name' => 'Zoe Rolph', 'email' => 'zoe.rolph@example.com'],
        ];

        foreach ($coaches as $coach) {
            Coach::firstOrCreate(
                ['email' => $coach['email']],
                ['name' => $coach['name']]
            );
        }
    }
}

