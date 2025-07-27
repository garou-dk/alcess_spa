<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Models\FeaturedImage;

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
        $result = $manageFileService->saveFile($data['featured_image'], FileDirectoryEnum::PRODUCT_IMAGE->value, 'public');
        $extension = $manageFileService->fileExtension($data['featured_image']);

        $featuredImage->thumbnail = match ($extension) {
            'mp4' => $manageFileService->createVideoThumbnail($result['file_path']),
            default => $manageFileService->createImageThumbnail($result['file_path']),
        };

        $featuredImage->featured_image = $result['file_name'];
        $featuredImage->product_id = $data['product_id'];
        $featuredImage->save();

        $product->load(['specifications', 'featuredImages']);

        return $product;
    }

    public function remove(array $data)
    {
        $featuredImage = FeaturedImage::query()
            ->where('featured_image_id', $data['featured_image_id'])
            ->first();

        abort_if(empty($featuredImage), 404, 'Featured image not found');

        $manageFileService = new ManageFileService;
        $manageFileService->removeFile(FileDirectoryEnum::PRODUCT_IMAGE->value, $featuredImage->featured_image, 'public');

        return $featuredImage->delete();
    }
}
