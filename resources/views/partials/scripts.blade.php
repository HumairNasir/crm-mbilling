<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('css/charts.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            url: '/get_currentYear_sales',
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

        var options = {
            series: [{
                name: "Sale",
                data: [0, 300, 350, 900, 600, 1000, 850, 400, 500, 800, 10000, 600, 800]
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
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            }
        };

        var chart = new ApexCharts(document.querySelector("#saleschart"), options);
        chart.render();

        $.ajax({
            type: 'GET',
            url: '/get_monthly_sales',
            dataType: 'json',
            success: function (res) {
                var data = res;

                // Extract the month names and sales values from the data
                var monthNames = data.map(item => item.month);
                var salesValues = data.map(item => item.total_sales);

                chart.updateSeries([{
                    data: salesValues
                }]);


                chart.updateOptions({
                    xaxis: {
                        categories: monthNames
                    }
                });
            },
            error: function (msg) {
                console.error('Error:', msg);
            },
        });

        // Weekly sales for stateManager

    });
</script>
