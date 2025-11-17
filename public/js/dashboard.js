(function($) {

    'use strict';

    $(function() {

        if ($("#orders-chart").length) {



            var base_path = "get-booking-chart";



            $('#graph-button').on('click', function() {

                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {

                    $.ajax({

                        type: "GET",

                        url: base_path,

                        data: { start_date: start_date, end_date: end_date, _token: "{{ csrf_token() }}" },

                        success: function(response) {



                            var booking_titles = [];



                            var booking_totals = [];



                            var booking_colors = [];



                            console.log(response);



                            if (response !== null) {



                                $.each(response, function(index, value) {

                                    booking_titles.push(value.name + '- $' + value.total_amount);

                                    booking_totals.push(value.total);

                                    booking_colors.push(value.service_color);

                                });

                            }



                            $('#orders-chart').remove();



                            $('#chartBar').append('<canvas id="orders-chart"></canvas>');



                            var currentChartCanvas = $("#orders-chart").get(0).getContext("2d");

                            var currentChart = new Chart(currentChartCanvas, {

                                type: 'bar',

                                data: {

                                    labels: booking_titles,

                                    datasets: [{

                                        label: 'Bookings',

                                        data: booking_totals,

                                        backgroundColor: booking_colors

                                    }]

                                },

                                options: {

                                    responsive: true,

                                    maintainAspectRatio: true,

                                    layout: {

                                        padding: {

                                            left: 0,

                                            right: 0,

                                            top: 20,

                                            bottom: 0

                                        }

                                    },

                                    scales: {

                                        yAxes: [{

                                            gridLines: {

                                                drawBorder: true,

                                            },

                                            ticks: {

                                                stepSize: 5,

                                                fontColor: "#686868"

                                            }

                                        }],

                                        xAxes: [{



                                            stacked: true,

                                            ticks: {

                                                beginAtZero: true,

                                                fontColor: "#686868"

                                            },

                                            gridLines: {

                                                display: false,

                                            },

                                            barPercentage: 0.4

                                        }]

                                    },

                                    legend: {

                                        display: false

                                    },

                                    elements: {

                                        point: {

                                            radius: 0

                                        }

                                    },

                                    legendCallback: function(chart) {

                                        console.log(chart.data.datasets);

                                        var text = [];

                                        text.push('<ul class="legend' + chart.id + '">');

                                        for (var i = 0; i < chart.data.datasets.length; i++) {

                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[i].backgroundColor + '"></span>');

                                            if (chart.data.datasets[i].label) {

                                                text.push(chart.data.datasets[i].label);

                                            }

                                            text.push('</li>');

                                        }

                                        text.push('</ul>');

                                        return text.join("");

                                    },

                                }

                            });

                            document.getElementById('orders-chart-legend').innerHTML = currentChart.generateLegend();

                        }

                    });

                }

            });



            // when page reload below code run



            $.ajax({

                type: "get",

                url: base_path,

                success: function(response) {



                    var booking_titles = [];



                    var booking_totals = [];



                    var booking_colors = [];



                    if (response !== null) {



                        $.each(response, function(index, value) {

                            booking_titles.push(value.name + '- $' + value.total_amount);

                            booking_totals.push(value.total);

                            booking_colors.push(value.service_color);

                        });

                    }





                    var currentChartCanvas = $("#orders-chart").get(0).getContext("2d");

                    var currentChart = new Chart(currentChartCanvas, {

                        type: 'bar',

                        data: {

                            labels: booking_titles,

                            datasets: [{

                                label: 'Bookings',

                                data: booking_totals,

                                backgroundColor: booking_colors

                            }]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: true,

                            layout: {

                                padding: {

                                    left: 0,

                                    right: 0,

                                    top: 20,

                                    bottom: 0

                                }

                            },

                            scales: {

                                yAxes: [{

                                    gridLines: {

                                        drawBorder: true,

                                    },

                                    ticks: {

                                        stepSize: 5,

                                        fontColor: "#686868"

                                    }

                                }],

                                xAxes: [{



                                    stacked: true,

                                    ticks: {

                                        beginAtZero: true,

                                        fontColor: "#686868"

                                    },

                                    gridLines: {

                                        display: false,

                                    },

                                    barPercentage: 0.4

                                }]

                            },

                            legend: {

                                display: false

                            },

                            elements: {

                                point: {

                                    radius: 0

                                }

                            },

                            legendCallback: function(chart) {

                                console.log(chart.data.datasets);

                                var text = [];

                                text.push('<ul class="legend' + chart.id + '">');

                                for (var i = 0; i < chart.data.datasets.length; i++) {

                                    text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[i].backgroundColor + '"></span>');

                                    if (chart.data.datasets[i].label) {

                                        text.push(chart.data.datasets[i].label);

                                    }

                                    text.push('</li>');

                                }

                                text.push('</ul>');

                                return text.join("");

                            },

                        }

                    });

                    document.getElementById('orders-chart-legend').innerHTML = currentChart.generateLegend();

                }

            });

        }

        if ($("#sales-status-chart").length) {
            var base_path = "customer-graph-report";
            $('#graph-button').on('click', function() {

                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {

                    $.ajax({

                        type: "GET",

                        url: base_path,

                        data: { start_date: start_date, end_date: end_date, _token: "{{ csrf_token() }}" },

                        success: function(response) {
                            var titles = [];
                            var totals = [];
                            if (response !== null) {

                                $.each(response, function(index, value) {
                                    titles.push(value.referred_by);
                                    totals.push(value.total);
                                });
                            }

                            $('#sales-status-chart').remove();

                            $('#chartBar').append('<canvas id="sales-status-chart"></canvas>');
                            var pieChartCanvas = $("#sales-status-chart").get(0).getContext("2d");
                            var pieChart = new Chart(pieChartCanvas, {
                                type: 'pie',
                                data: {
                                    datasets: [{
                                        data: totals,
                                        backgroundColor: [
                                            '#392c70',
                                            '#04b76b',
                                            '#ff5e6d',
                                            '#eeeeee',
                                            '#FFE333'
                                        ],
                                        borderColor: [
                                            '#392c70',
                                            '#04b76b',
                                            '#ff5e6d',
                                            '#eeeeee',
                                            '#FFE333'
                                        ],
                                    }],

                                    // These labels appear in the legend and in the tooltips when hovering different arcs
                                    labels: titles
                                },
                                options: {
                                    responsive: true,
                                    animation: {
                                        animateScale: true,
                                        animateRotate: true
                                    },
                                    legend: {
                                        display: false
                                    },
                                    legendCallback: function(chart) {
                                        var text = [];
                                        text.push('<ul class="legend' + chart.id + '">');
                                        for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                            if (chart.data.labels[i]) {
                                                text.push(chart.data.labels[i]);
                                            }
                                            text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">' + chart.data.datasets[0].data[i] + '</label>');
                                            text.push('</li>');
                                        }
                                        text.push('</ul>');
                                        return text.join("");
                                    }
                                }
                            });
                            document.getElementById('sales-status-chart-legend').innerHTML = pieChart.generateLegend();



                        }

                    });

                }

            });
            $.ajax({
                type: "get",
                url: base_path,
                success: function(response) {
                    var titles = [];
                    var totals = [];
                    if (response !== null) {

                        $.each(response, function(index, value) {
                            titles.push(value.referred_by);
                            totals.push(value.total);
                        });
                    }
                    var pieChartCanvas = $("#sales-status-chart").get(0).getContext("2d");
                    var pieChart = new Chart(pieChartCanvas, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: totals,
                                backgroundColor: [
                                    '#392c70',
                                    '#04b76b',
                                    '#ff5e6d',
                                    '#eeeeee',
                                    '#FFE333'
                                ],
                                borderColor: [
                                    '#392c70',
                                    '#04b76b',
                                    '#ff5e6d',
                                    '#eeeeee',
                                    '#FFE333'
                                ],
                            }],

                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: titles
                        },
                        options: {
                            responsive: true,
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            },
                            legend: {
                                display: false
                            },
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="legend' + chart.id + '">');
                                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                    text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                    if (chart.data.labels[i]) {
                                        text.push(chart.data.labels[i]);
                                    }
                                    text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">' + chart.data.datasets[0].data[i] + '</label>');
                                    text.push('</li>');
                                }
                                text.push('</ul>');
                                return text.join("");
                            }
                        }
                    });
                    document.getElementById('sales-status-chart-legend').innerHTML = pieChart.generateLegend();

                }
            });

        }




        //customer country graph report
        if ($("#country-status-chart").length) {
            var base_path = "customer-country-graph-report";
            var countryChart = null;
        
            function fetchChartData(start_date = '', end_date = '') {
                $.ajax({
                    type: "GET",
                    url: base_path,
                    data: { start_date: start_date, end_date: end_date },
                    success: function(response) {
                        if (!response.countries || response.countries.length === 0) {
                            alert("No data available for the selected date range.");
                            return;
                        }
        
                        var titles = [];
                        var totals = [];
        
                        response.countries.forEach(function(item) {
                            titles.push(item.country);
                            totals.push(item.total);
                        });
        
                        if (countryChart) {
                            countryChart.destroy(); // Destroy the existing chart instance before redrawing
                        }
        
                        var pieChartCanvas = $("#country-status-chart").get(0).getContext("2d");
                        countryChart = new Chart(pieChartCanvas, {
                            type: 'pie',
                            data: {
                                labels: titles,
                                datasets: [{
                                    data: totals,
                                    backgroundColor: ['#392c70', '#04b76b', '#ff5e6d', '#eeeeee', '#FFE333'],
                                    borderColor: ['#392c70', '#04b76b', '#ff5e6d', '#eeeeee', '#FFE333']
                                }]
                            },
                            options: {
                                responsive: true,
                                animation: {
                                    animateScale: true,
                                    animateRotate: true
                                },
                                legend: {
                                    display: false
                                },
                                legendCallback: function(chart) {
                                    var text = [];
                                    text.push('<ul class="legend' + chart.id + '">');
                                    for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                        text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                        text.push(chart.data.labels[i]);
                                        text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">' + chart.data.datasets[0].data[i] + '</label>');
                                        text.push('</li>');
                                    }
                                    text.push('</ul>');
                                    return text.join("");
                                }
                            }
                        });
        
                        document.getElementById('sales-status-chart-legend').innerHTML = countryChart.generateLegend();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                        alert("Error fetching data. Please try again.");
                    }
                });
            }
        
            // Initial Chart Load (Default Dates)
            fetchChartData();
        
            // Fetch Data on Button Click
            $('#graph-button').on('click', function() {
                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {
                    fetchChartData(start_date, end_date);
                } else {
                    alert("Please select both start and end dates.");
                }
            });
        }
        


        //Profit & Loss Report
        if ($("#profit-loss-chart").length) {
            var base_path = "profit-loss-data";
            $('#graph-button').on('click', function() {

                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {

                    $.ajax({

                        type: "GET",

                        url: base_path,

                        data: { start_date: start_date, end_date: end_date, _token: "{{ csrf_token() }}" },

                        success: function(response) {
                            console.log(response);
                            
                            var titles = ['Expenses', 'Sales', 'Profit'];
                            var totals = [];
                            if (response !== null) {
                                totals.push(response.expense);
                                totals.push(response.sales);
                                totals.push(response.revenue);
                            }

                            $('#profit-loss-chart').remove();

                            $('#chartBar').append('<canvas id="profit-loss-chart"></canvas>');
                            var pieChartCanvas = $("#profit-loss-chart").get(0).getContext("2d");
                            var pieChart = new Chart(pieChartCanvas, {
                                type: 'pie',
                                data: {
                                    datasets: [{
                                        data: totals,
                                        backgroundColor: [
                                            '#392c70',
                                            '#04b76b',
                                            '#ff5e6d',
                                        ],
                                        borderColor: [
                                            '#392c70',
                                            '#04b76b',
                                            '#ff5e6d',
                                        ],
                                    }],

                                    // These labels appear in the legend and in the tooltips when hovering different arcs
                                    labels: titles
                                },
                                options: {
                                    responsive: true,
                                    animation: {
                                        animateScale: true,
                                        animateRotate: true
                                    },
                                    legend: {
                                        display: false
                                    },
                                    legendCallback: function(chart) {
                                        var text = [];
                                        text.push('<ul class="legend' + chart.id + '">');
                                        for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                                            if (chart.data.labels[i]) {
                                                text.push(chart.data.labels[i]);
                                            }
                                            text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">$' + chart.data.datasets[0].data[i] + '</label>');
                                            text.push('</li>');
                                        }
                                        text.push('</ul>');
                                        return text.join("");
                                    }
                                }
                            });
                            document.getElementById('profit-loss-chart-legend').innerHTML = pieChart.generateLegend();



                        }

                    });

                }

            });
            // $.ajax({
            //     type: "get",
            //     url: base_path,
            //     success: function(response) {
            //         var titles = ['Expenses', 'Sales', 'Profit'];
            //         var totals = [];
            //         if (response !== null) {
            //             totals.push(response.expense);
            //             totals.push(response.sales);
            //             totals.push(response.revenue);
            //         }
            //         var pieChartCanvas = $("#profit-loss-chart").get(0).getContext("2d");
            //         var pieChart = new Chart(pieChartCanvas, {
            //             type: 'pie',
            //             data: {
            //                 datasets: [{
            //                     data: totals,
            //                     backgroundColor: [
            //                         '#392c70',
            //                         '#04b76b',
            //                         '#ff5e6d',
            //                     ],
            //                     borderColor: [
            //                         '#392c70',
            //                         '#04b76b',
            //                         '#ff5e6d',
            //                     ],
            //                 }],

            //                 // These labels appear in the legend and in the tooltips when hovering different arcs
            //                 labels: titles
            //             },
            //             options: {
            //                 responsive: true,
            //                 animation: {
            //                     animateScale: true,
            //                     animateRotate: true
            //                 },
            //                 legend: {
            //                     display: false
            //                 },
            //                 legendCallback: function(chart) {
            //                     var text = [];
            //                     text.push('<ul class="legend' + chart.id + '">');
            //                     for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
            //                         text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
            //                         if (chart.data.labels[i]) {
            //                             text.push(chart.data.labels[i]);
            //                         }
            //                         text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">$' + chart.data.datasets[0].data[i] + '</label>');
            //                         text.push('</li>');
            //                     }
            //                     text.push('</ul>');
            //                     return text.join("");
            //                 }
            //             }
            //         });
            //         document.getElementById('profit-loss-chart-legend').innerHTML = pieChart.generateLegend();

            //     }
            // });

        }


        //zip code graph report
        if ($("#zipcode-chart").length) {
            var base_path = "zipcode-data";
            $('#graph-button').on('click', function() {

                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {

                    $.ajax({

                        type: "GET",

                        url: base_path,

                        data: { start_date: start_date, end_date: end_date, _token: "{{ csrf_token() }}" },

                        success: function(response) {
                            var titles = [];
                            var totals = [];
                            var backgroundcolor = [];
                            var forgroundgroundcolor = [];
                            if (response !== null) {

                                $.each(response, function(index, value) {
                                    titles.push(value.zip);
                                    totals.push(value.total);
                                    backgroundcolor.push(getRandomColor());
                                    forgroundgroundcolor.push(getRandomColor());
                                });
                            }
                            $('#zipcode-chart').remove();

                            $('#chartBar').append('<canvas id="zipcode-chart"></canvas>');
                            var barChartCanvas = $("#zipcode-chart").get(0).getContext("2d");
                            var barChart = new Chart(barChartCanvas, {
                                type: 'bar',
                                data: {
                                    labels: titles,
                                    datasets: [{
                                        label: '# of Customer',
                                        data: totals,
                                        backgroundColor: backgroundcolor,
                                        borderColor: forgroundgroundcolor,
                                        borderWidth: 1,
                                        fill: false
                                    }]
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    },
                                    legend: {
                                        display: false
                                    },
                                    elements: {
                                        point: {
                                            radius: 0
                                        }
                                    }

                                }
                            });



                        }

                    });

                }

            });
            $.ajax({
                type: "get",
                url: base_path,
                success: function(response) {
                    var titles = [];
                    var totals = [];
                    var backgroundcolor = [];
                    var forgroundgroundcolor = [];
                    if (response !== null) {

                        $.each(response, function(index, value) {
                            titles.push(value.zip);
                            totals.push(value.total);
                            backgroundcolor.push(getRandomColor());
                            forgroundgroundcolor.push(getRandomColor());
                        });
                    }
                    var barChartCanvas = $("#zipcode-chart").get(0).getContext("2d");
                    var barChart = new Chart(barChartCanvas, {
                        type: 'bar',
                        data: {
                            labels: titles,
                            datasets: [{
                                label: '# of Customer',
                                data: totals,
                                backgroundColor: backgroundcolor,
                                borderColor: forgroundgroundcolor,
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            elements: {
                                point: {
                                    radius: 0
                                }
                            }

                        }
                    });

                }
            });

        }


        // employee revenue chart
        if ($("#employee-orders-chart").length) {



            var base_path = "employee-booking-chart";



            $('#employee-graph-button').on('click', function() {

                var start_date = $("#start-date").val();
                var end_date = $("#end-date").val();
                if (start_date && end_date) {

                    $.ajax({

                        type: "GET",

                        url: base_path,

                        data: { start_date: start_date, end_date: end_date, _token: "{{ csrf_token() }}" },

                        success: function(response) {



                            var revenue_titles = [];



                            var services_totals = [];



                            var booking_colors = [];



                            console.log(response);



                            if (response !== null) {



                                $.each(response, function(index, value) {

                                    revenue_titles.push(value.employee_name + '- $' + value.total_revenue);

                                    services_totals.push(value.total_services);

                                    booking_colors.push('#3e4187');

                                });

                            }



                            $('#employee-orders-chart').remove();



                            $('#chartBar').append('<canvas id="employee-orders-chart"></canvas>');



                            var currentChartCanvas = $("#employee-orders-chart").get(0).getContext("2d");

                            var currentChart = new Chart(currentChartCanvas, {

                                type: 'bar',

                                data: {

                                    labels: revenue_titles,

                                    datasets: [{

                                        label: 'Total Bookings',

                                        data: services_totals,

                                        backgroundColor: booking_colors

                                    }]

                                },

                                options: {

                                    responsive: true,

                                    maintainAspectRatio: true,

                                    layout: {

                                        padding: {

                                            left: 0,

                                            right: 0,

                                            top: 20,

                                            bottom: 0

                                        }

                                    },

                                    scales: {

                                        yAxes: [{

                                            gridLines: {

                                                drawBorder: true,

                                            },

                                            ticks: {

                                                stepSize: 5,

                                                fontColor: "#686868"

                                            }

                                        }],

                                        xAxes: [{



                                            stacked: true,

                                            ticks: {

                                                beginAtZero: true,

                                                fontColor: "#686868"

                                            },

                                            gridLines: {

                                                display: false,

                                            },

                                            barPercentage: 0.4

                                        }]

                                    },

                                    legend: {

                                        display: false

                                    },

                                    elements: {

                                        point: {

                                            radius: 0

                                        }

                                    },

                                    legendCallback: function(chart) {

                                        console.log(chart.data.datasets);

                                        var text = [];

                                        text.push('<ul class="legend' + chart.id + '">');

                                        for (var i = 0; i < chart.data.datasets.length; i++) {

                                            text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[i].backgroundColor + '"></span>');

                                            if (chart.data.datasets[i].label) {

                                                text.push(chart.data.datasets[i].label);

                                            }

                                            text.push('</li>');

                                        }

                                        text.push('</ul>');

                                        return text.join("");

                                    },

                                }

                            });

                            document.getElementById('employee-orders-chart-legend').innerHTML = currentChart.generateLegend();

                        }

                    });

                }

            });



            // when page reload below code run



            // $.ajax({

            //     type: "get",

            //     url: base_path,

            //     success: function(response) {



            //         var revenue_titles = [];



            //         var services_totals = [];



            //         var booking_colors = [];



            //         if (response !== null) {



            //             $.each(response, function(index, value) {

            //                 revenue_titles.push(value.employee_name + '- $' + value.total_revenue);

            //                 services_totals.push(value.total_services);

            //                 booking_colors.push('#3e4187');

            //             });

            //         }





            //         var currentChartCanvas = $("#employee-orders-chart").get(0).getContext("2d");

            //         var currentChart = new Chart(currentChartCanvas, {

            //             type: 'bar',

            //             data: {

            //                 labels: revenue_titles,

            //                 datasets: [{

            //                     label: 'Bookings',

            //                     data: services_totals,

            //                     backgroundColor: booking_colors

            //                 }]

            //             },

            //             options: {

            //                 responsive: true,

            //                 maintainAspectRatio: true,

            //                 layout: {

            //                     padding: {

            //                         left: 0,

            //                         right: 0,

            //                         top: 20,

            //                         bottom: 0

            //                     }

            //                 },

            //                 scales: {

            //                     yAxes: [{

            //                         gridLines: {

            //                             drawBorder: true,

            //                         },

            //                         ticks: {

            //                             stepSize: 5,

            //                             fontColor: "#686868"

            //                         }

            //                     }],

            //                     xAxes: [{



            //                         stacked: true,

            //                         ticks: {

            //                             beginAtZero: true,

            //                             fontColor: "#686868"

            //                         },

            //                         gridLines: {

            //                             display: false,

            //                         },

            //                         barPercentage: 0.4

            //                     }]

            //                 },

            //                 legend: {

            //                     display: false

            //                 },

            //                 elements: {

            //                     point: {

            //                         radius: 0

            //                     }

            //                 },

            //                 legendCallback: function(chart) {

            //                     console.log(chart.data.datasets);

            //                     var text = [];

            //                     text.push('<ul class="legend' + chart.id + '">');

            //                     for (var i = 0; i < chart.data.datasets.length; i++) {

            //                         text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[i].backgroundColor + '"></span>');

            //                         if (chart.data.datasets[i].label) {

            //                             text.push(chart.data.datasets[i].label);

            //                         }

            //                         text.push('</li>');

            //                     }

            //                     text.push('</ul>');

            //                     return text.join("");

            //                 },

            //             }

            //         });

            //         document.getElementById('employee-orders-chart-legend').innerHTML = currentChart.generateLegend();

            //     }

            // });

        }

    });

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
})(jQuery);