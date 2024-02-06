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
                legend: {
                            position: 'bottom',
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

            var donutchart = document.querySelector("#donutchart")
            if(donutchart){
                var chart = new ApexCharts(donutchart, options);
                chart.render();
            }
        }
        $.ajax({
            type: 'GET',
            url: '/get_response',
            dataType: 'json',
            success: function (res) {
                renderDonutChart(res);
            },
            error: function (msg) {
                console.log('Error:', msg);
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
                    labels: {
                        formatter: function (value) {
                            return '$' + value;
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        columnWidth: '60%'
                    }
                },
                colors: ['#00E396'],
                dataLabels: {
                    enabled: false,
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

            var employeeChart = document.querySelector("#employeeChart")
            if(employeeChart){
                var chart = new ApexCharts(employeeChart, options);
                chart.render();
            }
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
                console.log('Error:', msg);
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
                    text: 'Total Sales by Month',
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
                },
                yaxis: {
                    max: 1000000,
                    labels: {
                        formatter: function (value) {
                            return '$' + value;
                        }
                    }
                }
            };

            var saleschart = document.querySelector("#saleschart")
            if(saleschart){
                var chart = new ApexCharts(saleschart, options);
                chart.render();
            }

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
                console.log('Error:', msg);
            },
        });

        // Weekly sales for stateManager

        var options = {
            series: [
                {
                    name: 'Last Month',
                    data: []
                },
                {
                    name: 'This Month',
                    data: []
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
                enabled: false,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                },
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
                categories: [],
                max: 100000,
                labels: {
                    formatter: function (value) {
                        return '$' + value;
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return '$' + value;
                    }
                }
            }
        };

        var barchart = document.querySelector("#barchart")
        if(barchart){
            var chart = new ApexCharts(barchart, options);
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
                    console.log('Error:', msg);
                },
            });
        }

    });
</script>
<script>
$(document).ready(function () {

    var options = {
        series: [],
        chart: {
            type: 'radialBar',
            offsetY: -20,
            width: 230,
            sparkline: {
                enabled: true
            },
        },
        plotOptions: {
            radialBar: {
                startAngle: -90,
                endAngle: 90,
                track: {
                    background: "#e7e7e7",
                    strokeWidth: '97%',
                    margin: 5,
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 0,
                        color: '#999',
                        opacity: 1,
                        blur: 2
                    }
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        offsetY: -2,
                        fontSize: '22px'
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -10
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                shadeIntensity: 0.4,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 53, 91]
            },
        },
        labels: ['Average Results'],
    };

    var guagechart = document.querySelector("#guagechart")
    if(guagechart){
        var chart = new ApexCharts(guagechart, options);
        chart.render();
        $.ajax({
            type: 'GET',
            url: '/get_won_sales',
            dataType: 'json',
            success: function (res) {
                options.series = [res];
    
    
                chart.updateSeries(options.series);
    
                chart.render();
            },
            error: function (msg) {
                console.log('Error:', msg);
            },
        });
    }

});
</script>
<script>
    $(document).ready(function () {

        var options = {
            series: [],
            chart: {
                type: 'radialBar',
                offsetY: -20,
                width: 230,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#e7e7e7",
                        strokeWidth: '97%',
                        margin: 5,
                        dropShadow: {
                            enabled: true,
                            top: 2,
                            left: 0,
                            color: '#999',
                            opacity: 1,
                            blur: 2
                        }
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: '22px'
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -10
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 53, 91]
                },
            },
            labels: ['Average Results'],
        };

        var guage_chart = document.querySelector("#guage_chart")
        if(guage_chart){
            var chart = new ApexCharts(guage_chart, options);
            chart.render();
            $.ajax({
                type: 'GET',
                url: '/get_schedule_sales',
                dataType: 'json',
                success: function (res) {
                    options.series = [res];


                    chart.updateSeries(options.series);

                    chart.render();
                },
                error: function (msg) {
                    console.log('Error:', msg);
                },
            });
        }

    });
</script>
<script>
    $(document).ready(function () {

        var options = {
            series: [],
            chart: {
                type: 'radialBar',
                offsetY: -20,
                width: 230,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#e7e7e7",
                        strokeWidth: '97%',
                        margin: 5,
                        dropShadow: {
                            enabled: true,
                            top: 2,
                            left: 0,
                            color: '#999',
                            opacity: 1,
                            blur: 2
                        }
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: '22px'
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -10
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 53, 91]
                },
            },
            labels: ['Average Results'],
        };
        var guages_charts = document.querySelector("#guages_charts")
        if(guages_charts){
            var chart = new ApexCharts(guages_charts, options);
            chart.render();
            $.ajax({
                type: 'GET',
                url: '/get_reschedule_sales',
                dataType: 'json',
                success: function (res) {
                    options.series = [res];
    
    
                    chart.updateSeries(options.series);
    
                    chart.render();
                },
                error: function (msg) {
                    console.log('Error:', msg);
                },
            });
        }

    });
</script>
