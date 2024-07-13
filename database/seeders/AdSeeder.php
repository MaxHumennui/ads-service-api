<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Ad::create([
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'impressions' => $faker->numberBetween(100, 10000),
                'image' => $faker->imageUrl(640, 480, 'ads', true),
                'clicks' => $faker->numberBetween(0, 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
