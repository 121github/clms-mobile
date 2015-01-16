"use strict";

/* ==========================================================================
 FRAMEWORK LISTENERS & PAGE INIT FUNCTIONS
 ========================================================================== */
$.ajaxSetup({
  //timeout: 15000 //5 second timeout
});

$(document).ajaxError(function(event, xhr, settings) {
  if(settings.saveOnError===true){
  if(xhr.statusText==="timeout"||xhr.statusText==="error"){
  var requestType, coname, errorMsg, url = "";
  coname = settings.coname;
  url = settings.url;

  //adds the update type to the local storage
  if (settings.url === helper.baseUrl + 'leads/update_company_details') {
    requestType = "Company update";
  } else if (settings.url === helper.baseUrl + 'leads/update_contact') {
    requestType = "Contact update";
  } else if (settings.url === helper.baseUrl + 'leads/delete_contacts') {
    requestType = "Contact deleted";
  } else if (settings.url === helper.baseUrl + 'leads/update_policy') {
    requestType = "Policy update";
  } else if (settings.url === helper.baseUrl + 'appointment/delete') {
    requestType = "Appointment deleted";
  } else if (settings.url === helper.baseUrl + 'appointment/save') {
    requestType = "Appointment saved";
  } else if (settings.url === helper.baseUrl + 'delete_policy') {
    requestType = "Policy deleted";
  } else if (settings.url === helper.baseUrl + 'file/delete') {
    requestType = "File deleted";
  }

  if (requestType!=="") {
    var timestamp = $.now();
//add post request to HTML5 local storage
    var storage = JSON.parse(localStorage.getItem('pending'));
    var postVals = $.unserialize(settings.data);
    console.log(postVals);
    var storageItem = new Array(requestType, postVals, timestamp, coname, url);
    storage.push(storageItem);
    localStorage.setItem('pending', JSON.stringify(storage));
    alert("Connection is down but data was saved to the local storage. You can update the record when you are back online");
    $.mobile.loading('hide');
    
  }
  }
  }
});



$(document).ajaxSuccess(function(event, xhr, settings) {
  var page = window.location.href.replace(helper.baseUrl, '');
  if (xhr.responseText === 'Timeout') {
    window.location = helper.baseUrl + 'user/login/?r=' + encodeURIComponent(page);
  }
});

$(document).on('pagehide', 'div[data-role="page"]', function() {
  $(window).unbind('scroll');
  return false;
});

/*
 * Click the header menu button to show/hide the side navbar.
 */
$(document).on('click', '.navmenu-btn', function() {
  //Only open the panel on the active page
  $('#' + $.mobile.activePage.attr('id')).find('.navmenu-panel').panel('open');
  return false;
});

/*
 * Click inside the datebox input to show the date picker.
 */
$(document).on('click', 'input[data-role="datebox"]', function() {
  $(this).datebox().datebox('open');
});

/* ==========================================================================
 HELPER FUNCTIONS
 ========================================================================== */

var helper = {
  /*
   * Zero pad numbers less than 10
   */
  zeroPad: function(num) {
    return num < 10 ? '0' + num : num;
  },
  switchDiaryView: function(url, params) {
    if (url === null) {
      return false;
    }
    url = params ? url + '/' + params : url;
    $(window).unbind('scroll');
    $.mobile.changePage(url);
    return true;
  },
  //Convert 'yyyy-mm-dd' to 'd/m/yyyy' for the ui datepicker
  datetimeToDatepicker: function(date) {
    var split = date.split('-');
    return helper.zeroPad(parseInt(split[2], 10)) + '/' + helper.zeroPad(parseInt(split[1], 10)) + '/' + split[0];
  },
  //Convert 'd/m/yyyy' to 'yyyy-mm-dd'
  DatepickerToDatetime: function(date) {
    var split = date.split('/');
    return split[2] + '-' + helper.zeroPad(parseInt(split[1], 10)) + '-' + helper.zeroPad(parseInt(split[0], 10));
  },
  //Convert hr & mins to 'hh:mm AM/PM' for the ui timepicker & convert from 24 to 12 hr time format
  hoursMinsToTimepicker: function(hour, mins) {
    var amPm = ' PM';
    if (hour > 12) {
      hour -= 12;
    } else if (hour < 12) {
      amPm = ' AM';
    }
    return helper.zeroPad(hour) + ':' + helper.zeroPad(mins) + amPm;
  },
  extractHrsAndMins: function(time) {
    var split = time.split(/[: ]+/),
            hour = parseInt(split[0], 10),
            hr24 = split[2] == 'PM' && hour < 10 ? hour + 12 : hour;
    return {
      'hour': hr24,
      'mins': parseInt(split[1], 10)
    };
  },
  ucfirst: function(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  },
  timestamp_to_uk: function(timestamp) {
    // multiplied by 1000 so that the argument is in milliseconds, not seconds
    var d = new Date(timestamp);
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var hours = d.getHours();
    var minutes = d.getMinutes();
    var seconds = d.getSeconds();

// will display time in 10:30:23 format
    var formattedTime = day + '/' + month + '/' + year + ' ' + hours + ':' + minutes + ':' + seconds;
    return formattedTime;

  }

};
/* ==========================================================================
 FOOTER RELATED FUNCTIONS
 ========================================================================== */

var footer = {
  addFooterButtons: function(page, btns) {
    var $footer, $ul, numBtns, i, $btn, args;
    $ul = $('<ul/>');
    $footer = $('#' + page + ' .footer').empty().append(
            $('<div/>', {
      'data-role': 'navbar'
    }).append($ul)
            );
    numBtns = btns.length;
    for (i = 0; i < numBtns; i++) {
      $btn = $('<a/>').text(btns[i].text);
      args = btns[i].args || null;
      footer.addFooterBtnClick($btn, btns[i].callback, args);
      $ul.append($('<li/>').append($btn));
    }
    $footer.trigger('create');
    return false;
  },
  addFooterBtnClick: function($btn, callback, args) {
    var pieces, obj, fn, cbFn;
    pieces = callback.split('.');
    if (pieces[0] && pieces[1]) {
      obj = pieces[0];
      fn = pieces[1];
      cbFn = window[obj][fn];
    }
    if (typeof cbFn === 'function') {
      $btn.click(function() {
        cbFn(args);
      });
    }
  }
};

/* ==========================================================================
 POPUPS
 ========================================================================== */


/**
 * When click in a date/time field in an popup, close the popup to
 * reveal the date/time selector.
 */
$(document).on('click', 'div[data-role="popup"] input[data-role="datebox"]', function() {
  if ($('.ui-datebox-container').length > 0) {
    $('div[data-role="popup"]').popup('close');
  }
});

/*
 $(document).on('click', 'div[data-role="popup"][data-clear_on_cancel="true"] .cancel-btn, .close-btn', function () {
 var $form = $(this).closest('div[data-role="popup"]').find('form');
 $form[0].reset();
 $form.find('input').trigger('change');
 });
 */

/* Function fired before the appointment popup is shown */
$(document).on('popupbeforeposition', 'div[data-role="popup"]', function() {
  $(this).find('.error-txt').hide();
});

/**
 * Call back function used by popup date/time inputs to open up the popup after
 * selection.
 */
function showPopup(date, id) {
  $(id).show().popup('open');
}


$(document).on('keyup', 'input[type="number"]', function() {
  $(this).val($(this).val().replace(/\d.-/g, ''));
});

    $(document).on('click','.back-button',function(){
        parent.history.back();
        return false;
    });