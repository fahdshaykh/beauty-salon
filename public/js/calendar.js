(function($) {
    $(function() {
        var $calendar = $('#calendar');

        // Detect which filters are present
        var $userSelect = $('#user_id');              // Only for admin
        var $statusSelectAdmin = $('#service_status_admin'); // Admin status
        var $statusSelectUser = $('#service_status');         // Regular user status

        // Determine which status select to use
        var $statusSelect = $statusSelectAdmin.length ? $statusSelectAdmin : $statusSelectUser;

        function buildUrl() {
            var params = [];

            // Add user_id only if the dropdown exists and value != 0 (admin only)
            if ($userSelect.length) {
                var user_id = $userSelect.val();
                if (user_id && user_id != '0') {
                    params.push('user_id=' + encodeURIComponent(user_id));
                }
            }

            // Add service_status if any status dropdown exists and value != 0
            if ($statusSelect.length) {
                var status = $statusSelect.val();
                if (status && status != '0') {
                    params.push('service_status=' + encodeURIComponent(status));
                }
            }

            var baseUrl = '/get-booking-orders';
            return params.length > 0 ? baseUrl + '?' + params.join('&') : baseUrl;
        }

        function loadCalendar() {
            if (!$calendar.length) return;

            var url = buildUrl();
            console.log("Loading calendar from:", url); // Debug

            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    if (!response || typeof response !== 'object') {
                        alert("Invalid response from server");
                        return;
                    }

                    var events = response.events || [];
                    var holidays = response.holidays || [];

                    // Calculate daily turnover
                    var holder = {};
                    events.forEach(function(d) {
                        var price = parseFloat(d.total_price) || 0;
                        if (holder[d.due_date]) {
                            holder[d.due_date] += price;
                        } else {
                            holder[d.due_date] = price;
                        }
                    });

                    var eventsArray = [];
                    $.each(events, function(index, value) {
                        var colorCode = value.service_color || '#392c70';

                        var book_time = moment(value.due_time, ["h:mm A"]).format("HH:mm");
                        var end_time = moment(value.due_time, ["h:mm A"])
                            .add(value.service_time, 'minutes')
                            .format("HH:mm");

                        var jobcomment = value.comment || 'no comments';
                        var survey = value.service_status == 5 ? '<b>Survey</b>: <b>Yes</b><br>' : '';
                        if (value.service_status == 5) colorCode = '#272625';

                        eventsArray.push({
                            id: value.id,
                            title: value.customer_name + ' - ' + value.due_time +
                                   ' Price: ' + window.currencySymbol + value.total_price +
                                   ', Services: ' + value.services +
                                   ', Comments: ' + jobcomment +
                                   ', Address: ' + value.customer_address,
                            description: survey +
                                         'Price: ' + window.currencySymbol + value.total_price +
                                         ', Services: ' + value.services +
                                         ', Comments: ' + jobcomment +
                                         ', Address: ' + value.customer_address,
                            start: value.due_date + 'T' + book_time + ':00',
                            end: value.due_date + 'T' + end_time + ':00',
                            color: colorCode,
                            extendedProps: {
                                servicestatus: value.service_status
                            }
                        });
                    });

                    // Destroy previous calendar to avoid conflicts
                    if ($calendar.hasClass('fc')) {
                        $calendar.fullCalendar('destroy');
                    }

                    // Initialize FullCalendar
                    $calendar.fullCalendar({
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay'
                        },
                        navLinks: true,
                        selectable: true,
                        selectHelper: true,
                        minTime: "06:00:00",
                        maxTime: "21:00:00",
                        select: function(start, end, jsEvent, view) {
                            if ($("#creat-booking-perm").val() !== 'yes') return;

                            var dateStr = start.format('YYYY-MM-DD');
                            var hasHoliday = holidays.some(function(h) {
                                return dateStr >= h.start_date && dateStr < h.end_date;
                            });

                            if (hasHoliday) {
                                alert("You cannot book on this day!");
                                return;
                            }

                            var eventDate = moment(start);
                            if (view.name === "month") {
                                eventDate.set({ hour: 10, minute: 30 });
                            }
                            var dateFormatted = eventDate.format('YYYY-MM-DDTHHmm');
                            window.location = "get-booking-date/" + dateFormatted;
                        },
                        eventMouseover: function(calEvent, jsEvent) {
                            var tooltip = '<div class="tooltipevent" style="position:absolute;z-index:10000;">' + calEvent.description + '</div>';
                            $("body").append(tooltip);
                            $('.tooltipevent')
                                .css('top', jsEvent.pageY + 10)
                                .css('left', jsEvent.pageX + 20)
                                .fadeIn('fast');
                        },
                        eventMouseout: function() {
                            $('.tooltipevent').remove();
                        },
                        eventClick: function(calEvent) {
                            if (calEvent.extendedProps.servicestatus == 5) return false;

                            $('#myModal').empty();
                            $.ajax({
                                url: '/show-job-detail/' + calEvent.id,
                                type: "GET",
                                success: function(resp) {
                                    $('#myModal').html(resp).modal('show');
                                }
                            });
                        },
                        editable: false,
                        eventLimit: true,
                        events: eventsArray,
                        dayRender: function(date, cell) {
                            var dateStr = date.format('YYYY-MM-DD');
                            holidays.forEach(function(h) {
                                if (dateStr >= h.start_date && dateStr < h.end_date) {
                                    cell.css("background-color", "lightgray");
                                }
                            });
                        },
                        eventAfterAllRender: function(view) {
                            // Add daily turnover below day headers
                            $('span.turnover-day').remove();
                            for (var date in holder) {
                                var $header = $('th.fc-day-header[data-date="' + date + '"]');
                                if ($header.length) {
                                    $header.append(
                                        '<br><span class="turnover-day" style="color:red;font-weight:bold;">' +
                                        window.currencySymbol + holder[date].toFixed(2) + '</span>'
                                    );
                                }
                            }
                        }
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert("Error loading calendar data");
                }
            });
        }

        // Reload calendar when ANY filter changes
        $(document).on('change', '#user_id, #service_status_admin, #service_status', function() {
            loadCalendar();
        });

        // Initial load
        loadCalendar();
    });
})(jQuery);