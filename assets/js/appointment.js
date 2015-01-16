/**
 * Click save in the ADD appointment popup.
 */
$(document).on('click', '.add-appointment-popup button.save', function () {
    
    var $popup = $(this).closest('.add-appointment-popup'), $form  = $popup.find('form'), empty  = 0, error;
        
    $popup.find('input, textarea').each(function (i, o) {
        o = $(o);
        error = $('.error-txt.' + o.attr('name'));
        if ($.trim(o.val()) === '') {
            error.show();
            empty++;
        } else {
            error.hide();
        }
    });
    
    if (empty === 0) {
        
        $.ajax({
            type: 'post',
            url: helper.baseUrl + 'appointment/save',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            }
        }).done(function(data){ 
        
      $.mobile.loading('hide');
                if (data.success) {
                    $form[0].reset();
                    $popup.popup('close');
                    //If we are on the lead detail page, append a new row to the contacts table.
                    if ($('div[data-role="page"]').hasClass('lead-detail')) {
                        lead.apps.loadView();
                        lead.history.loadView();
                    }
                } else {
                    alert(data.message);
                }
      });
        
    }
    
});

/**
 * Click save in the EDIT appointment popup.
 */
$(document).on('click', '.edit-appointment-popup button.save', function () {
    
    var $popup = $(this).closest('.edit-appointment-popup'), $form = $popup.find('form'), empty = 0, 
        error, $page = $('div[data-role="page"]');
        
    $popup.find('input, textarea').each(function (i, o) {
        o = $(o);
        error = $('.error-txt.' + o.attr('name'));
        if ($.trim(o.val()) === '') {
            error.show();
            empty++;
        } else {
            error.hide();
        }
    });
    
    if (empty === 0) {
        
        $.ajax({
            type: 'post',
            url: helper.baseUrl + 'appointment/save',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function (data) { 
                $.mobile.loading('hide');
                if (data.success) {
                    $popup.popup('close');
                    //If we are on the lead detail page, append a new row to the contacts table.
                    if ($page.hasClass('lead-detail')) {
                        lead.apps.loadView();
                        lead.history.loadView();
                    }
                    else if ($page.hasClass('appointment-planner')) {
                        //This is a cheat. Force the page to refresh
                        jQuery.mobile.changePage(window.location.href, {
                            allowSamePageTransition: true,
                            transition: 'none',
                            reloadPage: true
                        });
                    }
                    else if ($page.hasClass('diary-manager-day')) {
                        day.editAppointmentsUI();
                    }
                } else {
                    alert(data.message);
                }
            }
        });
        
    }
    
});