<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Product;

class BatchService
{
    /**
     * Generate a batch number per product in the format DDMMYY-N
     * where DD is day, MM is month, YY is year, and N is a sequential counter per product
     * Each product has its own counter that continues throughout the year and resets yearly
     * 
     * @param int $productId The product ID to generate batch number for
     * @return string The generated batch number (e.g., "211125-1")
     */
    public function generateBatchNumber(int $productId): string
    {
        // Get current date
        $date = now(); // Uses server timezone
        
        // Format date as DDMMYY
        $datePrefix = $date->format('dmy'); // d=day (01-31), m=month (01-12), y=2-digit year
        
        // Get the 2-digit year from the date prefix (last 2 characters)
        $currentYear = substr($datePrefix, -2);
        
        // Query for existing batches with the same year for this specific product
        // Match any batch number that ends with the same year (e.g., ______YY-N)
        $latestBatch = Batch::query()
            ->where('batch_number', 'LIKE', '%' . $currentYear . '-%')
            ->whereHas('batchProducts', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->orderByRaw('CAST(SUBSTRING_INDEX(batch_number, "-", -1) AS UNSIGNED) DESC')
            ->first();
        
        // Extract counter and increment
        if ($latestBatch) {
            $parts = explode('-', $latestBatch->batch_number);
            $counter = (int)$parts[1] + 1;
        } else {
            $counter = 1;
        }
        
        // Return formatted batch number: DDMMYY-N
        return $datePrefix . '-' . $counter;
    }

    /**
     * Create a batch for a product stock addition
     * 
     * @param int $productId The product ID
     * @param int $quantity The quantity being added
     * @param int $userId The user ID performing the stock addition
     * @return Batch The created batch instance
     */
    public function createBatchForProduct(int $productId, int $quantity, int $userId): Batch
    {
        // Generate unique batch number per product
        $batchNumber = $this->generateBatchNumber($productId);
        
        // Create batch record with batch_number and user_id
        $batch = Batch::query()->create([
            'batch_number' => $batchNumber,
            'user_id' => $userId
        ]);
        
        // Create batch_products record linking batch to product
        $batch->batchProducts()->create([
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
        
        // Return batch instance
        return $batch;
    }

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
                ->whereRaw('product_quantity <= low_stock_threshold')
                ->where('is_active', true)
                ->get()
        ];
    }

    /**
     * Filter batches by batch number
     * 
     * Allows querying and filtering batches by their batch_number field.
     * Supports exact match or partial match using LIKE operator.
     * 
     * @param string $batchNumber The batch number to filter by
     * @param bool $exactMatch Whether to use exact match (true) or partial match (false)
     * @return \Illuminate\Database\Eloquent\Collection Collection of matching batches
     */
    public function filterByBatchNumber(string $batchNumber, bool $exactMatch = true)
    {
        $query = Batch::query()
            ->with(['batchProducts.product', 'user']);
        
        if ($exactMatch) {
            $query->where('batch_number', $batchNumber);
        } else {
            $query->where('batch_number', 'LIKE', '%' . $batchNumber . '%');
        }
        
        return $query->get();
    }

    /**
     * Get batch history for a specific product
     * 
     * Retrieves all batches that have been associated with a product,
     * ordered by creation date (most recent first).
     * 
     * @param int $productId The product ID to retrieve batch history for
     * @return \Illuminate\Database\Eloquent\Collection Collection of batches with their details
     */
    public function getBatchHistoryForProduct(int $productId)
    {
        return Batch::query()
            ->with(['batchProducts' => function ($query) use ($productId) {
                $query->where('product_id', $productId);
            }, 'user'])
            ->whereHas('batchProducts', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
