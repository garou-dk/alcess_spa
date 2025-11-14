<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Events\ProductActiveCountEvent;
use App\Events\ProductCountEvent;
use App\Events\ProductEvent;
use App\Events\ProductLowStockCountEvent;
use App\Events\ProductOutStockCountEvent;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rate;

class ProductService
{
    public function bestSelling()
    {
        $products = Product::query()
            ->with(['category'])
            ->where('is_active', true)
            ->where('available_online', true)
            ->whereNot('product_quantity', 0)
            ->take(5)
            ->get();

        return $products;
    }

    public function index(array $data)
    {
        $products = Product::query();
        $products->with(['category', 'unit', 'specifications', 'featuredImages']);

        if (isset($data['category_id'])) {
            $products->where('category_id', $data['category_id']);
        }

        if (! empty($data['search']) && ! blank($data['search'])) {
            $products->whereLike('product_name', "%{$data['search']}%");
        }

        if (isset($data['status'])) {
            $products->where('is_active', $data['status']);
        }

        if (isset($data['low_stock']) && $data['low_stock'] === true) {
            $products->whereColumn('product_quantity', '<=', 'low_stock_threshold');
        }

        if (isset($data['available_online']) && $data['available_online'] === true) {
            $products->where('available_online', true);
        }

        $products->orderBy('product_name', 'asc');

        return $products->paginate($data['limit'] ?? 5);
    }

    public function store(array $data)
    {
        $product = new Product;
        $product->product_name = $data['product_name'];
        $product->description = $data['description'];
        $product->category_id = $data['category_id'];
        $product->unit_id = $data['unit_id'];
        $product->product_price = $data['product_price'];
        $product->product_quantity = $data['product_quantity'];
        $product->low_stock_threshold = $data['low_stock_threshold'];
        $product->is_active = $data['is_active'];
        $product->sku = $data['sku'] ?? null;
        $product->available_online = $data['available_online'];
        $product->save();

        $product->load(['category', 'unit']);

        $product = $product->toArray();

        $product['specifications'] = [];
        $product['featured_images'] = [];

        ProductEvent::dispatch($product);
        ProductCountEvent::dispatch($this->countAllProduct()['result']);
        ProductLowStockCountEvent::dispatch($this->countLowStock()['result']);
        ProductOutStockCountEvent::dispatch($this->outOfStockCount()['result']);
        ProductActiveCountEvent::dispatch($this->activeProductCount()['result']);

        return $product;
    }

    public function update(array $data)
    {
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $product->product_name = $data['product_name'];
        $product->description = $data['description'];
        $product->category_id = $data['category_id'];
        $product->unit_id = $data['unit_id'];
        $product->product_price = $data['product_price'];
        $product->product_quantity = $data['product_quantity'];
        $product->low_stock_threshold = $data['low_stock_threshold'];
        $product->is_active = $data['is_active'];
        $product->sku = $data['sku'] ?? null;
        $product->available_online = $data['available_online'];
        $product->save();

        $product->load(['specifications', 'featuredImages', 'category', 'unit']);

        ProductEvent::dispatch($product->toArray());
        ProductCountEvent::dispatch($this->countAllProduct()['result']);
        ProductLowStockCountEvent::dispatch($this->countLowStock()['result']);
        ProductOutStockCountEvent::dispatch($this->outOfStockCount()['result']);
        ProductActiveCountEvent::dispatch($this->activeProductCount()['result']);

        return $product;
    }

    public function updateImage(array $data)
    {
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $oldImage = $product->product_image;

        $manageFileService = new ManageFileService;

        $result = $manageFileService->saveFile($data['product_image'], FileDirectoryEnum::PRODUCT_IMAGE->value, 'public');
        $product->product_image = $result['file_name'];
        $product->save();

        if ($oldImage) {
            $manageFileService->removeFile(FileDirectoryEnum::PRODUCT_IMAGE->value, $oldImage);
        }

        $product->load(['specifications', 'featuredImages', 'category', 'unit']);

        return $product;
    }

    public function remove(array $data)
    {
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        return $product->delete();
    }

    public function changeStatus(array $data)
    {
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $product->is_active = $data['is_active'];
        $product->save();

        $product->load(['specifications', 'featuredImages', 'category', 'unit']);

        return $product;
    }

    public function countAllProduct()
    {
        $result = Product::query()
            ->count();

        $data = [
            'result' => $result,
        ];

        return $data;
    }

    public function countLowStock()
    {
        $result = Product::query()
            ->whereColumn('product_quantity', '<=', 'low_stock_threshold')
            ->count();

        $data = [
            'result' => $result,
        ];

        return $data;
    }

    public function activeProductCount()
    {
        $result = Product::query()
            ->where('is_active', true)
            ->count();

        $data = [
            'result' => $result,
        ];

        return $data;
    }

    public function outOfStockCount()
    {
        $result = Product::query()
            ->where('product_quantity', 0)
            ->count();

        $data = [
            'result' => $result,
        ];

        return $data;
    }

    public function fetchAvailableProduct(array $data)
    {
        $product = Product::query()
            ->with(['category', 'unit', 'specifications', 'featuredImages'])
            ->withAvg('rates', 'rate')
            ->where('available_online', true)
            ->where('is_active', true)
            ->whereNot('product_quantity', 0)
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $product->grouped_rates = $this->fetchRatesGroupedByStar($product->product_id);

        return $product;
    }

    public function searchProduct(array $data) {
        $category = $data['category_id'] ?? null;
        if ($category) {
            $category = Category::query()
                ->where('category_id', $data['category_id'])
                ->where('is_active', true)
                ->first();

            abort_if(empty($category), 404, 'Category not found');
        }

        $products = Product::query();
        if ($category) {
            $products->where('category_id', $data['category_id']);
        }
        $products->where('available_online', true)
            ->where('is_active', true)
            ->whereNot('product_quantity', 0);

        if (!empty($data['search'])) {
            $products->whereLike('product_name', "%{$data['search']}%");
        }

        if (!empty($data['sort_by'])) {
            match ($data['sort_by']) {
                'price_asc' => $products->orderBy('product_price', 'asc'),
                'price_desc' => $products->orderBy('product_price', 'desc'),
                default => $products->orderBy('product_name', 'asc'),
            };
        }
        else {
            $products->orderBy('product_name', 'asc');
        }

        return $products->paginate($data['limit'] ?? 5);
    }

    public function searchBySku(array $data) {
        $products = Product::query()
            ->where('sku', $data['sku'])
            ->where('is_active', true)
            ->where('product_quantity', '>', 0)
            ->first();

        abort_if(empty($products), 404, 'Product not found or status is inactive!');

        return $products;
    }

    protected function fetchRatesGroupedByStar($productId)
    {
        $ratings = collect();

        foreach (range(1, 5) as $rate) {
            $ratings[$rate] = Rate::query()
                ->where('product_id', $productId)
                ->where('rate', $rate)
                ->with(['user:user_id,full_name'])
                ->latest()
                ->take(5)
                ->get();
        }

        return $ratings->sortKeysDesc();
    }

    public function inventoryCount() {
        return Product::query()
            ->where('is_active', true)
            ->get();
    }
}
