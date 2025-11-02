<?php

namespace Database\Seeders;

use App\Models\IslandGroup;
use App\Models\Region;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $http = new Client();
        $response = $http->get('https://psgc.gitlab.io/api/regions/');
        $result = $response->getBody()->getContents();
        $regions = json_decode($result, true);

        foreach ($regions as $region) {
            $islandGroup = IslandGroup::where('island_group_code', $region['islandGroupCode'])->firstOrFail();

            Region::firstOrCreate([
                'region_code' => $region['code'],
                'region_name' => $region['regionName'],
                'region_name_long' => $region['name'],
                'island_group_id' => $islandGroup->island_group_id,
            ]);
        }
    }
}
