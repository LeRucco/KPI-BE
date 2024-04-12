<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{

    public function run(): void
    {
        /** @var \App\Models\User */
        $asep = User::where('full_name', '=', 'asep')->first();

        /** @var \App\Models\User */
        $budi = User::where('full_name', '=', 'budi')->first();

        for ($i = 1; $i <= 31; $i++) {
            if (
                $i == 6 || $i == 7 ||
                $i == 13 || $i == 14 ||
                $i == 20 || $i == 21 ||
                $i == 27 || $i == 28
            )
                continue;
            // asep
            Assignment::create([
                'user_id' => $asep->id,
                'work_id' => rand(1, 10),
                'date' => Carbon::createFromDate($year = 2024, $month = 1, $day = $i),
                'description' => fake()->text(),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
            Assignment::create([
                'user_id' => $asep->id,
                'work_id' => rand(1, 10),
                'date' => Carbon::createFromDate($year = 2024, $month = 1, $day = $i),
                'description' => fake()->text(),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);

            // budi
            Assignment::create([
                'user_id' => $budi->id,
                'work_id' => rand(1, 10),
                'date' => Carbon::createFromDate($year = 2024, $month = 1, $day = $i),
                'description' => fake()->text(),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
            Assignment::create([
                'user_id' => $budi->id,
                'work_id' => rand(1, 10),
                'date' => Carbon::createFromDate($year = 2024, $month = 1, $day = $i),
                'description' => fake()->text(),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
        }
    }
}
