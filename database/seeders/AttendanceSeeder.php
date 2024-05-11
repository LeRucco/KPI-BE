<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{

    public function run(): void
    {
        /** @var \App\Models\User */
        $kusdi = User::where('nrp', '=', '50110041')->first();

        /** @var \App\Models\User */
        $nur = User::where('nrp', '=', '1010003')->first();

        for ($i = 1; $i <= 31; $i++) {
            if (
                $i == 6 || $i == 7 ||
                $i == 13 || $i == 14 ||
                $i == 20 || $i == 21 ||
                $i == 27 || $i == 28
            )
                continue;
            // asep Karyawan
            Attendance::create([
                'user_id' => $kusdi->id,
                'clock_in' => Carbon::createFromTime($hour = 8, $minute = 0, $second = $i - 1),
                // 'clock_out' => fake()->time('H:i:s', '17:0:0'),
                'description' => fake()->text(),
                'status' => rand(1, 2),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
            Attendance::create([
                'user_id' => $kusdi->id,
                // 'clock_in' => fake()->time('H:i:s', '8:0:0'),
                'clock_in' => Carbon::createFromTime($hour = 17, $minute = 0, $second = $i - 1),
                'description' => fake()->text(),
                'status' => rand(1, 2),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);

            // budi Karyawan
            Attendance::create([
                'user_id' => $nur->id,
                'clock_in' => Carbon::createFromTime($hour = 8, $minute = 0, $second = $i - 1),
                // 'clock_out' => fake()->time('H:i:s', '17:0:0'),
                'description' => fake()->text(),
                'status' => rand(1, 2),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
            Attendance::create([
                'user_id' => $nur->id,
                // 'clock_in' => fake()->time('H:i:s', '8:0:0'),
                'clock_in' => Carbon::createFromTime($hour = 17, $minute = 0, $second = $i - 1),
                'description' => fake()->text(),
                'status' => rand(1, 2),
                'latitude' => fake()->latitude($min = -90, $max = 90),
                'longitude' => fake()->longitude($min = -180, $max = 180),
                'location_address' => fake()->streetAddress()
            ]);
        }
    }
}
