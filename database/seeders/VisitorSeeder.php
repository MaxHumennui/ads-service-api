<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visitor;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Define the countries, regions, and cities
        $locations = [
            'USA' => [
                'California' => ['Los Angeles', 'San Francisco', 'San Diego'],
                'Texas' => ['Houston', 'Dallas', 'Austin'],
                'New York' => ['New York City', 'Buffalo', 'Rochester']
            ],
            'Canada' => [
                'Ontario' => ['Toronto', 'Ottawa', 'Mississauga'],
                'Quebec' => ['Montreal', 'Quebec City', 'Laval'],
                'British Columbia' => ['Vancouver', 'Victoria', 'Surrey']
            ],
            'UK' => [
                'England' => ['London', 'Manchester', 'Birmingham'],
                'Scotland' => ['Edinburgh', 'Glasgow', 'Aberdeen'],
                'Wales' => ['Cardiff', 'Swansea', 'Newport']
            ],
            'Australia' => [
                'New South Wales' => ['Sydney', 'Newcastle', 'Wollongong'],
                'Victoria' => ['Melbourne', 'Geelong', 'Ballarat'],
                'Queensland' => ['Brisbane', 'Gold Coast', 'Cairns']
            ],
        ];

        // Majority of visitors from USA
        $majorityCountry = 'USA';
        $majorityCount = 70; // 70% from USA
        $minorityCount = 30; // 30% from other countries

        // Generate majority visitors
        for ($i = 0; $i < $majorityCount; $i++) {
            $country = $majorityCountry;
            $region = $faker->randomElement(array_keys($locations[$country]));
            $city = $faker->randomElement($locations[$country][$region]);

            Visitor::create([
                'ip_address' => hash('sha256', $faker->ipv4),
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'ad_clicks' => []
            ]);
        }

        // Generate minority visitors
        for ($i = 0; $i < $minorityCount; $i++) {
            $country = $faker->randomElement(array_diff(array_keys($locations), [$majorityCountry]));
            $region = $faker->randomElement(array_keys($locations[$country]));
            $city = $faker->randomElement($locations[$country][$region]);

            Visitor::create([
                'ip_address' => hash('sha256', $faker->ipv4),
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'ad_clicks' => []
            ]);
        }
    }
}
