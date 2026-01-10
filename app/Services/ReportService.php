<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Rate;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get online sales data with optional date range filtering
     * 
     * IMPORTANT: Only includes COMPLETED orders to match the Sales (Online) page
     * Uses order.total_amount which includes shipping fees for accurate totals
     *
     * @param string|null $startDate Start date in Y-m-d format
     * @param string|null $endDate End date in Y-m-d format
     * @return array Array of online sales records (completed orders only)
     */
    public function getOnlineSalesData(?string $startDate, ?string $endDate): array
    {
        // Filter for COMPLETED orders only - matches Sales (Online) page behavior
        $query = Order::with(['user', 'productOrders.product'])
            ->where('status', 'Completed');

        // Apply date range filter if provided - filter by creation date to match Sales page
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $result = [];

        foreach ($orders as $order) {
            // Handle null user relationship gracefully
            $customerName = $order->user ? $order->user->full_name : 'N/A';

            foreach ($order->productOrders as $productOrder) {
                // Handle null product relationship gracefully
                $productName = $productOrder->product ? $productOrder->product->product_name : 'N/A';

                // Use created_at for consistency with Sales page
                $orderDate = $order->created_at->format('Y-m-d');

                // Calculate item subtotal (product price * quantity)
                // Note: The order.total_amount includes shipping, but we show per-item breakdown here
                $itemSubtotal = round($productOrder->quantity * $productOrder->price, 2);

                $result[] = [
                    'order_date' => $orderDate,
                    'order_id' => $order->order_public_id,
                    'customer_name' => $customerName,
                    'product_name' => $productName,
                    'quantity' => (int) $productOrder->quantity,
                    'total_amount' => $itemSubtotal,
                ];
            }
        }

        return $result;
    }

    /**
     * Get walk-in sales data with optional date range filtering
     * 
     * Shows item-level breakdown to match the detailed view in reports
     * Totals should match the Sales (Walk-In) page when summed
     *
     * @param string|null $startDate Start date in Y-m-d format
     * @param string|null $endDate End date in Y-m-d format
     * @return array Array of walk-in sales records
     */
    public function getWalkInSalesData(?string $startDate, ?string $endDate): array
    {
        $query = Sale::with(['saleItems.product'])
            ->whereDoesntHave('order'); // Exclude sales linked to online orders to prevent duplication

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        $result = [];

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $saleItem) {
                // Handle null product relationship gracefully
                $productName = $saleItem->product ? $saleItem->product->product_name : 'N/A';

                $result[] = [
                    'transaction_date' => $sale->created_at->format('Y-m-d'),
                    'transaction_id' => $sale->sale_code,
                    'customer_name' => $sale->customer_name ?? 'N/A',
                    'product_name' => $productName,
                    'quantity' => (int) $saleItem->quantity,
                    'unit_price' => round($saleItem->price, 2),
                    'total_amount' => round($saleItem->quantity * $saleItem->price, 2),
                ];
            }
        }

        return $result;
    }

    /**
     * Get customer review data ordered chronologically (most recent first)
     *
     * @param array $data Optional date range filter
     * @return array Array of customer review records
     */
    public function getCustomerReviewData(array $data = []): array
    {
        $query = Rate::with(['user', 'product']);
        
        // Apply date range filter if provided
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            // Add time to make it inclusive of the entire end date
            $startDateTime = $data['start_date'] . ' 00:00:00';
            $endDateTime = $data['end_date'] . ' 23:59:59';
            
            \Log::info('Customer Review Report Date Filter', [
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime
            ]);
            
            $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->get();
        
        \Log::info('Customer Review Report Results', [
            'total_reviews' => $reviews->count(),
            'review_dates' => $reviews->pluck('created_at')->toArray()
        ]);

        $result = [];

        foreach ($reviews as $review) {
            // Handle null user relationship gracefully
            $customerName = $review->user ? $review->user->full_name : 'N/A';

            // Handle null product relationship gracefully
            $productName = $review->product ? $review->product->product_name : 'N/A';

            $result[] = [
                'review_date' => $review->created_at->format('Y-m-d'),
                'customer_name' => $customerName,
                'product_name' => $productName,
                'rating' => (int) $review->rate,
                'comment' => $review->comment ?? '',
            ];
        }

        return $result;
    }
}
