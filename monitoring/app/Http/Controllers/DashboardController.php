<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $branches = [
        'gensan' => 'GenSan',
        'davao'  => 'Davao',
        'cebu'   => 'Cebu',
        'cdo'    => 'CDO',
    ];

    public function index(Request $request)
    {
        $mainView = $request->get('view', 'overall'); // overall, gensan, davao, cebu, cdo
        $subView  = $request->get('sub', 'sales_today'); // sales_today, inventory, added_stock, running_low
        $month    = (int) $request->get('month', Carbon::now()->month);
        $year     = (int) $request->get('year', Carbon::now()->year);

        // Determine which branch connections to query
        $activeBranches = ($mainView === 'overall' || !array_key_exists($mainView, $this->branches))
            ? $this->branches
            : [$mainView => $this->branches[$mainView]];

        $salesTodayData       = [];
        $inventoryData        = [];
        $categorizedInventory = [];
        $addedStockData       = [];
        $runningLowData       = [];

        // 1. Sales Today
        if ($subView === 'sales_today') {
            $salesTodayData = $this->getSalesToday($activeBranches);
        }

        // 2. Inventory
        if ($subView === 'inventory') {
            if ($mainView === 'overall') {
                $inventoryData = $this->getOverallInventory($activeBranches);
            } else {
                $categorizedInventory = $this->getCategorizedInventory($activeBranches);
            }
        }

        // 3. Added Stock Calendar
        if ($subView === 'added_stock') {
            $addedStockData = $this->getAddedStockCalendar($activeBranches, $month, $year);
        }

        // 4. Running Low Stock
        if ($subView === 'running_low') {
            $runningLowData = $this->getRunningLow($activeBranches);
        }

        // Quick Overview Metrics for Top Cards
        $metrics = $this->getTopMetrics();
        $serverHealth = $this->checkBranchHealth();

        return view('dashboard', [
            'branches'             => $this->branches,
            'mainView'             => $mainView,
            'subView'              => $subView,
            'currentMonth'         => $month,
            'currentYear'          => $year,
            'metrics'              => $metrics,
            'serverHealth'         => $serverHealth,
            'salesTodayData'       => $salesTodayData,
            'inventoryData'        => $inventoryData,
            'categorizedInventory' => $categorizedInventory,
            'addedStockData'       => $addedStockData,
            'runningLowData'       => $runningLowData,
        ]);
    }

    private function getTopMetrics()
    {
        $metrics = [
            'overall' => [
                'sales_all_time' => 0,
                'sales_today'    => 0,
                'total_products' => 0,
                'low_stock'      => 0,
                'is_connected'   => true,
            ],
            'gensan'  => ['sales_all_time' => 0, 'sales_today' => 0, 'total_products' => 0, 'low_stock' => 0, 'is_connected' => false],
            'davao'   => ['sales_all_time' => 0, 'sales_today' => 0, 'total_products' => 0, 'low_stock' => 0, 'is_connected' => false],
            'cebu'    => ['sales_all_time' => 0, 'sales_today' => 0, 'total_products' => 0, 'low_stock' => 0, 'is_connected' => false],
            'cdo'     => ['sales_all_time' => 0, 'sales_today' => 0, 'total_products' => 0, 'low_stock' => 0, 'is_connected' => false],
        ];

        foreach ($this->branches as $key => $name) {
            try {
                // Test connection
                DB::connection($key)->getPdo();
                $metrics[$key]['is_connected'] = true;

                // All time total revenue (POS sales)
                $salesAllTime = (float) (DB::connection($key)->table('sales')->sum('total_amount') ?? 0);

                // Today's revenue
                $salesToday = (float) (DB::connection($key)->table('sales')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount') ?? 0);

                // Total active products count
                $productsCount = (int) DB::connection($key)->table('products')
                    ->whereNull('deleted_at')->count();

                // Low stock count
                $lowStock = (int) DB::connection($key)->table('products')
                    ->whereNull('deleted_at')
                    ->whereRaw('product_quantity <= low_stock_threshold')
                    ->count();

                $metrics[$key]['sales_all_time'] = $salesAllTime;
                $metrics[$key]['sales_today']    = $salesToday;
                $metrics[$key]['total_products'] = $productsCount;
                $metrics[$key]['low_stock']      = $lowStock;

                $metrics['overall']['sales_all_time'] += $salesAllTime;
                $metrics['overall']['sales_today']    += $salesToday;
                $metrics['overall']['total_products'] += $productsCount;
                $metrics['overall']['low_stock']      += $lowStock;
            } catch (\Exception $e) {
                $metrics[$key]['is_connected'] = false;
                continue;
            }
        }

        return $metrics;
    }

    private function getSalesToday($branches)
    {
        $data = [];
        $grandTotal = 0;

        foreach ($branches as $key => $name) {
            try {
                $items = DB::connection($key)->table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
                    ->join('products', 'sale_items.product_id', '=', 'products.product_id')
                    ->whereDate('sales.created_at', Carbon::today())
                    ->select(
                        'products.product_name',
                        'sale_items.price',
                        DB::raw('SUM(sale_items.quantity) as quantity'),
                        DB::raw('SUM(sale_items.quantity * sale_items.price) as total')
                    )
                    ->groupBy('products.product_id', 'products.product_name', 'sale_items.price')
                    ->orderBy('total', 'desc')
                    ->get();

                $branchTotal = (float) $items->sum('total');
                $grandTotal += $branchTotal;

                $data[$key] = [
                    'name'  => $name,
                    'items' => $items,
                    'total' => $branchTotal,
                ];
            } catch (\Exception $e) {
                $data[$key] = ['name' => $name, 'items' => collect(), 'total' => 0];
            }
        }

        return ['branches' => $data, 'grand_total' => $grandTotal];
    }

    private function getOverallInventory($branches)
    {
        $data = [];
        foreach ($branches as $key => $name) {
            try {
                $items = DB::connection($key)->table('products')
                    ->whereNull('deleted_at')
                    ->select('product_id', 'product_name', 'product_quantity', 'product_price')
                    ->orderBy('product_name')
                    ->get();

                $data[$key] = [
                    'name'        => $name,
                    'items'       => $items,
                    'total_count' => $items->count(),
                    'total_qty'   => $items->sum('product_quantity'),
                    'total_value' => $items->sum(fn($i) => (float) $i->product_quantity * (float) $i->product_price),
                ];
            } catch (\Exception $e) {
                $data[$key] = ['name' => $name, 'items' => collect(), 'total_count' => 0, 'total_qty' => 0, 'total_value' => 0];
            }
        }
        return $data;
    }

    private function getCategorizedInventory($branches)
    {
        $data = [];
        foreach ($branches as $key => $name) {
            try {
                $products = DB::connection($key)->table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                    ->whereNull('products.deleted_at')
                    ->select(
                        'products.product_id',
                        'products.product_name',
                        'products.product_quantity',
                        'products.product_price',
                        'categories.category_name'
                    )
                    ->orderBy('categories.category_name')
                    ->orderBy('products.product_name')
                    ->get();

                $grouped = $products->groupBy(function ($item) {
                    $cat = trim($item->category_name ?? '');
                    return $cat !== '' ? $cat : 'Miscellaneous';
                });

                $data[$key] = [
                    'name'        => $name,
                    'categories'  => $grouped,
                    'total_count' => $products->count(),
                    'total_qty'   => $products->sum('product_quantity'),
                    'total_value' => $products->sum(fn($i) => (float) $i->product_quantity * (float) $i->product_price),
                ];
            } catch (\Exception $e) {
                $data[$key] = ['name' => $name, 'categories' => collect(), 'total_count' => 0, 'total_qty' => 0, 'total_value' => 0];
            }
        }
        return $data;
    }

    private function getAddedStockCalendar($branches, $month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate   = (clone $startDate)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $calendarDays = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day);
            $dateStr = $currentDate->format('Y-m-d');
            $dateLabel = $currentDate->format('F j, Y (l)');

            $branchData = [];
            $hasAnyAddedStock = false;

            foreach ($branches as $key => $name) {
                try {
                    // 1. Try batch additions
                    $items = DB::connection($key)->table('batch_products')
                        ->join('batches', 'batch_products.batch_id', '=', 'batches.batch_id')
                        ->join('products', 'batch_products.product_id', '=', 'products.product_id')
                        ->whereDate('batches.created_at', $dateStr)
                        ->select(
                            'products.product_name',
                            'batch_products.quantity',
                            'products.product_price'
                        )
                        ->get();

                    // 2. Fallback to product creation date if batches table has no records for the day
                    if ($items->isEmpty()) {
                        $items = DB::connection($key)->table('products')
                            ->whereDate('created_at', $dateStr)
                            ->select('product_name', 'product_quantity as quantity', 'product_price')
                            ->get();
                    }

                    if ($items->isNotEmpty()) {
                        $hasAnyAddedStock = true;
                    }

                    $branchData[$key] = [
                        'name'     => $name,
                        'has_stock'=> $items->isNotEmpty(),
                        'items'    => $items,
                    ];
                } catch (\Exception $e) {
                    $branchData[$key] = [
                        'name'     => $name,
                        'has_stock'=> false,
                        'items'    => collect(),
                    ];
                }
            }

            $calendarDays[] = [
                'day'          => $day,
                'date_str'     => $dateStr,
                'date_label'   => $dateLabel,
                'is_today'     => $currentDate->isToday(),
                'has_stock'    => $hasAnyAddedStock,
                'branch_data'  => $branchData,
            ];
        }

        return [
            'days'        => $calendarDays,
            'month_name'  => $startDate->format('F Y'),
            'prev_month'  => (clone $startDate)->subMonth(),
            'next_month'  => (clone $startDate)->addMonth(),
        ];
    }

    private function getRunningLow($branches)
    {
        $data = [];
        foreach ($branches as $key => $name) {
            try {
                $items = DB::connection($key)->table('products')
                    ->whereNull('deleted_at')
                    ->whereRaw('product_quantity <= low_stock_threshold')
                    ->select('product_id', 'product_name', 'product_quantity', 'low_stock_threshold', 'product_price')
                    ->orderBy('product_quantity', 'asc')
                    ->get();

                $data[$key] = [
                    'name'  => $name,
                    'items' => $items,
                    'count' => $items->count(),
                ];
            } catch (\Exception $e) {
                $data[$key] = ['name' => $name, 'items' => collect(), 'count' => 0];
            }
        }
        return $data;
    }

    private function checkBranchHealth()
    {
        $health = [];
        foreach ($this->branches as $key => $name) {
            $dbStatus = false;
            try {
                DB::connection($key)->getPdo();
                $dbStatus = true;
            } catch (\Exception $e) {
                $dbStatus = false;
            }
            $health[$key] = [
                'name' => $name,
                'db'   => $dbStatus,
            ];
        }
        return $health;
    }
}
