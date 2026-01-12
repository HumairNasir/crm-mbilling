@extends('layouts.backend')
@section('content')
<?php $assets_url= config('constants.assets_url'); ?>

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

<div class="content-main">
    <div class="dashboard">
        <h3>Dashboard</h3>
    </div>
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
{{--                    <div id="simplepie"></div>--}}
                </div>
                <p class="activities">Activities Won</p>
                <p class="activities-won">{{$active_won}}</p>
            </div>
        </div>
    @else
        <div class="sales-main-graph">
            <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h1 style="color:#4D4D4D;" class="year_sale">
                            Sales
                        </h1>
                        <p style="color:#4D4D4D;"  class="year_sale">
                            {{$year = date("Y")}}
                        </p>

                        <h3>${{ number_format($total_sale_safe, 2) }}</h3>
                    </div>
                    <div> <img src="{{$assets_url}}/images/calen.svg" alt="" class="filter">
                    </div>
                </div>
                <div id="circular-chart"></div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h4>Response</h4>
                    </div>
                    <div> <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                    </div>
                </div>
                <div id="donutchart"></div>
            </div>
            <div class="wd-bg">
                <div class="sales-resp">
                    <div>
                        <h4>Revenue by Week</h4>
                    </div>
                    <div><img src="{{$assets_url}}/images/filter.svg" alt="" class="filter"></div>
                </div>
                <div id="barchart"></div>
            </div>
        </div>
    @endif
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
                    <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                </div>
            </div>
            <div id="employeeChart"></div>
        </div>
        <div class="sales-record">
            <div class="sales-resp">
                <div>
                    <h4>Sales Record</h4>
                </div>
                <div>
                    <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
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
                            <td><span>{{$dental_office->receptive}}</span></td>
                            <td class="email-button"><a href="mailto:{{$dental_office->contact_person}}"><button><img src="{{$assets_url}}/images/email.svg" alt="">Email</button></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sales-record">
            <div class="sales-resp">
                <div>
                    <h4>User Engagement Trends</h4>
                </div>
                <div>
                    <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                </div>
            </div>
            <div id="subscription-bar"></div>
        </div>
    </div>
</div>
@endsection
