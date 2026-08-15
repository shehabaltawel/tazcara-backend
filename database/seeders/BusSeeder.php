<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;

/**
 * Bus Seeder
 */
class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = [
            ['class' => 'First Class', 'plate_number' => 'ABC-1234'],
            ['class' => 'First Class', 'plate_number' => 'ABC-5678'],
            ['class' => 'Business',    'plate_number' => 'DEF-1234'],
            ['class' => 'Business',    'plate_number' => 'DEF-5678'],
            ['class' => 'Standard',    'plate_number' => 'GHI-1234'],
            ['class' => 'Standard',    'plate_number' => 'GHI-5678'],
        ];

        foreach ($buses as $bus) {
            Bus::updateOrCreate(
                ['plate_number' => $bus['plate_number']],
                [
                    'class' => $bus['class'],
                ]
            );
        }
    }
}
