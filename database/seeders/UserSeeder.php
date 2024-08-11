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
        /// Developer
        User::create([
            'position' => 'GOD',
            'nrp' => 'superadmin',
            'full_name' => 'super admin',
            'password' => Hash::make('superadmin')
        ])->assignRole(RoleEnum::SUPER_ADMIN);

        User::create([
            'nrp' => 'admin',
            'full_name' => 'admin',
            'password' => Hash::make('admin')
        ])->assignRole(RoleEnum::ADMIN);

        User::create([
            'nrp' => 'developer',
            'full_name' => 'developer',
            'password' => Hash::make('qwerty')
        ])->assignRole(RoleEnum::DEVELOPER);

        /// Employee
        User::create([
            'position' => 'DIREKTUR',
            'nrp' => '1010001',
            'full_name' => 'Adhetya Rakhmani Fauzi',
            'password' => Hash::make('1010001')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'ACCOUNTING',
            'nrp' => '1010002',
            'full_name' => 'Indah Susanti',
            'password' => Hash::make('1010002')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010003',
            'full_name' => 'Nur Wahyudi',
            'password' => Hash::make('1010003')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010005',
            'full_name' => 'Rinto Tonapa',
            'password' => Hash::make('1010005')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010006',
            'full_name' => 'Akmal Qahdafiq',
            'password' => Hash::make('1010006')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010007',
            'full_name' => 'Endro Setyo Laksono',
            'password' => Hash::make('1010007')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'MARKETING',
            'nrp' => '1010008',
            'full_name' => 'Resgi Kurniawan',
            'password' => Hash::make('1010008')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'GENERAL ADMIN',
            'nrp' => '1010009',
            'full_name' => 'Chasiati Rahmadana',
            'password' => Hash::make('1010009')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010012',
            'full_name' => 'Pitri Andrianto',
            'password' => Hash::make('1010012')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010013',
            'full_name' => 'Hery Lesmana',
            'password' => Hash::make('1010013')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'ADMIN MARKETING',
            'nrp' => '1010014',
            'full_name' => 'Riska Noviya Enjelina',
            'password' => Hash::make('1010014')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'ADMIN',
            'nrp' => '1010015',
            'full_name' => 'Sari Putriani',
            'password' => Hash::make('1010015')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010016',
            'full_name' => 'Mohammad Iskandar Zulkarnain',
            'password' => Hash::make('1010016')
        ])->assignRole(RoleEnum::EMPLOYEE);

        User::create([
            'position' => 'TRAINER',
            'nrp' => '1010017',
            'full_name' => 'Abdul Rachman',
            'password' => Hash::make('1010017')
        ])->assignRole(RoleEnum::EMPLOYEE);
    }
}
