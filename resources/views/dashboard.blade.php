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
        <div class="sales-record-main new-followup-table-main">
            <div>
                <div class="region-map">
                    <div class="sales-by-region representativesales"><h3>Dental offices contacted this week</h3></div>
                    <div class="search-main search-employee"><input type="search" placeholder="Search..."><img src="{{$assets_url}}/images/search.svg"></div>
                    <div class="filter-employee"><img src="{{$assets_url}}/images/filter.svg" class="filter"></div>
                </div>
                <div class="new-followup-table">
                    <table>
                        <thead><tr><td>#</td><td colspan="2">Name</td></tr></thead>
                        <tbody>
                        @foreach($contacted_dental_offices as $co)
                            <tr><td>{{$co->id}}</td><td>{{$co->name}}</td><td><a href="#"><button>Follow up</button></a></td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div><h3 class="sales-year">Total Sales</h3><h3 id="display_total_sales_count">{{$total_sale_count}}</h3></div>
                    <div><img src="{{$assets_url}}/images/filter.svg" class="filter"></div>
                </div>
                <div id="donutchart"></div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div><h4 id="capturingTitle">Capturing Stats</h4></div>
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" id="capturingFilterBtn"><img src="{{$assets_url}}/images/filter.svg" class="filter"></button>
                        <div class="filter-menu" id="capturingFilterMenu" style="display:none;">
                            <button class="filter-menu-item" onclick="updateCapturing('today', 'Today')">Today</button>
                            <button class="filter-menu-item" onclick="updateCapturing('month', 'This Month')">This Month</button>
                        </div>
                    </div>
                </div>
                <div class="chart-groups">
                    <div style="position:relative;"><div id="revenueArc"></div><div style="text-align:center;color:#fff;">Revenue</div></div>
                    <div style="position:relative;"><div id="conversionArc"></div><div style="text-align:center;color:#fff;">Conversion</div></div>
                    <div style="position:relative;"><div id="activityArc"></div><div style="text-align:center;color:#fff;">Activity</div></div>
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
                        <div class="stat-item"><span class="stat-label">Pending</span><h2 class="stat-value">{{ $pending_tasks ?? 0 }}</h2></div>
                        <div class="stat-separator"></div>
                        <div class="stat-item"><span class="stat-label">Done Today</span><h2 class="stat-value text-blue">{{ $completed_tasks ?? 0 }}</h2></div>
                        <div class="stat-separator"></div>
                        <div class="stat-item"><span class="stat-label">Overall</span><h2 class="stat-value" style="color: #4ade80;">{{ $total_completed_tasks ?? 0 }}</h2></div>
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
                            <button class="filter-menu-item" onclick="updateRevenueChart('weekly')">Weekly</button>
                            <button class="filter-menu-item" onclick="updateRevenueChart('monthly')">Monthly</button>
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
                    <div class="search-main search-employee"><input type="search" id="search" placeholder="Search..."><img src="{{$assets_url}}/images/search.svg"></div>
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

        <div class="sales-record-main dental-offices-main">
             <div class="top-representatives">
                <div class="dental-office-table-main">
                    <div class="sales-by-region dental-offices"><h3>Dental Offices</h3></div>
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

        // If a chart already exists on this ID, destroy it before re-creating
        if (charts.revenue && typeof charts.revenue.destroy === 'function') {
            charts.revenue.destroy();
        }

        var options = {
            series: [{ name: 'Revenue', data: [] }],
            chart: { 
                type: 'bar', 
                height: 350, 
                toolbar: { show: false },
                id: 'main-revenue-chart' // Unique ID to track the instance
            },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '50%' } },
            xaxis: {
                categories: [],
                labels: {
                    style: { colors: '#fff' },
                    formatter: function (val) {
                        // PREVENT CRASH: Only format if val is a valid number
                        if (val === undefined || val === null || isNaN(val) || val === "") return "";
                        return val >= 1000 ? "$" + (val / 1000).toFixed(1) + "k" : "$" + val;
                    }
                }
            },
            yaxis: { labels: { style: { colors: '#fff' } } },
            colors: ['#38bdf8'],
            grid: { borderColor: '#40475D' }
        };

        try {
            charts.revenue = new ApexCharts(document.querySelector("#barchart"), options);
            charts.revenue.render();
        } catch (e) {
            console.error("Revenue Chart render error:", e);
        }

        updateRevenueChart('weekly');
    }

    function updateRevenueChart(range) {
        if(!charts.revenue) return;
        
        fetch("{{ route('get_weekly_sales') }}?range=" + range + "&state=" + (currentState || ''))
            .then(res => res.json())
            .then(data => {
                // Check if the data is valid and has the correct keys
                if (data && data.series && data.series.length > 0) {
                    charts.revenue.updateOptions({
                        xaxis: { categories: data.labels || [] },
                        series: data.series
                    }, false, true); // true as third param to animate
                }
            })
            .catch(err => console.error("Revenue Fetch Error:", err));
    }

    // --- 4. RESPONSE ---
    function initResponseChart() {
        if(!document.querySelector("#donutchart")) return;
        var options = { series: [], labels: [], chart: { type: 'donut', height: 350 }, colors: ['#ef4444', '#f59e0b', '#3b82f6'], legend: { position: 'bottom', labels: { colors: '#fff' } }, stroke: { show: false } };
        charts.response = new ApexCharts(document.querySelector("#donutchart"), options);
        charts.response.render();
        updateResponseChart();
    }
    function updateResponseChart(status = 'all') {
        if(!charts.response) return;
        fetch("{{ route('get_response') }}?state=" + (currentState || ''))
            .then(res => res.json()).then(data => charts.response.updateOptions({ labels: data.labels, series: data.series }));
    }

    // --- 5. TOP SALES ---
    function initTopSales() {
        if(!document.querySelector("#employeeChart")) return;
        var options = { series: [], chart: { type: 'bar', height: 350, toolbar: { show: false } }, 
        plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '50%' } }, 
        xaxis: { categories: [], labels: { style: { colors: '#fff' } } }, yaxis: { labels: { style: { colors: '#fff' } } }, 
        colors: ['#fbbf24'], grid: { borderColor: '#40475D' } };
        charts.topSales = new ApexCharts(document.querySelector("#employeeChart"), options);
        charts.topSales.render();
        updateTopSales('year');
    }
    function updateTopSales(range) {
        if(!charts.topSales) return;
        fetch("{{ route('get_top_sales') }}?range=" + range + "&state=" + (currentState || ''))
            .then(res => res.json()).then(data => charts.topSales.updateOptions({ xaxis: { categories: data.labels }, series: data.series }));
    }

    // --- 6. SUBSCRIPTION ---
    function initSubscriptionChart() {
        if(!document.querySelector("#subscription-bar")) return;
        var options = { series: [], chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false } }, colors: ['#6366f1', '#a855f7'], plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: '40%' } }, xaxis: { categories: [], labels: { style: { colors: '#fff' } } }, yaxis: { labels: { style: { colors: '#fff' } } }, legend: { position: 'top', labels: { colors: '#fff' } }, grid: { borderColor: '#40475D' } };
        charts.subs = new ApexCharts(document.querySelector("#subscription-bar"), options);
        charts.subs.render();
        updateSubscriptionChart();
    }
    function updateSubscriptionChart() {
        if(!charts.subs) return;
        fetch("{{ route('get_subscriptions_sale') }}?state=" + (currentState || ''))
            .then(res => res.json()).then(data => charts.subs.updateOptions({ xaxis: { categories: data.labels }, series: [ { name: 'Standard', data: data.standard }, { name: 'Premium', data: data.premium } ] }));
    }

    // --- 7. CAPTURING ---
    function initCapturing() { if(document.querySelector("#revenueArc")) { } }

    // --- GLOBAL UPDATE ---
  // --- UPDATED GLOBAL UPDATE (To fix the State Click Issue) ---
    // window.updateDashboardByState = function(stateName) {
    //     currentState = stateName;
    //     document.getElementById('dashboard_title').innerText = 'Dashboard - ' + stateName;
    //     document.getElementById('resetStateFilter').style.display = 'inline-block';

    //     // 1. Update the Sales Card (Fetch the specific state data)
    //     loadTotalSale('year'); 

    //     // 2. Update the Dashboard Stats (Revenue Text + Circle Count)
    //     fetch("{{ route('get_dashboard_stats') }}?state=" + stateName)
    //         .then(res => res.json())
    //         .then(data => { 
    //             // FIXED ID: Using total_sales_amount_display
    //             if(document.getElementById('total_sales_amount_display')) {
    //                 document.getElementById('total_sales_amount_display').innerText = data.total_revenue;
    //             }
                
    //             if(charts.circular) {
    //                 salesCountSafe = data.total_sales_count;
    //                 charts.circular.updateOptions({
    //                     plotOptions: {
    //                         radialBar: {
    //                             dataLabels: {
    //                                 value: { formatter: function () { return data.total_sales_count; } },
    //                                 total: { formatter: function () { return data.total_sales_count; } }
    //                             }
    //                         }
    //                     }
    //                 });
    //             }
    //         });

    //     // Refresh all other working charts
    //     updateSalesRecord('year'); 
    //     updateRevenueChart('weekly'); 
    //     updateResponseChart(); 
    //     updateTopSales('year'); 
    //     updateSubscriptionChart();
    // };

    window.updateDashboardByState = function(stateName) {
        currentState = stateName;
        document.getElementById('dashboard_title').innerText = 'Dashboard - ' + stateName;
        document.getElementById('resetStateFilter').style.display = 'inline-block';

        // 1. Refresh the Sales Card correctly (Amount + Circle Count)
        loadTotalSale('year'); 

        // 2. Refresh all other working charts
        updateSalesRecord('year'); 
        updateRevenueChart('weekly'); 
        updateResponseChart(); 
        updateTopSales('year'); 
        updateSubscriptionChart();
    };

    // --- ADD THIS MISSING BRIDGE (To fix the Filter Buttons Issue) ---
    window.updateSalesSummary = function(range) {
        loadTotalSale(range);
    };

    window.resetStateFilter = function() {
        currentState = null; document.getElementById('dashboard_title').innerText = 'Dashboard'; document.getElementById('resetStateFilter').style.display = 'none';
        window.location.reload(); 
    };

    document.addEventListener('DOMContentLoaded', function() {
        initCircularChart(); initSalesRecord(); initRevenueChart(); initResponseChart(); initTopSales(); initSubscriptionChart(); initCapturing();
    });

    document.querySelectorAll('.filter-button').forEach(btn => { btn.addEventListener('click', function(e) { e.stopPropagation(); let m = this.nextElementSibling; document.querySelectorAll('.filter-menu').forEach(x => {if(x!==m) x.style.display='none'}); m.style.display = m.style.display==='block'?'none':'block'; }); });
    document.addEventListener('click', function() { document.querySelectorAll('.filter-menu').forEach(m => m.style.display='none'); });
</script>


@endsection