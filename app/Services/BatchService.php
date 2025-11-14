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
                ->whereColumn('product_quantity', '<=', 'low_stock_threshold')
                ->where('is_active', true)
                ->get()
        ];
    }
}
