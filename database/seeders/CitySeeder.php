<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Cairo',            'code' => 'CAI'],
            ['name' => 'Giza',             'code' => 'GIZ'],
            ['name' => 'Alexandria',       'code' => 'ALX'],
            ['name' => 'AlFayyum',         'code' => 'FYM'],
            ['name' => 'AlMinya',          'code' => 'MNY'],
            ['name' => 'Asyut',            'code' => 'ASY'],
            ['name' => 'Sohag',            'code' => 'SOH'],
            ['name' => 'Qena',             'code' => 'QNA'],
            ['name' => 'Luxor',            'code' => 'LXR'],
            ['name' => 'Aswan',            'code' => 'ASW'],
            ['name' => 'BeniSuef',         'code' => 'BNS'],
            ['name' => 'Ismailia',         'code' => 'ISM'],
            ['name' => 'PortSaid',         'code' => 'PSD'],
            ['name' => 'Suez',             'code' => 'SUZ'],
            ['name' => 'Mansoura',         'code' => 'MNS'],
            ['name' => 'Tanta',            'code' => 'TNT'],
            ['name' => 'Zagazig',          'code' => 'ZAG'],
            ['name' => 'Damietta',         'code' => 'DMT'],
            ['name' => 'KafrElSheikh',     'code' => 'KFS'],
            ['name' => 'Damanhur',         'code' => 'DMH'],
            ['name' => 'Hurghada',         'code' => 'HRG'],
            ['name' => 'ElArish',          'code' => 'ARS'],
            ['name' => 'Marsa Matruh',     'code' => 'MMH'],
            ['name' => 'Beni Mazar',       'code' => 'BMZ'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['code' => $city['code']],
                [
                    'name' => $city['name'],
                ]
            );
        }
    }
}
