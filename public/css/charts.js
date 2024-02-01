var options = {
    series: [{
        name: 'Last Month',
        data: [44, 55, 41, 64]
    }, {
        name: 'This Month',
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
        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    },
};

var chart = new ApexCharts(document.querySelector("#barchart"), options);
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
