<?php

namespace Database\Seeders;

use App\Enums\AppConfigurationEnum;
use App\Models\AppConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => AppConfigurationEnum::OnlineBankAccount->value,
                'value' => null,
            ],
            [
                'key' => AppConfigurationEnum::OnlineBankScreenShot->value,
                'value' => null,
            ]
        ];

        foreach ($data as $value) {
            AppConfiguration::firstOrCreate($value);
        }
    }
}
