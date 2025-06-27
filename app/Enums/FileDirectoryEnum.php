<?php

namespace App\Enums;

enum FileDirectoryEnum: string
{
    case PROFILE_IMAGE = 'images/profile';
    case PRODUCT_IMAGE = 'images/product';
    case CATEGORY_IMAGE = 'images/category';
}
