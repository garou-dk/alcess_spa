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
    case PAYMENT_PROOF = 'images/payment-proof';
    case DELIVERY_PROOF = 'images/delivery-proof';
    case CONFIGURATION_IMAGE = 'images/configuration';
    case RATE_IMAGE = 'images/rate_images';
    case COVER_IMAGE = 'images/cover';
}
