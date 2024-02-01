var options = {
    series: [{
        name: 'Standard',
        data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
    }, {
        name: 'Premium',
        data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
    },
    yaxis: {
        title: {
            text: '$ (thousands)'
        }
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$ " + val + " thousands"
            }
        }
    },
    legend: {
        show: true,
        position: "top",
        horizontalAlign: "left",
    },
};

var chart = new ApexCharts(document.querySelector("#subscription-bar"), options);
chart.render();

var options = {
    series: [45],
    chart: {
        type: 'radialBar',
        // width: 300,
        offsetY: -20,
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
                margin: 5, // margin is in pixels
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

var chart = new ApexCharts(document.querySelector("#guagechart"), options);
chart.render();

var options = {
    series: [33, 33, 34],
    chart: {
        // width: 300,
        type: 'pie',
    },
    legend: {
        position: 'bottom'
    },
    labels: ['Team A', 'Team B', 'Team C'],
    responsive: [{
        breakpoint: 480,
        options: {
            // chart: {
            //     width: 200
            // },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var chart = new ApexCharts(document.querySelector("#simplepie"), options);
chart.render();