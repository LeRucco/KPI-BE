<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Work;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Work::create([
            'name' => 'Pendidikan',
            'description' => 'Mengajari anak murid di sebuah instansi'
        ]);
        Work::create([
            'name' => 'Bertani',
            'description' => 'Bersama pak Tani menanam padi dan menyangkol'
        ]);
        Work::create([
            'name' => 'Berkebun',
            'description' => 'Menyemai dan menanam ladang jagung bersama rekan-rekan'
        ]);
        Work::create([
            'name' => 'Masak',
            'description' => 'Masak nasi goreng kambing menggunakan bumbu spesial'
        ]);
        Work::create([
            'name' => 'Kerajinan Kayu',
            'description' => 'Mengukir kayu jati menggunakan jarum peniti'
        ]);

        Work::create([
            'name' => 'Ngaduk semen',
            'description' => 'Semen sepuluh roda pake blender'
        ]);
        Work::create([
            'name' => 'Instalasi listrik',
            'description' => 'Electro Wizard Craftmans'
        ]);
        Work::create([
            'name' => 'Painting dinding',
            'description' => 'Warna dinding polkadot pink ungu'
        ]);
        Work::create([
            'name' => 'Tandon air',
            'description' => 'Waterbender'
        ]);
        Work::create([
            'name' => 'Kelaparan',
            'description' => 'Mukbang 10 nasi padang ala Tanboy kun'
        ]);
    }
}
