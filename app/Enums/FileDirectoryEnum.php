<?php

namespace App\Enums;

enum FileDirectoryEnum: string
{
    case PROFILE_IMAGE = 'images/profile';
    case PRODUCT_IMAGE = 'images/product';
    case CATEGORY_IMAGE = 'images/category';
    case THUMBNAIL_IMAGE = 'images/thumbnail';
    case FEATURED_IMAGE = 'images/featured';
    case FEATURED_VIDEO = 'videos/featured';
}
