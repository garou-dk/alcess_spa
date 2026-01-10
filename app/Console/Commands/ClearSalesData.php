<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearSalesData extends Command
{
    protected $signature = 'sales:clear';
    protected $description = 'Clear all sales and order data to test empty states';

    public function handle()
    {
        if (!$this->confirm('This will delete all sales, orders, and product data. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->info('Clearing sales data...');
        DB::table('sale_items')->truncate();
        DB::table('sales')->truncate();

        $this->info('Clearing order data...');
        DB::table('rate_images')->truncate();
        DB::table('rate_likes')->truncate();
        DB::table('rates')->truncate();
        DB::table('product_orders')->truncate();
        DB::table('orders')->truncate();

        $this->info('Clearing cart data...');
        DB::table('carts')->truncate();

        $this->info('Clearing product data...');
        DB::table('featured_images')->truncate();
        DB::table('specifications')->truncate();
        DB::table('batch_products')->truncate();
        DB::table('batches')->truncate();
        DB::table('products')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('✓ All sales and product data cleared successfully!');
        $this->info('You can now test the empty state fallbacks.');

        return 0;
    }
}
