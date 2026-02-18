@extends('layouts.backend')

@section('content')
<?php $assets_url = config('constants.assets_url'); ?>

@php
    $contacted_dental_offices = is_iterable($contacted_dental_offices ?? null) ? $contacted_dental_offices : [];
    $dental_offices = is_iterable($dental_offices ?? null) ? $dental_offices : [];
    $total_sale_safe = (float) preg_replace('/[^\d\.\-]/', '', $total_sale ?? 0);
    $safe_sale_count = $total_sale_count ?? 0;
@endphp

<style>
    /* PREMIUM AUTO-PILOT STYLES */
    .auto-pilot-card {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        border-radius: 16px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 25px; /* Spacing below the card */
    }
    .glow-effect {
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(56,189,248,0.2) 0%, rgba(0,0,0,0) 70%);
        z-index: 0;
    }
    .relative-z { position: relative; z-index: 1; }
    
    .icon-box {
        background: rgba(255,255,255,0.1);
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 15px;
    }
    .card-title { font-size: 20px; font-weight: 700; margin: 0 0 5px 0; color: white; }
    
    /* Status Indicators */
    .status-indicator { font-size: 13px; display: flex; align-items: center; gap: 8px; font-weight: 500; }
    .status-indicator.running { color: #4ade80; }
    .status-indicator.waiting { color: #fbbf24; }
    
    .pulse-dot {
        width: 8px; height: 8px; background-color: #4ade80; border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
        animation: pulse-green 2s infinite;
    }
    .static-dot { width: 8px; height: 8px; background-color: #fbbf24; border-radius: 50%; }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }

    /* Stats Grid */
    .stats-grid { display: flex; align-items: center; gap: 20px;margin-left: 20px;}
    .stat-item { text-align: right; }
    .stat-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
    .stat-value { font-size: 32px; font-weight: 800; margin: 0; line-height: 1; color: white; }
    .text-blue { color: #38bdf8; }
    .stat-separator { width: 1px; height: 40px; background: rgba(255,255,255,0.1); }

    .card-footer-info { margin-top: 25px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 12px; opacity: 0.5; color: white; }
    .iteration-badge {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: default;
    }
    
    .iteration-badge:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .iteration-number {
        background: white;
        color: #0f172a; /* Dark text for contrast */
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
    }

    /* --- Sales Rep Dashboard Styles --- */
 
/* --- NEW GAUGE & REVENUE STYLES --- */
.gauge-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    height: 300px; /* Adjust based on your card height */
}
.gauge-container { text-align: center; position: relative; }
.gauge-label { color: #d1d5db; font-size: 14px; font-weight: 500; margin-bottom: 5px; }
.task-subtext { font-size: 12px; color: #6b7280; display: block; margin-top: -10px; }

/* Center the Revenue Text */
.revenue-center {
    text-align: center;
    padding: 20px 0;
}
.revenue-text-big {
    font-size: 36px;
    font-weight: 800;
    color: #fff;
    margin: 0;
}

/* Fix Table scroll inside card
.table-scroll {
    overflow-y: auto;
    max-height: 300px;
} */
</style>

<div class="content-main">
    <div class="dashboard">
        <h3 id="dashboard_title">Dashboard</h3>
        <button id="resetStateFilter" onclick="resetStateFilter()" style="display:none; margin-left:15px; background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">
            Reset Filter <i class="fa fa-times"></i>
        </button>
    </div>

    {{-- SALES REP VIEW --}}

    @if(Auth::user()->roles[0]->name == 'SalesRepresentative')


    <div class="sales-main-graph" style="display: flex; gap: 20px; width: 100%;">
        
        <div class="wd-sm" style="flex: 1; min-width: 0;">
            <div class="sales-resp">
                <div><h4>Converted Offices</h4></div>
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleFilter('menuClients')">
                        <img src="{{$assets_url}}/images/filter.svg" class="filter">
                    </button>
                    <div class="filter-menu" id="menuClients">
                        <div class="filter-menu-item" onclick="setFilter('clients', 'this_week')">This Week</div>
                        <div class="filter-menu-item" onclick="setFilter('clients', 'this_month')">This Month</div>
                        <div class="filter-menu-item" onclick="setFilter('clients', 'this_year')">This Year</div>
                    </div>
                </div>
            </div>
            
            <div style="overflow-y: auto; max-height: 300px;">
                <table class="category-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="color:#9ca3af; text-align:left; padding:10px;">Name</th>
                            <th style="color:#9ca3af; text-align:right; padding:10px;">Date</th>
                        </tr>
                    </thead>
                    <tbody id="listClients">
                        </tbody>
                </table>
            </div>
        </div>

        <div class="wd-sm" style="flex: 1; min-width: 0;">
            <div class="sales-resp">
                <div><h4>Sales</h4></div>
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleFilter('menuRevenue')">
                        <img src="{{$assets_url}}/images/filter.svg" class="filter">
                    </button>
                    <div class="filter-menu" id="menuRevenue">
                        <div class="filter-menu-item" onclick="setFilter('revenue', 'this_week')">This Week</div>
                        <div class="filter-menu-item" onclick="setFilter('revenue', 'this_month')">This Month</div>
                        <div class="filter-menu-item" onclick="setFilter('revenue', 'this_year')">This Year</div>
                    </div>
                </div>
            </div>

            <div class="revenue-center">
                <h1 id="textRevenue" class="revenue-text-big">$0.00</h1>
            </div>
            
            <div style="position: relative;">
                <div id="chartSalesDonut"></div>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                    <span style="font-size: 12px; color: #9ca3af;">Total Sales</span>
                    <br>
                    <span id="textSalesCount" style="font-size: 24px; font-weight: 800; color: #fff;">0</span>
                </div>
            </div>
        </div>

        <div class="wd-sm" style="flex: 1; min-width: 0;">
            <div class="sales-resp">
                <div><h4>Performance</h4></div>
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleFilter('menuPerformance')">
                        <img src="{{$assets_url}}/images/filter.svg" class="filter">
                    </button>
                    <div class="filter-menu" id="menuPerformance">
                        <div class="filter-menu-item" onclick="setFilter('performance', 'today')">Today</div>
                        <div class="filter-menu-item" onclick="setFilter('performance', 'this_week')">This Week</div>
                        <div class="filter-menu-item" onclick="setFilter('performance', 'this_month')">This Month</div>
                    </div>
                </div>
            </div>

            <div class="gauge-wrapper">
                <div class="gauge-container">
                    <div class="gauge-label">✅ Tasks Completed</div>
                    <div id="gaugeTasks"></div>
                    <span id="textTasks" class="task-subtext">(0 of 0 tasks)</span>
                </div>
            </div>
        </div>

    </div>

    {{-- MANAGER VIEW --}}
    @else
        <div class="sales-main-graph">
            <div class="auto-pilot-card">
                <div class="glow-effect"></div>
                <div class="d-flex justify-content-between align-items-center relative-z">
                    <div class="d-flex align-items-center">
                        <div class="icon-box"><img src="{{ asset('images/auto pilot system.svg') }}" width="24" style="filter: brightness(0) invert(1);"></div>
                        <div>
                            <h4 class="card-title">Auto-Pilot System</h4>
                            @if(($pending_tasks ?? 0) > 0) <div class="status-indicator running"><span class="pulse-dot"></span> Active Iteration Running</div>
                            @else <div class="status-indicator waiting"><span class="static-dot"></span> Waiting for Next Batch</div> @endif
                        </div>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item"><span class="stat-label">PENDING TASKS</span><h2 class="stat-value">{{ $pending_tasks ?? 0 }}</h2></div>
                        <div class="stat-separator"></div>
                        <div class="stat-item"><span class="stat-label">COMPLETED TODAY</span><h2 class="stat-value text-blue">{{ $completed_tasks ?? 0 }}</h2></div>
                        <div class="stat-separator"></div>
                        <div class="stat-item"><span class="stat-label">OVERALL COMPLETED</span><h2 class="stat-value" style="color: #4ade80;">{{ $total_completed_tasks ?? 0 }}</h2></div>
                    </div>
                </div>
                <div style="margin-top: 25px; display: flex; align-items: center;">
                 <div class="iteration-badge"><span style="opacity: 0.7; font-weight: 500;">Current Batch:</span><span class="iteration-number">{{ $current_iteration ?? 1 }}</span></div>
                </div>
                <div class="card-footer-info relative-z"><i class="fa fa-clock-o" style="margin-right: 5px; opacity: 0.7;"></i> System checks for new assignments automatically every hour.</div>
            </div>
        </div>

        <div class="sales-main-graph">
           <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h1 style="color:#fff;" class="year_sale">Sales</h1>
                        <h3 id="total_sales_amount_display">$0.00</h3> </div>
                    <div class="filter-dropdown">
                        <button class="filter-button" id="salesFilterBtn">
                            <img src="{{$assets_url}}/images/calen.svg" class="filter">
                        </button>
                        <div class="filter-menu" id="salesFilterMenu" style="display:none;">
                            <button class="filter-menu-item" onclick="updateSalesSummary('week')">This Week</button>
                            <button class="filter-menu-item" onclick="updateSalesSummary('month')">This Month</button>
                            <button class="filter-menu-item" onclick="updateSalesSummary('year')">This Year</button>
                        </div>
                    </div>
                </div>
                <div id="circular-chart"></div>
            </div>
            
            <div class="wd-sm">
                <div class="sales-resp">
                    <div><h4>Response</h4></div>
                    <div class="filter-dropdown">
                        <button class="filter-button"><img src="{{$assets_url}}/images/filter.svg" class="filter"></button>
                        <div class="filter-menu" id="responseFilterMenu">
                            <button class="filter-menu-item" onclick="updateResponseChart('all')">All</button>
                            <button class="filter-menu-item" onclick="updateResponseChart('hot')">Hot</button>
                            <button class="filter-menu-item" onclick="updateResponseChart('warm')">Warm</button>
                            <button class="filter-menu-item" onclick="updateResponseChart('cold')">Cold</button>
                        </div>
                    </div>
                </div>
                <div id="donutchart"></div>
            </div>
            
            <div class="wd-bg">
                <div class="sales-resp">
                    <div><h4>Revenue</h4></div>
                    <div class="filter-dropdown">
                        <button class="filter-button"><img src="{{$assets_url}}/images/filter.svg" class="filter"></button>
                        <div class="filter-menu" id="revenueFilterMenu">
                            <button class="filter-menu-item" onclick="console.log('🟢 Weekly filter clicked'); updateRevenueChart('weekly')">Weekly</button>
                            <button class="filter-menu-item" onclick="console.log('🟢 Monthly filter clicked'); updateRevenueChart('monthly')">Monthly</button>
                        </div>
                    </div>
                </div>
                <div id="barchart"></div>
            </div>
        </div>
    
        @include('partials.mapbox')
        
        <div class="sales-record-main">
             <div class="top-representatives">
                <div class="region-map">
                    <div class="sales-by-region representativesales"><h3>Top Sales Representatives</h3></div>
                    <div class="search-main search-employee">
                        <!-- <input type="search" id="search" placeholder="Search..."><img src="{{$assets_url}}/images/search.svg"> -->
                    </div>
                    <div class="filter-employee">
                        <div class="filter-dropdown">
                            <button type="button" class="filter-button" id="topSalesFilterBtn"><img src="{{$assets_url}}/images/filter.svg" class="filter"></button>
                            <div class="filter-menu" id="topSalesFilterMenu">
                                <button class="filter-menu-item" onclick="updateTopSales('year')">This Year</button>
                                <button class="filter-menu-item" onclick="updateTopSales('month')">This Month</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="employeeChart"></div>
            </div>
            
            <div class="sales-record">
                <div class="sales-resp">
                    <div><h4>Sales Record</h4></div>
                    <div class="filter-dropdown">
                        <button class="filter-button"><img src="{{$assets_url}}/images/filter.svg" class="filter"></button>
                        <div class="filter-menu" id="salesRecordFilterMenu">
                            <button class="filter-menu-item" onclick="updateSalesRecord('today')">Today</button>
                            <button class="filter-menu-item" onclick="updateSalesRecord('week')">Week</button>
                            <button class="filter-menu-item" onclick="updateSalesRecord('month')">Month</button>
                            <button class="filter-menu-item" onclick="updateSalesRecord('quarter')">Quarter</button>
                            <button class="filter-menu-item" onclick="updateSalesRecord('year')">Year</button>
                            <button class="filter-menu-item" onclick="updateSalesRecord('all')">All Time</button>
                        </div>
                    </div>
                </div>
                <div id="saleschart"></div>
            </div>
        </div>

        <!-- <div class="sales-record-main dental-offices-main">
             <div class="top-representatives">
                <div class="dental-office-table-main">
                    <div class="sales-by-region dental-offices"><h3>Doctors Offices</h3></div>
                    <div class="category-search search-employee"><select><option>Sorted by</option><option>All</option></select></div>
                    <div class="search-main search-employee dental-search"><input type="search" placeholder="Search..."><img src="{{$assets_url}}/images/search.svg"></div>
                </div>
                <div class="category-table-main">
                    <table class="category-table">
                        <tbody>
                        @forelse($dental_offices as $dental_office)
                            <tr>
                                <td><img src="{{$assets_url}}/images/img.svg" alt=""></td>
                                <td class="dental-office-name"><h5>{{$dental_office->name}}</h5></td>
                                <td class="dental-office-receptive"><span>{{$dental_office->receptive}}</span></td>
                                <td class="email-button"><a href="mailto:{{$dental_office->contact_person}}"><button>Email</button></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No dental offices found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="sales-record">
                <div class="sales-resp">
                    <div><h4>User Engagement Trends</h4></div>
                    <div><img src="{{$assets_url}}/images/filter.svg" class="filter"></div>
                </div>
                <div id="subscription-bar"></div>
            </div>
        </div> -->
        <div class="sales-record-main dental-offices-main">
        <div class="top-representatives">
            <div class="dental-office-table-main" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                
                <div class="sales-by-region dental-offices">
                    <h4 style="color:#fff;">Recent Clients</h4>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" onclick="toggleFilter('clientTimeFilter')">
                            <img src="{{$assets_url}}/images/filter.svg" class="filter">
                        </button>
                        
                        <div class="filter-menu" id="clientTimeFilter" style="cursor: pointer;">
                            <div class="filter-menu-item" onclick="refreshClientList('all')">All Time</div>
                            <div class="filter-menu-item" onclick="refreshClientList('month')">This Month</div>
                            <div class="filter-menu-item" onclick="refreshClientList('week')">This Week</div>
                            <div class="filter-menu-item" onclick="refreshClientList('today')">Today</div>
                        </div>
                    </div>

                    <div class="search-main search-employee dental-search" style="position: relative;">
                        <input type="search" id="clientSearchInput" placeholder="Name, Dr, State..." onkeyup="refreshClientList()" style="padding: 8px 12px 8px 35px; border-radius: 8px; border: 1px solid #eee; width: 200px;">
                        <img src="{{$assets_url}}/images/search.svg" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px;">
                    </div>

                </div>
            </div>

            <div class="category-table-main" style="max-height: 400px; overflow-y: auto;">
                <table class="category-table" style="width: 100%;">
                    <tbody id="clientTableBody">
                        @include('partials.client_rows', ['clients' => $dental_offices]) 
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="sales-record">
            <div class="sales-resp">
                <div><h4>User Engagement Trends</h4></div>
                
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleFilter('subsFilterMenu')">
                        <img src="{{$assets_url}}/images/filter.svg" class="filter">
                    </button>
                    <div class="filter-menu" id="subsFilterMenu" style="cursor: pointer;">
                        <div class="filter-menu-item" onclick="updateSubscriptionChart('today')">Today</div>
                        <div class="filter-menu-item" onclick="updateSubscriptionChart('week')">This Week</div>
                        <div class="filter-menu-item" onclick="updateSubscriptionChart('month')">This Month</div>
                        <div class="filter-menu-item" onclick="updateSubscriptionChart('year')">This Year</div>
                    </div>
                </div>
                </div>
            <div id="subscription-bar"></div>
        </div>
</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var currentState = null;
    var charts = {};
    var salesCountSafe = {{ $safe_sale_count }};

    // --- 1. CIRCULAR CHART ---
    // --- 1. CIRCULAR CHART ---
    function initCircularChart() {
        if(!document.querySelector("#circular-chart")) return;
        var options = {
            series: [100], 
            chart: { height: 250, type: 'radialBar' },
            colors: ['#38bdf8'],
            plotOptions: {
                radialBar: {
                    hollow: { size: '70%' },
                    dataLabels: {
                        name: { show: true, fontSize: '16px', color: '#fff', offsetY: -10 },
                        value: {
                            color: '#fff', fontSize: '24px', fontWeight: 'bold', show: true, offsetY: 5,
                            formatter: function () { return salesCountSafe; } 
                        },
                        total: {
                            show: true, label: 'Total Sales', color: '#ccc',
                            formatter: function () { return salesCountSafe; }
                        }
                    }
                }
            },
            labels: ['Total Sales'],
        };
        charts.circular = new ApexCharts(document.querySelector("#circular-chart"), options);
        charts.circular.render();
        
        // Load initial data for the year
        loadTotalSale('year');
    }

    // FIX: Define the missing function to fetch data on load and filters
    // function loadTotalSale(range) {
    //     fetch("{{ route('get_total_sale') }}?range=" + range + "&state=" + (currentState || ''))
    //         .then(res => res.json())
    //         .then(data => {
    //             // Align the ID with your HTML: total_sales_amount_display
    //             if(document.getElementById('total_sales_amount_display')) {
    //                 document.getElementById('total_sales_amount_display').innerText = '$' + data.total_amount;
    //             }
                
    //             salesCountSafe = data.total_count;
    //             if(charts.circular) {
    //                 charts.circular.updateOptions({
    //                     plotOptions: { radialBar: { dataLabels: { value: { formatter: () => data.total_count }, total: { formatter: () => data.total_count } } } }
    //                 });
    //             }
    //         });
    // }

    function loadTotalSale(range) {
        // Use currentState if it exists to ensure state-specific filtering
        const stateParam = currentState ? "&state=" + currentState : "";
        fetch("{{ route('get_total_sale') }}?range=" + range + stateParam)
            .then(res => res.json())
            .then(data => {
                // Update text amount above circle
                if(document.getElementById('total_sales_amount_display')) {
                    document.getElementById('total_sales_amount_display').innerText = '$' + data.total_amount;
                }
                
                // Update global count variable
                salesCountSafe = data.total_count;

                // FORCE the chart to re-render internal labels
                if(charts.circular) {
                    charts.circular.updateOptions({
                        plotOptions: {
                            radialBar: {
                                dataLabels: {
                                    value: { formatter: () => data.total_count },
                                    total: { formatter: () => data.total_count }
                                }
                            }
                        }
                    });
                    // Refresh series to trigger animation/redraw
                    charts.circular.updateSeries([100]); 
                }
            });
    }

    // --- 2. SALES RECORD ---
    function initSalesRecord() {
        if(!document.querySelector("#saleschart")) return;
        var options = { series: [], chart: { height: 350, type: 'area', toolbar: { show: false } }, dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 3 }, fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.9, stops: [0, 90, 100] } }, xaxis: { categories: [], labels: { style: { colors: '#fff' } } }, yaxis: { labels: { style: { colors: '#fff' } } }, colors: ['#00E396'], grid: { borderColor: '#40475D' } };
        charts.salesRecord = new ApexCharts(document.querySelector("#saleschart"), options);
        charts.salesRecord.render();
        updateSalesRecord('year');
    }
    function updateSalesRecord(range) {
        if(!charts.salesRecord) return;
        fetch("{{ route('get_monthly_sales') }}?range=" + range + "&state=" + (currentState || ''))
            .then(res => res.json()).then(data => charts.salesRecord.updateOptions({ xaxis: { categories: data.labels }, series: data.series }));
    }

    // --- 3. REVENUE (FIXED: Added Safety Check for Formatter) ---
    // --- 2. REVENUE CHART (Crash-Proof Version) ---
    function initRevenueChart() {
        if(!document.querySelector("#barchart")) return;

        console.log("🔵 [initRevenueChart] Initializing revenue chart...");

        // If a chart already exists on this ID, destroy it before re-creating
        if (charts.revenue && typeof charts.revenue.destroy === 'function') {
            console.log("🟡 [initRevenueChart] Destroying previous chart instance");
            charts.revenue.destroy();
        }

        var options = {
            series: [{ name: 'Revenue', data: [0, 0, 0, 0] }], // Placeholder data
            chart: { 
                type: 'bar', // Horizontal bars
                height: 400, // Increased height for better spacing
                toolbar: { show: false },
                id: 'main-revenue-chart',
                stacked: false
            },
            plotOptions: { 
                bar: { 
                    horizontal: true, // Horizontal bars show week names on left
                    borderRadius: 4,
                    barHeight: '65%'
                } 
            },
            xaxis: {
                // For horizontal bars ApexCharts expects categories on xaxis (they render on Y axis visually)
                categories: ['W1', 'W2', 'W3', 'W4'], // Placeholder will be replaced with real week labels
                type: 'numeric',
                min: 0,
                max: 10000, // Default, will be updated on data
                tickAmount: 5, // Limit ticks to avoid crowding (will be computed dynamically on update)
                labels: {
                    style: { colors: '#fff', fontSize: '11px' },
                    formatter: function (val) {
                        if (val === undefined || val === null || isNaN(val)) return "";
                        // Format as $Xk when >= 1000
                        var v = Number(val);
                        if (Math.abs(v) >= 1000) {
                            var k = v / 1000;
                            return "$" + (k % 1 === 0 ? k.toString() : k.toFixed(1)) + "k";
                        }
                        return "$" + v;
                    }
                }
            },
            yaxis: {
                // Y-axis label styling (categories are set on xaxis for horizontal bars)
                labels: {
                    style: { colors: '#fff', fontSize: '12px' },
                    show: true,
                    maxWidth: 150
                }
            },
            colors: ['#38bdf8'],
            grid: { 
                borderColor: '#40475D',
                padding: { left: 10, right: 20 }
            },
            tooltip: {
                enabled: true,
                x: {
                    formatter: function(value) {
                        return "$" + (value || 0).toFixed(2);
                    }
                }
            }
        };

        try {
            console.log("🟢 [initRevenueChart] Creating new ApexChart instance (bar type)");
            charts.revenue = new ApexCharts(document.querySelector("#barchart"), options);
            console.log("✅ [initRevenueChart] Chart instance created, rendering...");
            charts.revenue.render();
            console.log("✅ [initRevenueChart] Chart rendered, now loading real data...");
        } catch (e) {
            console.error("❌ [initRevenueChart] Creation error:", e);
        }

        updateRevenueChart('weekly');
    }

    function updateRevenueChart(range) {
        const _callId = Math.random().toString(36).slice(2,9);
        console.log("🔵 [updateRevenueChart] (call", _callId + ") Updating chart with range:", range);

        // Debounce duplicate rapid calls
        const now = Date.now();
        if (window._lastRevenueUpdateAt && (now - window._lastRevenueUpdateAt) < (window._revenueUpdateDebounceMs || 500)) {
            console.log("⏱️ [updateRevenueChart] (call", _callId + ") Debounced duplicate call");
            return;
        }
        window._lastRevenueUpdateAt = now;

        if(!charts.revenue) {
            console.error("❌ [updateRevenueChart] Chart instance not found!");
            return;
        }
        
        const stateParam = currentState ? "&state=" + currentState : "";
        const url = "{{ route('get_weekly_sales') }}?range=" + range + stateParam;
        console.log("📡 [updateRevenueChart] Fetching from URL:", url);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log("📥 [updateRevenueChart] Raw data received:", data);
                
                // Check if the data is valid and has the correct keys
                if (data && data.series && data.series.length > 0 && data.labels) {
                    console.log("✅ [updateRevenueChart] Data validation passed");
                    console.log("📋 Labels (weeks):", data.labels);
                    console.log("📊 Series data (raw):", data.series[0].data);
                    
                    // Convert all data values to numbers to avoid string/number mixing
                    let numericData = data.series[0].data.map(val => parseFloat(val) || 0);
                    console.log("📊 Series data (converted to numbers):", numericData);
                    
                    // Calculate max value for better x-axis scaling - round to nearest 2000
                    let maxVal = Math.max(...numericData, 1000);
                    let roundedMax = Math.ceil(maxVal / 2000) * 2000; // Round up to nearest 2000
                    console.log("📊 Max value:", maxVal, "Rounded max:", roundedMax);
                    
                    // Only show labels at clean intervals (every 2000)
                    let tickAmount = Math.ceil(roundedMax / 2000) + 1;
                    console.log("📊 Tick amount:", tickAmount);
                    
                    // Instead of updating options in-place (which can append duplicate SVG nodes in some ApexCharts versions),
                    // destroy and recreate the chart instance with the updated options and data for a clean redraw.
                    console.log("🔄 [updateRevenueChart] Recreating chart with new categories and range... (call", _callId + ")");

                    try {
                        // Build fresh options (mirrors initRevenueChart but with real data)
                        var newOptions = {
                            series: [{ name: 'Revenue', data: numericData }],
                            chart: { type: 'bar', height: 400, toolbar: { show: false }, id: 'main-revenue-chart', stacked: false },
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '65%' } },
                            xaxis: {
                                // categories are applied here for horizontal bars (they render on y-axis visually)
                                categories: data.labels || [],
                                min: 0,
                                max: roundedMax,
                                tickAmount: Math.min(5, tickAmount),
                                labels: {
                                    style: { colors: '#fff', fontSize: '11px' },
                                    formatter: function(val) {
                                        if (val === undefined || val === null || isNaN(val)) return "";
                                        var v = Number(val);
                                        if (Math.abs(v) >= 1000) {
                                            var k = v / 1000;
                                            return "$" + (k % 1 === 0 ? k.toString() : k.toFixed(1)) + "k";
                                        }
                                        return "$" + v;
                                    }
                                }
                            },
                            yaxis: { labels: { style: { colors: '#fff', fontSize: '12px' }, show: true, maxWidth: 150 } },
                            colors: ['#38bdf8'],
                            grid: { borderColor: '#40475D', padding: { left: 10, right: 20 } },
                            tooltip: { enabled: true }
                        };

                        // Remove previous chart instance cleanly and clear container to avoid leftover SVG nodes
                        var container = document.querySelector("#barchart");
                        if (charts.revenue && typeof charts.revenue.destroy === 'function') {
                            try { charts.revenue.destroy(); } catch (e) { /* ignore destroy errors */ }
                        }
                        try { container.innerHTML = ''; } catch (e) { /* ignore */ }
                        charts.revenue = null;

                        // Create new chart
                        charts.revenue = new ApexCharts(container, newOptions);
                        charts.revenue.render().then(function() {
                            console.log("✅ [updateRevenueChart] Recreated and rendered chart (call", _callId + ")");

                            // Clean duplicate tspans that sometimes appear due to rendering quirks
                            function cleanAxisLabels() {
                                try {
                                    const cleanTextNode = (el) => {
                                        // collect non-empty child text values
                                        const tTexts = Array.from(el.childNodes).map(n => (n.textContent || '').trim()).filter(Boolean);
                                        if (tTexts.length === 0) return; // nothing to do
                                        const first = tTexts[0];
                                        // Replace content with a single tspan + title (if title desirable)
                                        // Keep only one tspan and one title
                                        el.innerHTML = '<tspan>' + first + '</tspan>' + (el.querySelector('title') ? ('<title>' + first + '</title>') : '');
                                    };

                                    const xTextEls = Array.from(document.querySelectorAll('#barchart .apexcharts-xaxis text'));
                                    const yTextEls = Array.from(document.querySelectorAll('#barchart .apexcharts-yaxis text'));

                                    xTextEls.forEach(cleanTextNode);
                                    yTextEls.forEach(cleanTextNode);

                                    // Recompute details for logging
                                    const xAxisDetails = xTextEls.map((el, idx) => ({ index: idx, textContent: el.textContent.trim(), innerHTML: el.innerHTML, childCount: el.childNodes.length }));
                                    const yAxisDetails = yTextEls.map((el, idx) => ({ index: idx, textContent: el.textContent.trim(), innerHTML: el.innerHTML, childCount: el.childNodes.length }));

                                    console.log('🧭 [updateRevenueChart] x-axis elements details after clean:', xAxisDetails);
                                    console.log('🧭 [updateRevenueChart] y-axis elements details after clean:', yAxisDetails);
                                } catch (err) {
                                    console.warn('⚠️ [updateRevenueChart] cleanAxisLabels failed:', err);
                                }
                            }

                            setTimeout(function() {
                                cleanAxisLabels();

                                if (charts.revenue && charts.revenue.w) {
                                    console.log('🔬 [updateRevenueChart] Chart internals (config.xaxis, globals):', {
                                        xaxis: charts.revenue.w.config.xaxis || null,
                                        yaxis: charts.revenue.w.config.yaxis || null,
                                        globals: {
                                            minX: charts.revenue.w.globals.minX, maxX: charts.revenue.w.globals.maxX,
                                            minY: charts.revenue.w.globals.minY, maxY: charts.revenue.w.globals.maxY,
                                            labels: charts.revenue.w.globals.labels
                                        }
                                    });
                                }
                            }, 120);
                        }).catch(function(err){
                            console.error('❌ [updateRevenueChart] Error rendering recreated chart:', err);
                        });

                    } catch (err) {
                        console.warn('⚠️ [updateRevenueChart] Recreate path failed, falling back to updateSeries:', err);
                        try {
                            charts.revenue.updateSeries([{ name: 'Revenue', data: numericData }], true);
                        } catch (e) {
                            console.error('❌ [updateRevenueChart] Fallback updateSeries also failed:', e);
                        }
                    }
                } else {
                    console.error("❌ [updateRevenueChart] Invalid data structure:", data);
                }
            })
            .catch(err => console.error("❌ [updateRevenueChart] Fetch Error:", err));
    }

    // --- 4. RESPONSE ---
    function initResponseChart() {
        if(!document.querySelector("#donutchart")) return;
        var options = { series: [], labels: [], chart: { type: 'donut', height: 350 }, colors: ['#ef4444', '#f59e0b', '#3b82f6'], legend: { position: 'bottom', labels: { colors: '#fff' } }, stroke: { show: false } };
        charts.response = new ApexCharts(document.querySelector("#donutchart"), options);
        charts.response.render();
        updateResponseChart();
    }
    // Replace your current updateResponseChart function with this:
    function updateResponseChart(status = 'all') {
        if(!charts.response) return;

        // Construct URL with both State and the clicked Status
        let url = "{{ route('get_response') }}?state=" + (currentState || '') + "&receptive=" + status;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                charts.response.updateOptions({ 
                    labels: data.labels, 
                    series: data.series 
                });
            });
    }

    // --- 5. TOP SALES ---
    function initTopSales() {
        if(!document.querySelector("#employeeChart")) return;
        var options = {
            series: [],
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '50%' } },
            xaxis: { categories: [], labels: { style: { colors: '#fff' }, formatter: function(val){ if(isNaN(val)) return val; var v = Number(val); if(Math.abs(v)>=1000){var k=v/1000; return "$" + (k%1===0? k.toString(): k.toFixed(1)) + "k";} return "$"+v;} } },
            yaxis: { labels: { style: { colors: '#fff' } } },
            colors: ['#fbbf24'], grid: { borderColor: '#40475D' }
        };
        charts.topSales = new ApexCharts(document.querySelector("#employeeChart"), options);
        charts.topSales.render();
        updateTopSales('year');
    }
    function updateTopSales(range) {
        // Recreate chart with fresh options/data to avoid duplicate SVG/tspan nodes
        var container = document.querySelector("#employeeChart");
        fetch("{{ route('get_top_sales') }}?range=" + range + "&state=" + (currentState || ''))
            .then(res => res.json()).then(data => {
                try {
                    // build options similar to init but with real data
                    var maxVal = Math.max(...(data.series && data.series[0] && data.series[0].data ? data.series[0].data : [0,1]));
                    var roundedMax = Math.ceil(maxVal/1000)*1000;
                    var tickAmount = Math.min(5, Math.ceil(roundedMax/2000)+1);

                    var newOptions = {
                        series: data.series || [],
                        chart: { type: 'bar', height: 350, toolbar: { show: false } },
                        plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '50%' } },
                        xaxis: { categories: data.labels || [], min: 0, max: roundedMax, tickAmount: tickAmount, labels: { style: { colors: '#fff' }, formatter: function(val){ if(isNaN(val)) return val; var v=Number(val); if(Math.abs(v)>=1000){var k=v/1000; return "$"+(k%1===0? k.toString(): k.toFixed(1))+"k";} return "$"+v; } } },
                        yaxis: { labels: { style: { colors: '#fff' } } },
                        colors: ['#fbbf24'], grid: { borderColor: '#40475D' }
                    };

                    // destroy/clear previous
                    if (charts.topSales && typeof charts.topSales.destroy === 'function') { try{ charts.topSales.destroy(); }catch(e){} }
                    try{ container.innerHTML = ''; }catch(e){}
                    charts.topSales = new ApexCharts(container, newOptions);
                    charts.topSales.render().then(function(){
                        // clean duplicated tspan nodes if any
                        setTimeout(function(){
                            try{
                                const els = Array.from(container.querySelectorAll('.apexcharts-xaxis text, .apexcharts-yaxis text'));
                                els.forEach(el=>{
                                    const texts = Array.from(el.childNodes).map(n=> (n.textContent||'').trim()).filter(Boolean);
                                    if(texts.length) el.innerHTML = '<tspan>'+texts[0]+'</tspan>' + (el.querySelector('title')? '<title>'+texts[0]+'</title>':'') ;
                                });
                            }catch(e){ }
                        }, 80);
                    }).catch(e=>console.error('TopSales render error', e));
                } catch (err) {
                    console.error('updateTopSales error', err);
                }
            });
    }

    // --- 6. SUBSCRIPTION ---
    function initSubscriptionChart() {
        if(!document.querySelector("#subscription-bar")) return;
        
        var options = { 
            series: [], 
            chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false } }, 
            colors: ['#6366f1', '#a855f7'], 
            plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: '40%' } }, 
            xaxis: { categories: [], labels: { style: { colors: '#fff' } } }, 
            yaxis: { labels: { style: { colors: '#fff' } } }, 
            legend: { position: 'top', labels: { colors: '#fff' } }, 
            grid: { borderColor: '#40475D' } 
        };
        
        charts.subs = new ApexCharts(document.querySelector("#subscription-bar"), options);
        charts.subs.render();
        
        // Pass default 'year' on initial load
        updateSubscriptionChart('year'); 
    }

    function updateSubscriptionChart(range = 'year') {
        if(!charts.subs) return;

        // 1. Close the filter menu when a selection is made
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display='none');

        // 2. Pass the 'range' parameter in the URL
        fetch("{{ route('get_subscriptions_sale') }}?range=" + range + "&state=" + (currentState || ''))
            .then(res => res.json())
            .then(data => {
                charts.subs.updateOptions({ 
                    xaxis: { categories: data.labels }, 
                    series: [ 
                        { name: 'Standard', data: data.standard }, 
                        { name: 'Premium', data: data.premium } 
                    ] 
                });
            });
    }

    // --- 7. CAPTURING ---
    function initCapturing() { if(document.querySelector("#revenueArc")) { } }

    window.updateDashboardByState = function(stateName) {
        currentState = stateName;
        document.getElementById('dashboard_title').innerText = 'Dashboard - ' + stateName;
        document.getElementById('resetStateFilter').style.display = 'inline-block';

        // This ensures if you click the Map, the Search Bar updates to match
        const headerSearch = document.getElementById('globalStateSearch');
        if(headerSearch) {
            headerSearch.value = stateName; 
        }

        // 1. Refresh the Sales Card correctly (Amount + Circle Count)
        loadTotalSale('year'); 

        // 2. Refresh all other working charts
        updateSalesRecord('year'); 
        updateRevenueChart('weekly'); 
        updateResponseChart(); 
        updateTopSales('year'); 
        updateSubscriptionChart();
        refreshClientList();
    };

    // --- ADD THIS MISSING BRIDGE (To fix the Filter Buttons Issue) ---
    window.updateSalesSummary = function(range) {
        loadTotalSale(range);
    };

    window.resetStateFilter = function() {
        currentState = null; 
        document.getElementById('dashboard_title').innerText = 'Dashboard';
        document.getElementById('resetStateFilter').style.display = 'none';
        // [NEW] Clear the search bar
        const headerSearch = document.getElementById('globalStateSearch');
        if(headerSearch) {
            headerSearch.value = ''; 
        }
        window.location.reload(); 
    
    };

    document.addEventListener('DOMContentLoaded', function() {
        
        initCircularChart(); 
        // console.log("✅ Circular chart initialized");
        initSalesRecord(); 
        // console.log("✅ Sales record chart initialized");
        initRevenueChart(); 
        // console.log("✅ Revenue chart initialized");
        initResponseChart(); 
        // console.log("✅ Response chart initialized");
        initTopSales(); 
        // console.log("✅ Top sales chart initialized");
        initSubscriptionChart(); 
        // console.log("✅ Subscription chart initialized");
        initCapturing();
        // console.log("✅ All charts initialized successfully");
        // --- 2. INITIALIZE RECENT CLIENTS LIST ---
    // Loads the default list (Latest 20) on page load
    if (typeof refreshClientList === 'function') {
        refreshClientList('all');
    }

    // --- 3. GLOBAL HEADER SEARCH TRIGGER ---
    // Wires up the top search bar to update the whole dashboard on "Enter"
    const headerSearch = document.getElementById('globalStateSearch');
    if (headerSearch) {
        headerSearch.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Stop page reload
                
                let typedState = this.value.trim();
                
                if (typedState.length > 0) {
                    console.log("🔍 Global Search Triggered for:", typedState);
                    // Update dashboard with the typed state
                    updateDashboardByState(typedState); 
                } else {
                    // If empty, reset the dashboard
                    resetStateFilter();
                }
            }
        });
    }
    });

    document.querySelectorAll('.filter-button').forEach(btn => { 
        btn.addEventListener('click', function(e) { 
            console.log("🔘 [Filter Button Clicked]", this);
            e.stopPropagation(); 
            let m = this.nextElementSibling; 
            document.querySelectorAll('.filter-menu').forEach(x => {
                if(x!==m) x.style.display='none'
            }); 
            m.style.display = m.style.display==='block'?'none':'block'; 
        }); 
    });
    document.addEventListener('click', function() { 
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display='none'); 
    });

    var currentClientRange = 'all';

    function refreshClientList(range = null) {
        if(range) {
            currentClientRange = range;
            // Hide dropdown after selection
            document.getElementById('clientTimeFilter').style.display = 'none'; 
        }

        let search = document.getElementById('clientSearchInput').value;
        let state = currentState || ''; 

        // Construct URL
        let url = `{{ route('clients.filter_dashboard') }}?range=${currentClientRange}&search=${search}&state=${state}`;

        // Fetch and Update
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('clientTableBody').innerHTML = html;
            })
            .catch(error => console.error('Error loading clients:', error));
    }

    // Initial Load on Page Ready
    document.addEventListener('DOMContentLoaded', function() {
        refreshClientList('all');
    });
</script>
<script>
    @if(Auth::user()->roles[0]->name == 'SalesRepresentative')

    // --- 1. DEFINE GLOBALS (OUTSIDE document.ready) ---
    var currentFilters = {
        clients: 'this_month',
        revenue: 'this_month',
        performance: 'this_month'
    };

    var chartTasks = null;
    var chartSales = null;

    // --- 2. DEFINE HELPER FUNCTIONS (Globally accessible) ---

    function toggleFilter(menuId) {
        $('.filter-menu').not('#' + menuId).removeClass('show'); // Close others
        $('#' + menuId).toggleClass('show');
    }

    function setFilter(type, value) {
        currentFilters[type] = value;
        $('#menu' + type.charAt(0).toUpperCase() + type.slice(1)).removeClass('show'); // Close menu
        loadData(type); // Reload specific module
    }

    function loadData(type) {
        var filterVal = currentFilters[type];

        if (type === 'performance') {
            $.get('/get-rep-performance', { filter: filterVal }, function(res) {
                if(chartTasks) {
                    chartTasks.updateSeries([res.tasks.percentage]);
                }
                $('#textTasks').text('(' + res.tasks.text + ')');
            }).fail(function() { console.error("Error loading performance"); });
        } 
        else if (type === 'revenue') {
            $.get('/get-rep-revenue', { filter: filterVal }, function(res) {
                $('#textRevenue').text('$' + res.revenue);
                $('#textSalesCount').text(res.count);
            }).fail(function() { console.error("Error loading revenue"); });
        } 
        else if (type === 'clients') {
            $('#listClients').html('<tr><td colspan="2" class="text-center" style="padding:20px; color:#6b7280;">Loading...</td></tr>');
            
            $.get('/get-rep-converted-list', { filter: filterVal }, function(res) {
                var html = '';
                if(res.offices && res.offices.length > 0) {
                    $.each(res.offices, function(i, office) {
                        var drName = office.dr_name ? office.dr_name : (office.contact_person ? office.contact_person : 'No Doctor');
                        html += `
                        <tr>
                            <td style="padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <div style="color: #fff; font-weight: 600; font-size: 14px;">${office.name}</div>
                                <div style="color: #6b7280; font-size: 12px; margin-top: 2px;">${drName}</div>
                            </td>
                            <td style="padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: right; color: #9ca3af; font-size: 12px;">
                                ${office.formatted_date}
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="2" class="text-center" style="padding:20px; color:#6b7280;">No data found.</td></tr>';
                }
                $('#listClients').html(html);
            }).fail(function() { 
                $('#listClients').html('<tr><td colspan="2" class="text-center text-danger">Error loading data</td></tr>');
            });
        }
    }

    function createGaugeOptions(color) {
        return {
            series: [0],
            chart: { type: 'radialBar', height: 180, fontFamily: 'Inter, sans-serif' },
            plotOptions: {
                radialBar: {
                    hollow: { size: '60%' },
                    track: { background: 'rgba(255,255,255,0.05)' },
                    dataLabels: {
                        name: { show: false },
                        value: { fontSize: '22px', fontWeight: 700, color: '#fff', offsetY: 10, show: true }
                    }
                }
            },
            colors: [color],
            stroke: { lineCap: 'round' }
        };
    }

    function createDonutOptions() {
        return {
            series: [100], 
            chart: { type: 'donut', height: 240, fontFamily: 'Inter, sans-serif' },
            colors: ['#3b82f6', 'rgba(255,255,255,0.05)'], 
            dataLabels: { enabled: false },
            legend: { show: false },
            tooltip: { enabled: false },
            stroke: { width: 0 },
            plotOptions: { pie: { donut: { size: '75%' } } }
        };
    }

    // --- 3. EXECUTE ON LOAD (Inside document.ready) ---
    $(document).ready(function() {
        console.log("🚀 Sales Rep Dashboard Init");

        // Initialize ApexCharts
        if(document.querySelector("#gaugeTasks")) {
            chartTasks = new ApexCharts(document.querySelector("#gaugeTasks"), createGaugeOptions('#10b981'));
            chartTasks.render();
        }

        if(document.querySelector("#chartSalesDonut")) {
            chartSales = new ApexCharts(document.querySelector("#chartSalesDonut"), createDonutOptions());
            chartSales.render();
        }

        // Load Initial Data
        loadData('clients');
        loadData('revenue');
        loadData('performance');

        // Close menus when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.filter-dropdown').length) {
                $('.filter-menu').removeClass('show');
            }
        });
    });

@endif
</script>
@endsection