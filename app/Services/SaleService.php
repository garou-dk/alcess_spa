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
            $productIds = collect($data["products"])->pluck("product_id")->toArray();

            $productList = Product::query()
                ->whereIn("product_id", $productIds)
                ->get();

            $total_amount = 0;
            foreach ($data["products"] as $item) {
                $total_amount += $item["quantity"] * $productList->where("product_id", $item["product_id"])->value("product_price");
            }

            $sale = Sale::query()->create([
                "sale_code" => Uuid::uuid4()->toString(),
                "user_id" => $user->user_id,
                "total_amount" => $total_amount,
                "payment_method" => $data["payment_method"]
            ]);
            $products = [];
            foreach ($data["products"] as $item) {
                $products[] = [
                    "sale_id" => $sale->sale_id, 
                    "product_id" => $item["product_id"], 
                    "quantity" => $item["quantity"], 
                    "price" => $productList->where("product_id", $item["product_id"])->value("product_price")
                ];
                Product::query()
                    ->where("product_id", $item["product_id"])
                    ->decrement("product_quantity", $item["quantity"]);
            }
            $sale->saleItems()->createMany($products);

            $sale->refresh();

            $sale->load('saleItems');

            return $sale;
        });
    }

    public function getSale(array $data) {
        $sale = Sale::query()
            ->with(['saleItems.product', 'user'])
            ->where('sale_id', $data['sale_id'])
            ->first();

        abort_if(empty($sale), 404, 'Sale not found');

        return $sale;
    }
}
