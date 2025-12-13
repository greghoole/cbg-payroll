<?php

namespace Database\Seeders;

use App\Models\Closer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CloserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $closers = [
            ['name' => 'Closer 1', 'email' => 'closer1@example.com'],
            ['name' => 'Closer 2', 'email' => 'closer2@example.com'],
            ['name' => 'Closer 3', 'email' => 'closer3@example.com'],
        ];

        foreach ($closers as $closer) {
            Closer::firstOrCreate(
                ['email' => $closer['email']],
                ['name' => $closer['name']]
            );
        }
    }
}

