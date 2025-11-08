<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Municity;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $http = new Client();
        $response = $http->get('https://psgc.gitlab.io/api/barangays/');
        $result = $response->getBody()->getContents();
        $barangays = json_decode($result, true);
        // foreach ($barangays as $key => $value) {
        //     $barangays[] = $value;
        // }

        foreach ($barangays as $barangay) {
            $municity = Municity::where('municity_code', $barangay['municipalityCode'])->first();
            if (empty($municity)) {
                $municity = Municity::where('municity_code', $barangay['cityCode'])->firstOrFail();
            }
            Barangay::firstOrCreate([
                'barangay_code' => $barangay['code'],
                'barangay_name' => $barangay['name'],
                'old_name' => $barangay['oldName'],
                'municity_id' => $municity->municity_id,
            ]);
        }
    }
}
