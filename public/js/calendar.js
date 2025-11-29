(function($) {

    $(function() {

        var chageUser = false;

        var base_path = "get-booking-orders";

        $('#user_id').on('change', function() {

            var user_id = $(this).val();

            base_path = "get-user-bookings/" + user_id;

            chageUser = true;

            loadCalendar(base_path);

        });

        $('#service_status').on('change', function() {

            var service_status = $(this).val();

            base_path = "get-service-status-bookings/" + service_status;
            chageUser = true;

            loadCalendar(base_path);

        });


        function loadCalendar(base_path) {



            if ($('#calendar').length) {

                $.ajax({

                    type: "get",

                    url: base_path,

                    success: function(response) {
                        console.log("Raw response:", response); // DEBUG

                        if (!response || typeof response !== 'object') {
                            alert("Invalid response from server");
                            return;
                        }

                        if (!response.events || !Array.isArray(response.events)) {
                            console.warn("No events found or invalid format", response);
                            response.events = [];
                        }

                        var holder = {};

                        response.events.forEach(function(d) {
                            if (holder.hasOwnProperty(d.due_date)) {
                                holder[d.due_date] = holder[d.due_date] + d.total_price;
                            } else {
                                holder[d.due_date] = d.total_price;
                            }
                        });

                        var totals = [];

                        for (var prop in holder) {
                            totals.push({ name: prop, value: holder[prop] });
                        }



                        var eventsArray = [];

                        $.each(response.events, function(index, value) {

                            var colorCode = '#392c70';



                            var book_time = moment(value.due_time, ["h:mm A"]).format("HH:mm");

                            var end_time = moment(value.due_time, ["h:mm A"]).add(value.finish_time, 'minutes').format("HH:mm");

                            if (value.service_color != '') {

                                colorCode = value.service_color;

                            }





                            var jobcomment = 'no comments';

                            if (value.comment != null) {

                                jobcomment = value.comment;

                            }
                            var survey = '';
                            if (value.service_status == 5) {
                                colorCode = '#272625';
                                survey = '<b>Survey</b>: <b>Yes</b>';

                            }
                            eventsArray.push({

                                id: value.id,

                                title: value.customer_name + ' - ' + value.due_time + ' Price: '+ window.currencySymbol + value.total_price + ', Services: ' + value.services + ', Comments: ' + value.comments + ', Address: ' + value.customer_address,

                                description: survey + ' Price: '+ window.currencySymbol + value.total_price + ', Services: ' + value.services + ', Comments: ' + jobcomment + ', Address: ' + value.customer_address + '',

                                start: value.due_date + 'T' + book_time + ':00',

                                end: value.due_date + 'T' + end_time + ':00',

                                color: colorCode,
                                extendedProps: {
                                    servicestatus: value.service_status
                                },

                            });

                        });

                        if (chageUser) {

                            $('#calendar').fullCalendar('removeEvents');

                            $('#calendar').fullCalendar('addEventSource', eventsArray, true);

                        }

                        $('#calendar').fullCalendar({

                            header: {

                                left: 'prev,next today',

                                center: 'title',

                                right: 'month,agendaWeek,agendaDay'

                            },

                            navLinks: true, // can click day/week names to navigate views

                            selectable: true,

                            selectHelper: true,

                            minTime: "06:00:00",
                            maxTime: "21:00:00",
                            select: function(date, start, jsEvent, view) {

                                if ($("#creat-booking-perm").val() == 'yes') {
                                    if (response.holidays != "") {
                                        var startdate = response.holidays[0].start_date;
                                        var enddate = response.holidays[0].end_date;

                                        
                                        if (date.format('YYYY-MM-DD') >= startdate && date.format('YYYY-MM-DD') < enddate) {
                                            //TRUE Clicked date smaller than today + daysToadd
                                            alert("You cannot book on this day!");
                                        } else {
                                            // var event = new Date(start);
                                            // let date = JSON.stringify(event)
                                            // date = date.slice(1, 17)
                                            // date = date.replace(":", "");

                                            // //FLASE Clicked date larger than today + daysToadd
                                            // window.location = "get-booking-date/" + date

                                            var eventDate = moment(date);

                                            if (view.name === "month") {
                                                eventDate = eventDate.set({ hour: 10, minute: 30 }); // Set default time to 10:30 AM
                                            }
                                        
                                            let dateFormatted = eventDate.format('YYYY-MM-DDTHHmm'); // Format properly
    
                                            window.location = "get-booking-date/" + dateFormatted;
                                        }

                                    } else {
                                        // var event = new Date(start);
                                        // let date = JSON.stringify(event)
                                        // date = date.slice(1, 17)
                                        // date = date.replace(":", "");

                                        // //FLASE Clicked date larger than today + daysToadd
                                        // window.location = "get-booking-date/" + date

                                        var eventDate = moment(date);

                                        if (view.name === "month") {
                                            eventDate = eventDate.set({ hour: 10, minute: 30 }); // Set default time to 10:30 AM
                                        }
                                    
                                        let dateFormatted = eventDate.format('YYYY-MM-DDTHHmm'); // Format properly

                                        window.location = "get-booking-date/" + dateFormatted;
                                    }

                                }



                            },

                            eventMouseover: function(calEvent, jsEvent) {

                                var tooltip = '<div class="tooltipevent">' + calEvent.description + '</div>';

                                $("body").append(tooltip);



                                $(this).mouseover(function(e) {

                                    $(this).css('z-index', 10000);

                                    $('.tooltipevent').fadeIn('500');

                                    $('.tooltipevent').fadeTo('10', 1.9);

                                }).mousemove(function(e) {

                                    $('.tooltipevent').css('top', e.pageY + 10);

                                    $('.tooltipevent').css('left', e.pageX + 20);

                                });

                            },

                            eventMouseout: function(calEvent, jsEvent) {

                                $(this).css('z-index', 8);

                                $('.tooltipevent').remove();

                            },

                            eventClick: function(calEvent, jsEvent, view) {
                                if (calEvent.extendedProps.servicestatus == 5) {
                                    return false;
                                }
                                $('#myModal').empty();

                                $.ajax({

                                    url: '/show-job-detail/' + calEvent.id,

                                    type: "get",

                                    success: function(response) {

                                        $('#myModal').html(response);

                                        $('#myModal').modal('toggle');

                                    }

                                });



                            },
                            showNonCurrentDates: false,
                            editable: false,

                            eventLimit: true,

                            views: {

                                day: {

                                    titleFormat: 'dddd, MMMM Do YYYY',

                                    eventLimit: 500,

                                },

                            },

                            events: eventsArray,
                            dayRender: function(date, cell) {

                                if (Object.keys(response.holidays).length != 0) {
                                    Object.keys(response.holidays).forEach(function(key) {
                                        var startdate = response.holidays[key].start_date;
                                        var enddate = response.holidays[key].end_date;
                                        if (date.format('YYYY-MM-DD') >= startdate && date.format('YYYY-MM-DD') < enddate) {
                                            idx = cell.index() + 1;
                                            cell.css("background-color", "lightgray");
                                            $(
                                                ".fc-time-grid .fc-bg table tbody tr td:nth-child(" +
                                                idx + ")").css(
                                                "background-color", "lightgray");
                                        }
                                    });
                                }

                            },
                            eventAfterAllRender: function(view) {
                                if (view.name == 'month' || view.name == 'agendaWeek') {
                                    $('span[id^="turnover"]').remove();
                                    var start = moment(view.start._d).format('YYYY-MM-DD');
                                    var end = moment(view.end._d).format('YYYY-MM-DD');
                                    var sum = 0;
                                    for (var el in holder) {

                                        if (el >= start && el <= end) {
                                            sum += parseFloat(holder[el]);
                                        }
                                    }
                                    // $('.fc-header-toolbar').append('<span id="turnover" style="color:red;">Turnover- '+ window.currencySymbol + sum + '</span>')
                                } else {
                                    $('span[id^="turnover"]').remove();
                                }


                                totals.forEach(function(d) {

                                    $('th.fc-day-header[data-date="' + d.name + '"]').append('<br><span style="color:red;"> '+ window.currencySymbol + d.value + '</span>');
                                });
                            }


                        }).on('click', '.fc-basicDay-button', function(event, element, view) {

                            $("td .fc-day-grid-event .fc-title").css("font-size", "20px");

                            /* Act on the event */

                        });


                    }


                });

            }

        }

        loadCalendar(base_path);

    });

})(jQuery);