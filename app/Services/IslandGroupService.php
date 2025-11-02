<?php

namespace App\Services;

use App\Models\IslandGroup;

class IslandGroupService
{
    public function getAll() {
        return IslandGroup::all();
    }
}
