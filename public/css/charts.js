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
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
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
        name: 'XYZ MOTORS',
        data: dates
    }],
    chart: {
        type: 'area',
        stacked: false,
        height: 350,
        zoom: {
            type: 'x',
            enabled: true,
            autoScaleYaxis: true
        },
        toolbar: {
            autoSelected: 'zoom'
        }
    },
    dataLabels: {
        enabled: false
    },
    markers: {
        size: 0,
    },
    title: {
        text: 'Stock Price Movement',
        align: 'left'
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.5,
            opacityTo: 0,
            stops: [0, 90, 100]
        },
    },
    yaxis: {
        labels: {
            formatter: function (val) {
                return (val / 1000000).toFixed(0);
            },
        },
        title: {
            text: 'Price'
        },
    },
    xaxis: {
        type: 'datetime',
    },
    tooltip: {
        shared: false,
        y: {
            formatter: function (val) {
                return (val / 1000000).toFixed(0)
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#saleschart"), options);
chart.render();