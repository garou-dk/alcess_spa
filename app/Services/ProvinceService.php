<?php

namespace App\Services;

use App\Models\Province;
use App\Models\Region;

class ProvinceService
{
    public function index(array $data) {
        $found = Region::query()
            ->where('region_id', $data['region_id'])
            ->exists();
        
        abort_unless($found, 404, 'Region not found.');
        
        return Province::query()
            ->where('region_id', $data['region_id'])
            ->orderBy('province_name')
            ->get();
    }
}
