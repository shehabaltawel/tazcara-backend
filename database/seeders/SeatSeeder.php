<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;

/**
 * A class seeds the seats for all buses
 */
class SeatSeeder extends Seeder
{
    private const SEATS_PER_BUS = 12;

    /**
     * Seed the seats for all buses.
     */
    public function run(): void
    {
        Bus::all()->each(fn (Bus $bus) => $this->seedSeatsFor($bus));
    }

    /**
     * Seed the seats for a given bus.
     */
    private function seedSeatsFor(Bus $bus): void
    {
        if ($bus->seats()->exists()) {
            return;
        }

        $seats = collect(range(1, self::SEATS_PER_BUS))
            ->map(fn (int $n) => ['code' => "A{$n}"]);

        $bus->seats()->createMany($seats->all());
    }
}
