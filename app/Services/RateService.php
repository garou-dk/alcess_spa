<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\ProductOrder;
use App\Models\Rate;

class RateService
{
    public function addRate(array $data) {
        $productOrder = ProductOrder::query()
            ->with(['order'])
            ->where([
                'product_order_id' => $data['product_order_id'],
            ])
            ->first();

        abort_if(empty($productOrder), 404, 'Product order not found.');
        
        // Only allow rating for completed orders (orders marked as received by customer)
        abort_if($productOrder->order->status != OrderStatusEnum::COMPLETED->value, 400, 'Order must be completed before rating.');

        abort_if($productOrder->order->user_id != $data['user_id'], 400, 'You are not allowed to rate this order.');
        
        // Check if this product order has already been reviewed
        $existingRate = Rate::query()
            ->where('product_order_id', $data['product_order_id'])
            ->first();
        
        abort_if(!empty($existingRate), 422, 'You have already submitted a review for this order.');
        
        $rate = Rate::query()
            ->create([
                'product_id' => $productOrder->product_id,
                'user_id' => $data['user_id'],
                'product_order_id' => $data['product_order_id'],
                'rate' => $data['rate'],
                'comment' => $data['comment'] ?? null,
            ]);

        // Handle media uploads
        if (isset($data['media']) && is_array($data['media'])) {
            $manageFileService = new ManageFileService();
            
            foreach ($data['media'] as $file) {
                $result = $manageFileService->saveFile($file, \App\Enums\FileDirectoryEnum::RATE_IMAGE->value, 'public');
                
                $rate->images()->create([
                    'image_path' => $result['file_name'],
                ]);
            }
        }

        $rate->load(['images']);

        return $rate;
    }

    public function addReply(array $data) {
        $rate = Rate::query()
            ->where('rate_id', $data['rate_id'])
            ->first();

        abort_if(empty($rate), 404, 'Rate not found.');

        abort_unless(empty($rate->reply), 422, 'This rate already has a reply.');

        $rate->reply = $data['reply'];

        $rate->save();

        return $rate;
    }

    public function fetchCommentsWithoutReply() {
        $rate = Rate::query()
            ->whereNull('reply')
            ->with(['user:user_id,full_name', 'product', 'images'])
            ->oldest()
            ->get();

        return $rate;
    }

    public function fetchAllRatings() {
        return Rate::query()
            ->with(['user:user_id,full_name', 'product', 'images'])
            ->latest()
            ->get();
    }

    public function toggleLike(string $rateId, int $userId) {
        $rate = Rate::query()
            ->where('rate_id', $rateId)
            ->first();

        abort_if(empty($rate), 404, 'Rate not found.');

        $existingLike = $rate->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $rate->likes()->create(['user_id' => $userId]);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'likes_count' => $rate->likes()->count(),
        ];
    }
}
