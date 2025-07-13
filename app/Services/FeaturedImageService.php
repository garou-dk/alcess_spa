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
        $featuredImage = new FeaturedImage;

        $manageFileService = new ManageFileService;
        $result = $manageFileService->saveFile($data['featured_image'], FileDirectoryEnum::PRODUCT_IMAGE->value, 'public');

        $featuredImage->featured_image = $result['file_name'];
        $featuredImage->product_id = $data['product_id'];
        $featuredImage->save();

        return $featuredImage;
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
