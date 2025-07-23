<?php

namespace App\Services;

use App\Models\Product;
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
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $specification = new Specification;
        $specification->product_id = $data['product_id'];
        $specification->specification_name = $data['specification_name'];
        $specification->specification_value = $data['specification_value'];
        $specification->save();

        $product->load(['specifications', 'featuredImages']);

        return $product;
    }

    public function update(array $data)
    {
        $specification = Specification::query()
            ->where('specification_id', $data['specification_id'])
            ->first();

        $product = Product::query()
            ->where('product_id', $specification->product_id)
            ->first();

        abort_if(empty($specification), 404, 'Specification not found');
        abort_if(empty($product), 404, 'Product not found');

        $specification->specification_name = $data['specification_name'];
        $specification->specification_value = $data['specification_value'];
        $specification->save();

        $product->load(['specifications', 'featuredImages']);

        return $product;
    }

    public function remove(array $data)
    {
        $specification = Specification::query()
            ->where('specification_id', $data['specification_id'])
            ->first();

        $product = Product::query()
            ->where('product_id', $specification->product_id)
            ->first();

        abort_if(empty($specification), 404, 'Specification not found');
        abort_if(empty($product), 404, 'Product not found');

        $specification->delete();

        $product->load(['specifications', 'featuredImages']);

        return $product;
    }
}
