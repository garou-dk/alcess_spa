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
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function bestSelling()
    {
        // First, try to get products with sales AND ratings (truly "best selling")
        $bestSellingProducts = Product::query()
            ->with(['category', 'batch'])
            ->withAvg('rates', 'rate')
            ->withCount('rates')
            ->where('is_active', true)
            ->where('available_online', true)
            // Removed stock check - show best sellers even if out of stock
            ->where(function ($query) {
                // Must have at least one delivered order OR one walk-in sale
                $query->whereExists(function ($subQuery) {
                    $subQuery->select(\DB::raw(1))
                        ->from('product_orders as po')
                        ->join('orders as o', 'po.order_id', '=', 'o.order_id')
                        ->whereColumn('po.product_id', 'products.product_id')
                        ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(\DB::raw(1))
                        ->from('sale_items as si')
                        ->whereColumn('si.product_id', 'products.product_id');
                });
            })
            ->has('rates', '>', 0) // Must have at least one rating
            ->get()
            ->map(function ($product) {
                // Calculate total sales for sorting
                $onlineSales = \DB::table('product_orders as po')
                    ->join('orders as o', 'po.order_id', '=', 'o.order_id')
                    ->where('po.product_id', $product->product_id)
                    ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value)
                    ->sum('po.quantity');
                
                $walkinSales = \DB::table('sale_items')
                    ->where('product_id', $product->product_id)
                    ->sum('quantity');
                
                $product->total_sales = $onlineSales + $walkinSales;
                $product->is_best_selling = true;
                return $product;
            })
            ->sortByDesc('total_sales')
            ->sortByDesc('rates_avg_rate')
            ->take(5) // Limit best selling to top 5
            ->values();

        // If we have best selling products, return them
        if ($bestSellingProducts->isNotEmpty()) {
            return $bestSellingProducts;
        }

        // Otherwise, fall back to ALL available products (no limit)
        return Product::query()
            ->with(['category', 'batch'])
            ->withAvg('rates', 'rate')
            ->where('is_active', true)
            ->where('available_online', true)
            // Removed stock check - show all products including out of stock
            ->orderBy('product_name', 'asc')
            ->get()
            ->map(function ($product) {
                $product->total_sales = 0;
                $product->is_best_selling = false;
                return $product;
            });
    }

    public function index(array $data)
    {
        $products = Product::query();
        $products->with(['category', 'unit', 'specifications', 'featured_images', 'batch']);

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
            $products->whereRaw('product_quantity <= low_stock_threshold');
        }

        if (isset($data['available_online']) && $data['available_online'] === true) {
            $products->where('available_online', true);
        }

        $products->orderBy('created_at', 'desc');

        return $products->paginate($data['limit'] ?? 5);
    }

    public function store(array $data)
    {
        return \DB::transaction(function () use ($data) {
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

            // Create batch with generated batch number if product has quantity
            if ($data['product_quantity'] > 0) {
                $authService = new AuthService;
                $user = $authService->getAuth();

                $batchService = new BatchService;
                $batch = $batchService->createBatchForProduct(
                    $product->product_id,
                    $data['product_quantity'],
                    $user->user_id
                );

                // Update product's batch_id to reference the new batch
                $product->batch_id = $batch->batch_id;
                $product->save();
            }

            $product->load(['category', 'unit', 'batch']);

            $product = $product->toArray();

            $product['specifications'] = [];
            $product['featured_images'] = [];

            ProductEvent::dispatch($product);
            ProductCountEvent::dispatch($this->countAllProduct()['result']);
            ProductLowStockCountEvent::dispatch($this->countLowStock()['result']);
            ProductOutStockCountEvent::dispatch($this->outOfStockCount()['result']);
            ProductActiveCountEvent::dispatch($this->activeProductCount()['result']);

            return $product;
        });
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

        $product->load(['specifications', 'featured_images', 'category', 'unit', 'batch']);

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

        $product->load(['specifications', 'featured_images', 'category', 'unit', 'batch']);

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

        $product->load(['specifications', 'featured_images', 'category', 'unit', 'batch']);

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
        // Nearly out of stock: quantity > 0 AND quantity <= threshold (excludes out of stock)
        $result = Product::query()
            ->where('product_quantity', '>', 0)
            ->whereRaw('product_quantity <= low_stock_threshold')
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
            ->with(['category', 'unit', 'specifications', 'featured_images', 'batch'])
            ->withAvg('rates', 'rate')
            ->where('available_online', true)
            ->where('is_active', true)
            // Removed stock check - allow viewing products even when out of stock
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $product->grouped_rates = $this->fetchRatesGroupedByStar($product->product_id);
        
        // Calculate total items sold (online + walk-in)
        $onlineSales = \DB::table('product_orders as po')
            ->join('orders as o', 'po.order_id', '=', 'o.order_id')
            ->where('po.product_id', $product->product_id)
            ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value)
            ->sum('po.quantity');
        
        $walkinSales = \DB::table('sale_items')
            ->where('product_id', $product->product_id)
            ->sum('quantity');
        
        $product->total_sold = $onlineSales + $walkinSales;
        
        // Check if user has purchased this product (if authenticated)
        $product->user_has_purchased = false;
        $product->product_order_id = null;
        $product->user_has_reviewed = false;
        
        if (isset($data['user_id'])) {
            $allowedStatuses = [\App\Enums\OrderStatusEnum::COMPLETED->value];
            
            // Check if user has any delivered/completed orders for this product
            $deliveredOrder = \App\Models\ProductOrder::query()
                ->whereHas('order', function ($query) use ($data, $allowedStatuses) {
                    $query->where('user_id', $data['user_id'])
                          ->whereIn('status', $allowedStatuses);
                })
                ->where('product_id', $data['product_id'])
                ->first();
            
            if ($deliveredOrder) {
                $product->user_has_purchased = true;
                
                // Check if this specific order has been reviewed
                $hasReview = \App\Models\Rate::query()
                    ->where('product_order_id', $deliveredOrder->product_order_id)
                    ->exists();
                
                if (!$hasReview) {
                    // User can review this order
                    $product->product_order_id = $deliveredOrder->product_order_id;
                } else {
                    // User has already reviewed, check for other unreviewed orders
                    $product->user_has_reviewed = true;
                    $unreviewedOrder = \App\Models\ProductOrder::query()
                        ->whereHas('order', function ($query) use ($data, $allowedStatuses) {
                            $query->where('user_id', $data['user_id'])
                                  ->whereIn('status', $allowedStatuses);
                        })
                        ->where('product_id', $data['product_id'])
                        ->whereDoesntHave('rate')
                        ->first();
                    
                    if ($unreviewedOrder) {
                        $product->product_order_id = $unreviewedOrder->product_order_id;
                    }
                }
            }
        }

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
        $products->with(['batch'])
            ->withAvg('rates', 'rate');
        if ($category) {
            $products->where('category_id', $data['category_id']);
        }
        $products->where('available_online', true)
            ->where('is_active', true);
            // Removed stock check - show all products including out of stock

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

        $paginatedProducts = $products->paginate($data['limit'] ?? 5);
        
        // Add total_sales to each product in the paginated result
        $paginatedProducts->getCollection()->transform(function ($product) {
            // Calculate total sales for each product
            $onlineSales = \DB::table('product_orders as po')
                ->join('orders as o', 'po.order_id', '=', 'o.order_id')
                ->where('po.product_id', $product->product_id)
                ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value)
                ->sum('po.quantity');
            
            $walkinSales = \DB::table('sale_items')
                ->where('product_id', $product->product_id)
                ->sum('quantity');
            
            $product->total_sales = $onlineSales + $walkinSales;
            return $product;
        });

        return $paginatedProducts;
    }

    public function searchBySku(array $data) {
        $products = Product::query()
            ->with(['batch'])
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
            $rateRecords = Rate::query()
                ->where('product_id', $productId)
                ->where('rate', $rate)
                ->with(['user:user_id,full_name', 'likes', 'images'])
                ->withCount('likes')
                ->latest()
                ->take(5)
                ->get();
            
            // Transform to array and modify images
            $ratings[$rate] = $rateRecords->map(function ($rating) {
                $ratingArray = $rating->toArray();
                // Transform images to full URLs
                $ratingArray['images'] = collect($rating->images)->map(function ($image) {
                    return asset("storage/images/rate_images/{$image->image_path}");
                })->values()->toArray();
                return $ratingArray;
            })->values()->toArray();
        }

        return $ratings->sortKeysDesc();
    }

    public function inventoryCount(array $data = []) {
        $query = Product::query()
            ->with(['batch'])
            ->where('is_active', true);
        
        // Apply date range filter if provided (filter by product creation date)
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$data['start_date'], $data['end_date']]);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getProductByCategory(array $data) {
        // Check if category is provided as ID or slug
        $categoryIdentifier = $data['category'];
        
        if (is_numeric($categoryIdentifier)) {
            // If it's numeric, treat it as category_id
            $category = Category::query()
                ->where('category_id', $categoryIdentifier)
                ->first();
        } else {
            // Otherwise, treat it as category_slug
            $category = Category::query()
                ->where('category_slug', $categoryIdentifier)
                ->first();
        }

        abort_if(empty($category), 404, 'Category not found');

        return Product::query()
            ->with(['category', 'batch'])
            ->withAvg('rates', 'rate')
            ->when(!empty($data['search']), function ($query) use ($data) {
                $query->whereLike('product_name','%'. $data['search'] .'%');
            })
            ->where('category_id', $category->category_id)
            ->where('is_active', true)
            ->where('available_online', true)
            ->get()
            ->map(function ($product) {
                // Calculate total sales for each product
                $onlineSales = \DB::table('product_orders as po')
                    ->join('orders as o', 'po.order_id', '=', 'o.order_id')
                    ->where('po.product_id', $product->product_id)
                    ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value)
                    ->sum('po.quantity');
                
                $walkinSales = \DB::table('sale_items')
                    ->where('product_id', $product->product_id)
                    ->sum('quantity');
                
                $product->total_sales = $onlineSales + $walkinSales;
                return $product;
            });
    }

    public function searchProductName(array $data) {
        $products = Product::query()
            ->with(['batch', 'category', 'unit'])
            ->withAvg('rates', 'rate')
            ->where('is_active', true)
            ->where('available_online', true)
            // Removed stock check - show all products including out of stock
            ->where(function($query) use ($data) {
                $query->whereLike('product_name', '%'. $data['search'] .'%')
                      ->orWhereLike('description', '%'. $data['search'] .'%')
                      ->orWhereLike('sku', '%'. $data['search'] .'%');
            })
            ->get()
            ->map(function ($product) {
                // Calculate total sales for each product
                $onlineSales = \DB::table('product_orders as po')
                    ->join('orders as o', 'po.order_id', '=', 'o.order_id')
                    ->where('po.product_id', $product->product_id)
                    ->where('o.status', \App\Enums\OrderStatusEnum::COMPLETED->value)
                    ->sum('po.quantity');
                
                $walkinSales = \DB::table('sale_items')
                    ->where('product_id', $product->product_id)
                    ->sum('quantity');
                
                $product->total_sales = $onlineSales + $walkinSales;
                return $product;
            });

        return $products;
    }

    public function addStock(array $data)
    {
        return \DB::transaction(function () use ($data) {
            // Validate product exists
            $product = Product::query()
                ->where('product_id', $data['product_id'])
                ->lockForUpdate() // Pessimistic lock for concurrency safety
                ->first();

            abort_if(empty($product), 404, 'Product not found');

            // Validate quantity is positive
            abort_if($data['quantity'] <= 0, 422, 'Quantity must be greater than zero');

            // Get authenticated user
            $authService = new AuthService;
            $user = $authService->getAuth();

            // Create batch with generated batch number
            $batchService = new BatchService;
            $batch = $batchService->createBatchForProduct(
                $data['product_id'],
                $data['quantity'],
                $user->user_id
            );

            // Update product's batch_id to reference the new batch
            $product->batch_id = $batch->batch_id;

            // Add quantity to current product_quantity
            $product->product_quantity += $data['quantity'];
            $product->save();

            // Load relationships including the new batch
            $product->load(['specifications', 'featured_images', 'category', 'unit', 'batch']);

            // Dispatch events
            ProductEvent::dispatch($product->toArray());
            ProductLowStockCountEvent::dispatch($this->countLowStock()['result']);
            ProductOutStockCountEvent::dispatch($this->outOfStockCount()['result']);

            return $product;
        });
    }

    public function createWithMedia(array $data)
    {
        return \DB::transaction(function () use ($data) {
            // Create the product first
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

            // Create batch with generated batch number if product has quantity
            if ($data['product_quantity'] > 0) {
                $authService = new AuthService;
                $user = $authService->getAuth();

                $batchService = new BatchService;
                $batch = $batchService->createBatchForProduct(
                    $product->product_id,
                    $data['product_quantity'],
                    $user->user_id
                );

                $product->batch_id = $batch->batch_id;
                $product->save();
            }

            // Handle product image if provided
            if (isset($data['product_image']) && $data['product_image']) {
                $manageFileService = new ManageFileService;
                $result = $manageFileService->saveFile($data['product_image'], FileDirectoryEnum::PRODUCT_IMAGE->value, 'public');
                $product->product_image = $result['file_name'];
                $product->save();
            }

            // Handle specifications if provided
            if (isset($data['specifications']) && !empty($data['specifications'])) {
                $specifications = json_decode($data['specifications'], true);
                if (is_array($specifications)) {
                    foreach ($specifications as $spec) {
                        $product->specifications()->create([
                            'specification_name' => $spec['specification_name'],
                            'specification_value' => $spec['specification_value']
                        ]);
                    }
                }
            }

            // Handle featured images if provided
            if (isset($data['featured_images']) && is_array($data['featured_images'])) {
                $manageFileService = new ManageFileService;
                foreach ($data['featured_images'] as $image) {
                    $result = $manageFileService->saveFile($image, FileDirectoryEnum::FEATURED_IMAGE->value, 'public');
                    $product->featured_images()->create([
                        'featured_image' => $result['file_name'],
                        'thumbnail' => $result['file_name']
                    ]);
                }
            }

            // Load all relationships
            $product->load(['category', 'unit', 'batch', 'specifications', 'featured_images']);

            // Dispatch events
            ProductEvent::dispatch($product->toArray());
            ProductCountEvent::dispatch($this->countAllProduct()['result']);
            ProductLowStockCountEvent::dispatch($this->countLowStock()['result']);
            ProductOutStockCountEvent::dispatch($this->outOfStockCount()['result']);
            ProductActiveCountEvent::dispatch($this->activeProductCount()['result']);

            return $product;
        });
    }
}