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
        
        abort_if($productOrder->order->status != OrderStatusEnum::DELIVERED->value, 400, 'Order is not delivered.');

        abort_if($productOrder->order->user_id != $data['user_id'], 400, 'You are not allowed to rate this order.');
        
        $rate = Rate::query()
            ->create([
                'product_id' => $productOrder->product_id,
                'user_id' => $data['user_id'],
                'product_order_id' => $data['product_order_id'],
                'rate' => $data['rate'],
                'comment' => $data['comment'] ?? null,
            ]);

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
            ->with(['user:user_id,full_name', 'product'])
            ->oldest()
            ->get();

        return $rate;
    }
}
