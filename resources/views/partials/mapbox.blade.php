@if(Auth::user()->roles[0]->name == 'CountryManager' || Auth::user()->roles[0]->name == 'CountryManager')
<div class="region-map-main">
    <div class="region-map">
        <div class="sales-by-region">
            @if(Auth::user()->roles[0]->name == 'CountryManager')
                <h3>Sales by Country</h3>
            @elseif(Auth::user()->roles[0]->name == 'RegionalManager')
                <h3>Sales by Region</h3>
            @elseif(Auth::user()->roles[0]->name == 'AreaManager')
                <h3>Sales by Area</h3>
            @else
                <h3>Sales by Sales Rep</h3>
            @endif
        </div>
        <div class="search-main">
            <input type="search" name="search" id="search" placeholder="Search...">
            <img src="{{$assets_url}}/images/search.svg" alt="">
        </div>
        <div>
            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
        </div>
    </div>
    <div class="map-vectors">
        <img src="{{$assets_url}}/images/Map.png" alt="" class="">
    </div>
</div>
@endif
