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
