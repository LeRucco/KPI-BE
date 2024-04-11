<?php

namespace Database\Seeders;

use App\Models\Work;
use App\Models\WorkRatio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkRatioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Work::all() as $work)
            WorkRatio::create([
                'work_id' => $work->id,
                'percentage' => 10.00
            ]);
    }
}
