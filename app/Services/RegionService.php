<?php

namespace App\Services;

use App\Models\IslandGroup;
use App\Models\Region;

class RegionService
{
    public function index(array $data) {
        $islandGroup = IslandGroup::query()
            ->where('island_group_id', $data['island_group_id'])
            ->exists();
        
        abort_unless($islandGroup, 404, 'Island Group not found.');
        
        return Region::query()
            ->where('island_group_id', $data['island_group_id'])
            ->orderBy('region_name')
            ->get();
    }
}
