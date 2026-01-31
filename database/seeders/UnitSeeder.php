<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'unit_name' => 'Piece',
            ],
        ];

        foreach ($units as $key => $value) {
            Unit::updateOrCreate(
                ['unit_name' => $value['unit_name']],
                $value
            );
        }
    }
}
