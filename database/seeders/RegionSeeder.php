<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                'name' => 'Region 1',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 2',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 3',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 4',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 5',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 6',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 7',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 8',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 9',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
            [
                'name' => 'Region 10',
                'latitude' => '0.12',
                'longitude' => '0.123'
            ],
        ];
        foreach ($regions as $region) {
            Region::create([
                'name' => $region['name'],
                'latitude' => $region['latitude'],
                'longitude' => $region['longitude'],
            ]);
        }
    }
}
