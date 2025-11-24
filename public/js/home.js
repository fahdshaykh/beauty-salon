(function($) {
    'use strict';
    $(function() {

        let bookingChart = null;
        let salesStatusChart = null;
        let dailySalesChart = null;

        // 1. Booking Overview (Bar Chart)
        if ($("#home-orders-chart").length) {
            function loadBookingChart() {
                $.ajax({
                    url: '/dashboard/booking-overview',
                    type: 'GET',
                    success: function(response) {
                        if (bookingChart) bookingChart.destroy();

                        var ctx = $("#home-orders-chart").get(0).getContext("2d");
                        bookingChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    label: 'Bookings',
                                    data: response.data,
                                    backgroundColor: '#392c70'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    yAxes: [{
                                        ticks: { beginAtZero: true, fontColor: "#686868" },
                                        gridLines: { drawBorder: false }
                                    }],
                                    xAxes: [{
                                        ticks: { fontColor: "#686868" },
                                        gridLines: { display: false },
                                        barPercentage: 0.4
                                    }]
                                },
                                legend: { display: false }
                            }
                        });

                        $('#home-orders-chart-legend').html('<ul class="legend"><li><span class="legend-label" style="background-color:#392c70"></span>Bookings</li></ul>');
                    },
                    error: function() {
                        alert('Failed to load booking overview');
                    }
                });
            }
            loadBookingChart();
        }

        // 2. Sales Status Pie Chart
        if ($("#home-sales-status-chart").length) {
            function loadSalesStatusChart() {
                $.ajax({
                    url: '/dashboard/sales-status',
                    type: 'GET',
                    success: function(response) {
                        if (salesStatusChart) salesStatusChart.destroy();

                        var ctx = $("#home-sales-status-chart").get(0).getContext("2d");
                        salesStatusChart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    data: response.data,
                                    backgroundColor: ['#392c70', '#04b76b', '#ff5e6d', '#eeeeee'],
                                    borderColor: ['#392c70', '#04b76b', '#ff5e6d', '#eeeeee']
                                }]
                            },
                            options: {
                                responsive: true,
                                animation: { animateScale: true, animateRotate: true },
                                legend: { display: false },
                                legendCallback: function(chart) {
                                    var text = [];
                                    text.push('<ul class="legend'+ chart.id +'">');
                                    for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                        if (chart.data.datasets[0].data[i] > 0) {
                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                            text.push(chart.data.labels[i]);
                                            text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">'+ chart.data.datasets[0].data[i] + '</label>');
                                            text.push('</li>');
                                        }
                                    }
                                    text.push('</ul>');
                                    return text.join("");
                                }
                            }
                        });
                        document.getElementById('home-sales-status-chart-legend').innerHTML = salesStatusChart.generateLegend();
                    }
                });
            }
            loadSalesStatusChart();
        }

        // 3. Daily Sales Doughnut Chart
        if ($("#home-daily-sales-chart").length) {
            function loadDailySalesChart() {
                $.ajax({
                    url: '/dashboard/daily-sales',
                    type: 'GET',
                    success: function(response) {
                        if (dailySalesChart) dailySalesChart.destroy();

                        var ctx = $("#home-daily-sales-chart").get(0).getContext("2d");
                        dailySalesChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                datasets: [{
                                    data: response.data,
                                    backgroundColor: ['#ff5e6d', '#e9e8ef', '#04b76b'],
                                    borderWidth: 0
                                }],
                                labels: response.labels
                            },
                            options: {
                                responsive: true,
                                cutoutPercentage: 70,
                                legend: { display: false },
                                legendCallback: function(chart) {
                                    var text = [];
                                    text.push('<ul class="legend'+ chart.id +'">');
                                    for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                        if (chart.data.datasets[0].data[i] > 0) {
                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                            text.push(chart.data.labels[i] + ' : ' + chart.data.datasets[0].data[i]);
                                            text.push('</li>');
                                        }
                                    }
                                    text.push('</ul>');
                                    return text.join("");
                                }
                            }
                        });
                        document.getElementById('home-daily-sales-chart-legend').innerHTML = dailySalesChart.generateLegend();
                    }
                });
            }
            loadDailySalesChart();
        }

    });
})(jQuery);