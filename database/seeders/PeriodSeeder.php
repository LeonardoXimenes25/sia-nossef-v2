<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Period::Create([
            'academic_year_id' => 1,
            'name' => 'primeiru',
            'order' => 1,
            'start_date' => '2025-01-01',
            'end_date' => '2025-03-01',
        ]);

        Period::Create([
            'academic_year_id' => 1,
            'name' => 'Segundu',
            'order' => 2,
            'start_date' => '2025-05-01',
            'end_date' => '2025-9-01',
        ]);

        Period::Create([
            'academic_year_id' => 1,
            'name' => 'Terceiru',
            'order' => 3,
            'start_date' => '2025-10-01',
            'end_date' => '2025-12-01',
        ]);
    }
}
