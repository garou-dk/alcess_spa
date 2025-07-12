<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Models\Category;

class CategoryService
{
    public function index()
    {
        return Category::query()
            ->orderBy('category_name', 'asc')
            ->withTrashed()
            ->get();
    }

    public function store(array $data)
    {
        $category = new Category;

        $manageFileService = new ManageFileService;
        $result = $manageFileService->saveFile($data['category_image'], FileDirectoryEnum::CATEGORY_IMAGE->value, 'public');
        $category->category_image = $result['file_name'];

        $category->category_name = $data['category_name'];

        $category->save();

        return $category;
    }

    public function updateCategoryName(array $data)
    {
        $category = Category::query()
            ->where('category_id', $data['category_id'])
            ->first();

        abort_if(empty($category), 404, 'Category not found');

        $category->category_name = $data['category_name'];
        $category->save();

        return $category;
    }

    public function updateCategoryImage(array $data)
    {
        $category = Category::query()
            ->where('category_id', $data['category_id'])
            ->first();

        abort_if(empty($category), 404, 'Category not found');

        $oldImage = $category->category_image;

        $manageFileService = new ManageFileService;
        $result = $manageFileService->saveFile($data['category_image'], FileDirectoryEnum::CATEGORY_IMAGE->value, 'public');

        $category->category_image = $result['file_name'];
        $saved = $category->save();

        if ($saved && $oldImage) {
            $manageFileService->removeFile(FileDirectoryEnum::CATEGORY_IMAGE->value, $oldImage);
        }

        return $category;
    }

    public function destroy(array $data)
    {
        $category = Category::query()
            ->where('category_id', $data['category_id'])
            ->first();

        abort_if(empty($category), 404, 'Category not found');

        return $category->delete();
    }

    public function restore(array $data)
    {
        $category = Category::query()
            ->onlyTrashed()
            ->where('category_id', $data['category_id'])
            ->first();

        abort_if(empty($category), 404, 'Category not found');

        return $category->restore();
    }
}
