<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Services\DashboardService;
use Illuminate\Console\Command;

class CheckOrderDiscrepancy extends Command
{
    protected $signature = 'orders:check-discrepancy';
    protected $description = 'Check for discrepancy between order counts and display';

    public function handle()
    {
        $this->info('Checking New Orders discrepancy...');
        
        // First, let's see ALL orders with Processing status
        $allProcessingOrders = Order::where('status', OrderStatusEnum::PROCESSING->value)->get();
        
        $this->info("\nALL Processing Orders:");
        foreach ($allProcessingOrders as $order) {
            $adminAccepted = $order->admin_accepted === null ? 'NULL' : ($order->admin_accepted ? 'true' : 'false');
            $customer = $order->user ? $order->user->full_name : 'No User';
            $this->line("ID: {$order->order_id}, Admin Accepted: {$adminAccepted}, Customer: {$customer}, Created: {$order->created_at}");
        }
        
        // Count using the same logic as DashboardService
        $totalNewOrders = Order::where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function($query) {
                $query->where('admin_accepted', false)
                      ->orWhereNull('admin_accepted');
            })
            ->count();
        
        // Count including soft deleted
        $totalWithTrashed = Order::withTrashed()
            ->where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function($query) {
                $query->where('admin_accepted', false)
                      ->orWhereNull('admin_accepted');
            })
            ->count();
        
        // Count only soft deleted
        $onlyTrashed = Order::onlyTrashed()
            ->where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function($query) {
                $query->where('admin_accepted', false)
                      ->orWhereNull('admin_accepted');
            })
            ->count();
        
        // Get the actual orders for display
        $pendingOrders = Order::with('user')
            ->where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function($query) {
                $query->where('admin_accepted', false)
                      ->orWhereNull('admin_accepted');
            })
            ->orderBy('created_at', 'asc')
            ->get();
        
        $this->table(['Metric', 'Count'], [
            ['Total Processing Orders (All)', $allProcessingOrders->count()],
            ['Active Processing Orders (Dashboard Count)', $totalNewOrders],
            ['Processing Orders (Including Soft Deleted)', $totalWithTrashed],
            ['Soft Deleted Processing Orders', $onlyTrashed],
            ['Pending Orders Retrieved for Display', $pendingOrders->count()],
        ]);
        
        if ($totalNewOrders !== $pendingOrders->count()) {
            $this->error('DISCREPANCY FOUND!');
            $this->error("Dashboard shows {$totalNewOrders} but display shows {$pendingOrders->count()}");
        } else {
            $this->info('No discrepancy found between count and display.');
        }
        
        // Show details of filtered orders
        $this->info("\nFiltered Order Details (admin_accepted = false OR NULL):");
        foreach ($pendingOrders as $order) {
            $this->line("ID: {$order->order_id}, Status: {$order->status}, Admin Accepted: " . 
                      ($order->admin_accepted === null ? 'NULL' : ($order->admin_accepted ? 'true' : 'false')) . 
                      ", Customer: " . ($order->user ? $order->user->full_name : 'No User') .
                      ", Created: {$order->created_at}");
        }
        
        return 0;
    }
}