<?php

namespace App\Services;

use App\Models\Unit;

class UnitService
{
    public function fetchUnits()
    {
        return Unit::query()
            ->orderBy('unit_name', 'asc')
            ->withTrashed()
            ->get();
    }

    public function addUnit(array $data)
    {
        return Unit::create($data);
    }

    public function updateUnit(array $data)
    {
        $unit = Unit::query()
            ->where('unit_id', $data['unit_id'])
            ->first();

        abort_if(empty($unit), 404, 'Unit not found');

        $unit->unit_name = $data['unit_name'];
        $unit->save();

        return $unit;
    }

    public function deleteUnit(array $data)
    {
        $unit = Unit::query()
            ->where('unit_id', $data['unit_id'])
            ->first();

        abort_if(empty($unit), 404, 'Unit not found');

        return $unit->delete();
    }

    public function recoverUnit(array $data)
    {
        $unit = Unit::onlyTrashed()
            ->where('unit_id', $data['unit_id'])
            ->first();

        abort_if(empty($unit), 404, 'Unit not found');

        return $unit->restore();
    }
}
