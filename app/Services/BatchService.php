<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Product;

class BatchService
{
    public function createBatch(array $data) {
        $authService = new AuthService;
        $user = $authService->getAuth();
        $batch = Batch::query()->create([
            'user_id' => $user->user_id
        ]);

        $batch->batchProducts()->createMany($data['batch_products']);

        foreach ($data['batch_products'] as $value) {
            $product = Product::query()
                ->where('product_id', $value['product_id'])
                ->first();

            $product->product_quantity = $product->product_quantity + $value['quantity'];
            $product->batch_id = $batch->batch_id;
            
            $product->save();
        }

        return $batch;
    }

    public function recommendedBatch() {
        $latestBatch = Batch::query()->latest()->first();
        $batchId = 1;
        if (!empty($latestBatch)) {
            $batchId = $latestBatch->batch_id + 1;
        }
        return [
            'batch_id' => $batchId,
            'batch_products' => Product::query()
                ->with(['category'])
                ->whereColumn('product_quantity', '<=', 'low_stock_threshold')
                ->where('is_active', true)
                ->get()
        ];
    }
}
