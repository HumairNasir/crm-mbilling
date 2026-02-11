@extends('layouts.backend')

@section('content')
<?php $assets_url = config('constants.assets_url'); ?>

@php
    // Make sure these are iterable (sometimes controller returns JSON/string)
    $contacted_dental_offices = is_iterable($contacted_dental_offices ?? null)
        ? $contacted_dental_offices
        : (is_string($contacted_dental_offices ?? null) ? (json_decode($contacted_dental_offices) ?? []) : []);

    $dental_offices = is_iterable($dental_offices ?? null)
        ? $dental_offices
        : (is_string($dental_offices ?? null) ? (json_decode($dental_offices) ?? []) : []);

    // Safe numeric formatting input (avoids number_format type errors)
    $total_sale_safe = (float) preg_replace('/[^\d\.\-]/', '', $total_sale ?? 0);
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
        <h3>Dashboard</h3>
    </div>

    {{-- 1. SALES REP VIEW --}}
    @if(Auth::user()->roles[0]->name == 'SalesRepresentative')
        
        <div class="sales-record-main new-followup-table-main">
            <div class="">
                <div class="region-map">
                    <div class="sales-by-region representativesales">
                        <h3>Dental offices contacted this week</h3>
                    </div>
                    <div class="search-main search-employee">
                        <input type="search" name="search" id="search" placeholder="Search...">
                        <img src="{{$assets_url}}/images/search.svg" alt="">
                    </div>
                    <div class="filter-employee">
                        <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                    </div>
                </div>
                <div class="new-followup-table">
                    <table>
                        <thead>
                        <tr>
                            <td>#</td>
                            <td colspan="2">Name</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contacted_dental_offices as $contacted_dental_office)
                            <tr>
                                <td>{{$contacted_dental_office->id}}</td>
                                <td>{{$contacted_dental_office->name}}</td>
                                <td><a href="#"><button>Follow up</button></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h3 class="sales-year">Total Sales</h3>
                        <h3>{{$total_sale_count}}</h3>
                    </div>
                    <div> <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                    </div>
                </div>
                <div id="donutchart"></div>
            </div>
            <div class="wd-sm">
                <div class="graph-section-icons">
                    <h4>Capturing by month</h4>
                    <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                </div>
                <div class="chart-groups">
                    <div id="guagechart"></div>
                    <div id="guage_chart"></div>
                    <div id="guages_charts"></div>
                </div>
                <p class="activities">Activities Won</p>
                <p class="activities-won">{{$active_won}}</p>
            </div>
        </div>

    {{-- 2. MANAGER/ADMIN VIEW (Includes Auto-Pilot Card) --}}
    @else
        
        <div class="sales-main-graph">
            <div class="auto-pilot-card">
                <div class="glow-effect"></div>

                <div class="d-flex justify-content-between align-items-center relative-z">
                    <div class="d-flex align-items-center" style="display: flex; align-items: center;">
                        <div class="icon-box">
                            <img src="{{ asset('images/auto pilot system.svg') }}" width="24" style="filter: brightness(0) invert(1);">
                        </div>
                        <div>
                            <h4 class="card-title">Auto-Pilot System</h4>
                            @if(($pending_tasks ?? 0) > 0)
                                <div class="status-indicator running">
                                    <span class="pulse-dot"></span> Active Iteration Running
                                </div>
                            @else
                                <div class="status-indicator waiting">
                                    <span class="static-dot"></span> Waiting for Next Batch
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-label">Pending Tasks</span>
                            <h2 class="stat-value">{{ $pending_tasks ?? 0 }}</h2>
                        </div>
                        <div class="stat-separator"></div>
                        <div class="stat-item">
                            <span class="stat-label">Completed Today</span>
                            <h2 class="stat-value text-blue">{{ $completed_tasks ?? 0 }}</h2>
                        </div>
                        <div class="stat-separator"></div>

                        <div class="stat-item">
                            <span class="stat-label">Overall Completed</span>
                            <h2 class="stat-value" style="color: #4ade80;">{{ $total_completed_tasks ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 25px; display: flex; align-items: center;">
                 <div class="iteration-badge">
                    <span style="opacity: 0.7; font-weight: 500;">Current Batch:</span>
                    <span class="iteration-number">{{ $current_iteration ?? 1 }}</span>
                  </div>
             </div>

                <div class="card-footer-info relative-z">
                    <i class="fa fa-clock-o" style="margin-right: 5px; opacity: 0.7;"></i>
                    System checks for new assignments automatically every hour.
                </div>
            </div>
        </div>
        <div class="sales-main-graph">
            <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h1 style="color:#fff;" class="year_sale">Sales</h1>
                        <p style="color:#fff;" class="year_sale">{{$year = date("Y")}}</p>
                        <h3 id="total_sales_amount">${{ number_format($total_sale_safe, 2) }}</h3>
                    </div>
                    
                   <div class="filter-dropdown">
                        <button type="button" class="filter-button" id="salesFilterBtn" aria-haspopup="true" aria-expanded="false">
                            <img src="{{$assets_url}}/images/calen.svg" alt="" class="filter">
                        </button>
                        <div class="filter-menu" id="salesFilterMenu" role="menu" aria-label="Sales filter">
                            <button type="button" class="filter-menu-item" data-range="year" role="menuitem">This Year</button>
                            <button type="button" class="filter-menu-item" data-range="month" role="menuitem">This Month</button>
                            <button type="button" class="filter-menu-item" data-range="week" role="menuitem">This Week</button>
                        </div>
                    </div>
                </div>
                <div id="circular-chart"></div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div><h4>Response</h4></div>
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" id="responseFilterBtn" aria-haspopup="true" aria-expanded="false">
                            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                        </button>
                        <div class="filter-menu" id="responseFilterMenu" role="menu" aria-label="Response filter">
                            <button type="button" class="filter-menu-item" data-response="all" role="menuitem">All</button>
                            <button type="button" class="filter-menu-item" data-response="hot" role="menuitem">Hot</button>
                            <button type="button" class="filter-menu-item" data-response="warm" role="menuitem">Warm</button>
                            <button type="button" class="filter-menu-item" data-response="cold" role="menuitem">Cold</button>
                        </div>
                    </div>
                </div>
                <div id="donutchart"></div>
            </div>
            <div class="wd-bg">
                <div class="sales-resp">
                    <div><h4>Revenue</h4></div>

                   <div class="filter-dropdown">
                        <button type="button" class="filter-button" id="revenueFilterBtn" aria-haspopup="true" aria-expanded="false">
                            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                        </button>
                        <div class="filter-menu" id="revenueFilterMenu" role="menu" aria-label="Revenue filter">
                            <button type="button" class="filter-menu-item" data-range="weekly" role="menuitem">Weekly</button>
                            <button type="button" class="filter-menu-item" data-range="monthly" role="menuitem">Monthly</button>
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
                    <div class="sales-by-region representativesales">
                        <h3>Top Sales Representatives</h3>
                    </div>
                    <div class="search-main search-employee">
                        <input type="search" name="search" id="search" placeholder="Search...">
                        <img src="{{$assets_url}}/images/search.svg" alt="">
                    </div>
                   <div class="filter-employee">
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" id="topSalesFilterBtn" aria-haspopup="true" aria-expanded="false">
                            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                        </button>
                        <div class="filter-menu" id="topSalesFilterMenu" role="menu" aria-label="Top sales filter">
                            <button type="button" class="filter-menu-item" data-range="year" role="menuitem">This Year</button>
                            <button type="button" class="filter-menu-item" data-range="month" role="menuitem">This Month</button>
                            <button type="button" class="filter-menu-item" data-range="week" role="menuitem">This Week</button>
                            <button type="button" class="filter-menu-item" data-range="all" role="menuitem">All Time</button>
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
                    <button type="button" class="filter-button" id="salesRecordFilterBtn" aria-haspopup="true" aria-expanded="false">
                        <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                    </button>
                    <div class="filter-menu" id="salesRecordFilterMenu" role="menu" aria-label="Sales record filter">
                        <button type="button" class="filter-menu-item" data-range="today" role="menuitem">Today</button>
                        <button type="button" class="filter-menu-item" data-range="week" role="menuitem">This Week</button>
                        <button type="button" class="filter-menu-item" data-range="month" role="menuitem">This Month</button>
                        <button type="button" class="filter-menu-item" data-range="quarter" role="menuitem">This Quarter</button>
                        <button type="button" class="filter-menu-item" data-range="year" role="menuitem">This Year</button>
                        <button type="button" class="filter-menu-item" data-range="all" role="menuitem">All Time</button>
                    </div>
                </div>
                </div>
                <div id="saleschart"></div>
            </div>
        </div>

        <div class="sales-record-main dental-offices-main">
            <div class="top-representatives">
                <div class="dental-office-table-main">
                    <div class="sales-by-region dental-offices">
                        <h3>Dental Offices</h3>
                    </div>
                    <div class="category-search search-employee">
                        <select name="cats" id="categpries">
                            <option value="" disabled selected>Sorted by</option>
                            <option value="dummy1">All</option>
                            <option value="dummy1">Recently Added</option>
                        </select>
                    </div>
                    <div class="search-main search-employee dental-search">
                        <input type="search" name="search" id="search" placeholder="Search...">
                        <img src="{{$assets_url}}/images/search.svg" alt="">
                    </div>
                </div>
                <div class="category-table-main">
                    <table class="category-table">
                        <tbody>
                        @foreach($dental_offices as $dental_office)
                            <tr>
                                <td><img src="{{$assets_url}}/images/img.svg" alt=""></td>
                                <td class="dental-office-name">
                                    <h5>{{$dental_office->name}}</h5>
                                </td>
                                <td class="dental-office-receptive"><span>{{$dental_office->receptive}}</span></td>
                                <td class="email-button"><a href="mailto:{{$dental_office->contact_person}}"><button><img src="{{$assets_url}}/images/email.svg" alt="">Email</button></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="sales-record">
                <div class="sales-resp">
                    <div><h4>User Engagement Trends</h4></div>
                    <div><img src="{{$assets_url}}/images/filter.svg" alt="" class="filter"></div>
                </div>
                <div id="subscription-bar"></div>
            </div>
        </div>
    @endif
</div>
@endsection