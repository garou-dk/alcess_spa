<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Region;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $http = new Client();
        $response = $http->get('https://psgc.gitlab.io/api/provinces/');
        $result = $response->getBody()->getContents();
        $provinces = json_decode($result, true);

        foreach ($provinces as $province) {
            $region = Region::where('region_code', $province['regionCode'])->firstOrFail();

            Province::firstOrCreate([
                'province_code' => $province['code'],
                'province_name' => $province['name'],
                'region_id' => $region->region_id,
            ]);
        }
    }
}
