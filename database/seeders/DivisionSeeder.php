<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['name' => 'PMED'],
            ['name' => 'PFPD'],
            ['name' => 'DRD'],
            ['name' => 'PDIPBD'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['name' => $division['name']]);
        }
    }
}
