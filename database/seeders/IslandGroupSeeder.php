<?php

namespace Database\Seeders;

use App\Models\IslandGroup;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IslandGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $http = new Client();
        $response = $http->get('https://psgc.gitlab.io/api/island-groups/');
        $result = $response->getBody()->getContents();
        $islandGroups = json_decode($result, true);

        foreach ($islandGroups as $islandGroup) {
            IslandGroup::firstOrCreate([
                'island_group_code' => $islandGroup['code'],
                'island_group_name' => $islandGroup['name'],
            ]);
        }
    }
}
