<?php

namespace App\Services;

use App\Models\Specification;

class SpecificationService
{
    public function find(array $data)
    {
        return Specification::query()
            ->where('product_id', $data['product_id'])
            ->get();
    }

    public function store(array $data)
    {
        $specification = new Specification;
        $specification->product_id = $data['product_id'];
        $specification->specification_name = $data['specification_name'];
        $specification->specification_value = $data['specification_value'];
        $specification->save();

        return $specification;
    }

    public function update(array $data)
    {
        $specification = Specification::query()
            ->where('specification_id', $data['specification_id'])
            ->first();

        abort_if(empty($specification), 404, 'Specification not found');

        $specification->specification_name = $data['specification_name'];
        $specification->specification_value = $data['specification_value'];
        $specification->save();

        return $specification;
    }

    public function remove(array $data)
    {
        $specification = Specification::query()
            ->where('specification_id', $data['specification_id'])
            ->first();

        abort_if(empty($specification), 404, 'Specification not found');

        return $specification->delete();
    }
}
