<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Major::create([
            'code' => 'CSH',
            'name' => 'Ciencia Socias no Humanidade'
        ]);

        Major::create([
            'code' => 'CT',
            'name' => 'Ciencia Teknolojia'
        ]);
    }
}
