"use strict";

/* ==========================================================================
   MONTH VIEW
   ========================================================================== */

$(document).on('click', '.calday', function () {
    helper.switchDiaryView('day', $(this).attr('name'));
    return false;
});

$(document).on('change', '#select-month', function () {
    calendar.month = parseInt($(this).val());
    return false;
});

$(document).on('change', '#select-year', function () {
    calendar.year = parseInt($(this).val());
    return false;
});

$(document).on('click', '#change-month-btn', function () {
    calendar.updateCalendar();
});

$(document).on('click', '#calendar-controls .prev', function () {
    if (calendar.month === 0) {
        calendar.year--;
        calendar.month = 11;
    } else {
        calendar.month--;
    }
    calendar.updateCalendar();
});

$(document).on('click', '#calendar-controls .next', function () {
    if (calendar.month === 11) {
        calendar.year++;
        calendar.month = 0;
    } else {
        calendar.month++;
    }
    calendar.updateCalendar();
    return false;
});




$(document).delegate("#uuid-4-dialog", "pagehide", function() {
    console.log("closed dialog");
    calendar.updateCalendar();
});

var calendar = {

    months: ['January', 'February', 'March', 'April', 'May', 'June', 'July',
    'August', 'September', 'October', 'November', 'December'],

    init: function (appointments, today) {
        var date       = new Date();
        calendar.apps  = appointments;
        calendar.today = today;
        calendar.month = date.getMonth();
        calendar.year  = date.getFullYear();
        var attendees = $('select.select-attendees');
        var regions = $('select.select-regions');
        /*
         * Add the calendar control footer buttons.
         */
        footer.addFooterButtons('diary-manager-month', [{
            text: 'Today', 
            callback: 'calendar.loadToday', 
            args: null
        },{
            text: 'Day', 
            callback: 'helper.switchDiaryView', 
            args: 'day'
        }]);
      
            $('button.update-calender').on("click",function(e){ 
              e.preventDefault();
              calendar.updateCalendar();
            });
      
     attendees.on( "change", function(event, ui) {
    $.ajax({url:helper.baseUrl + 'diary/diary_view',
      type:"POST",
      data:{ attendees:$(this).val(),region:regions.val() }
      });
});
      
        regions.on("change",function(){
          console.log($(this).val());
          $.ajax({url:helper.baseUrl + 'leads/get_managers',
                  type:"POST",
                  dataType:"JSON",
                  data:{rep_group:$(this).find('option:selected').val()} }).done(function(data){ 
                  if(data.success){
                    attendees.empty();
          for (var i in data.managers) {
                  attendees.append("<option value='" + data.managers[i] + "'>" + data.managers[i] + "</option>");
                        } 
                  attendees.selectmenu('refresh');      
                        
                  } else {
                   alert(data.message); 
                  }
                    });
                 });
        //Display the calendar.
        calendar.displayCalendar();
        //Set the height to fit the screen.
        $('.calendar table').height($(window).height() - 150);
        return false;
    },

    updateCalendar: function () {
        var prevMonth, nextMonth;
        $('#calendar-controls .curr .ui-btn-text').text(calendar.months[calendar.month] + ' ' + calendar.year);
        prevMonth = calendar.month - 1 === -1 ? 11 : calendar.month - 1;
        $('#calendar-controls .prev .ui-btn-text').text(calendar.months[prevMonth].substr(0, 3));
        nextMonth = calendar.month + 1 === 12 ? 0 : calendar.month + 1;
        $('#calendar-controls .next .ui-btn-text').text(calendar.months[nextMonth].substr(0, 3));
        calendar.loadAppointments();
        return false;
    },

    loadToday: function () {
        var date       = new Date();
        calendar.month = date.getMonth();
        calendar.year  = date.getFullYear();
        calendar.updateCalendar();
        return false;
    },

    displayCalendar: function () {

        var numDays, dayOne, i, dayIndex, month, year, dateStr;

        month   = calendar.month;
        year    = calendar.year;
        numDays = 32 - new Date(year, month, 32).getDate();
        dayOne  = new Date(year, month, 1).getDay();
        dayOne  = dayOne === 0 ? 7 : dayOne;

        $('.calendar td').removeClass('today calday appmarker bdemarker');
        $('.calendar td').each(function (i, $td) {
            $td = $($td);
            i  += 1;
            dayIndex = i - (dayOne - 1);
            if (i < dayOne || dayIndex > numDays) {
                $td.attr('name', '').html('&nbsp;');
            } else {
              $td.text(dayIndex);
                dateStr = year + '-' + helper.zeroPad(month + 1) + '-' + helper.zeroPad(dayIndex);
                if(calendar.apps[dateStr]){
                if (calendar.apps[dateStr]['Live'] === "Live") {
                  $td.append($('<span/>',{'class':'appmarker'}));
                }
                if (calendar.apps[dateStr]['BDE'] === "BDE") {
                    $td.append($('<span/>',{'class':'bdemarker'}));
                }
                }
                $td.addClass('calday').attr('name', dateStr);
            }
        });
        $('td[name="' + calendar.today + '"]').addClass('today');
        return false;
    },

    loadAppointments: function () {
        $.ajax({
            url: 'month',
            type: 'post',
            dataType: 'json',
            data: {
                date: calendar.year + '-' + helper.zeroPad((calendar.month + 1)) + '-01'
            },
            beforeSend: function () {
                $.mobile.loading('show');
            }
        }).done(function(data) {
                $.mobile.loading('hide');
                if (data.SUCCESS) {
                    calendar.apps = data.APPOINTMENTS;
                    calendar.displayCalendar();
                } else {
                    alert(data.MESSAGE);
                }
                $('#popupChangeMonth').popup('close');
            });
        return false;
    }

};

/* ==========================================================================
   DAY VIEW
   ========================================================================== */

/*
 * Click on an appointment in the day view of the diary manager to bring up the
 * appointment details popup. At the same time, fill the edit appointment popup
 * with the appointment details incase they want to edit the appointment.
 */
$(document).on('click', '.day-container .appointment', function () {
    day.setupAppointmentDetail($(this));
});

$(document).on('click', '#popupAppDetail .delete', function () {
    day.deleteAppointment();
});

var day = {
    /*
     *  today boolean. True if the day selected is today.
     */
    init: function (today, appointments, $page) {
        
        /*
         * Add the appointments after the hour slots have been created.
         */
        $page.bind('pageshow', function () {
            day.addAppointments();
        });
        /*
         * Re add the appointments on page resize so that they change size to fill the screen.
         */
        $(window).resize(function () {
            day.addAppointments();
            return false;
        });
        /*
         * Clear the interval when the page is unloaded.
         */
        $page.bind('pagehide', function () {
            clearInterval(day.timelineInterval);
            $(window).unbind('resize');
            $page.remove();
        });
        //Cache the appointments to load on pageshow.
        day.appointments = appointments;
        //Draw the hour slots/
        day.displayTimeSlots();
    
        /*
         * If the day selected is today, show the timeline and set the interval
         * so that it updates itself every minute. Make this smarter?
         */
        if (today) {
            //Minutes range from 0 - 59 so set the starting min at 60.
            day.min = 60;
            day.placeTimeLine();
            day.timelineInterval = setInterval(function () {
                day.placeTimeLine();
            }, 1000);
        }
        return false;
    },
    
    setupAppointmentDetail: function ($appointment) {
        day.editAppIndex = $appointment.attr('data-appindex');
        var appDetail    = day.appointments[day.editAppIndex],
            $detailPopup = $('#popupAppDetail'),
            $editPopup   = $('.edit-appointment-popup');
    console.log(appDetail);
        //Fill the appointment details popup.
        $detailPopup.find('.view').attr('href',helper.baseUrl + 'leads/detail/' + appDetail.urn);
        $detailPopup.find('.urn').html('<strong>URN:</strong> ' + appDetail.urn);
        $detailPopup.find('.coname').html('<strong>Company:</strong> ' + appDetail.coname);
        $detailPopup.find('.timeStr').html('<strong>Time:</strong> (' + helper.zeroPad(appDetail.begin_hour) + ':' + helper.zeroPad(appDetail.begin_mins) + ' - ' + helper.zeroPad(appDetail.end_hour) + ':' + helper.zeroPad(appDetail.end_mins) + ') ');
        $detailPopup.find('.title').html('<strong>Title:</strong> ' + appDetail.title);
        $detailPopup.find('.comments').html('<strong>Comments:</strong> ' + appDetail.text);
        $detailPopup.attr('data-appid', appDetail.id).popup('open');
        //Fill the edit appointment popup form.
        $editPopup.find('.appointment_id').val(appDetail.id);
        $editPopup.find('.urn').val(appDetail.urn);
        $editPopup.find('.title').val(appDetail.title);
        $editPopup.find('.comments').val(appDetail.text).trigger('change');
        $editPopup.find('.begin_date').val(helper.datetimeToDatepicker(appDetail.begin_date));
        $editPopup.find('.start_time').val(helper.hoursMinsToTimepicker(appDetail.begin_hour, appDetail.begin_mins));
        $editPopup.find('.finish_time').val(helper.hoursMinsToTimepicker(appDetail.end_hour, appDetail.end_mins));  
        if(appDetail.status ==="Live"){
        $editPopup.find('#cae').prop("checked","checked");
        $editPopup.find('#bde').prop("checked",false);
      } else {
        $editPopup.find('#bde').prop("checked","checked");
        $editPopup.find('#cae').prop("checked",false);
        }
        $editPopup.find("input[type='radio']").checkboxradio("refresh");
    },

    /*
     * Display time slots from 7am - 7pm. Confirm with swinton that this
     * time period is ok.
     */
    displayTimeSlots: function () {
        var $dayCt = $('.day-container').empty();
        for (var i = 7; i <= 19; i++) {
            $dayCt.append(
                $('<div/>', {
                    'class': 'hour ' + 'hour-' + i
                }).append(
                    $('<div/>', {
                        'class': 'timeslot'
                    }).text(helper.zeroPad(i) + ':00'),
                    $('<div/>', {
                        'class': 'appCt'
                    })
                    )
                );
        }
    },

    /*
     * Add the time line with current time.
     */
    placeTimeLine: function () {
        var d, hr, min, minStr;
        d   = new Date();
        hr  = d.getHours();
        min = d.getMinutes();
        if (min != day.min) {
            minStr = min < 10 ? '0' + min : min;
            $('.timeline').remove();
            $('.hour-' + hr).append(
                $('<div/>', {
                    'class': 'timeline'
                }).css('top', (min-1) + 'px').append(
                    $('<div/>', {
                        'class': 'time'
                    }).text(hr + ':' + minStr)
                    )
                );
            day.min = min;
        }
    },

    /*
     * Loop through the appointments, adding them to the appropriate slots.
     * They might want more that one appointment in each time slot. If this
     * happens, adjust the widths so that they fit into the slot next to
     * each other.
     */
    addAppointments: function () {

        var fullWidth, $appCt, $appointments, numApps, borderAdjust, startTime, 
        endTime, height, startStr, endStr, app, hrSlot,type,classType;

        fullWidth = $('.appCt').empty().width();
        
        for (var i in day.appointments) {

            app       = day.appointments[i];
            hrSlot    = app.begin_hour;
            startStr  = helper.zeroPad(hrSlot)       + ':' + helper.zeroPad(app.begin_mins);
            endStr    = helper.zeroPad(app.end_hour) + ':' + helper.zeroPad(app.end_mins);
            startTime = new Date('2000/01/01 ' + startStr);
            endTime   = new Date('2000/01/01 ' + endStr);
            height    = Math.round((endTime.getTime() - startTime.getTime()) / 60000);
            type      = app.status;

if(type==="Live"){ classType = "cal-cae"; } else { classType = "cal-bde"; }
if(app.attendee==null){ classType = "cal-own"; }
            $appCt = $('.hour-' + hrSlot + ' .appCt').append(
                $('<div/>', {
                    id: app.id,
                    'class': 'appointment ui-bar ui-bar-e '+ classType,
                    'data-appindex': i
                }).css({
                    top: (app.begin_mins - 1) + 'px',
                    height: (height - 1) + 'px'
                }).append(
                    $('<p/>').text('(' + startStr + ' - ' + endStr + ') ' + app.title))
                );

            $appointments = $appCt.find('.appointment');
            numApps       = $appointments.length;
            borderAdjust  = Math.ceil((numApps + 1) / numApps);
            $appointments.width((fullWidth / numApps) - borderAdjust);
            
        }
    },
    
    removeAppointmentFromUI: function (id) {
        for (var i in day.appointments) {
            if (day.appointments[i].id == id) {
                delete day.appointments[i];
            }
        }
        day.addAppointments();
    },
    
    deleteAppointment: function () {
        if (confirm('Are you sure you want to delete this appointment?')) {
            var appId = $('#popupAppDetail').attr('data-appid');
            $.ajax({
                url: helper.baseUrl + 'appointment/delete',
                type: 'post',
                dataType: 'json',
                data: {
                    ids: JSON.stringify([appId])
                },
                beforeSend: function () {
                    $.mobile.loading('show');
                },
                success: function (data) {
                    $.mobile.loading('hide');
                    if (data.success) {
                        $('#popupAppDetail').popup('close');
                        day.removeAppointmentFromUI(appId);
                    } else {
                        alert(data.MESSAGE);
                    }
                }
            });
        }
    },
    
    editAppointmentsUI: function () {
        var hrMins, data, $editPopup;
        data = day.appointments[day.editAppIndex];
        $editPopup = $('#popupEditApp');
        var newDate = helper.DatepickerToDatetime($editPopup.find('.begin_date').val());
        //If the date had changed, remove it from the ui for this date.
        if (data.begin_date != newDate) {
            delete day.appointments[day.editAppIndex];
        } else {
            data.title = $editPopup.find('.title').val();
            data.text  = $editPopup.find('.comments').val();
            data.begin_date = newDate;
            hrMins = helper.extractHrsAndMins($editPopup.find('.start_time').val());
            data.begin_hour = hrMins.hour;
            data.begin_mins = hrMins.mins;
            hrMins = helper.extractHrsAndMins($editPopup.find('.finish_time').val());
            data.end_hour = hrMins.hour;
            data.end_mins = hrMins.mins;
            day.appointments[day.editAppIndex] = data;
        }
        day.addAppointments();
    }

};