(function($) {
  'use strict';
  $(function() {

    if ($('#calendar').length) {
      
      var base_path = "get-booking-orders"; 
      $.ajax({
        type: "get",
        url: base_path,
        success: function(response) {
          var eventsArray = [];
          $.each(response, function( index, value ) {
            eventsArray.push({
              id:index,
              title:value.customer_name+' - '+value.due_time+' GBP:'+ value.total_price +' ('+value.services_names+')',
              start:value.due_date
            })
          });

      $('#calendar').fullCalendar('today').find('.fc-title').css("font-size","20px");

      let today = new Date().toISOString().slice(0, 10)
      $('#calendar').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,basicWeek,basicDay'
        },
        defaultDate: today,
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectHelper: true,
        select: function(start, jsEvent, view){
          //wrtie your redirection code here
          // var root_url="http://localhost/";
          if(start.isBefore(moment())) {
            $('#calendar').fullCalendar('unselect');
            return false;
          }

          var event = new Date(start);

          let date = JSON.stringify(event)
          date = date.slice(1,11)

          window.location = "get-booking-date/"+date
        },

        

        eventMouseover: function(calEvent, jsEvent) {
          var tooltip = '<div class="tooltipevent" style="width:250px;height:50px;background:#FFFFFF;position:absolute;z-index:10001;">' + calEvent.title + '</div>';
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

      eventClick: function (calEvent, jsEvent, view) {
        $('#calendarModal').modal();
      },

      // eventRender: function( event, element, view ) { //for event title increase
      //   element.find('.fc-title').css("font-size","20px");
      // },
        editable: false,
        eventLimit: true,

          views: {
            day: {
              eventLimit: 500,
            },
          },
        events: eventsArray,
      }).on('click', '.fc-basicDay-button', function(event, element, view) {
        $("td .fc-day-grid-event .fc-title").css("font-size","20px");
        /* Act on the event */
        });
    }
    });
    }
  });

  // $(document).on('click', '.fc-day-number', function () {
  //   $("td .fc-day-grid-event .fc-title").css("font-size","20px");
  // });

})(jQuery);