<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Laptop',
                'category_slug' => Str::slug('Laptop'),
                'category_image' => null,
                'editable' => false,
            ],
            [
                'category_name' => 'PC',
                'category_slug' => Str::slug('PC'),
                'category_image' => null,
                'editable' => false,
            ]
        ];

        foreach ($categories as $key => $value) {
            Category::create($value);
        }
    }
}
