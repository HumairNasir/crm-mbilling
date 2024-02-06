@extends('layouts.backend')
@section('content')
<?php $assets_url= config('constants.assets_url'); ?>
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
                <div class="graph-section-icons">
                    <img src="{{$assets_url}}/images/calendar.svg" alt="" class="calendar">
                    <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                </div>
                <p class="sales-year">Year {{$year = date("Y")}} Sales</p>
                <p class="sales-amount">${{number_format($total_sale)}}</p>
                <div>
                    <p class="sales-year">Total Sales</p>
                    <p class="sales-amount">{{$total_sale_count}}</p>
                </div>
            </div>
            <div class="wd-sm">
                <div class="sales-resp">
                    <div>
                        <h4>Dental Offices Response</h4>
                    </div>
                    <div> <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
                    </div>
                </div>
                <div id="donutchart"></div>
            </div>
            <div class="wd-bg">
                <div class="sales-resp">
                    <div>
                        <h4>Sales by Money</h4>
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
                        <option value="dummy1">Dummy1</option>
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
