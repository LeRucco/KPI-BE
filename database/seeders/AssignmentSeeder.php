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
        $developer = User::where('full_name', '=', 'developer')->first();

        for ($i = 1; $i <= 31; $i++) {
            if (
                $i == 6 || $i == 7 ||
                $i == 13 || $i == 14 ||
                $i == 20 || $i == 21 ||
                $i == 27 || $i == 28
            )
                continue;
            // developer
            Assignment::create([
                'user_id' => $developer->id,
                'work_id' => rand(1, 10),
                'date' => Carbon::createFromDate($year = 2024, $month = 1, $day = $i),
                'description' => fake()->text(),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
            Assignment::create([
                'user_id' => $developer->id,
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
