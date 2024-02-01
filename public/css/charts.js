var options = {
    series: [44, 55, 41],
    chart: {
        type: 'donut',
    },
    plotOptions: {
        pie: {
            // customScale: 1,
            donut: {
                size: "40%",
            },
        },
    },
    responsive: [{
        breakpoint: 1024,
        options: {
            chart: {
                width: 230
            },
            legend: {
                position: 'bottom'
            }
        }
    },
    {
        breakpoint: 767,
        options: {
            chart: {
                width: 300
            },
            legend: {
                position: 'right'
            }
        }
    }
    ]
};

var chart = new ApexCharts(document.querySelector("#donutchart"), options);
chart.render();


var options = {
    series: [{
        name: 'Week 1',
        data: [44, 55, 41, 64]
    }, {
        name: 'Week 2',
        data: [53, 32, 33, 52]
    }],
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
        categories: ['Month 1', 'Month 2', 'Month 3', 'Month 4'],
    },
};

var chart = new ApexCharts(document.querySelector("#barchart"), options);
chart.render();


var options = {
    series: [{
        name: 'Actual',
        data: [{
            x: '2011',
            y: 1292,
            goals: [{
                name: 'Expected',
                value: 1400,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2012',
            y: 4432,
            goals: [{
                name: 'Expected',
                value: 5400,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2013',
            y: 5423,
            goals: [{
                name: 'Expected',
                value: 5200,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2014',
            y: 6653,
            goals: [{
                name: 'Expected',
                value: 6500,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2015',
            y: 8133,
            goals: [{
                name: 'Expected',
                value: 6600,
                strokeHeight: 13,
                strokeWidth: 0,
                strokeLineCap: 'round',
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2016',
            y: 7132,
            goals: [{
                name: 'Expected',
                value: 7500,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2017',
            y: 7332,
            goals: [{
                name: 'Expected',
                value: 8700,
                strokeHeight: 5,
                strokeColor: '#775DD0'
            }]
        },
        {
            x: '2018',
            y: 6553,
            goals: [{
                name: 'Expected',
                value: 7300,
                strokeHeight: 2,
                strokeDashArray: 2,
                strokeColor: '#775DD0'
            }]
        }
        ]
    }],
    chart: {
        height: 350,
        type: 'bar'
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
        customLegendItems: ['Actual', 'Expected'],
        markers: {
            fillColors: ['#00E396', '#775DD0']
        }
    }
};

var chart = new ApexCharts(document.querySelector("#employeehart"), options);
chart.render();

var options = {
    series: [{
        name: "Desktops",
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