"use strict";
/* ==========================================================================
 SEARCH LEADS PAGE
 ========================================================================== */

/*
 * Click to show/hide the advanced search fields.
 */
$(document).on('change', '#adv-search-switch', function() {
    if ($(this).val() === 'on') {
        $('.adv-search').show();
    } else {
        $('.adv-search').hide();
    }
    return false;
});

/*
 * When to search leads page has loaded the initial quick search fields, make
 * an ajax request to load the advanced search fields.
 */
$(document).on('pageshow', '#search-leads', function() {
    $.ajax({
        url: 'load_advanced_search_data',
        type: 'post',
        dataType: 'json',
        success: function(data) {
            for (var i in data.options) {
                var $dropDown = $('#' + i);
                for (var j in data.options[i]) {
                    $dropDown.append(
                        $('<option/>', {
                            value: data.options[i][j]
                        }).text(data.options[i][j])
                        );
                }
            }
        }
    });
    return false;
});

/* ==========================================================================
 SEARCH RESULTS PAGE
 ========================================================================== */

/*
 * Paginator is used to load more results when they scroll to the bottom of the page.
 */
var paginator = {
    scrollCount: 0,
    init: function(total) {
        paginator.total = parseInt(total, 10);
        paginator.$results = $('#results-ct');
        paginator.toggle_paginator();
        $(window).unbind('scroll').scroll(function() {
            paginator.load_more();
        });
        return false;
    },
    load_more: function() {
        var i, numLeads, dist;
        if ($(window).scrollTop() + $(window).height() == $(document).height() &&
            paginator.scrollCount > 0 && paginator.$results.find('li').length < paginator.total) {
            $.ajax({
                url: 'paginate_leads_results',
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    paginator.scrollCount = 0;
                    $('#load-more-ct .pull-down').hide();
                    $('#load-more-ct .loading-more').show();
                },
                success: function(data) {
                    $('#load-more-ct .loading-more').hide();
                    $('#load-more-ct .pull-down').show();
                    $('#search-results .ui-title').text(data.title);
                    numLeads = data.leads.length;
                    for (i = 0; i < numLeads; i++) {
                        dist = !data.leads[i].distance ? '' : '<p class="lv-rt"><strong>' + parseFloat(data.leads[i].distance).toFixed(2) + '</strong> (miles)</p>';
                        paginator.$results.append(
                            $('<li/>').append(
                                $('<a/>', {
                                    href: 'detail/' + data.leads[i].urn,
                                    'class': 'hreflink'
                                }).append(
                                    $('<h2/>').text(data.leads[i].coname),
                                    $('<p/>').html('<strong class="label">URN:</strong> ' + data.leads[i].urn),
                                    $('<p/>').html('<strong class="label">Postcode:</strong> ' + data.leads[i].postcode),
                                    dist
                                    ),
                                '<a href="#results-popup" data-rel="popup" data-icon="gear" data-theme="d" class="action-btn">Action</a>'
                                )
                            );
                    }
                    paginator.$results.listview('refresh');
                    document.title = 'Showing ' + paginator.$results.find('li').length + ' of ' + paginator.total + ' results';
                    paginator.toggle_paginator();
                }
            });
        }
        paginator.scrollCount++;
        return false;
    },
    toggle_paginator: function() {
        if (paginator.$results.find('li').length >= paginator.total) {
            $('#load-more-ct .pull-down').hide();
        }
        return false;
    }

};


function add_to_planner($urn) {
    $.ajax({
        url: helper.baseUrl + 'leads/add_to_planner',
        type: 'post',
        data: {
            urn: $urn
        },
        success: function(data) {
        //do something after?
        }
    });
    return false;
}

function remove_from_planner($urn) {
    $.ajax({
        url: helper.baseUrl + 'leads/remove_from_planner',
        type: 'post',
        data: {
            urn: $urn
        },
        success: function(data) {
        //do something after?
        }
    });
    return false;
}

$(document).on('click', '.addToPlanner', function() {
    $('#results-popup,#options-popup').popup('close');
    var $urn;
    $urn = $(this).attr('urn');
    add_to_planner($urn);
    if($.mobile.activePage.hasClass("lead-detail")){
        refreshPage();
    } else {
        $('#' + $urn).append('<p class="lv-rb ui-li-desc"><img src="https://www.swintoncommercial-clms.co.uk/clms-mobile/assets/img/icons/flag.png"></p>');
    }
});

$(document).on('click', '.removeFromPlanner', function() {
    $('#results-popup,#options-popup').popup('close');
    var $urn;
    var activePage = $.mobile.activePage.attr("id");
    $urn = $(this).attr('urn');
    remove_from_planner($urn);
    if($.mobile.activePage.hasClass("lead-detail")){
        refreshPage();  
    } else {
        $('#' + $urn + ' .lv-rb').remove();
    }
});

/* ==========================================================================
 LEAD DETAILS PAGE
 ========================================================================== */

/* Company Details Section */

$(document).on('pageinit', '.lead-detail', function() {
    codetails.init();
    contacts.init();
});

var codetails = {
    init: function() {

        /* Click to set company details editable */
        $(document).on('click', '#edit-company-details-btn', function() {
            codetails.setEditable($(this));
        });

        /* Click to cancel company details edit */
        codetails.$cancelBtn = $(document).on('click', '#company-detail-edit-ctrl-btns .cancel', function() {
            codetails.cancelEdit();
        });

        /* Click to save company details & update the ui */
        $(document).on('click', '#company-detail-edit-ctrl-btns .save', function() {
            codetails.saveDetails();
        });

    },
    setEditable: function($button) {
        $('.company-details .ui-block-b .text').hide();
        $('.company-details .ui-block-b .input').show();
        $('.codetail').each(function(i, o) {
            o = $(o);
            if (o.hasClass('editable')) {
                o.show();
            } else {
                o.hide();
            }
        });
        $('#company-detail-edit-ctrl-btns').show();
        $button.hide();
        return false;
    },
    cancelEdit: function() {
        $('.company-details .ui-block-b .input').hide();
        $('.company-details .ui-block-b .text').show();
        $('#company-detail-edit-ctrl-btns').hide();
        $('#edit-company-details-btn').show();
        $('.codetail').each(function(i, o) {
            o = $(o);
            if (!o.hasClass('hidden')) {
                o.show();
            } else {
                o.hide();
            }
        });
        return false;
    },
    saveDetails: function() {
        $.ajax({
            url: helper.baseUrl + 'leads/update_company_details',
            type: 'post',
            dataType: 'json',
            data: $('#company-details-form').serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function(data) {
                $.mobile.loading('hide');
                if (data.success) {
                    if (data.fields) {
                        for (var i in data.fields) {
                            $('input[name="' + i + '"], select[name="' + i + '"]').val(data.fields[i]);
                            $('#' + i).find('.text').text(data.fields[i]);
                            $('.block-' + i).removeClass('hidden');
                        }
                    }
                    codetails.cancelEdit();
                } else {
                    alert(data.message);
                }
            }
        });
        return false;
    }

};

/* Contacts Section */

var contacts = {
    
    init: function () {
        
        contacts.$addForm = $('#add-contact-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this), formFn = $form.attr('data-function');
            if (formFn == 'add') {
                contacts.add($form);
            } else if (formFn == 'edit') {
                contacts.update($form);
            }
        });
        
        $(document).on('click','#add-contact-form .cancel', function (e) {
            e.preventDefault();
            contacts.$addForm[0].reset();
            contacts.$addForm.hide();
        });
        
        $(document).on('change', '.lead-detail .contacts-chkbx', function() {
            contacts.toggleControlBtns();
        });
        
        $(document).on('click', '.contacts-ctrls .add', function() {
            contacts.$addForm.attr('data-function', 'add').show();
        });
        
        $(document).on('click', '.contacts-ctrls .delete', function() {
            contacts.deleteSelected();
        });
        
        $(document).on('click', '.contacts-ctrls .edit', function() {
            contacts.setEditing();
        });

    },
    
    setEditing: function () {
        var $form = $('#add-contact-form'), $editing = $('.lead-detail .contacts-chkbx:checked').closest('.contact-row');
        $form.attr('data-function', 'edit');
        $('select#priority').val($editing.find('.priority span').text().toLowerCase()).selectmenu().selectmenu('refresh');
        $('select#title').val($editing.find('.title span').text()).selectmenu().selectmenu('refresh');
        $('select#position').val($editing.find('.position span').text()).selectmenu().selectmenu('refresh');
        $('select#keydm').val($editing.find('.keydm span').text()).selectmenu().selectmenu('refresh');
        $('input#firstname').val($editing.find('.firstname span').text());
        $('input#lastname').val($editing.find('.lastname span').text());
        $('input#telephone').val($editing.find('.telephone span').text());
        $('input#mobile').val($editing.find('.mobile span').text());
        $('input#email').val($editing.find('.email span').text());
        $('input#contact_id').val($editing.attr('data-contact_id'));
        contacts.$addForm.show();
    },
    
    deleteSelected: function () {
        if (confirm('Are you sure you want to delete the selected contact(s)?')) {
            var ids = [];
            $('.lead-detail .contacts-chkbx:checked').each(function () {
                ids.push($(this).closest('.contact-row').attr('data-contact_id'));
            });
            $.ajax({
                url: helper.baseUrl + 'leads/delete_contacts',
                type: 'post',
                dataType: 'json',
                data: {
                    ids: JSON.stringify(ids)
                },
                beforeSend: function () {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    $.mobile.loading('hide');
                    if (data.success) {
                        for (var i in ids) {
                            $('.contact-row[data-contact_id="' + ids[i] + '"]').remove();
                        }
                    } else {
                        alert(data.message);
                    }
                }
            });  
        }
        return false;
    },
    
    update: function ($form) {
        $.ajax({
            url: helper.baseUrl + 'leads/update_contact',
            type: 'post',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function(data) {
                $.mobile.loading('hide');
                if (data.success) {
                    var $row = $('.contact-row[data-contact_id="' + data.contact.id + '"]');
                    $row.find('.priority span').text(helper.ucfirst(data.contact.priority));
                    $row.find('.title span').text(data.contact.title);
                    $row.find('.firstname span').text(data.contact.firstname);
                    $row.find('.lastname span').text(data.contact.lastname);
                    $row.find('.position span').text(data.contact.position);
                    $row.find('.keydm span').text(data.contact.keydm);
                    $row.find('.telephone span').text(data.contact.telephone);
                    $row.find('.mobile span').text(data.contact.mobile);
                    $row.find('.email span').text(data.contact.email);
                    contacts.$addForm[0].reset();
                    contacts.$addForm.hide();
                } else {
                    alert(data.message);
                }
            }
        });  
    },
    
    add: function ($form) {
        $.ajax({
            url: helper.baseUrl + 'leads/update_contact',
            type: 'post',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function(data) {
                $.mobile.loading('hide');
                if (data.success) {
                    $('#contacts-table tbody').append(
                        '<tr class="contact-row" data-contact_id="">' +
                        '<td class="priority">' + helper.ucfirst(data.contact.priority) + '</td>' +
                        '<td class="title">' + data.contact.title + '</td>' +
                        '<td class="firstname">' + data.contact.firstname + '</td>' +
                        '<td class="lastname">' + data.contact.lastname + '</td>' +
                        '<td class="position">' + data.contact.position + '</td>' + 
                        '<td class="keydm">' + data.contact.keydm + '</td>' +
                        '<td class="telephone">' + data.contact.telephone + '</td>' +
                        '<td class="mobile">' + data.contact.mobile + '</td>' +
                        '<td class="email">' + data.contact.email + '</td>' +
                        '<td class="chkbx-fixed">' +
                        '<fieldset data-role="controlgroup" class="chkbx">' +
                        '<input type="checkbox" name="contacts-chkbx" id="contacts-chkbx-new" class="contacts-chkbx" data-iconpos="notext" />' +
                        '<label for="contacts-chkbx-new" data="test"></label>' +
                        '</fieldset>' +
                        '</td>' +
                        '</tr>'
                    );
                    $('.chkbx-fixed').trigger("create");
                    $form[0].reset();
                } else {
                    alert(data.message);
                }
            }
        });  
    },
    
    toggleControlBtns: function () {
        var numSelected = $('.lead-detail .contacts-chkbx:checked').length;
        if (numSelected == 1) {
             $('.lead-detail .contacts-ctrls').find('.delete, .edit').removeClass('ui-disabled');
        } else if (numSelected > 1) {
            $('.lead-detail .contacts-ctrls').find('.edit').addClass('ui-disabled');
            $('.lead-detail .contacts-ctrls').find('.delete').removeClass('ui-disabled');
        } else {
            $('.lead-detail .contacts-ctrls').find('.delete, .edit').addClass('ui-disabled');
        }
    }
    
};

/* appointments section */

var appointments = {
    
    init: function () {
        
        appointments.$addForm = $('#add-appointment-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this), formFn = $form.attr('data-function');
            if (formFn == 'add') {
                appointment.add($form);
            } else if (formFn == 'edit') {
                appointment.update($form);
            }
        });
        
        $(document).on('click','#add-appointment-form .cancel', function (e) {
            e.preventDefault();
            appointments.$addForm[0].reset();
            appointments.$addForm.hide();
        });
        
        $(document).on('change', '.lead-detail .appointments-chkbx', function() {
            appointments.toggleControlBtns(); alert("test");
        });
        
        $(document).on('click', '.appointmenst-ctrls .add', function() {
            appointments.$addForm.attr('data-function', 'add').show();
        });
        
        $(document).on('click', '.appointments-ctrls .delete', function() {
            appointments.deleteSelected();
        });
        
        $(document).on('click', '.appointments-ctrls .edit', function() {
            appointments.setEditing();
        });

    },
    
    setEditing: function () {
        var $form = $('#add-appointment-form'), $editing = $('.lead-detail .appointments-chkbx:checked').closest('.appointment-row');
        $form.attr('data-function', 'edit');
        $('select#priority').val($editing.find('.priority span').text().toLowerCase()).selectmenu().selectmenu('refresh');
        $('select#title').val($editing.find('.title span').text()).selectmenu().selectmenu('refresh');
        $('select#position').val($editing.find('.position span').text()).selectmenu().selectmenu('refresh');
        $('select#keydm').val($editing.find('.keydm span').text()).selectmenu().selectmenu('refresh');
        $('input#firstname').val($editing.find('.firstname span').text());
        $('input#lastname').val($editing.find('.lastname span').text());
        $('input#telephone').val($editing.find('.telephone span').text());
        $('input#mobile').val($editing.find('.mobile span').text());
        $('input#email').val($editing.find('.email span').text());
        $('input#contact_id').val($editing.attr('data-contact_id'));
        appointments.$addForm.show();
    },
    
    deleteSelected: function () {
        if (confirm('Are you sure you want to delete the selected appointment(s)?')) {
            var ids = [];
            $('.lead-detail .appointments-chkbx:checked').each(function () {
                ids.push($(this).closest('.appointment-row').attr('data-appointment_id'));
            });
            $.ajax({
                url: helper.baseUrl + 'leads/delete_appointments',
                type: 'post',
                dataType: 'json',
                data: {
                    ids: JSON.stringify(ids)
                },
                beforeSend: function () {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    $.mobile.loading('hide');
                    if (data.success) {
                        for (var i in ids) {
                            $('.appointment-row[data-appointment_id="' + ids[i] + '"]').remove();
                        }
                    } else {
                        alert(data.message);
                    }
                }
            });  
        }
        return false;
    },
    
    update: function ($form) {
        $.ajax({
            url: helper.baseUrl + 'leads/update_appointment',
            type: 'post',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function(data) {
                $.mobile.loading('hide');
                if (data.success) {
                    var $row = $('.appointment-row[data-appointment_id="' + data.appointment.id + '"]');
                    $row.find('.priority span').text(helper.ucfirst(data.appointment.priority));
                    $row.find('.title span').text(data.appointment.title);
                    $row.find('.firstname span').text(data.appointment.firstname);
                    $row.find('.lastname span').text(data.appointment.lastname);
                    $row.find('.position span').text(data.appointment.position);
                    $row.find('.keydm span').text(data.appointment.keydm);
                    $row.find('.telephone span').text(data.appointment.telephone);
                    $row.find('.mobile span').text(data.appointment.mobile);
                    $row.find('.email span').text(data.appointment.email);
                    appointments.$addForm[0].reset();
                    appointments.$addForm.hide();
                } else {
                    alert(data.message);
                }
            }
        });  
    },
    
    add: function ($form) {
        $.ajax({
            url: helper.baseUrl + 'leads/update_appointment',
            type: 'post',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $.mobile.loading('show');
            },
            success: function(data) {
                $.mobile.loading('hide');
                if (data.success) {
                    $('#appointments-table tbody').append(
                        '<tr class="appointment-row" data-appointment_id="">' +
                        '<td class="priority">' + helper.ucfirst(data.appointment.priority) + '</td>' +
                        '<td class="title">' + data.appointment.title + '</td>' +
                        '<td class="firstname">' + data.appointment.firstname + '</td>' +
                        '<td class="lastname">' + data.appointment.lastname + '</td>' +
                        '<td class="position">' + data.appointment.position + '</td>' + 
                        '<td class="keydm">' + data.appointment.keydm + '</td>' +
                        '<td class="telephone">' + data.appointment.telephone + '</td>' +
                        '<td class="mobile">' + data.appointment.mobile + '</td>' +
                        '<td class="email">' + data.appointment.email + '</td>' +
                        '<td class="chkbx-fixed">' +
                        '<fieldset data-role="controlgroup" class="chkbx">' +
                        '<input type="checkbox" name="appointments-chkbx" id="appointments-chkbx-new" class="appointments-chkbx" data-iconpos="notext" />' +
                        '<label for="contacts-chkbx-new" data="test"></label>' +
                        '</fieldset>' +
                        '</td>' +
                        '</tr>'
                    );
                    $('.chkbx-fixed').trigger("create");
                    $form[0].reset();
                } else {
                    alert(data.message);
                }
            }
        });  
    },
    
    toggleControlBtns: function () {
        var numSelected = $('.lead-detail .appointments-chkbx:checked').length;
        if (numSelected == 1) {
             $('.lead-detail .appointments-ctrls').find('.delete, .edit').removeClass('ui-disabled');
        } else if (numSelected > 1) {
            $('.lead-detail .appointments-ctrls').find('.edit').addClass('ui-disabled');
            $('.lead-detail .appointments-ctrls').find('.delete').removeClass('ui-disabled');
        } else {
            $('.lead-detail .appointments-ctrls').find('.delete, .edit').addClass('ui-disabled');
        }
    }
    
};

/* ==========================================================================
   GEO LOCATION
 ========================================================================== */

function getLocation()
{
    if (navigator.geolocation)
    {
        return navigator.geolocation.getCurrentPosition(reverseGeoLookup);
    }
    else {
        alert("Geolocation is not enabled on this device");
    }
}

function reverseGeoLookup(position) {
    //put the longitude and latitude into the API query
    var postcode = '';
    $.ajax({
        url:"https://maps.googleapis.com/maps/api/geocode/json?latlng=" + position.coords.latitude + "," + position.coords.longitude + "&sensor=true",
        async : false,
        dataType:"json"
    }).done(function(data){
        var result = data.results;
        for(var i = 0, length = result.length; i < length; i++) {
            //each result has an address with multiple parts (it's all in the reference)
            for(var j = 0; j < result[i].address_components.length; j++) {
                var component = result[i].address_components[j];
                //if the address component has postal code then write it out
                if(component.types[0]==="postal_code") {
                    postcode = component.long_name;
                }
            }
        }
    });

    if(postcode.length>0){
        $.ajax({
            url:helper.baseUrl + "planner/store_postcode/" +postcode
        });  
    } else {
        alert("Cannot find your location");
    }
}
var current_postcode = '';

function get_session_val(field){
    $.ajax({
        url:helper.baseUrl + "leads/get_session_value/" +field,
        async: false,
        success: function(value){
            current_postcode = value;
        }
    });
}

$(document).on("click",".locate-postcode",function(){
    $(".locate-postcode").attr("src","https://www.swintoncommercial-clms.co.uk/clms-mobile/assets/img/icons/loading9.gif");
    get_session_val("current_postcode");
    if(current_postcode.length>0){
        $(".current_postcode_input").val(current_postcode);
        $(".current_postcode_text").text(current_postcode);
    }
    $(".locate-postcode").attr("src","https://www.swintoncommercial-clms.co.uk/clms-mobile/assets/img/icons/location.jpg"); 
});

$(document).on("click",".update-postcode",function(){
getLocation();
refreshPage();
});