<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            [
                'name' => 'Washington',
                'region_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Oregon',
                'region_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Northern California',
                'region_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Hawaii',
                'region_id' => '1',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'California',
                'region_id' => '2',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Idaho',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Montana',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Wyoming',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'North Dakota',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'South Dakota',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Nebraska',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Minnesota',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Iowa',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Wisconsin',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Michigan',
                'region_id' => '3',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Nevada',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Utah',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Arizona',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Colorado',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'New Mexico',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Kansas',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Oklahoma',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Missouri',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Arkansas',
                'region_id' => '4',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Texas',
                'region_id' => '5',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Louisiana',
                'region_id' => '5',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Mississippi',
                'region_id' => '5',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Alabama',
                'region_id' => '5',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Illinois',
                'region_id' => '6',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Indiana',
                'region_id' => '6',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Ohio',
                'region_id' => '6',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'West Virginia',
                'region_id' => '6',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Kentucky',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Tennessee',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'North Carolina',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Virginia',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Maryland',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Delaware',
                'region_id' => '7',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'South Carolina',
                'region_id' => '8',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Georgia',
                'region_id' => '8',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Florida',
                'region_id' => '8',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Pennsylvania',
                'region_id' => '9',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'New York',
                'region_id' => '9',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'New Jersey',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Connecticut',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Rhode Island',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Massachusetts',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Vermont',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'New Hampshire',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Maine',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
            [
                'name' => 'Alaska',
                'region_id' => '10',
                'latitude' => '0.12',
                'longitude' => '0.123',
            ],
        ];
        foreach ($states as $state) {
            State::create([
                'name' => $state['name'],
                'region_id' => $state['region_id'],
                'latitude' => $state['latitude'],
                'longitude' => $state['longitude'],
            ]);
        }
    }
}
