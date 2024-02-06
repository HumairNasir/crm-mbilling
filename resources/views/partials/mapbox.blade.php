@if(Auth::user()->roles[0]->name == 'CountryManager' || Auth::user()->roles[0]->name == 'CountryManager')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet">
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

        <div id="map" class="w-100" style="height:30rem"></div>

        <!-- @if(Auth::user()->roles[0]->name == 'CountryManager')
            <img src="{{$assets_url}}/images/Map.png" alt="">
        @elseif(Auth::user()->roles[0]->name == 'RegionalManager')
            <img src="{{$assets_url}}/images/Map(1).png" alt="">
        @elseif(Auth::user()->roles[0]->name == 'AreaManager')
            <img src="{{$assets_url}}/images/1.png" alt="">
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="469" height="397" viewBox="0 0 469 397" fill="none">
                <path d="M456.017 275.68C459.399 313.955 462.693 352.23 466.164 390.593V392.196C461.302 391.447 456.793 389.207 453.258 385.787C450.231 382.582 448.629 377.954 444.98 375.284C438.838 370.477 430.115 373.147 422.371 373.948C410.403 374.4 398.543 371.535 388.101 365.67L355.524 327.574C351.17 320.928 345.896 314.934 339.858 309.771C332.921 306.965 325.165 306.965 318.228 309.771C293.928 315.468 269.806 321.343 245.506 327.574L100.952 362.377L55.4672 373.414C48.8803 374.927 22.4443 386.054 19.2399 375.907C18.8841 373.129 18.8841 370.317 19.2399 367.539C18.6561 362.723 16.4229 358.257 12.9199 354.9C9.45415 351.504 6.75345 347.407 4.99805 342.884C20.1299 332.024 34.9948 320.987 49.5036 309.86C52.8362 307.974 55.7427 305.419 58.0404 302.355C60.3381 299.291 61.9774 295.786 62.8552 292.058C62.3964 285.304 59.4466 278.96 54.5773 274.256C14.6113 225.033 126.943 231.175 148.484 229.75C156.805 230.029 164.971 227.453 171.627 222.451C175.543 218.624 177.59 213.55 181.774 209.545C189.785 202.602 201.98 205.005 212.572 203.403C220.769 201.886 228.254 197.753 233.903 191.622C239.552 185.492 243.061 177.695 243.904 169.401C244.438 162.992 241.233 154.714 235.003 154.981C232.417 155.53 229.927 156.46 227.614 157.741C223.667 158.665 219.515 158.059 215.997 156.044C212.478 154.029 209.855 150.754 208.655 146.881C203.226 130.147 224.232 110.209 231.976 96.1449C241.47 77.9985 253.822 61.4984 268.56 47.2778C284.671 33.3031 318.228 29.4757 338.611 22.2658C358.995 15.0559 378.488 8.91411 398.338 2.23828L398.783 3.39547C404.747 42.0263 410.621 80.568 416.585 119.11C416.891 122.545 417.827 125.893 419.344 128.99C422.282 133.975 428.245 136.645 432.073 140.739C438.126 146.525 435.455 153.735 435.455 161.123C435.455 165.484 433.764 188.271 436.88 190.853C441.419 194.502 445.247 198.152 449.786 201.712C449.786 202.602 449.786 203.403 449.786 204.204L456.017 275.68Z" fill="#999999" stroke="#5F5F5F" stroke-width="8" stroke-miterlimit="10" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="424" height="282" viewBox="0 0 424 282" fill="none">
                <path d="M188.85 255.302C158.675 260.375 128.411 265.36 98.1477 269.9C73.8477 273.816 49.5476 277.377 25.1586 280.937C21.6871 277.021 20.708 274.35 17.6817 270.612C17.4204 270.2 17.1224 269.813 16.7915 269.455C15.5453 254.59 14.6554 238.924 13.3202 223.881C12.1631 210.974 10.9167 198.068 9.75954 185.161C5.93207 142.525 1.9266 99.8885 -1.90088 57.3412C7.89034 50.4874 17.6816 43.4556 27.3838 36.4237C29.1392 40.9474 31.8402 45.0441 35.3059 48.4401C38.809 51.7971 41.0419 56.2631 41.6256 61.0797C41.2699 63.8574 41.2699 66.6691 41.6256 69.4467C44.83 79.594 71.2664 68.4676 77.8532 66.9544L123.338 55.9172L267.892 21.1138C292.192 15.2391 316.314 9.36436 340.614 3.3116C347.551 0.505507 355.306 0.505507 362.243 3.3116C368.281 8.47441 373.556 14.4683 377.909 21.1138L410.487 59.2105C408.084 79.6831 403.277 100.601 400.874 121.518C408.15 126.759 414.22 133.497 418.676 141.279C423.004 149.167 424.028 158.449 421.525 167.092C419.91 171.278 417.134 174.917 413.524 177.582C409.913 180.246 405.618 181.826 401.141 182.135L399.005 216.493H398.471C328.746 230.497 258.872 243.434 188.85 255.302Z" fill="#797979" stroke="#5F5F5F" stroke-width="1.756" stroke-miterlimit="10" />
            </svg>
        @endif -->

    </div>
</div>

<script>
    mapboxgl.accessToken =
        'pk.eyJ1IjoiY2FuZGljZWhhbGxzZXR0IiwiYSI6ImNsczRydmJrbTE4cDYya3BpeWVwanRkYW8ifQ.WJrp7UCOMY1KqY5UjKCwKA';
    const map = new mapboxgl.Map({
        container: 'map',
        // Choose from Mapbox's core styles, or make your own style with Mapbox Studio
        style: 'mapbox://styles/mapbox/light-v11',
        projection: 'equalEarth',
        center: [-98, 39], // Centered on the United States
        zoom: 4, // Adjust the zoom level as needed
        maxBounds: [
            [-125, 24], // Southwest bound
            [-66, 50]   // Northeast bound
        ]
    });

    let hoveredPolygonId = null;

    map.on('load', () => {
        map.addSource('states', {
            'type': 'geojson',
            'data': 'https://docs.mapbox.com/mapbox-gl-js/assets/us_states.geojson'
        });

        // The feature-state dependent fill-opacity expression will render the hover effect
        // when a feature's hover state is set to true.
        map.addLayer({
            'id': 'state-fills',
            'type': 'fill',
            'source': 'states',
            'layout': {},
            'paint': {
                'fill-color': '#627BC1',
                'fill-opacity': [
                    'case',
                    ['boolean', ['feature-state', 'hover'], false],
                    0.7,
                    0.1
                ]
            }
        });

        map.addLayer({
            'id': 'state-borders',
            'type': 'line',
            'source': 'states',
            'layout': {},
            'paint': {
                'line-color': '#627BC1',
                'line-width': 1
            }
        });

        // When the user moves their mouse over the state-fill layer, we'll update the
        // feature state for the feature under the mouse.
        map.on('mousemove', 'state-fills', (e) => {
            if (e.features.length > 0) {
                if (hoveredPolygonId !== null) {
                    map.setFeatureState({
                        source: 'states',
                        id: hoveredPolygonId
                    }, {
                        hover: false
                    });
                }
                hoveredPolygonId = e.features[0].id;
                map.setFeatureState({
                    source: 'states',
                    id: hoveredPolygonId
                }, {
                    hover: true
                });
            }
        });

        map.on('click', 'state-fills', (e) => {
            console.log(e.features[0].properties.STATE_NAME,'======> Name Of State');
        })

        // When the mouse leaves the state-fill layer, update the feature state of the
        // previously hovered feature.
        map.on('mouseleave', 'state-fills', () => {
            if (hoveredPolygonId !== null) {
                map.setFeatureState({
                    source: 'states',
                    id: hoveredPolygonId
                }, {
                    hover: false
                });
            }
            hoveredPolygonId = null;
        });
    });

</script>

<!-- <script>
    const accessToken = 'pk.eyJ1IjoiY2FuZGljZWhhbGxzZXR0IiwiYSI6ImNsczRydmJrbTE4cDYya3BpeWVwanRkYW8ifQ.WJrp7UCOMY1KqY5UjKCwKA';

    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-98, 39], // Centered on the United States
        zoom: 3, // Adjust the zoom level as needed
        maxBounds: [
            [-125, 24], // Southwest bound
            [-66, 50]   // Northeast bound
        ]
    });
  
</script> -->
@endif
