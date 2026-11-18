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

            // Add start_date and end_date if present
            var startDate = $('#calendar_start_date').val();
            var endDate = $('#calendar_end_date').val();
            if (startDate) {
                params.push('start_date=' + encodeURIComponent(startDate));
            }
            if (endDate) {
                params.push('end_date=' + encodeURIComponent(endDate));
            }

            var baseUrl = '/get-booking-orders';
            return params.length > 0 ? baseUrl + '?' + params.join('&') : baseUrl;
        }

        function loadCalendar(targetDate) {
            if (!$calendar.length) return;

            var rememberedDate = targetDate;
            if (!rememberedDate && $calendar.hasClass('fc')) {
                try {
                    rememberedDate = $calendar.fullCalendar('getDate');
                } catch(e) {}
            }

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

                        var startMoment = moment(value.due_date + ' ' + value.due_time, [
                            "YYYY-MM-DD h:mm A",
                            "YYYY-MM-DD hh:mm A",
                            "YYYY-MM-DD HH:mm:ss",
                            "YYYY-MM-DD HH:mm",
                            "YYYY-MM-DD H:mm:ss",
                            "YYYY-MM-DD H:mm"
                        ]);

                        var startTimeStr = startMoment.isValid()
                            ? startMoment.format("HH:mm")
                            : moment(value.due_time, ["h:mm A", "hh:mm A", "HH:mm:ss", "HH:mm"]).format("HH:mm");

                        var serviceDuration = parseInt(value.service_time, 10) || 30;
                        var endMoment = startMoment.isValid()
                            ? moment(startMoment).add(serviceDuration, 'minutes')
                            : moment(startTimeStr, "HH:mm").add(serviceDuration, 'minutes');

                        var startIso = startMoment.isValid()
                            ? startMoment.format("YYYY-MM-DDTHH:mm:00")
                            : (value.due_date + 'T' + startTimeStr + ':00');

                        var endIso = endMoment.isValid()
                            ? endMoment.format("YYYY-MM-DDTHH:mm:00")
                            : (value.due_date + 'T' + endMoment.format("HH:mm") + ':00');

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
                            start: startIso,
                            end: endIso,
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

                    // Calendar configuration
                    var calConfig = {
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay'
                        },
                        navLinks: true,
                        selectable: true,
                        selectHelper: true,
                        minTime: "00:00:00",
                        maxTime: "24:00:00",
                        scrollTime: "08:00:00",
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
                        windowResize: function(view) {
                            $calendar.fullCalendar('render');
                        },
                        eventMouseover: function(calEvent, jsEvent) {
                            if ($(window).width() < 768) return;
                            $('.tooltipevent').remove();
                            var tooltip = '<div class="tooltipevent" style="position:absolute;z-index:10000;">' + calEvent.description + '</div>';
                            $("body").append(tooltip);
                            var posX = jsEvent.pageX + 15;
                            var maxLeft = $(window).width() - 290;
                            if (posX > maxLeft) {
                                posX = Math.max(10, maxLeft);
                            }
                            $('.tooltipevent')
                                .css('top', jsEvent.pageY + 10)
                                .css('left', posX)
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
                    };

                    if (rememberedDate) {
                        calConfig.defaultDate = moment(rememberedDate);
                    }

                    // Initialize FullCalendar
                    $calendar.fullCalendar(calConfig);
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert("Error loading calendar data");
                }
            });
        }

        function toggleClearBtn() {
            var s = $('#calendar_start_date').val();
            var e = $('#calendar_end_date').val();
            if (s || e) {
                $('#clear_calendar_dates').show();
            } else {
                $('#clear_calendar_dates').hide();
            }
        }

        var reloadTimer = null;
        function debouncedLoadCalendar(targetDate) {
            clearTimeout(reloadTimer);
            reloadTimer = setTimeout(function() {
                loadCalendar(targetDate);
            }, 100);
        }

        // Reload calendar when staff or status filter changes
        $(document).on('change', '#user_id, #service_status_admin, #service_status', function() {
            loadCalendar();
        });

        // Datepicker change handlers
        $('#datepicker-popup-new').on('changeDate', function(e) {
            var startVal = $('#calendar_start_date').val();
            if (startVal && $('#datepicker-popup-new2').length) {
                $('#datepicker-popup-new2').datepicker('setStartDate', startVal);
            }
            toggleClearBtn();
            debouncedLoadCalendar(startVal);
        });

        $('#datepicker-popup-new2').on('changeDate', function(e) {
            var endVal = $('#calendar_end_date').val();
            if (endVal && $('#datepicker-popup-new').length) {
                $('#datepicker-popup-new').datepicker('setEndDate', endVal);
            }
            toggleClearBtn();
            debouncedLoadCalendar();
        });

        // Manual text change / clear on date inputs
        $(document).on('change', '#calendar_start_date, #calendar_end_date', function() {
            var startVal = $('#calendar_start_date').val();
            toggleClearBtn();
            debouncedLoadCalendar(startVal);
        });

        // Clear dates button
        $(document).on('click', '#clear_calendar_dates', function() {
            $('#calendar_start_date').val('');
            $('#calendar_end_date').val('');
            if ($('#datepicker-popup-new').length) {
                $('#datepicker-popup-new').datepicker('clearDates');
                $('#datepicker-popup-new').datepicker('setEndDate', null);
            }
            if ($('#datepicker-popup-new2').length) {
                $('#datepicker-popup-new2').datepicker('clearDates');
                $('#datepicker-popup-new2').datepicker('setStartDate', null);
            }
            toggleClearBtn();
            loadCalendar(moment().format('YYYY-MM-DD'));
        });

        // Initial load
        toggleClearBtn();
        loadCalendar();
    });
})(jQuery);
