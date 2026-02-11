<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('css/charts.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        let open = false;
        $(".burger-menu-button").click(function() {
            if(open){
                $(".sidebar").animate({ left: "-100%" }, 500);
                open = false;
            } else {
                $(".sidebar").animate({ left: "0%" }, 500);
                $(".sidebar-overlay").animate({ right: "0%" }, 500);
                open = true;
            }
        });

        $(".sidebar-overlay, .overlay-close").click(function() {
            $(".sidebar").animate({ left: "-100%" }, 500);
            $(".sidebar-overlay").animate({ right: "-100%" }, 500);
            open = false;
        });
    });
</script>

<script>
    $(document).ready(function() {
        console.log("🚀 Master Dashboard Script Loaded");

        // Helper: Format Currency
        function formatMoney(amount) {
            return '$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        }

        // =================================================
        // A. TOTAL SALES CARD (Text + Circular Graph)
        // =================================================
        var circularOptions = {
            series: [0],
            chart: { height: 250, type: 'radialBar', foreColor: '#e0e0e0' },
            plotOptions: {
                radialBar: {
                    hollow: { size: '70%', margin: 0, background: '#fff' },
                    track: { background: "#e7e7e7", strokeWidth: '97%', margin: 5 },
                    dataLabels: {
                        showOn: 'always',
                        name: { offsetY: -10, show: true, color: '#888', fontSize: '16px' },
                        value: { offsetY: 5, color: '#111', fontSize: '30px', show: true, formatter: function(val) { return val } }
                    }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#008FFB'], stops: [0, 100] } },
            labels: ['Total Sales'],
            colors: ['#008FFB']
        };

        var circularChartEl = document.querySelector("#circular-chart");
        var circularChart = circularChartEl ? new ApexCharts(circularChartEl, circularOptions) : null;
        if(circularChart) circularChart.render();

        function loadTotalSale(range) {
            $.get("{{ route('get_total_sale') }}", { range: range }, function(data) {
                var val = parseFloat(data.total || 0);
                
                // 1. Update Text
                $('#total_sales_amount').text(formatMoney(val));
                
                // 2. Update Circular Graph
                if(circularChart) {
                    // Update value inside the circle
                    circularChart.updateOptions({
                        plotOptions: { radialBar: { dataLabels: { value: { formatter: function() { return val } } } } }
                    });
                    // Update the arc (Visual percentage, max capped at 1000 for effect)
                    var percent = Math.min(100, (val / 1000) * 100); 
                    circularChart.updateSeries([percent]); 
                }
            });
        }

        // =================================================
        // B. SALES RESPONSE (Donut + Filters) - #donutchart
        // =================================================
        var responseDataCache = null; // Store data for client-side filtering

        var donutOptions = {
            series: [],
            labels: [],
            chart: { type: 'donut', height: 250, foreColor: '#e0e0e0' },
            colors: ['#ef4444', '#f59e0b', '#3b82f6'], // Hot, Warm, Cold
            plotOptions: { pie: { donut: { size: '40%' } } },
            legend: { position: 'bottom' },
            noData: { text: 'Loading...' }
        };

        var donutEl = document.querySelector("#donutchart");
        var donutChart = donutEl ? new ApexCharts(donutEl, donutOptions) : null;
        if(donutChart) donutChart.render();

        function loadResponse() {
            $.get("{{ route('get_response') }}", function(data) {
                if(data.series && data.labels) {
                    responseDataCache = data; // Save for filtering
                    updateDonut(data.series, data.labels);
                }
            });
        }

        function updateDonut(series, labels) {
            if(donutChart) {
                donutChart.updateOptions({ labels: labels });
                donutChart.updateSeries(series);
            }
        }

        // Client-side Filter Logic
        function applyResponseFilter(filter) {
            if (!responseDataCache) return;

            if (filter === 'all') {
                updateDonut(responseDataCache.series, responseDataCache.labels);
            } else {
                // Find specific index (Hot, Warm, or Cold)
                var labels = ['HOT', 'WARM', 'COLD']; // Must match DB uppercase
                var idx = labels.indexOf(filter.toUpperCase());
                
                // Create a "Single Slice" view or filtered view
                // For a donut, usually we just highlight one, but let's try to isolate it
                if(idx > -1) {
                    var val = responseDataCache.series[idx];
                    updateDonut([val], [filter.toUpperCase()]);
                }
            }
        }

        // =================================================
        // C. REVENUE CARD (Bar Chart) - #barchart
        // =================================================
        var revenueOptions = {
            series: [],
            chart: { type: 'bar', height: 265, foreColor: '#e0e0e0', toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            colors: ['#3b82f6', '#10b981'],
            xaxis: { categories: [], labels: { style: { colors: '#e0e0e0' }, formatter: function(val) { return formatMoney(val) } } },
            yaxis: { labels: { style: { colors: '#e0e0e0' } } },
            grid: { borderColor: '#404040' },
            tooltip: { theme: 'dark' }
        };

        var barEl = document.querySelector("#barchart");
        var revenueChart = barEl ? new ApexCharts(barEl, revenueOptions) : null;
        if(revenueChart) revenueChart.render();

        function loadRevenue(range) {
            if(!revenueChart) return;

            if(range === 'monthly') {
                // Load Monthly Logic
                $.get("{{ route('get_monthly_sales') }}", function(data) {
                    revenueChart.updateOptions({ xaxis: { categories: data.labels } });
                    revenueChart.updateSeries([{ name: 'Total Sales', data: data.series }]);
                });
            } else {
                // Load Weekly Logic (Default)
                $.get("{{ route('get_weekly_sales') }}", function(data) {
                    // Expecting data array of weeks
                    var lastMonth = [];
                    var thisMonth = [];
                    // Simple map assuming controller returns [Week1, Week2...] objects
                    data.forEach(function(w) {
                        lastMonth.push(w.previous_week.total_sales);
                        thisMonth.push(w.current_week.total_sales);
                    });

                    revenueChart.updateOptions({ xaxis: { categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'] } });
                    revenueChart.updateSeries([
                        { name: 'Last Month', data: lastMonth },
                        { name: 'This Month', data: thisMonth }
                    ]);
                });
            }
        }

        // =================================================
        // D. SALES RECORD (Area Chart) - #saleschart
        // =================================================
        var salesAreaOptions = {
            series: [],
            chart: { type: 'area', height: 350, toolbar: { show: false }, foreColor: '#e0e0e0' },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            xaxis: { categories: [], labels: { style: { colors: '#e0e0e0' } } },
            yaxis: { labels: { style: { colors: '#e0e0e0' } } },
            tooltip: { theme: 'dark', y: { formatter: function(val) { return formatMoney(val) } } },
            colors: ['#8b5cf6']
        };
        var salesAreaEl = document.querySelector("#saleschart");
        var salesAreaChart = salesAreaEl ? new ApexCharts(salesAreaEl, salesAreaOptions) : null;
        if(salesAreaChart) salesAreaChart.render();

        function loadSalesRecord(range) {
            if(!salesAreaChart) return;
            $.get("{{ route('get_monthly_sales') }}", { range: range }, function(data) {
                if(data.labels && data.series) {
                    salesAreaChart.updateOptions({ xaxis: { categories: data.labels } });
                    salesAreaChart.updateSeries([{ name: 'Sales', data: data.series }]);
                }
            });
        }

        // =================================================
        // E. TOP REPS (Bar) - #employeeChart
        // =================================================
        var empOptions = {
            series: [],
            chart: { type: 'bar', height: 350, toolbar: { show: false }, foreColor: '#e0e0e0' },
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: [], labels: { style: { colors: '#e0e0e0' }, formatter: function(val) { return formatMoney(val) } } },
            yaxis: { labels: { style: { colors: '#e0e0e0' } } },
            colors: ['#00E396'],
            tooltip: { theme: 'dark' }
        };
        var empEl = document.querySelector("#employeeChart") || document.querySelector("#employeehart");
        var empChart = empEl ? new ApexCharts(empEl, empOptions) : null;
        if(empChart) empChart.render();

        function loadTopReps(range) {
            if(!empChart) return;
            $.get("{{ route('get_top_sales') }}", { range: range }, function(data) {
                var names = data.labels || [];
                var values = data.series || [];
                empChart.updateOptions({ xaxis: { categories: names } });
                empChart.updateSeries([{ name: 'Revenue', data: values }]);
            });
        }

        // =================================================
        // F. ENGAGEMENT (Stacked) - #subscription-bar
        // =================================================
        var subOptions = {
            series: [],
            chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false }, foreColor: '#e0e0e0' },
            plotOptions: { bar: { horizontal: false, borderRadius: 4 } },
            xaxis: { categories: [], labels: { style: { colors: '#e0e0e0' } } },
            yaxis: { labels: { style: { colors: '#e0e0e0' } } },
            fill: { opacity: 1 },
            colors: ['#6366f1', '#f59e0b'],
            tooltip: { theme: 'dark' }
        };
        var subEl = document.querySelector("#subscription-bar");
        var subChart = subEl ? new ApexCharts(subEl, subOptions) : null;
        if(subChart) subChart.render();

        function loadEngagement(range) {
            if(!subChart) return;
            $.get("{{ route('get_subscriptions_sale') }}", { range: range }, function(data) {
                if(data.labels) {
                    subChart.updateOptions({ xaxis: { categories: data.labels } });
                    subChart.updateSeries([
                        { name: 'Standard', data: data.standard },
                        { name: 'Premium', data: data.premium }
                    ]);
                }
            });
        }

        // =================================================
        // G. GAUGE CHARTS (Bottom small charts)
        // =================================================
        var gaugeOpts = {
            series: [0],
            chart: { type: 'radialBar', offsetY: -20, width: 230, sparkline: { enabled: true } },
            plotOptions: {
                radialBar: {
                    startAngle: -90, endAngle: 90,
                    track: { background: "#e7e7e7", strokeWidth: '97%', margin: 5, dropShadow: { enabled: true, top: 2, left: 0, color: '#999', opacity: 1, blur: 2 } },
                    dataLabels: { name: { show: false }, value: { offsetY: -2, fontSize: '22px', color: '#e0e0e0' } }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'light', shadeIntensity: 0.4, inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 50, 53, 91] } },
            labels: ['Average Results'],
        };

        function initGauge(sel, route) {
            var el = document.querySelector(sel);
            if(el) {
                var chart = new ApexCharts(el, gaugeOpts);
                chart.render();
                $.get(route, function(res) { chart.updateSeries([parseInt(res) || 0]); });
            }
        }
        initGauge("#guagechart", "{{ route('get_won_sales') }}");
        initGauge("#guage_chart", "{{ route('get_schedule_sales') }}");
        initGauge("#guages_charts", "{{ route('get_reschedule_sales') }}"); 

        // =================================================
        // H. INITIAL LOAD & UI BINDINGS
        // =================================================
        loadTotalSale('year');
        loadSalesRecord('year');
        loadTopReps('year');
        loadEngagement('year');
        loadResponse();
        loadRevenue('weekly');

        // Generic Button Toggle Logic
        function setupDropdown(btnId, menuId, callback) {
            var btn = document.getElementById(btnId);
            var menu = document.getElementById(menuId);
            if (!btn || !menu) return;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('open');
            });

            menu.addEventListener('click', function(e) {
                var target = e.target;
                if (target && target.dataset) {
                    var val = target.dataset.range || target.dataset.response;
                    if(val) {
                        callback(val);
                        menu.classList.remove('open');
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.remove('open');
                }
            });
        }

        // Bind Filters
        setupDropdown('salesRecordFilterBtn', 'salesRecordFilterMenu', loadSalesRecord);
        setupDropdown('topSalesFilterBtn', 'topSalesFilterMenu', loadTopReps);
        setupDropdown('revenueFilterBtn', 'revenueFilterMenu', loadRevenue); 
        setupDropdown('salesFilterBtn', 'salesFilterMenu', loadTotalSale);
        setupDropdown('responseFilterBtn', 'responseFilterMenu', applyResponseFilter);

    });
</script>