<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A class seeds the application's database.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            CitySeeder::class,
            BusSeeder::class,
            SeatSeeder::class,
            TripSeeder::class,
        ]);
    }
}
