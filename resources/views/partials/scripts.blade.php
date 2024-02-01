<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('css/charts.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        function renderDonutChart(data) {
            var options = {
                series: data.series,
                labels: data.labels,
                chart: {
                    type: 'donut',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '40%',
                        },
                    },
                },
                responsive: [{
                    breakpoint: 1024,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
                    {
                        breakpoint: 767,
                        options: {
                            chart: {
                                width: 300,
                            },
                            legend: {
                                position: 'bottom',
                            },
                        },
                    }],
            };

            var chart = new ApexCharts(document.querySelector("#donutchart"), options);
            chart.render();
        }
        $.ajax({
            type: 'GET',
            url: '/get_response',
            dataType: 'json',
            success: function (res) {
                renderDonutChart(res);
            },
            error: function (msg) {
                console.error('Error:', msg);
            },
        });

        // Top Employee Chart
        function renderBarChart(data) {
            // Extract store names, total sales, and dental office IDs
            var storeNames = data.map(item => item.store_name);
            var totalSales = data.map(item => item.total_sales);
            var dentalOfficeIds = data.map(item => item.dental_office_id);

            var options = {
                series: [{
                    name: "Total Sales",
                    data: totalSales,
                }],
                chart: {
                    height: 350,
                    type: 'bar'
                },
                xaxis: {
                    categories: storeNames,
                },
                yaxis: {
                    max: 100000,
                },
                plotOptions: {
                    bar: {
                        columnWidth: '60%'
                    }
                },
                colors: ['#00E396'],
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    showForSingleSeries: true,
                    customLegendItems: ["Total Sales"],
                    markers: {
                        fillColors: ['#00E396']
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#employeeChart"), options);
            chart.render();
        }

        $.ajax({
            type: 'GET',
            url: '/get_top_sales',
            dataType: 'json',
            success: function (res) {
                var data = res;
                renderBarChart(data);
            },
            error: function (msg) {
                console.error('Error:', msg);
            },
        });

        // Monthly sales for sales rep

        function updateChart(monthNames, totalSales) {
            var options = {
                series: [{
                    name: "Sale",
                    data: totalSales
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                },
                colors: ["#424242"],
                markers: {
                    size: 4,
                    colors: ["#000"],
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                title: {
                    text: 'Product Trends by Month',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: monthNames
                }
            };

            var chart = new ApexCharts(document.querySelector("#saleschart"), options);
            chart.render();
        }
        $.ajax({
            type: 'GET',
            url: '/get_monthly_sales',
            dataType: 'json',
            success: function (res) {
                var data = res;

                // Extract month names and total sales values from the data
                var monthNames = data.map(function (item) {
                    return item.month;
                });

                var totalSales = data.map(function (item) {
                    return item.total_sales;
                });

                // Call the updateChart function with the extracted data
                updateChart(monthNames, totalSales);
            },
            error: function (msg) {
                console.error('Error:', msg);
            },
        });

        // Weekly sales for stateManager

        var options = {
            series: [
                {
                    name: 'Last Month',
                    data: [] // Leave this empty initially
                },
                {
                    name: 'This Month',
                    data: [] // Leave this empty initially
                }
            ],
            chart: {
                type: 'bar',
                height: 265
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            xaxis: {
                categories: [], // Leave this empty initially
            },
        };

        var chart = new ApexCharts(document.querySelector("#barchart"), options);
        chart.render();

        $.ajax({
            type: 'GET',
            url: '/get_weekly_sales',
            dataType: 'json',
            success: function (res) {
                var data = res;

                // Extract the data for Last Month and This Month
                var lastMonthData = [];
                var thisMonthData = [];

                data.forEach(function (weekData) {
                    lastMonthData.push(weekData.previous_week.total_sales);
                    thisMonthData.push(weekData.current_week.total_sales);
                });

                // Update the chart options with the extracted data
                chart.updateOptions({
                    series: [
                        {
                            name: 'Last Month',
                            data: lastMonthData
                        },
                        {
                            name: 'This Month',
                            data: thisMonthData
                        }
                    ],
                    xaxis: {
                        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4']
                    }
                });
            },
            error: function (msg) {
                console.error('Error:', msg);
            },
        });

    });
</script>
