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

var subscription_bar = document.querySelector("#subscription-bar")
if(subscription_bar){
    var chart = new ApexCharts(subscription_bar, options);
    chart.render();
}

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
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var simplepie = document.querySelector("#simplepie")
if(simplepie){
    var chart = new ApexCharts(simplepie, options);
    chart.render();
}