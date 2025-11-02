<?php

namespace Database\Seeders;

use App\Models\Municity;
use App\Models\Province;
use App\Models\Region;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $http = new Client();
        $response = $http->get('https://psgc.gitlab.io/api/cities-municipalities/');
        $result = $response->getBody()->getContents();
        $municities = json_decode($result, true);

        foreach ($municities as $municity) {
            $province = Province::where('province_code', $municity['provinceCode'])->first();
            if (empty($province)) {
                $region = Region::where('region_code', $municity['regionCode'])->firstOrFail();
                $province = Province::firstOrCreate([
                    'province_code' => $municity['code'],
                    'province_name' => $municity['name'],
                    'region_id' => $region->region_id,
                ]);
            }
            Municity::firstOrCreate([
                'municity_code' => $municity['code'],
                'municity_name' => $municity['name'],
                'province_id' => $province->province_id,
            ]);
        }
    }
}
