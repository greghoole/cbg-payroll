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
            ['name' => 'Justyna Dapuzzo', 'email' => 'coachjustyna@gmail.com'],
            ['name' => 'Amanda Jeffrey', 'email' => 'amandajeffrey46@gmail.com'],
            ['name' => 'Kelly Paulson', 'email' => 'kyp5125@gmail.com'],
            ['name' => 'Gab Bolin', 'email' => 'gab.bolin@example.com'],
            ['name' => 'Paige Aller', 'email' => 'paigeallert@gmail.com'],
            ['name' => 'Kali Butler', 'email' => 'kalibutler005@gmail.com'],
            ['name' => 'Annalise Moore', 'email' => 'Annalise.CBG@gmail.com'],
            ['name' => 'Elizabeth Tiseo', 'email' => 'elizabeth.tiseo@example.com'],
            ['name' => 'Brooke Haas', 'email' => 'brooke.haas@example.com'],
            ['name' => 'Liz Wolfgang', 'email' => 'Coachlizwolfgang@gmail.com'],
            ['name' => 'Zoe Rolph', 'email' => 'zoerolph@hotmail.com'],
        ];

        foreach ($coaches as $coach) {
            Coach::firstOrCreate(
                ['email' => $coach['email']],
                ['name' => $coach['name']]
            );
        }
    }
}

