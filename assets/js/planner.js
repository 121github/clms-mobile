"use strict";

/* ==========================================================================
 JOURNEY PLANNER SORT ORDER CONTROLS
 ========================================================================== */

var plansort = {
  init: function() {

    plansort.$ct = $('#planner-ct');

    $(document).on('click', '#planner-ct li .sort-up', function(e) {
      e.preventDefault();
      plansort.moveUp($(this));
    });

    $(document).on('click', '#planner-ct li .sort-down', function(e) {
      e.preventDefault();
      plansort.moveDown($(this));
    });
  },
  moveUp: function($upBtn) {
    var $row = $upBtn.closest('li.planner'),
            $prev = $row.prev();
    if ($prev.length > 0) {
      $prev.before($row);
      plansort.$ct.listview('refresh');
      plansort.updateSortOrder();
    }
    $row.find('.ui-btn').removeClass('ui-focus');
  },
  moveDown: function($downBtn) {
    var $row = $downBtn.closest('li.planner'),
            $next = $row.next();
    if ($next.length > 0) {
      $next.after($row);
      plansort.$ct.listview('refresh');
      plansort.updateSortOrder();
    }
    $row.find('.ui-btn').removeClass('ui-focus');
  },
  updateSortOrder: function() {
    var sortData = {};
    plansort.$ct.children('li').each(function(i) {
      sortData[i] = {
        urn: $(this).attr('id'),
        order: i
      };
    });
    $.ajax({
      url: helper.baseUrl + 'planner/update_planner_order',
      type: 'post',
      dataType: 'json',
      data: {
        sortData: JSON.stringify(sortData)
      },
      success: function(data) {
        if (!data.success) {
          alert(data.message);
        }
      }
    });
  }

};

function clear_planner(date) {
  var $page = $('div[data-role="page"]');
  var date = $page.find('#date-holder').val();
  $.ajax({
    url: helper.baseUrl + 'planner/clear_planner',
    dataType: 'json',
    type: 'post',
    data: {
      date: date
    },
    success: function(data) {
      if (data.success) {
        $page.find('#planner-ct').html('<li>Your planner is empty</li>').listview('refresh');
      } else {
        alert(data.message);
      }
    }
  });
}
;

function add_to_planner(urn) {
  var $page = $('div[data-role="page"]');
  $.ajax({
    url: helper.baseUrl + 'planner/add_to_planner',
    dataType: 'json',
    type: 'post',
    data: {
      urn: urn
    },
    success: function(data) {
      if (data.success) {
        if ($page.hasClass('lead-detail')) {
          $('span.addToPlanner').addClass('hidden');
          $('span.removeFromPlanner').removeClass('hidden');
        } else {
          $('#' + urn + ' .in-jplanner').text('Available in Journey Planner');
        }
      } else {
        alert(data.message);
      }
    }
  });
  return false;
}

function remove_from_planner(urn) {
  var $page = $('div[data-role="page"]');
  $.ajax({
    url: helper.baseUrl + 'planner/remove_from_planner',
    dataType: 'json',
    type: 'post',
    data: {
      urn: urn
    },
    success: function(data) {
      if (data.success) {
        if ($page.hasClass('lead-detail')) {
          $('span.addToPlanner').removeClass('hidden');
          $('span.removeFromPlanner').addClass('hidden');
        } else if ($page.hasClass('prospect-planner')) {
          $('#planner-ct li#' + urn).remove();
          $('#planner-ct').listview('refresh');
        } else {
          $('#' + urn + ' .in-jplanner').text('');
        }
      } else {
        alert(data.message);
      }
    }
  });
  return false;
}

function set_planner_date(urn, date) {
  var $page = $('div[data-role="page"]');
  $.ajax({
    url: helper.baseUrl + 'planner/set_planner_date',
    dataType: 'json',
    type: 'post',
    data: {
      urn: urn, date: date
    },
    success: function(data) {
      if (data.success) {
        $("#" + urn).find('.date').text(date);
      } else {
        alert(data.message);
      }
    }
  });
  return false;
}

//Click the planner button to add/remove from planner & update ui btn flag.
$(document).on('click', '.plannerBtn', function() {
  var $plannerBtn = $(this),
          urn = $plannerBtn.attr('data-urn');
  $('#results-popup, .options-popup').popup('close');
  if ($plannerBtn.hasClass('addToPlanner')) {
    add_to_planner(urn);
  } else if ($plannerBtn.hasClass('removeFromPlanner')) {
    remove_from_planner(urn);
  }
  else if ($plannerBtn.hasClass('setDate')) {
    $('#date-holder').datebox('open');
    $('#date-holder').attr("urn", urn);
  }
});

$(document).on('click', '.clear-planner', function() {
  $("#plan-top-options-popup").popup("close");
  if (confirm('This will delete all plan entries in view. Are you sure?')) {
    clear_planner();
  }
});


function setDate(date) {
  var urn = $('#date-holder').attr('urn'),
          date = $('#date-holder').val();
  set_planner_date(urn, date);
}