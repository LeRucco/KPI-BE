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
        $developer = User::where('nrp', '=', '1010001')->first();

        $month = 7;

        // Senin
        Attendance::create([
            'user_id' => $developer->id,
            'clock_in' => Carbon::create(2024, $month, 3, 8, 0, 0),
            'description' => fake()->text(),
            'status' => 2,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);
        Attendance::create([
            'user_id' => $developer->id,
            'clock_out' => Carbon::create(2024, $month, 3, 17, 0, 0),
            'description' => fake()->text(),
            'status' => 2,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);

        // Selasa
        Attendance::create([
            'user_id' => $developer->id,
            'clock_in' => Carbon::create(2024, $month, 4, 8, 30, 0),
            'description' => fake()->text(),
            'status' => 1,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);
        Attendance::create([
            'user_id' => $developer->id,
            'clock_out' => Carbon::create(2024, $month, 4, 17, 0, 0),
            'description' => fake()->text(),
            'status' => 2,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);

        // Rabu
        Attendance::create([
            'user_id' => $developer->id,
            'clock_in' => Carbon::create(2024, $month, 5, 8, 0, 0),
            'description' => fake()->text(),
            'status' => 2,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);
        Attendance::create([
            'user_id' => $developer->id,
            'clock_out' => Carbon::create(2024, $month, 5, 16, 30, 0),
            'description' => fake()->text(),
            'status' => 1,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);

        // Kamis
        Attendance::create([
            'user_id' => $developer->id,
            'clock_in' => Carbon::create(2024, $month, 6, 8, 30, 0),
            'description' => fake()->text(),
            'status' => 1,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);

        Attendance::create([
            'user_id' => $developer->id,
            'clock_out' => Carbon::create(2024, $month, 6, 16, 30, 0),
            'description' => fake()->text(),
            'status' => 1,
            'latitude' => fake()->latitude($min = -90, $max = 90),
            'longitude' => fake()->longitude($min = -180, $max = 180),
            'location_address' => fake()->streetAddress()
        ]);

        // Jumat Permit Seeder

        // for ($i = 1; $i <= 31; $i++) {
        //     if (
        //         $i == 6 || $i == 7 ||
        //         $i == 13 || $i == 14 ||
        //         $i == 20 || $i == 21 ||
        //         $i == 27 || $i == 28
        //     )
        //         continue;


        //     // asep Karyawan
        //     Attendance::create([
        //         'user_id' => $developer->id,
        //         'clock_in' => Carbon::create(2024, $month, $i, rand(8), 0, 0),
        //         // 'clock_out' => fake()->time('H:i:s', '17:0:0'),
        //         'description' => fake()->text(),
        //         'status' => rand(1, 2),
        //         'latitude' => fake()->latitude($min = -90, $max = 90),
        //         'longitude' => fake()->longitude($min = -180, $max = 180),
        //         'location_address' => fake()->streetAddress()
        //     ]);
        //     Attendance::create([
        //         'user_id' => $developer->id,
        //         // 'clock_in' => fake()->time('H:i:s', '8:0:0'),
        //         'clock_out' => Carbon::create(2024, $month, $i, rand(16, 17), 0, 0),
        //         'description' => fake()->text(),
        //         'status' => rand(1, 2),
        //         'latitude' => fake()->latitude($min = -90, $max = 90),
        //         'longitude' => fake()->longitude($min = -180, $max = 180),
        //         'location_address' => fake()->streetAddress()
        //     ]);

        //     // nur Karyawan
        //     Attendance::create([
        //         'user_id' => $nur->id,
        //         'clock_in' => Carbon::create(2024, $month, $i, rand(8, 9), 0, 0),
        //         // 'clock_out' => fake()->time('H:i:s', '17:0:0'),
        //         'description' => fake()->text(),
        //         'status' => rand(1, 2),
        //         'latitude' => fake()->latitude($min = -90, $max = 90),
        //         'longitude' => fake()->longitude($min = -180, $max = 180),
        //         'location_address' => fake()->streetAddress()
        //     ]);
        //     Attendance::create([
        //         'user_id' => $nur->id,
        //         // 'clock_in' => fake()->time('H:i:s', '8:0:0'),
        //         'clock_out' => Carbon::create(2024, $month, $i, rand(16, 17), 0, 0),
        //         'description' => fake()->text(),
        //         'status' => rand(1, 2),
        //         'latitude' => fake()->latitude($min = -90, $max = 90),
        //         'longitude' => fake()->longitude($min = -180, $max = 180),
        //         'location_address' => fake()->streetAddress()
        //     ]);
        // }
    }
}
