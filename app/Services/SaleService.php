<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class SaleService
{
    public function recordSale(array $data) {
        return DB::transaction(function () use ($data) {
            $authService = new AuthService();
            $user = $authService->getAuth();
            $total_amount = 0;

            $productIds = collect($data["products"])->pluck("product_id")->toArray();

            $productList = Product::query()
                ->whereIn("product_id", $productIds)
                ->get();

            $sale = Sale::query()->create([
                "sale_code" => Uuid::uuid4()->toString(),
                "user_id" => $user->user_id,
                "total_amount" => $total_amount,
            ]);
            $products = [];
            foreach ($data["products"] as $item) {
                $products[] = [
                    "sale_id" => $sale->sale_id, 
                    "product_id" => $item["product_id"], 
                    "quantity" => $item["quantity"], 
                    "price" => $productList->where("product_id", $item["product_id"])->value("product_price")
                ];
            }
            $sale->saleItems()->saveMany($products);

            $sale->refresh();

            $sale->load('saleItems');

            return $sale;
        });
    }
}
