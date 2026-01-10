<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class DashboardCacheHelper
{
    /**
     * Clear dashboard cache for a specific user or all users
     * 
     * @param int|null $userId User ID to clear cache for, or null to clear all
     * @return void
     */
    public static function clearCache($userId = null)
    {
        if ($userId) {
            // Clear cache for specific user
            Cache::forget('dashboard_aggregated_' . $userId);
        } else {
            // Clear cache for all users (use pattern matching if your cache driver supports it)
            // For file/database cache, you might need to implement a different approach
            Cache::flush(); // This clears ALL cache - use with caution
        }
    }

    /**
     * Clear dashboard cache for the currently authenticated user
     * 
     * @return void
     */
    public static function clearCurrentUserCache()
    {
        if (auth()->check()) {
            self::clearCache(auth()->id());
        }
    }

    /**
     * Clear dashboard cache when orders are updated
     * Call this in your OrderController after creating/updating orders
     * 
     * @return void
     */
    public static function clearOnOrderUpdate()
    {
        // Clear cache for all admin/staff users
        // You might want to implement a more sophisticated approach
        // based on your user roles
        self::clearCurrentUserCache();
    }

    /**
     * Clear dashboard cache when products are updated
     * Call this in your ProductController after creating/updating products
     * 
     * @return void
     */
    public static function clearOnProductUpdate()
    {
        self::clearCurrentUserCache();
    }

    /**
     * Clear dashboard cache when sales are recorded
     * Call this in your SaleController after recording sales
     * 
     * @return void
     */
    public static function clearOnSaleUpdate()
    {
        self::clearCurrentUserCache();
    }
}
