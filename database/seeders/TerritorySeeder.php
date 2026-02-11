<?php

namespace Database\Seeders;

use App\Models\Territory;
use Illuminate\Database\Seeder;

class TerritorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $territories = [
            [
                'name' => 'Test',
                'state_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test1',
                'state_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test2',
                'state_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test3',
                'state_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test5',
                'state_id' => '2',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test6',
                'state_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test7',
                'state_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test9',
                'state_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Test0',
                'state_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
        ];
        foreach ($territories as $territory) {
            Territory::create([
                'name' => $territory['name'],
                'state_id' => $territory['state_id'],
                'latitude' => $territory['latitude'],
                'longitude' => $territory['longitude'],
            ]);
        }
    }
}
