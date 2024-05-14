<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nrp' => 'superadmin',
            'full_name' => 'super admin',
            'password' => Hash::make('superadmin')
        ])->assignRole(RoleEnum::SUPER_ADMIN);

        User::create([
            'nrp' => 'admin',
            'full_name' => 'admin',
            'password' => Hash::make('admin')
        ])->assignRole(RoleEnum::ADMIN);

        // User::create([
        //     'nrp' => 'developer',
        //     'full_name' => 'developer',
        //     'password' => Hash::make('developer')
        // ])->assignRole(RoleEnum::DEVELOPER);

        User::create([
            'nrp' => '50110041',
            'full_name' => 'Kusdi',
            'password' => Hash::make('50110041')
        ])->assignRole(RoleEnum::ADMIN);

        User::create([
            'nrp' => '1010004',
            'full_name' => 'Ardi Ananta Dwi Prastyo',
            'password' => Hash::make('1010004')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'nrp' => '1010003',
            'full_name' => 'Nur Wahyudi',
            'password' => Hash::make('1010003')
        ])->assignRole(RoleEnum::EMPLOYEE);

        // User::create([
        //     'nrp' => 'asep',
        //     'full_name' => 'asep',
        //     'password' => Hash::make('asep')
        // ])->assignRole(RoleEnum::EMPLOYEE);

        // User::create([
        //     'nrp' => 'budi',
        //     'full_name' => 'budi',
        //     'password' => Hash::make('budi')
        // ])->assignRole(RoleEnum::EMPLOYEE);

        // User::create([
        //     'nrp' => 'coco',
        //     'full_name' => 'coco',
        //     'password' => Hash::make('coco')
        // ]);
    }
}
