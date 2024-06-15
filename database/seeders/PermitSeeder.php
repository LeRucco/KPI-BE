<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Permit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var \App\Models\User */
        $ardi = User::where('nrp', '=', '1010004')->first();

        $month = 6;

        Permit::create([
            'user_id' => $ardi->id,
            'type' => 1,
            'status' => 1,
            'date' => Carbon::create(2024, $month, 7, 0, 0, 0),
            'description' => 'Ketiban emas 100 Kg'
        ]);
    }
}
