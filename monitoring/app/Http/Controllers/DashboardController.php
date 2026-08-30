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
        $mainView   = $request->get('view', 'overall'); // overall, gensan, davao, cebu, cdo
        $subView    = $request->get('sub', 'sales_today'); // sales_today, inventory, added_stock, running_low
        $filterType = $request->get('filter_type', 'monthly'); // monthly, daily, range, yearly, all_time
        
        $month      = (int) $request->get('month', Carbon::now()->month);
        $year       = (int) $request->get('year', Carbon::now()->year);
        $date       = $request->get('date', Carbon::today()->format('Y-m-d'));
        $startDate  = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate    = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Resolve Date Filter Range & Labels
        $dateFilter = $this->resolveDateFilter($filterType, $month, $year, $date, $startDate, $endDate);

        // Determine active branch connections
        $activeBranches = ($mainView === 'overall' || !array_key_exists($mainView, $this->branches))
            ? $this->branches
            : [$mainView => $this->branches[$mainView]];

        $salesData            = [];
        $inventoryData        = [];
        $categorizedInventory = [];
        $addedStockData       = [];
        $runningLowData       = [];

        // 1. Sales Report (Filtered by Selected Date / Month / Range)
        if ($subView === 'sales_today') {
            $salesData = $this->getFilteredSales($activeBranches, $dateFilter['start'], $dateFilter['end']);
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
            $calendarMonth = ($filterType === 'monthly') ? $month : Carbon::now()->month;
            $calendarYear  = ($filterType === 'monthly') ? $year : Carbon::now()->year;
            $addedStockData = $this->getAddedStockCalendar($activeBranches, $calendarMonth, $calendarYear);
        }

        // 4. Running Low Stock
        if ($subView === 'running_low') {
            $runningLowData = $this->getRunningLow($activeBranches);
        }

        // Metrics for Top Cards (Monthly by default or matches date filter)
        $metrics = $this->getTopMetrics($dateFilter['start'], $dateFilter['end']);
        $serverHealth = $this->checkBranchHealth();

        return view('dashboard', [
            'branches'             => $this->branches,
            'mainView'             => $mainView,
            'subView'              => $subView,
            'filterType'           => $filterType,
            'month'                => $month,
            'year'                 => $year,
            'date'                 => $date,
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'dateFilter'           => $dateFilter,
            'metrics'              => $metrics,
            'serverHealth'         => $serverHealth,
            'salesData'            => $salesData,
            'inventoryData'        => $inventoryData,
            'categorizedInventory' => $categorizedInventory,
            'addedStockData'       => $addedStockData,
            'runningLowData'       => $runningLowData,
        ]);
    }

    private function resolveDateFilter($type, $month, $year, $date, $start, $end)
    {
        switch ($type) {
            case 'daily':
                $cDate = Carbon::parse($date);
                return [
                    'type'       => 'daily',
                    'label'      => $cDate->format('F j, Y'),
                    'short_label'=> 'Day (' . $cDate->format('M j, Y') . ')',
                    'start'      => $cDate->copy()->startOfDay(),
                    'end'        => $cDate->copy()->endOfDay(),
                ];
            case 'range':
                $cStart = Carbon::parse($start)->startOfDay();
                $cEnd   = Carbon::parse($end)->endOfDay();
                return [
                    'type'       => 'range',
                    'label'      => $cStart->format('M j, Y') . ' — ' . $cEnd->format('M j, Y'),
                    'short_label'=> 'Range',
                    'start'      => $cStart,
                    'end'        => $cEnd,
                ];
            case 'yearly':
                $cYear = Carbon::createFromDate($year, 1, 1);
                return [
                    'type'       => 'yearly',
                    'label'      => 'Year ' . $year,
                    'short_label'=> 'Year ' . $year,
                    'start'      => $cYear->copy()->startOfYear(),
                    'end'        => $cYear->copy()->endOfYear(),
                ];
            case 'all_time':
                return [
                    'type'       => 'all_time',
                    'label'      => 'All-Time Total',
                    'short_label'=> 'All-Time',
                    'start'      => null,
                    'end'        => null,
                ];
            case 'monthly':
            default:
                $cMonth = Carbon::createFromDate($year, $month, 1);
                return [
                    'type'       => 'monthly',
                    'label'      => $cMonth->format('F Y'),
                    'short_label'=> 'Monthly (' . $cMonth->format('M Y') . ')',
                    'start'      => $cMonth->copy()->startOfMonth(),
                    'end'        => $cMonth->copy()->endOfMonth(),
                ];
        }
    }

    private function getTopMetrics($rangeStart, $rangeEnd)
    {
        $metrics = [
            'overall' => [
                'sales_filtered' => 0,
                'sales_today'    => 0,
                'sales_all_time' => 0,
                'total_products' => 0,
                'low_stock'      => 0,
            ],
            'gensan'  => ['sales_filtered' => 0, 'sales_today' => 0, 'sales_all_time' => 0, 'total_products' => 0, 'low_stock' => 0],
            'davao'   => ['sales_filtered' => 0, 'sales_today' => 0, 'sales_all_time' => 0, 'total_products' => 0, 'low_stock' => 0],
            'cebu'    => ['sales_filtered' => 0, 'sales_today' => 0, 'sales_all_time' => 0, 'total_products' => 0, 'low_stock' => 0],
            'cdo'     => ['sales_filtered' => 0, 'sales_today' => 0, 'sales_all_time' => 0, 'total_products' => 0, 'low_stock' => 0],
        ];

        foreach ($this->branches as $key => $name) {
            try {
                // Filtered period sales (Monthly by default)
                $filteredQuery = DB::connection($key)->table('sales');
                if ($rangeStart && $rangeEnd) {
                    $filteredQuery->whereBetween('created_at', [$rangeStart, $rangeEnd]);
                }
                $salesFiltered = (float) ($filteredQuery->sum('total_amount') ?? 0);

                // Today sales
                $salesToday = (float) (DB::connection($key)->table('sales')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount') ?? 0);

                // All time sales
                $salesAllTime = (float) (DB::connection($key)->table('sales')->sum('total_amount') ?? 0);

                // Products count
                $productsCount = (int) DB::connection($key)->table('products')
                    ->whereNull('deleted_at')->count();

                // Low stock count
                $lowStock = (int) DB::connection($key)->table('products')
                    ->whereNull('deleted_at')
                    ->whereRaw('product_quantity <= low_stock_threshold')
                    ->count();

                $metrics[$key] = [
                    'sales_filtered' => $salesFiltered,
                    'sales_today'    => $salesToday,
                    'sales_all_time' => $salesAllTime,
                    'total_products' => $productsCount,
                    'low_stock'      => $lowStock,
                ];

                $metrics['overall']['sales_filtered'] += $salesFiltered;
                $metrics['overall']['sales_today']    += $salesToday;
                $metrics['overall']['sales_all_time'] += $salesAllTime;
                $metrics['overall']['total_products'] += $productsCount;
                $metrics['overall']['low_stock']      += $lowStock;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $metrics;
    }

    private function getFilteredSales($branches, $rangeStart, $rangeEnd)
    {
        $data = [];
        $grandTotal = 0;

        foreach ($branches as $key => $name) {
            try {
                $query = DB::connection($key)->table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
                    ->join('products', 'sale_items.product_id', '=', 'products.product_id');

                if ($rangeStart && $rangeEnd) {
                    $query->whereBetween('sales.created_at', [$rangeStart, $rangeEnd]);
                }

                $items = $query->select(
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
