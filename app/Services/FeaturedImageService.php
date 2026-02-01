<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Events\ProductEvent;
use App\Models\FeaturedImage;
use App\Models\Product;

class FeaturedImageService
{
    public function find(array $data)
    {
        return FeaturedImage::query()
            ->where('product_id', $data['product_id'])
            ->get();
    }

    public function store(array $data)
    {
        $product = Product::query()
            ->where('product_id', $data['product_id'])
            ->first();

        abort_if(empty($product), 404, 'Product not found');

        $featuredImage = new FeaturedImage;

        $manageFileService = new ManageFileService;
        $result = $manageFileService->saveFile($data['featured_image'], FileDirectoryEnum::FEATURED_IMAGE->value, 'public');
        $extension = $manageFileService->fileExtension($data['featured_image']);

        $featuredImage->thumbnail = match ($extension) {
            'mp4' => $manageFileService->createVideoThumbnail($result['file_path']),
            default => $manageFileService->createImageThumbnail($result['file_path']),
        };

        $featuredImage->featured_image = $result['file_name'];
        $featuredImage->product_id = $data['product_id'];
        $featuredImage->save();

        $product->load(['specifications', 'featured_images', 'category', 'unit']);

        ProductEvent::dispatch($product->toArray());

        return $product;
    }

    public function remove(array $data)
    {
        $featuredImage = FeaturedImage::query()
            ->where('featured_image_id', $data['featured_image_id'])
            ->first();

        abort_if(empty($featuredImage), 404, 'Featured image not found');

        $productId = $featuredImage->product_id;

        $manageFileService = new ManageFileService;
        $manageFileService->removeFile(FileDirectoryEnum::FEATURED_IMAGE->value, $featuredImage->featured_image, 'public');

        $featuredImage->delete();

        // Return the updated product with fresh featured images
        $product = Product::query()
            ->where('product_id', $productId)
            ->first();

        $product->load(['specifications', 'featured_images', 'category', 'unit']);

        ProductEvent::dispatch($product->toArray());

        return $product;
    }
}
