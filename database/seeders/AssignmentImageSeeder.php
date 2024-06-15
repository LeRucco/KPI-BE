<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentImage;
use Illuminate\Database\Seeder;

class AssignmentImageSeeder extends Seeder
{

    public function run(): void
    {
        for ($i = 1; $i <= 31; $i++) {
            if (
                $i == 6 || $i == 7 ||
                $i == 13 || $i == 14 ||
                $i == 20 || $i == 21 ||
                $i == 27 || $i == 28
            )
                continue;
        }

        // foreach (Assignment::all(['id']) as $assignment) {

        //     AssignmentImage::create([
        //         'assignment_id' => $assignment->id,
        //         'image' => fake()->imageUrl(400, 400)
        //     ]);

        //     // WKWKWKWKWK biar ada multiple image aja sih ini
        //     if ($assignment->id % 3 == 0)
        //         AssignmentImage::create([
        //             'assignment_id' => $assignment->id,
        //             'image' => fake()->imageUrl(400, 400)
        //         ]);
        // }
    }
}
