(function($) {



    'use strict';



    if ($("#timepicker-example").length) {



        $('#timepicker-example').datetimepicker({

            hour: 8,

            minute: 30,

            disabledTimeIntervals: [

                [moment().hour(0), moment().hour(6).minutes(29)],

                [moment().hour(18).minutes(30), moment().hour(24)]

            ],

            format: 'hh:mm A'



        });



    }



    if ($("#timepicker-schedule").length) {

        $('#timepicker-schedule').datetimepicker({

            format: 'hh:mm A',

            minDate: moment().add(1, 'h'),

            disabledTimeIntervals: [

                [moment().hour(0), moment().hour(6).minutes(29)],

                [moment().hour(23).minutes(59), moment().hour(24)]

            ]

        });



    }



    if ($(".color-picker").length) {



        $('.color-picker').asColorPicker();



    }



    if ($("#datepicker-popup").length) {



        $('#datepicker-popup').datepicker({



            enableOnReadonly: true,



            todayHighlight: true,



        });



    }







    //fahad changes
    if ($("#financial-year").length) {



        $('#financial-year').datepicker({
            format: 'yyyy-mm-dd',
            enableOnReadonly: true,
            todayHighlight: true,
            autoclose: true
        });


    }


    if ($("#datepicker-popup-booking").length) {



        $('#datepicker-popup-booking').datepicker({



            startDate: new Date(),



            format: 'yyyy-mm-dd',



            enableOnReadonly: true,



            todayHighlight: true,

            minDate: 0,

            autoclose: true



        });



    }







    if ($("#datepicker-popup-new").length) {



        $('#datepicker-popup-new').datepicker({



            format: 'yyyy-mm-dd',



            enableOnReadonly: true,



            todayHighlight: true,



            autoclose: true



        });



    }







    if ($("#datepicker-popup-new2").length) {



        $('#datepicker-popup-new2').datepicker({



            format: 'yyyy-mm-dd',



            enableOnReadonly: true,



            todayHighlight: true,



            autoclose: true



        });



    }



    //here







    if ($("#inline-datepicker").length) {



        $('#inline-datepicker').datepicker({



            enableOnReadonly: true,



            todayHighlight: true,



        });



    }



    if ($(".datepicker-autoclose").length) {



        $('.datepicker-autoclose').datepicker({



            autoclose: true



        });



    }



    if ($('input[name="date-range"]').length) {



        $('input[name="date-range"]').daterangepicker();



    }



    if ($('input[name="date-time-range"]').length) {



        $('input[name="date-time-range"]').daterangepicker({



            timePicker: true,



            timePickerIncrement: 30,



            locale: {



                format: 'MM/DD/YYYY hh:mm A'



            }



        });



    }



})(jQuery);