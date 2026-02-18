<script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet">

@if(Auth::user()->roles[0]->name == 'CountryManager')
<div class="region-map-main">
    <div class="region-map">
        <div class="sales-by-region">
            <h3>Sales by Country</h3>
            <small style="color: #ccc; font-size: 11px;">Click a state to filter dashboard</small>
        </div>
        <div class="search-main">
            <!-- <input type="search" name="search" id="search" placeholder="Search...">
            <img src="{{$assets_url}}/images/search.svg" alt=""> -->
        </div>
        <div>
            <!-- <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter"> -->
        </div>
    </div>
    <div class="map-vectors mt-3 border">
        <div id="map" class="w-100" style="height:30rem"></div>
    </div>
</div>
@elseif(Auth::user()->roles[0]->name == 'RegionalManager')
<div class="region-map-main">
    <div class="region-map">
        <div class="sales-by-region">
            <h3>Sales by Regional Manager</h3>
        </div>
        <div class="search-main">
            <input type="search" name="search" id="search" placeholder="Search...">
            <img src="{{$assets_url}}/images/search.svg" alt="">
        </div>
        <div>
            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
        </div>
    </div>
    <div class="map-vectors mt-3 border">
        <div id="map" class="w-100" style="height:40rem"></div>
    </div>
</div>
@elseif(Auth::user()->roles[0]->name == 'AreaManager')
<div class="region-map-main">
    <div class="region-map">
        <div class="sales-by-region">
            <h3>Sales by Area Manager</h3>
        </div>
        <div class="search-main">
            <input type="search" name="search" id="search" placeholder="Search...">
            <img src="{{$assets_url}}/images/search.svg" alt="">
        </div>
        <div>
            <img src="{{$assets_url}}/images/filter.svg" alt="" class="filter">
        </div>
    </div>
    <div class="map-vectors mt-3 border">
        <div id="map" class="w-100" style="height:40rem"></div>
    </div>
</div>
@endif

<script>

     mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/candicehallsett/clsa0u23j01iu01qy1ta00wcd',
        // style:"mapbox://styles/mapbox/light-v11",
        projection: 'albers',
        center: [-98, 39],
        zoom: 3.2,
        maxBounds: [
            // [-125, 22], // Southwest coordinates (longitude, latitude)
            // [-88, 60] // Northeast coordinates (longitude, latitude)
            [-170, 18],
            [-65, 72]
        ]

    });

    let hoveredPolygonId = null;
    let isCitySelected = false;

    map.on('load', () => {
        // Add source for US states geojson data
        map.addSource('states', {
            'type': 'geojson',
            'data': 'https://docs.mapbox.com/mapbox-gl-js/assets/us_states.geojson'
        });

        // Define state colors
        const stateColors = {
            'Alabama': 'blue', 'Alaska': 'green', 'Arizona': 'goldenrod', 'Arkansas': 'red',
            'California': 'blue', 'Colorado': 'green', 'Connecticut': 'goldenrod', 'Delaware': 'red',
            'Florida': 'red', 'Georgia': 'green', 'Hawaii': 'goldenrod', 'Idaho': 'red',
            'Illinois': 'blue', 'Indiana': 'green', 'Iowa': 'green', 'Kansas': 'red',
            'Kentucky': 'blue', 'Louisiana': 'green', 'Maine': 'goldenrod', 'Maryland': 'red',
            'Massachusetts': 'blue', 'Michigan': 'green', 'Minnesota': 'goldenrod', 'Mississippi': 'goldenrod',
            'Missouri': 'blue', 'Montana': 'goldenrod', 'Nebraska': 'goldenrod', 'Nevada': 'green',
            'New Hampshire': 'blue', 'New Jersey': 'green', 'New Mexico': 'blue', 'New York': 'red',
            'North Carolina': 'blue', 'North Dakota': 'green', 'Ohio': 'goldenrod', 'Oklahoma': 'green',
            'Oregon': 'blue', 'Pennsylvania': 'green', 'Rhode Island': 'goldenrod', 'South Carolina': 'red',
            'South Dakota': 'blue', 'Tennessee': 'green', 'Texas': 'goldenrod', 'Utah': 'red',
            'Vermont': 'blue', 'Virginia': 'green', 'Washington': 'goldenrod', 'West Virginia': 'red',
            'Wisconsin': 'blue', 'Wyoming': 'green'
        };

        const stateAbbreviations = {
            'Alabama': 'AL', 'Alaska': 'AK', 'Arizona': 'AZ', 'Arkansas': 'AR', 'California': 'CA',
            'Colorado': 'CO', 'Connecticut': 'CT', 'Delaware': 'DE', 'Florida': 'FL', 'Georgia': 'GA',
            'Hawaii': 'HI', 'Idaho': 'ID', 'Illinois': 'IL', 'Indiana': 'IN', 'Iowa': 'IA', 'Kansas': 'KS',
            'Kentucky': 'KY', 'Louisiana': 'LA', 'Maine': 'ME', 'Maryland': 'MD', 'Massachusetts': 'MA',
            'Michigan': 'MI', 'Minnesota': 'MN', 'Mississippi': 'MS', 'Missouri': 'MO', 'Montana': 'MT',
            'Nebraska': 'NE', 'Nevada': 'NV', 'New Hampshire': 'NH', 'New Jersey': 'NJ', 'New Mexico': 'NM',
            'New York': 'NY', 'North Carolina': 'NC', 'North Dakota': 'ND', 'Ohio': 'OH', 'Oklahoma': 'OK',
            'Oregon': 'OR', 'Pennsylvania': 'PA', 'Rhode Island': 'RI', 'South Carolina': 'SC',
            'South Dakota': 'SD', 'Tennessee': 'TN', 'Texas': 'TX', 'Utah': 'UT', 'Vermont': 'VT',
            'Virginia': 'VA', 'Washington': 'WA', 'West Virginia': 'WV', 'Wisconsin': 'WI', 'Wyoming': 'WY'
        };

        // Add fill layer for states with different colors
        map.addLayer({
            'id': 'state-fills',
            'type': 'fill',
            'source': 'states',
            'layout': {},
            'paint': {
                'fill-color': [
                    'match',
                    ['get', 'STATE_NAME'],
                    ...Object.entries(stateColors).flat(),
                    '#5F5F5F' // Default color
                ],
                'fill-opacity': 0.8
            }
        });

        // Add line layer for state borders
        map.addLayer({
            'id': 'state-borders',
            'type': 'line',
            'source': 'states',
            'layout': {},
            'paint': {
                'line-color': '#fff',
                'line-width': 1
            }
        });

        // Set up hover effects on state fills
        map.on('mousemove', 'state-fills', (e) => {
            if (e.features.length > 0) {
                const stateId = e.features[0].id;
                if (hoveredPolygonId !== null) {
                    map.setFeatureState({ source: 'states', id: hoveredPolygonId }, { hover: false });
                }
                hoveredPolygonId = stateId;
                map.setFeatureState({ source: 'states', id: hoveredPolygonId }, { hover: true });
                map.setPaintProperty('state-borders', 'line-color', [
                    'case',
                    ['boolean', ['feature-state', 'hover'], false],
                    '#000',
                    '#ddd'
                ]);
            }
        });

        map.on('mouseleave', 'state-fills', () => {
            if (hoveredPolygonId !== null) {
                map.setFeatureState({ source: 'states', id: hoveredPolygonId }, { hover: false });
            }
            hoveredPolygonId = null;
            map.setPaintProperty('state-borders', 'line-color', '#ddd');
        });

        // Popup logic
        const popup = new mapboxgl.Popup({ closeButton: false, closeOnClick: false });

        map.on('mousemove', 'state-fills', (e) => {
            if (e.features.length > 0) {
                const stateName = e.features[0].properties.STATE_NAME;
                const stateAbbr = stateAbbreviations[stateName];
                const label = stateAbbr ? `${stateName} (${stateAbbr})` : stateName;

                popup.setLngLat(e.lngLat)
                    .setHTML(`<span><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> ${label}</span>`)
                    .addTo(map);
                map.getCanvas().style.cursor = 'pointer';
            }
        });

        map.on('mouseleave', 'state-fills', () => {
            popup.remove();
            map.getCanvas().style.cursor = '';
        });

        // --- 1. CLICK EVENT (UPDATED FOR DASHBOARD FILTERING) ---
        map.on('click', 'state-fills', async (e) => {
            const stateProperties = e.features[0].properties;
            const selectedStateName = stateProperties.STATE_NAME;
            
            console.log(selectedStateName, '======> State Clicked');

            // --- CALL THE GLOBAL FILTER FUNCTION ---
            if (typeof window.updateDashboardByState === 'function') {
                window.updateDashboardByState(selectedStateName);
                
                // Show a simple alert or notification if you have a toaster
                // alert('Filtering Dashboard for: ' + selectedStateName);
            }

            // Visual Styling for Clicked State (Keep existing logic)
            if (hoveredPolygonId !== null) {
                map.setFeatureState({ source: 'states', id: hoveredPolygonId }, { hover: false });
            }

            const stateGeometry = e.features[0].geometry;
            if (stateGeometry && stateGeometry.coordinates && stateGeometry.coordinates[0]) {
                // Clear previous clicks
                map.getLayer('city-borders') && map.removeLayer('city-borders');
                map.getSource('city-borders') && map.removeSource('city-borders');
                map.getLayer('clicked-state-fill') && map.removeLayer('clicked-state-fill');
                map.getSource('clicked-state-fill') && map.removeSource('clicked-state-fill');

                // Highlight new state
                map.addSource('clicked-state-fill', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'geometry': stateGeometry
                    }
                });

                map.addLayer({
                    'id': 'clicked-state-fill',
                    'type': 'fill',
                    'source': 'clicked-state-fill',
                    'layout': {},
                    'paint': {
                        'fill-color': '#5F5F5F',
                        'fill-opacity': 0.5,
                    }
                });

                // Fetch Cities logic (Kept as is)
                const cityDataEndpoint = '{{$assets_url}}/mapJson/gadm41_USA_2.json';
                try {
                    const cityDataResponse = await fetch(cityDataEndpoint);
                    const cityData = await cityDataResponse.json();
                    const filteredFeatures = cityData.features.filter((feature) => {
                        return (feature.properties.NAME_1).replace(" ", "") === (stateProperties.STATE_NAME).replace(" ", "");
                    });

                    map.addSource('city-borders', {
                        'type': 'geojson',
                        'data': { 'type': 'FeatureCollection', 'features': filteredFeatures }
                    });

                    map.addLayer({
                        'id': 'city-borders',
                        'type': 'line',
                        'source': 'city-borders',
                        'layout': {},
                        'paint': { 'line-color': '#fff', 'line-width': 1 }
                    });
                } catch(err) {
                    console.error("City data load error:", err);
                }
            }
        });
    });
</script>