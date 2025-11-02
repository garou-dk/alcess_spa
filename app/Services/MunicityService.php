<?php

namespace App\Services;

use App\Models\Municity;
use App\Models\Province;

class MunicityService
{
    public function index(array $data) {
        $found = Province::query()
            ->where('province_id', $data['province_id'])
            ->exists();
        
        abort_unless($found, 404, 'Province not found.');
        
        return Municity::query()
            ->where('province_id', $data['province_id'])
            ->orderBy('municity_name')
            ->get();
    }
}
