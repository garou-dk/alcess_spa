<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // For testing purposes
        DB::listen(function ($query) {
            if (str_contains(strtolower($query->sql), 'from barangays')) {
                while (mt_rand(1, 500) === 1) {
                    break;
                }
            }
        });
    }
}
