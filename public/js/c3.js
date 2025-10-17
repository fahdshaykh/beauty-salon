(function($) {


    'use strict';
    var c3PieChart = c3.generate({


        bindto: '#c3-pie-chart',


        data: {


            // iris data from R


            columns: [


                ['Expense', 30],


                ['Sales', 180],
                ['Revenue', 120],

            ],


            type: 'pie',


            onclick: function(d, i) {


                console.log("onclick", d, i);


            },


            onmouseover: function(d, i) {


                console.log("onmouseover", d, i);


            },


            onmouseout: function(d, i) {


                console.log("onmouseout", d, i);


            }


        },


        color: {


            pattern: ['#6153F9', '#04b76b', '#ff5e6d']


        },


        padding: {


            top: 0,


            right: 0,


            bottom: 30,


            left: 0,


        }


    });


})(jQuery);