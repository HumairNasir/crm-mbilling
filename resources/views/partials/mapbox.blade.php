<script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet">
@if(Auth::user()->roles[0]->name == 'CountryManager')
<div class="region-map-main">
    <div class="region-map">
        <div class="sales-by-region">
            <h3>Sales by Country</h3>
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

        <div id="map" class="w-100" style="height:40rem"></div>

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
        <div class="map-vectors">

            <div id="map" class="w-100" style="height:40rem"></div>

        </div>
    </div>
@endif

<script>
    mapboxgl.accessToken =
        'pk.eyJ1IjoiY2FuZGljZWhhbGxzZXR0IiwiYSI6ImNsczRydmJrbTE4cDYya3BpeWVwanRkYW8ifQ.WJrp7UCOMY1KqY5UjKCwKA';
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/light-v11',
        projection: 'equalEarth',
        center: [-98, 39],
        zoom: 4,
        maxBounds: [
            [-170, 18],
            [-65, 72]
        ]
    });

    let hoveredPolygonId = null;
    let isCitySelected = false

    map.on('load', () => {
        map.addSource('states', {
            'type': 'geojson',
            'data': 'https://docs.mapbox.com/mapbox-gl-js/assets/us_states.geojson'
        });

        map.addLayer({
            'id': 'state-fills',
            'type': 'fill',
            'source': 'states',
            'layout': {},
            'paint': {
                'fill-color': '#133763',
                'fill-opacity': [
                    'case',
                    ['boolean', ['feature-state', 'hover'], false],
                    0.2,
                    0.01
                ]
            }
        });

        map.addLayer({
            'id': 'state-borders',
            'type': 'line',
            'source': 'states',
            'layout': {},
            'paint': {
                'line-color': '#ddd',
                'line-width': 1
            }
        });

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

        map.on('click', 'state-fills', async (e) => {
            const stateProperties = e.features[0].properties;
            console.log(stateProperties.STATE_NAME, '======> Name Of State');

            // Clear hover effect on the clicked state
            if (hoveredPolygonId !== null) {
                map.setFeatureState({
                    source: 'states',
                    id: hoveredPolygonId
                }, {
                    hover: false
                });
            }

            const stateGeometry = e.features[0].geometry;
            console.log(stateGeometry.coordinates,"stateProperties.STATE_NAME");
            if (stateGeometry && stateGeometry.coordinates && stateGeometry.coordinates[0]) {
                // Check if it's a 3D array (contains nested arrays)
                const is3DArray = Array.isArray(stateGeometry.coordinates[0][0][0]);

                // Create bounds based on the type of array
                const bounds = is3DArray
                    ? stateGeometry.coordinates.reduce((outerBounds, polygon) => {
                        return polygon.reduce((polyBounds, ring) => {
                            return ring.reduce((innerBounds, coord) => innerBounds.extend(coord), polyBounds);
                        }, outerBounds);
                    }, new mapboxgl.LngLatBounds(stateGeometry.coordinates[0][0][0], stateGeometry.coordinates[0][0][0]))
                    : stateGeometry.coordinates[0].reduce((polyBounds, coord) => polyBounds.extend(coord), new mapboxgl.LngLatBounds(stateGeometry.coordinates[0][0], stateGeometry.coordinates[0][0]));

                map.fitBounds(bounds, { padding: 20 });

                // Clear previously added city-borders layer
                map.getLayer('city-borders') && map.removeLayer('city-borders');
                map.getSource('city-borders') && map.removeSource('city-borders');
                map.getLayer('clicked-state-fill') && map.removeLayer('clicked-state-fill');
                map.getSource('clicked-state-fill') && map.removeSource('clicked-state-fill');

                // Add a source for the clicked state
                map.addSource('clicked-state-fill', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'geometry': stateGeometry
                    }
                });

                const cityDataEndpoint = '{{$assets_url}}/mapJson/gadm41_USA_2.json';
                const cityDataResponse = await fetch(cityDataEndpoint);
                const cityData = await cityDataResponse.json();
                const filteredFeatures = cityData.features.filter((feature) => {
                    return (feature.properties.NAME_1).replace(" ", "") === (stateProperties.STATE_NAME).replace(" ", "");
                });

                // Add a source for city boundaries of the selected state
                map.addSource('city-borders', {
                    'type': 'geojson',
                    'data': {
                        'type': 'FeatureCollection',
                        'features': filteredFeatures
                    }
                });

                // Add a layer for city boundaries of the selected state
                map.addLayer({
                    'id': 'city-borders',
                    'type': 'line',
                    'source': 'city-borders',
                    'layout': {},
                    'paint': {
                        'line-color': '#61d6cd',
                        'line-width': 1
                    }
                });

                // Add hover effect on city polygons
            map.on('mousemove', 'city-borders', (e) => {
                if (e.features.length > 0) {
                    const newHoveredPolygonId = e.features[0].id || e.features[0].properties.index;

                    if (newHoveredPolygonId) {
                        if (hoveredPolygonId !== null && hoveredPolygonId !== newHoveredPolygonId) {
                            map.setFeatureState({
                                source: 'city-borders',
                                id: hoveredPolygonId
                            }, {
                                hover: false
                            });
                        }

                        hoveredPolygonId = newHoveredPolygonId;

                        map.setFeatureState({
                            source: 'city-borders',
                            id: hoveredPolygonId
                        }, {
                            hover: true
                        });
                    }
                }else{
                    console.log('NOT 1');
                }
            });

            // Remove hover effect when leaving city polygons
            map.on('mouseleave', 'city-borders', () => {
                if (hoveredPolygonId !== null) {
                    map.setFeatureState({
                        source: 'city-borders',
                        id: hoveredPolygonId
                    }, {
                        hover: false
                    });
                    hoveredPolygonId = null;
                }else{
                    console.log('NOT 1222');
                }
            });
            }else{
                console.log('HERE');
            }
        });

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
