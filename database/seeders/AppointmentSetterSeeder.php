<?php

namespace Database\Seeders;

use App\Models\AppointmentSetter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSetterSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointmentSetters = [
            ['name' => 'Appointment Setter 1', 'email' => 'appointmentsetter1@example.com'],
            ['name' => 'Appointment Setter 2', 'email' => 'appointmentsetter2@example.com'],
            ['name' => 'Appointment Setter 3', 'email' => 'appointmentsetter3@example.com'],
        ];

        foreach ($appointmentSetters as $appointmentSetter) {
            AppointmentSetter::firstOrCreate(
                ['email' => $appointmentSetter['email']],
                ['name' => $appointmentSetter['name']]
            );
        }
    }
}

