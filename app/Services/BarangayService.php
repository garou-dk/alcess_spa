<?php

namespace App\Services;

use App\Models\Barangay;
use App\Models\Municity;

class BarangayService
{
    public function index(array $data) {
        $found = Municity::query()
            ->where('municity_id', $data['municity_id'])
            ->exists();
        
        abort_unless($found, 404, 'Municity not found.');
        
        return Barangay::query()
            ->where('municity_id', $data['municity_id'])
            ->orderBy('barangay_name')
            ->get();
    }
}
