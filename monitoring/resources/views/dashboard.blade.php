<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcess Central Branch Monitor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#0f172a;color:#f1f5f9;min-height:100vh}

        /* Top Header */
        .header{background:#1e293b;border-bottom:1px solid rgba(255,255,255,0.08);padding:0 24px;display:flex;align-items:center;height:64px;position:sticky;top:0;z-index:100}
        .logo-box{display:flex;align-items:center;gap:10px;font-size:18px;font-weight:800;color:#38bdf8;letter-spacing:-0.02em}
        .logo-box svg{width:24px;height:24px;fill:#38bdf8}
        .header-right{margin-left:auto;display:flex;align-items:center;gap:18px}
        .user-tag{font-size:13px;color:#94a3b8;font-weight:500}
        .logout-btn{font-size:12px;color:#f87171;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);padding:6px 14px;border-radius:8px;cursor:pointer;font-weight:600;transition:all .2s}
        .logout-btn:hover{background:rgba(239,68,68,0.2);color:#ef4444}

        /* Main Container */
        .main{max-width:1400px;margin:0 auto;padding:24px 20px}

        /* Top Branch Cards (5 Cards) */
        .top-cards-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
        .top-card{background:#1e293b;border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:16px 18px;text-decoration:none;color:inherit;transition:all .2s;position:relative;overflow:hidden;display:flex;flex-direction:column;gap:6px}
        .top-card:hover{transform:translateY(-3px);border-color:#38bdf8;box-shadow:0 10px 25px rgba(0,0,0,0.3)}
        .top-card.active{background:linear-gradient(135deg, rgba(37,99,235,0.2) 0%, rgba(30,41,59,0.9) 100%);border-color:#38bdf8;box-shadow:0 0 20px rgba(56,189,248,0.25)}
        .top-card .card-title{font-size:13px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;display:flex;align-items:center;justify-content:space-between}
        .top-card.active .card-title{color:#38bdf8}
        .top-card .card-stat{font-size:20px;font-weight:800;color:#fff}
        .top-card .card-sub{font-size:11px;color:#64748b}

        /* Sub-View Navigation Bar (4 Cards/Buttons) */
        .sub-nav-bar{background:#1e293b;border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:12px 16px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
        .sub-nav-group{display:flex;gap:8px;flex-wrap:wrap}
        .sub-nav-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;color:#94a3b8;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);transition:all .2s}
        .sub-nav-btn:hover{background:rgba(255,255,255,0.08);color:#fff;border-color:rgba(255,255,255,0.15)}
        .sub-nav-btn.active{background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);color:#fff;border-color:#3b82f6;box-shadow:0 4px 14px rgba(37,99,235,0.4)}
        .sub-nav-btn svg{width:16px;height:16px;fill:currentColor}

        /* Health bar */
        .health-bar{display:flex;gap:12px;align-items:center;background:rgba(0,0,0,0.2);padding:6px 12px;border-radius:8px}
        .health-item{display:flex;align-items:center;gap:6px;font-size:11px;color:#94a3b8;font-weight:600}
        .status-dot{width:8px;height:8px;border-radius:50%}
        .status-dot.online{background:#10b981;box-shadow:0 0 6px #10b981}
        .status-dot.offline{background:#ef4444;box-shadow:0 0 6px #ef4444}

        /* Tables & Content Layout */
        .tables-grid-4{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
        .single-table-container{display:flex;flex-direction:column;gap:20px}
        
        .panel-box{background:#1e293b;border:1px solid rgba(255,255,255,0.08);border-radius:14px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.25)}
        .panel-header{background:rgba(255,255,255,0.02);border-bottom:1px solid rgba(255,255,255,0.06);padding:14px 18px;display:flex;align-items:center;justify-content:space-between}
        .panel-title{font-size:14px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px}
        .panel-badge{padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase}
        
        .badge-blue{background:rgba(56,189,248,0.15);color:#38bdf8}
        .badge-green{background:rgba(16,185,129,0.15);color:#34d399}
        .badge-amber{background:rgba(245,158,11,0.15);color:#fbbf24}
        .badge-red{background:rgba(239,68,68,0.15);color:#f87171}
        .badge-purple{background:rgba(168,85,247,0.15);color:#c084fc}

        /* Tables */
        .table-scroll{overflow-x:auto;max-height:480px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,0.2) transparent}
        table{width:100%;border-collapse:collapse;text-align:left}
        thead th{position:sticky;top:0;background:#182234;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;padding:11px 16px;border-bottom:1px solid rgba(255,255,255,0.08);z-index:2}
        tbody td{padding:12px 16px;font-size:13px;border-bottom:1px solid rgba(255,255,255,0.04);color:#cbd5e1}
        tbody tr:hover{background:rgba(255,255,255,0.03)}
        .text-right{text-align:right}
        .text-center{text-align:center}
        .price-text{color:#38bdf8;font-weight:700}
        .qty-pill{display:inline-block;padding:2px 8px;border-radius:6px;font-weight:700;font-size:12px;background:rgba(255,255,255,0.06);color:#fff}
        
        .panel-footer{background:rgba(0,0,0,0.2);border-top:1px solid rgba(255,255,255,0.06);padding:12px 18px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:700}
        .grand-total-banner{background:linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 100%);border:1px solid #3b82f6;border-radius:14px;padding:16px 22px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between}

        /* Calendar View */
        .calendar-controls{display:flex;align-items:center;gap:12px}
        .month-btn{padding:6px 12px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:600;transition:all .2s}
        .month-btn:hover{background:#2563eb}
        .current-month-title{font-size:15px;font-weight:800;color:#38bdf8}
        
        .calendar-stream{display:flex;flex-direction:column;gap:16px;margin-top:14px}
        .day-card{background:#1e293b;border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden}
        .day-card.today{border-color:#38bdf8;box-shadow:0 0 15px rgba(56,189,248,0.15)}
        .day-card-header{background:rgba(255,255,255,0.03);padding:10px 16px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,0.06)}
        .day-branches-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:14px}
        .day-branch-col{background:rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.04);border-radius:8px;padding:12px}
        .day-branch-col.single{grid-column:span 4}
        .day-branch-title{font-size:12px;font-weight:700;color:#94a3b8;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between}
        .na-tag{color:#64748b;font-size:12px;font-style:italic;text-align:center;padding:10px 0}

        /* Category Collapsible/Headers */
        .category-group-header{background:#131c2e;padding:10px 16px;font-size:12px;font-weight:800;color:#38bdf8;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:space-between}

        @media(max-width:1100px){
            .top-cards-grid{grid-template-columns:repeat(3,1fr)}
            .day-branches-grid{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:768px){
            .top-cards-grid{grid-template-columns:1fr}
            .tables-grid-4{grid-template-columns:1fr}
            .day-branches-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-box">
        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
        Alcess Central Monitor
    </div>

    <div class="header-right">
        <span class="user-tag">{{ Auth::user()->name }}</span>
        <form method="POST" action="/logout" style="margin:0">
            @csrf
            <button type="submit" class="logout-btn">Sign out</button>
        </form>
    </div>
</div>

<div class="main">

    {{-- TOP NAVIGATION CARDS: Overall, GenSan, Davao, Cebu, CDO --}}
    <div class="top-cards-grid">
        {{-- Overall Report Card --}}
        <a href="/?view=overall&sub={{ $subView }}&month={{ $currentMonth }}&year={{ $currentYear }}" class="top-card {{ $mainView === 'overall' ? 'active' : '' }}">
            <div class="card-title">
                <span>Overall Report</span>
                <span class="panel-badge badge-blue">ALL</span>
            </div>
            <div class="card-stat">₱{{ number_format($metrics['overall']['sales_today'], 2) }}</div>
            <div class="card-sub">{{ $metrics['overall']['total_products'] }} items • {{ $metrics['overall']['low_stock'] }} low stock</div>
        </a>

        {{-- GenSan Card --}}
        <a href="/?view=gensan&sub={{ $subView }}&month={{ $currentMonth }}&year={{ $currentYear }}" class="top-card {{ $mainView === 'gensan' ? 'active' : '' }}">
            <div class="card-title">
                <span>GenSan Branch</span>
                <span class="panel-badge badge-blue">GS</span>
            </div>
            <div class="card-stat">₱{{ number_format($metrics['gensan']['sales_today'], 2) }}</div>
            <div class="card-sub">{{ $metrics['gensan']['total_products'] }} items • {{ $metrics['gensan']['low_stock'] }} low</div>
        </a>

        {{-- Davao Card --}}
        <a href="/?view=davao&sub={{ $subView }}&month={{ $currentMonth }}&year={{ $currentYear }}" class="top-card {{ $mainView === 'davao' ? 'active' : '' }}">
            <div class="card-title">
                <span>Davao Branch</span>
                <span class="panel-badge badge-red">DVO</span>
            </div>
            <div class="card-stat">₱{{ number_format($metrics['davao']['sales_today'], 2) }}</div>
            <div class="card-sub">{{ $metrics['davao']['total_products'] }} items • {{ $metrics['davao']['low_stock'] }} low</div>
        </a>

        {{-- Cebu Card --}}
        <a href="/?view=cebu&sub={{ $subView }}&month={{ $currentMonth }}&year={{ $currentYear }}" class="top-card {{ $mainView === 'cebu' ? 'active' : '' }}">
            <div class="card-title">
                <span>Cebu Branch</span>
                <span class="panel-badge badge-green">CEB</span>
            </div>
            <div class="card-stat">₱{{ number_format($metrics['cebu']['sales_today'], 2) }}</div>
            <div class="card-sub">{{ $metrics['cebu']['total_products'] }} items • {{ $metrics['cebu']['low_stock'] }} low</div>
        </a>

        {{-- CDO Card --}}
        <a href="/?view=cdo&sub={{ $subView }}&month={{ $currentMonth }}&year={{ $currentYear }}" class="top-card {{ $mainView === 'cdo' ? 'active' : '' }}">
            <div class="card-title">
                <span>CDO Branch</span>
                <span class="panel-badge badge-amber">CDO</span>
            </div>
            <div class="card-stat">₱{{ number_format($metrics['cdo']['sales_today'], 2) }}</div>
            <div class="card-sub">{{ $metrics['cdo']['total_products'] }} items • {{ $metrics['cdo']['low_stock'] }} low</div>
        </a>
    </div>

    {{-- SUB NAVIGATION BAR (4 Sub-Buttons/Cards) --}}
    <div class="sub-nav-bar">
        <div class="sub-nav-group">
            {{-- 1. Sales Today --}}
            <a href="/?view={{ $mainView }}&sub=sales_today&month={{ $currentMonth }}&year={{ $currentYear }}" class="sub-nav-btn {{ $subView === 'sales_today' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                Sales Today
            </a>

            {{-- 2. Inventory --}}
            <a href="/?view={{ $mainView }}&sub=inventory&month={{ $currentMonth }}&year={{ $currentYear }}" class="sub-nav-btn {{ $subView === 'inventory' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zm-1 5H5v-3h14v3zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-1 5H5V5h14v3z"/></svg>
                Inventory
            </a>

            {{-- 3. Added Stock --}}
            <a href="/?view={{ $mainView }}&sub=added_stock&month={{ $currentMonth }}&year={{ $currentYear }}" class="sub-nav-btn {{ $subView === 'added_stock' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                Added Stock
            </a>

            {{-- 4. Running Low --}}
            <a href="/?view={{ $mainView }}&sub=running_low&month={{ $currentMonth }}&year={{ $currentYear }}" class="sub-nav-btn {{ $subView === 'running_low' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Running Low
            </a>
        </div>

        {{-- DB Health Indicators --}}
        <div class="health-bar">
            <span style="font-size:11px;color:#64748b;font-weight:700">DATABASE:</span>
            @foreach ($serverHealth as $key => $h)
            <div class="health-item" title="{{ $h['name'] }}: {{ $h['db'] ? 'Online' : 'Offline' }}">
                <span class="status-dot {{ $h['db'] ? 'online' : 'offline' }}"></span>
                {{ $h['name'] }}
            </div>
            @endforeach
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- SUB-VIEW 1: SALES TODAY                                                  --}}
    {{-- ========================================================================= --}}
    @if ($subView === 'sales_today')

        @if ($mainView === 'overall')
        <div class="grand-total-banner">
            <div>
                <div style="font-size:12px;text-transform:uppercase;color:#93c5fd;font-weight:700">All Branches Grand Total (Today)</div>
                <div style="font-size:26px;font-weight:900;color:#fff">₱{{ number_format($salesTodayData['grand_total'], 2) }}</div>
            </div>
            <span class="panel-badge badge-green">4 BRANCHES ACTIVE</span>
        </div>
        @endif

        <div class="{{ $mainView === 'overall' ? 'tables-grid-4' : 'single-table-container' }}">
            @foreach ($salesTodayData['branches'] as $bKey => $b)
            <div class="panel-box">
                <div class="panel-header">
                    <span class="panel-title">
                        <span class="status-dot online"></span>
                        {{ $b['name'] }} — Sales Today
                    </span>
                    <span class="panel-badge badge-blue">₱{{ number_format($b['total'], 2) }}</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($b['items'] as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td class="text-center"><span class="qty-pill">{{ $item->quantity }}</span></td>
                                <td class="text-right price-text">₱{{ number_format($item->price, 2) }}</td>
                                <td class="text-right"><strong>₱{{ number_format($item->total, 2) }}</strong></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center" style="color:#64748b;padding:24px">No sales recorded today for this branch</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <span style="color:#94a3b8">Total Sales:</span>
                    <span style="color:#38bdf8;font-size:16px">₱{{ number_format($b['total'], 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- SUB-VIEW 2: INVENTORY                                                     --}}
    {{-- ========================================================================= --}}
    @if ($subView === 'inventory')

        {{-- Overall Inventory (4 Simple Tables) --}}
        @if ($mainView === 'overall')
        <div class="tables-grid-4">
            @foreach ($inventoryData as $bKey => $b)
            <div class="panel-box">
                <div class="panel-header">
                    <span class="panel-title">{{ $b['name'] }} Inventory</span>
                    <span class="panel-badge badge-purple">{{ $b['total_count'] }} Products</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($b['items'] as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td class="text-center"><span class="qty-pill">{{ $item->product_quantity }}</span></td>
                                <td class="text-right price-text">₱{{ number_format($item->product_price, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center" style="color:#64748b;padding:24px">No products in inventory</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <span style="color:#94a3b8">Total Stock Units: {{ number_format($b['total_qty']) }}</span>
                    <span style="color:#34d399">Value: ₱{{ number_format($b['total_value'], 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Branch Specific Inventory (Categorized: PC, Laptop, Keyboard, etc., Miscellaneous) --}}
        @else
        <div class="single-table-container">
            @foreach ($categorizedInventory as $bKey => $b)
            <div class="panel-box">
                <div class="panel-header">
                    <span class="panel-title">{{ $b['name'] }} — Categorized Inventory</span>
                    <span class="panel-badge badge-purple">{{ $b['total_count'] }} Total Products</span>
                </div>

                @forelse ($b['categories'] as $catName => $products)
                <div class="category-group-header">
                    <span>📂 {{ $catName }} ({{ $products->count() }} items)</span>
                    <span>Total Qty: {{ $products->sum('product_quantity') }}</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td class="text-center"><span class="qty-pill">{{ $item->product_quantity }}</span></td>
                                <td class="text-right price-text">₱{{ number_format($item->product_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @empty
                <div class="text-center" style="color:#64748b;padding:30px">No products available in this branch</div>
                @endforelse

                <div class="panel-footer">
                    <span style="color:#94a3b8">Total Stock Units: {{ number_format($b['total_qty']) }}</span>
                    <span style="color:#34d399">Total Value: ₱{{ number_format($b['total_value'], 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    @endif


    {{-- ========================================================================= --}}
    {{-- SUB-VIEW 3: ADDED STOCK (Interactive Calendar View)                       --}}
    {{-- ========================================================================= --}}
    @if ($subView === 'added_stock')

        <div class="panel-box" style="margin-bottom:16px">
            <div class="panel-header">
                <span class="panel-title">
                    📅 Added Stock Calendar Stream
                </span>
                <div class="calendar-controls">
                    <a href="/?view={{ $mainView }}&sub=added_stock&month={{ $addedStockData['prev_month']->month }}&year={{ $addedStockData['prev_month']->year }}" class="month-btn">← Prev Month</a>
                    <span class="current-month-title">{{ $addedStockData['month_name'] }}</span>
                    <a href="/?view={{ $mainView }}&sub=added_stock&month={{ $addedStockData['next_month']->month }}&year={{ $addedStockData['next_month']->year }}" class="month-btn">Next Month →</a>
                </div>
            </div>
        </div>

        <div class="calendar-stream">
            @foreach ($addedStockData['days'] as $day)
            <div class="day-card {{ $day['is_today'] ? 'today' : '' }}">
                <div class="day-card-header">
                    <span>
                        {{ $day['date_label'] }}
                        @if ($day['is_today'])
                        <span class="panel-badge badge-blue" style="margin-left:8px">TODAY</span>
                        @endif
                    </span>
                    @if ($day['has_stock'])
                    <span class="panel-badge badge-green">Stock Added</span>
                    @else
                    <span class="panel-badge" style="background:rgba(255,255,255,0.06);color:#64748b">No Additions</span>
                    @endif
                </div>

                <div class="day-branches-grid">
                    @foreach ($day['branch_data'] as $bKey => $bData)
                    <div class="day-branch-col {{ count($day['branch_data']) === 1 ? 'single' : '' }}">
                        <div class="day-branch-title">
                            <span>{{ $bData['name'] }}</span>
                            @if ($bData['has_stock'])
                            <span style="color:#34d399">{{ $bData['items']->count() }} items</span>
                            @endif
                        </div>

                        @if ($bData['has_stock'])
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bData['items'] as $item)
                                <tr>
                                    <td><strong style="color:#fff">{{ $item->product_name }}</strong></td>
                                    <td class="text-center"><span class="qty-pill" style="background:#059669">+{{ $item->quantity }}</span></td>
                                    <td class="text-right price-text">₱{{ number_format($item->product_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="na-tag">N/A (No added stock)</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

    @endif


    {{-- ========================================================================= --}}
    {{-- SUB-VIEW 4: RUNNING LOW                                                   --}}
    {{-- ========================================================================= --}}
    @if ($subView === 'running_low')
        <div class="{{ $mainView === 'overall' ? 'tables-grid-4' : 'single-table-container' }}">
            @foreach ($runningLowData as $bKey => $b)
            <div class="panel-box">
                <div class="panel-header">
                    <span class="panel-title">
                        ⚠️ {{ $b['name'] }} — Running Low Items
                    </span>
                    @if ($b['count'] > 0)
                    <span class="panel-badge badge-amber">{{ $b['count'] }} Warnings</span>
                    @else
                    <span class="panel-badge badge-green">Healthy Stock</span>
                    @endif
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($b['items'] as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    <div style="font-size:11px;color:#f59e0b">Min Threshold: {{ $item->low_stock_threshold }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="qty-pill" style="background:rgba(239,68,68,0.2);color:#f87171">{{ $item->product_quantity }} left</span>
                                </td>
                                <td class="text-right price-text">₱{{ number_format($item->product_price, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center" style="color:#10b981;padding:24px">✅ All products in this branch have sufficient stock</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <span style="color:#94a3b8">Total Low-Stock Items:</span>
                    <span style="color:{{ $b['count'] > 0 ? '#f59e0b' : '#10b981' }}">{{ $b['count'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

</body>
</html>
