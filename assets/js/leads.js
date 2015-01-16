"use strict";

/* PLEASE DONT USE NETBEANS AUTO FORMAT ON THIS PAGE */

/*
 * setting up offline data capture.
 */
if (localStorage.getItem("pending") === null) {
  var newStorage = new Array();
localStorage.setItem('pending',JSON.stringify(newStorage));
}
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
//prevents the form being submited but still allows it to check required fields when clicked
$(document).on("click", ".create-form .create", function(e) {
    e.preventDefault();
    $.ajax({
        url: helper.baseUrl + "leads/add_new_prospect",
        type: "POST",
        dataType: "json",
        data: $(".create-form").serialize(),
        beforeSend: function() {
            $.mobile.loading('show', {
                text: 'Checking for duplicates...',
                textVisible: true,
                theme: 'a',
                html: ""
            });
        },
        success: function(data) {
            if (data.success) {
                window.location.href = helper.baseUrl + 'leads/detail/' + data.urn;
            } else {
                $.mobile.loading('hide');
                alert(data.message);
                if (data.dupes) {
                    $.each(data.dupes, function(i, a) {
                      var link = "";
                      if(a.type==="Prospector"){ link = "<a href='"+helper.baseUrl + "leads/detail/"+a.id+"'>"+a.id+"</a>"; }
                      else { link = a.id; }
                        $('.dupe-table tbody').html('').append('<tr><td>' + a.type + '</td><td>' + link + '</td><td>' + a.coname + '</td><td>' + a.last_updated + '</td></tr>');
                    });
                    $('#dupes').removeClass('hidden');
                }
            }
        }
    });
    return false;
});



//show log data in popup for a history entry
$(document).on("click", ".show-log", function(e) {
    e.preventDefault();
    var id, data;
    var id = $(this).attr('id');
    $.ajax({
        url: helper.baseUrl + 'reports/get_log',
        type: 'post',
        dataType: 'json',
        data: {id: id},
        beforeSend: function() {
            $.mobile.loading('show');
        },
        success: function(data) {
            if (data.success === true) {
                $('#log-table tbody').html("");
                $.mobile.loading('hide');
                $.each(data.log, function(i, row) {
                    $('#log-table tbody').append('<tr><td>' + row.change_field + '</td><td>' + row.old_val + '</td><td>' + row.new_val + '</td><td>' + row.uk_date + '</td></tr>');
                });
                $('#popupLogInfo').show().popup('open');
            } else {
                $.mobile.loading('hide');
                alert(data.message);
            }
        }
    });


});


//find and display potential duplicates 
$(document).on("click", ".duplicate-info", function() {
    var urn = $(this).attr("data-urn");
    $.ajax({
        url: helper.baseUrl + 'leads/duplicates',
        type: 'post',
        dataType: 'json',
        data: {urn: urn},
        beforeSend: function() {
            $.mobile.loading('show');
        },
        success: function(data) {
            if (data.success === true) {
                $('#duplicate-table tbody').html("");
                $.mobile.loading('hide');
                $.each(data.data, function(i, row) {
                    $('#duplicate-table tbody').append('<tr><td>' + row.urn + '</td><td>' + row.coname + '</td><td>' + row.p_add1 + '</td><td>' + row.p_postcode + '</td><td>' + row.firstname + '</td><td>' + row.lastname + '</td><td>' + row.telephone + '</td><td><a href="' + row.urn + '">View</a></td></tr>');
                });
                $('#popupDuplicateInfo').show().popup('open');
            } else {
                $.mobile.loading('hide');
                alert(data.message);
            }
        }
    });
});

//find and display potential duplicates 
$(document).on("click", ".local-info", function() {
    var urn = $(this).attr("data-urn");
    $.ajax({
        url: helper.baseUrl + 'leads/local',
        type: 'post',
        dataType: 'json',
        data: {urn: urn},
        beforeSend: function() {
            $.mobile.loading('show');
        },
        success: function(data) {
            if (data.success == true) {
                $('#local-table tbody').html("");
                $.mobile.loading('hide');
                $.each(data.data, function(i, row) {
                    $('#local-table tbody').append('<tr><td>' + row.coname + '</td><td>' + row.p_add1 + '</td><td>' + row.p_postcode + '</td><td>' + row.firstname + '</td><td>' + row.lastname + '</td><td>' + row.type + '</td></tr>');

                });
                $('#popupLocalInfo').show().popup('open');
            } else {
                $.mobile.loading('hide');
                alert(data.message);
            }
        }
    });
});


function reset_policy_form() {
    $('#popupAddPolicy .policy, #popupAddPolicy .renewal, #popupAddPolicy .premium').val('');
    $('input[data-type="search"]').val('').trigger("keyup");
    $('#popupEditPolicy select').children().removeAttr('selected');
    $('#popupEditPolicy select').selectmenu("refresh", true);
}

/* ==========================================================================
 SEARCH RESULTS PAGE
 ========================================================================== */

/*
 * Paginator is used to load more results when they scroll to the bottom of the page.
 */
var paginator = {
    scrollCount: 0,
    init: function(total) {
       $('#page-selector').selectmenu()
        paginator.total = parseInt(total, 10);
        paginator.$results = $('#results-ct');
       
        $(document).on("click",".prev-page,.next-page",function(e){
          e.preventDefault();
            var page = $(this).attr('page');
            paginator.load_more(page);
        });
          $(document).on("change","#page-selector",function(e){
            var page = $(this).val();
            paginator.load_more(page);
        });
        
        return false;
    },
    load_more: function(page) {
        var i, numLeads, dist, jptxt;
        if (page) {
            $.ajax({
                url: helper.baseUrl + 'leads/paginate_leads_results',
                type: 'post',
                dataType: 'json',
                data: {page:page},
                beforeSend: function() {
                               $.mobile.loading('show', {
            text: 'Loading page...',
            textVisible: true,
            theme: 'a'});
                    paginator.scrollCount = 0;
                  
                     $('#page-selector').selectmenu('disable');
                    $('.next-page').addClass('ui-disabled');
                     $('.prev-page').addClass('ui-disabled');
                },
                success: function(data) {
                    paginator.$results.empty();
                    $('.prev-page').attr('page',Number(page)-1);
                    $('.next-page').attr('page',Number(page)+1);
                    if(page<2){
                    $('.prev-page').hide();  
                    } else {
                    $('.prev-page').show();   
                    }
                    if($('#page-selector').children().last().prop('selected')){
                      $('.next-page').hide();  
                    } else {
                      $('.next-page').show();  
                    }
                    var start = (Number(page)*50)+1;
                    $('#page-selector').val(Number(page));
                    $('#page-selector').selectmenu('refresh');
                    $('#page-selector').selectmenu('enable');
                    $('.next-page').removeClass('ui-disabled');
                    $('.prev-page').removeClass('ui-disabled');
                    $('#search-results .ui-title').text(data.title);
                    numLeads = data.leads.length;
                    for (i = 0; i < numLeads; i++) {
                        jptxt = data.leads[i].plan_id ? 'Available in journey planner' : '';
                        dist = !data.leads[i].distance ? '' : $('<p/>').html('<strong>' + parseFloat(data.leads[i].distance).toFixed(2) + '</strong> (miles)</p>');

 var nextcall = "";
 var lastcall = "";
 var postcode = "";
 if(data.leads[i].nextcontact_formatted){
   nextcall = $('<p/>').html('<strong class="label">Next Action:</strong> ' + data.leads[i].nextcontact_formatted+' ['+data.leads[i].nextcall_days+']');
 }
  if(data.leads[i].date_updated_formatted){
   lastcall = $('<p/>').html('<strong class="label">Last Action:</strong> ' + data.leads[i].date_updated_formatted+' ['+data.leads[i].lastcall_days+']');
 }
 if(data.leads[i].postcode){
  postcode = $('<p/>').html('<strong class="label">Postcode:</strong> ' + data.leads[i].postcode); 
 }
                        
                        paginator.$results.append(
                          $('<li/>',{'class' : 'result ' + data.leads[i].class}).append(
                          $('<a/>', {
                            href    : 'detail/' + data.leads[i].urn,
                            'class' : 'hreflink',
                            id      : data.leads[i].urn
                          }).append(
                            $('<h2/>').text(data.leads[i].coname),
                            nextcall,
                            lastcall,
                            postcode,
                            $('<div/>',{'class':'ui-li-aside'}).append(dist).append($('<p/>', {
                              'class':'in-jplanner'
                            }).text(jptxt))
                          ),
                          '<a href="#results-popup" data-rel="popup" data-icon="gear" class="action-btn"  data-urn="'+data.leads[i].urn+'" data-plan_id="'+data.leads[i].plan_id+'">Action</a>'
                          )
                        );
                    }
                    paginator.$results.listview('refresh');
                    $.mobile.loading('hide');
                    document.title = 'Showing ' + paginator.$results.find('li').length + ' of ' + paginator.total + ' results';
                }
            });
        }
        paginator.scrollCount++;
        return false;
    }
    

};

/* ==========================================================================
 LEAD DETAILS PAGE
 ========================================================================== */

var lead = {
    init: function(urn) {
        this.urn = urn;
        this.pageId = '#' + $.mobile.activePage.attr('id');
        this.geninfo.init();
        this.codetails.init();
        this.contacts.init();
        this.history.init();
        this.apps.init();
        this.policy.init();
        this.docs.init();
        this.update.init();
        
        $(document).on('click','.reset-record',function(){ 
        $.ajax({url:helper.baseUrl + 'leads/reset_record',
        type:"POST",
        data:{ urn: $(this).attr('data-urn') }
        }).done(function(){ 
          lead.geninfo.loadView(); 
          $('.reset-record').addClass('hidden');
          $('.update-record').removeClass('hidden');
        });
        });
        
    },
    geninfo: {
        init: function() {

            /* Add important classes to config so they can be easily changed */
            this.config = {
                ctCls: lead.pageId + ' .general-info-container',
                infoCls: lead.pageId + ' .geninfo',
                formCls: lead.pageId + ' .geninfo-edit-form',
                rgSelect: lead.pageId + ' .geninfo-edit-form select[name="rep_group"]',
                mgrSelect: lead.pageId + ' .geninfo-edit-form select[name="manager"]'
            };

            $(document).on('click', lead.geninfo.config.infoCls + ' .edit', function() {
                $(lead.geninfo.config.infoCls).addClass('hidden');
                $(lead.geninfo.config.formCls).removeClass('hidden');
            });

            $(document).on('click', lead.geninfo.config.formCls + ' .cancel', function() {
                $(lead.geninfo.config.formCls).addClass('hidden');
                $(lead.geninfo.config.infoCls).removeClass('hidden');
            });

            $(document).on('change', lead.geninfo.config.rgSelect, function() {
                lead.geninfo.loadRegroupManagers($(this));
            });

            $(document).on('click', lead.geninfo.config.formCls + ' .save', function() {
                lead.geninfo.save();
            });

        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_general_info_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.geninfo.config.ctCls).html(html).trigger('create');
                }
            });

        },
        loadRegroupManagers: function($repgroupSelect) {
            var $managerSelect = $(lead.geninfo.config.mgrSelect);
            $.ajax({
                url: helper.baseUrl + 'leads/get_managers',
                data: {
                    rep_group: $repgroupSelect.val()
                },
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    $.mobile.loading('hide');
                    if (data.success) {
                        $managerSelect
                                .empty()
                                .append('<option value="no_selection_made">Please make a selection...</option>');
                        for (var i in data.managers) {
                            $managerSelect
                                    .append("<option value='" + data.managers[i] + "'>" + data.managers[i] + "</option>");
                        }
                        $managerSelect.trigger('change');
                    } else {
                        alert(data.message);
                    }
                }
            });
        },
        save: function() {
            $.ajax({
                url: helper.baseUrl + 'leads/update_company_details',
                type: 'post',
                dataType: 'json',
                data: $(lead.geninfo.config.formCls).serialize(),
                coname: $('.company-name').text(),
                timeout:10000,
                saveOnError:true,
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    if (data.success) {
                        lead.geninfo.loadView();
                        lead.history.loadView();
                    } else {
                        $.mobile.loading('hide');
                        alert(data.message);
                    }
                }
            }).fail(function(){ 
              lead.geninfo.loadView();
              });
        }

    }, //End of geninfo

    codetails: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .company-detail-container',
                infoCls: lead.pageId + ' .compdetails',
                formCls: lead.pageId + ' .compdetails-edit-form'
            };

            $(document).on('click', lead.codetails.config.infoCls + ' .edit', function() {
                $(lead.codetails.config.infoCls).addClass('hidden');
                $(lead.codetails.config.formCls).removeClass('hidden');
            });

            $(document).on('click', lead.codetails.config.formCls + ' .cancel', function() {
                $(lead.codetails.config.formCls).addClass('hidden');
                $(lead.codetails.config.infoCls).removeClass('hidden');
            });

            $(document).on('click', lead.codetails.config.formCls + ' .save', function() {
                lead.codetails.save();
            });

            this.loadView();

        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_company_details_view',
                type: 'post',
                dataType: 'html',
                timeout:10000,
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.codetails.config.ctCls).html(html).trigger('create');
                }
            });

        },
        save: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/update_company_details',
                type: 'post',
                dataType: 'json',
                data: $(lead.codetails.config.formCls).serialize(),
                coname: $('.company-name').text(),
                timeout:10000,
                saveOnError:true,
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    if (data.success) {
                        lead.codetails.loadView();
                        lead.history.loadView();
                    } else {
                        $.mobile.loading('hide');
                        alert(data.message);
                    }
                }
            }).fail(function(){ 
              lead.codetails.loadView();
              });

        }

    }, //End of codetails

    contacts: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .contacts-container',
                formCls: lead.pageId + ' .contacts-form',
                ctrlCls: lead.pageId + ' .contacts-controls',
                chkbxCls: lead.pageId + ' .contacts-chkbx'
            };

            $(document).on('click', lead.contacts.config.ctrlCls + ' .add', function() {
                $(lead.contacts.config.formCls).removeClass('hidden');
            });

            $(document).on('click', lead.contacts.config.ctrlCls + ' .delete', function() {
                lead.contacts.deleteSelected();
            });

            $(document).on('click', lead.contacts.config.ctrlCls + ' .edit', function() {
                lead.contacts.edit();
            });

            $(document).on('click', lead.contacts.config.formCls + ' .cancel', function() {
                $(lead.contacts.config.formCls).addClass('hidden');
            });

            $(document).on('click', lead.contacts.config.formCls + ' .save', function() {
                lead.contacts.save();
            });

            $(document).on('change', lead.contacts.config.chkbxCls, function() {
                lead.contacts.toggleCtrlBtns();
            });

            this.loadView();

        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_contacts_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.contacts.config.ctCls).html(html).trigger('create');
                }
            });

        },
        toggleCtrlBtns: function() {

            var numSelected = $(lead.contacts.config.chkbxCls + ':checked').length,
                    $controls = $(lead.contacts.config.ctrlCls);

            if (numSelected === 1) {
                $controls.find('.delete, .edit').removeClass('ui-disabled');
            } else if (numSelected > 1) {
                $controls.find('.edit').addClass('ui-disabled');
                $controls.find('.delete').removeClass('ui-disabled');
            } else {
                $controls.find('.delete, .edit').addClass('ui-disabled');
            }

        },
        deleteSelected: function() {
            if (confirm('Are you sure you want to delete the selected contact(s)?')) {
                var ids = [];
                $(lead.contacts.config.chkbxCls + ':checked').each(function() {
                    ids.push($(this).closest('.contact-row').attr('data-contact_id'));
                });
                $.ajax({
                    url: helper.baseUrl + 'leads/delete_contacts',
                    type: 'post',
                    dataType: 'json',
                    coname: $('.company-name').text(),
                    timeout:10000,
                    saveOnError:true,
                    data: {
                        urn: lead.urn,
                        ids: JSON.stringify(ids)
                    },
                    beforeSend: function() {
                        $.mobile.loading('show');
                    },
                    success: function(data) {
                        if (data.success) {
                            lead.contacts.loadView();
                            lead.history.loadView();
                        } else {
                            $.mobile.loading('hide');
                            alert(data.message);
                        }
                    }
                }).fail(function(){ 
              lead.contacts.loadView();
              });
            }

        },
        edit: function() {

            var $row = $(lead.contacts.config.chkbxCls + ':checked').closest('tr'),
                    $form = $(lead.contacts.config.formCls);

            $form.find('select#priority').val($row.find('.priority').text().toLowerCase());
            $form.find('select#title').val($row.find('.title').text());
            $form.find('select#position').val($row.find('.position').text());
            $form.find('select#keydm').val($row.find('.keydm').text());
            $form.find('input#firstname').val($row.find('.firstname').text());
            $form.find('input#lastname').val($row.find('.lastname').text());
            $form.find('input#telephone').val($.trim($row.find('.telephone').text()));
            $form.find('input#mobile').val($.trim($row.find('.mobile').text()));
            $form.find('input#email').val($.trim($row.find('.email').text()));
            $form.find('input#contact_id').val($row.attr('data-contact_id'));

            $form.find('select').selectmenu().selectmenu('refresh');
            $form.removeClass('hidden');

        },
        save: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/update_contact',
                type: 'post',
                dataType: 'json',
                data: $(lead.contacts.config.formCls).serialize(),
                coname: $('.company-name').text(),
                timeout:10000,
                saveOnError:true,
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    if (data.success) {
                        lead.contacts.loadView();
                        lead.history.loadView();
                    } else {
                        $.mobile.loading('hide');
                        alert(data.message);
                    }
                }
            }).fail(function(){ 
              lead.contacts.loadView();
              });

        }

    }, //End of contacts

    history: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .history-container'
            };

            this.loadView();
        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_history_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.history.config.ctCls).html(html).trigger('create');
                }
            });

        }

    }, //End of history

    apps: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .appointments-container',
                chkbxCls: lead.pageId + ' .appointments-chkbx',
                ctrlCls: lead.pageId + ' .appointments-ctrls'
            };

            $(document).on('change', lead.apps.config.chkbxCls, function() {
                lead.apps.toggleCtrlBtns();
            });

            $(document).on('click', lead.apps.config.ctrlCls + ' .delete', function() {
                lead.apps.deleteSelected();
            });

            this.loadView();

        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_appointments_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.apps.config.ctCls).html(html).trigger('create');
                    $(lead.apps.config.ctCls).find(".select-attendees").on( "change", function(event, ui) {
$.ajax({url:helper.baseUrl + 'leads/update_attendees',type:"POST",data:{ id:$(this).attr('app'),attendees:$(this).val() }
});
                    
                    });
                }
            });
        },
        toggleCtrlBtns: function() {

            var $checked = $(lead.apps.config.chkbxCls + ':checked'),
                    numSelected = $checked.length,
                    $ctrls = $(lead.apps.config.ctrlCls);

            if (numSelected === 1) {
                $ctrls.find('.delete, .edit').removeClass('ui-disabled');
                lead.apps.prepEdit($checked.closest('.appointment-row'));
            } else if (numSelected > 1) {
                $ctrls.find('.edit').addClass('ui-disabled');
                $ctrls.find('.delete').removeClass('ui-disabled');
            } else {
                $ctrls.find('.delete, .edit').addClass('ui-disabled');
            }

        },
        deleteSelected: function() {

            if (confirm('Are you sure you want to delete the selected appointment(s)?')) {

                var ids = [];
                $(lead.apps.config.chkbxCls + ':checked').each(function() {
                    ids.push($(this).closest('.appointment-row').attr('data-appointment_id'));
                });

                $.ajax({
                    url: helper.baseUrl + 'appointment/delete',
                    type: 'post',
                    dataType: 'json',
                    coname: $('.company-name').text(),
                    timeout:10000,
                    saveOnError:true,
                    data: {
                        urn: lead.urn,
                        ids: JSON.stringify(ids)
                    },
                    beforeSend: function() {
                        $.mobile.loading('show');
                    },
                    success: function(data) {
                        if (data.success) {
                            lead.apps.loadView();
                            lead.history.loadView();
                        } else {
                            alert(data.message);
                            $.mobile.loading('hide');
                        }
                    }
                }).fail(function(){ 
              lead.apps.loadView();
              });
            }

        },
        prepEdit: function($editing) {
            var $popup = $(lead.pageId + ' .edit-appointment-popup');
            if($editing.find('.app_type span').text()==="CAE"){
              $popup.find('#cae').prop('checked',true);
              $popup.find('#bde').prop('checked',false);
            } else {
              $popup.find('#bde').prop('checked',true);
              $popup.find('#cae').prop('checked',false);
            }
            $popup.find('.appointment_id').val($editing.attr('data-appointment_id'));
            $popup.find('.urn').val($editing.find('.urn span').text());
            $popup.find('.manager').val($editing.find('.attendee span').text());
            $popup.find('.title').val($editing.find('.title span').text());
            $popup.find('.comments').val($editing.find('.text span').text()).trigger('change');
            $popup.find('.app_status').val($editing.attr('.app_status span'));
            $popup.find('.begin_date').val($editing.find('.begin_date span').text());
            $popup.find('.start_time').val($editing.find('.start_time span').text());
            $popup.find('.finish_time').val($editing.find('.end_time span').text());
            $popup.find('select').selectmenu("refresh", true);
            $popup.find("input[name=status]").checkboxradio("refresh");
        }


    }, //End of apps

    policy: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .policy-info-container',
                chkbxCls: lead.pageId + ' .policy-chkbx',
                ctrlCls: lead.pageId + ' .policy-ctrls',
                formCls: lead.pageId + ' .policy-form',
                popupCls: lead.pageId + ' .policy-popup'
            };

            $(document).on('click', lead.policy.config.ctrlCls + ' .save', function() {
                lead.policy.save($(this));
            });

            $(document).on('change', lead.policy.config.chkbxCls, function() {
                lead.policy.toggleCtrlBtns();
            });

            $(document).on('click', lead.policy.config.ctrlCls + ' .delete', function() {
                lead.policy.deleteSelected();
            });

            $(document).on('change', lead.policy.config.formCls + ' .policy', function() {

                var $policy = $(lead.policy.config.formCls);

                if ($(this).val() === "") {
                    $policy.find(".other_policy").removeClass("ui-disabled").attr("name", "policy");
                    $policy.find(".other_policy:first-child").html("Please Select...");
                } else {
                    $policy.find(".other_policy").addClass("ui-disabled").val("").removeAttr("name");
                    $policy.find(".policy").attr("name", "policy");
                    $policy.find(".other_policy:first-child").html("Other...");
                }
            });

            this.loadView();

        },
        toggleCtrlBtns: function() {

            var $checked = $(lead.policy.config.chkbxCls + ':checked'),
                    numSelected = $checked.length,
                    $ctrls = $(lead.policy.config.ctrlCls);

            if (numSelected === 1) {
                $ctrls.find('.delete, .edit').removeClass('ui-disabled');
                lead.policy.prepEdit($checked.closest('.policy-row'));
            } else if (numSelected > 1) {
                $ctrls.find('.edit').addClass('ui-disabled');
                $ctrls.find('.delete').removeClass('ui-disabled');
            } else {
                $ctrls.find('.delete, .edit').addClass('ui-disabled');
            }

        },
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'leads/update_policy',
                type: 'post',
                dataType: 'json',
                data: $btn.closest('.policy-popup').find('form').serialize(),
                coname: $('.company-name').text(),
                timeout:10000,
                saveOnError:true,
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    if (data.success) {
                        lead.policy.loadView();
                        lead.history.loadView();
                        $(lead.policy.config.popupCls).popup('close');
                        reset_policy_form();
                    } else {
                        $.mobile.loading('hide');
                        alert(data.message);
                    }
                }
            }).fail(function(){ 
              lead.policy.loadView();
              });
        },
        deleteSelected: function() {

            if (confirm('Are you sure you want to delete the selected policy(s)?')) {

                var ids = [];
                $(lead.policy.config.chkbxCls + ':checked').each(function() {
                    ids.push($(this).closest('.policy-row').attr('data-policy_id'));
                });

                $.ajax({
                    url: helper.baseUrl + 'leads/delete_policy',
                    type: 'post',
                    dataType: 'json',
                    coname: $('.company-name').text(),
                    data: {
                        urn: lead.urn,
                        ids: JSON.stringify(ids)
                    },
                    timeout:10000,
                    saveOnError:true,
                    beforeSend: function() {
                        $.mobile.loading('show');
                    },
                    success: function(data) {
                        if (data.success) {
                            lead.policy.loadView();
                            lead.history.loadView();
                        } else {
                            alert(data.message);
                            $.mobile.loading('hide');
                        }
                    }
                }).fail(function(){ 
              lead.policy.loadView();
              });
            }

        },
        prepEdit: function($editing) {
            var $popup = $('#popupEditPolicy');
            $popup.find('.id').val($editing.attr('data-policy_id'));
            $popup.find('.urn').val($editing.find('.urn span').text()).attr('selected', true).siblings('option').removeAttr('selected');
            var policy = $editing.find('.policy span').text();
            if ($popup.find(".policy option[value='" + policy + "']").length > 0) {
                $popup.find('.policy').val($editing.find('.policy span').text()).attr("name", "policy");
                $popup.closest(".policy-form").find(".other_policy").addClass("ui-disabled");
                $popup.closest(".policy-form").find(".other_policy:first-child").html("Other...");
                $popup.closest(".policy-form").find(".other_policy").removeAttr("name");
            } else {
                $popup.find('.policy').val("").removeAttr("name");
                $popup.find('.other_policy').val($editing.find('.policy span').text()).removeClass("ui-disabled").attr("name", "policy");
            }
            $popup.find('.broker').val($editing.find('.broker span').text());
            $popup.find('.insurer').val($editing.find('.insurer span').text());
            $popup.find('.input-insurer').prev('form').find('input').val($editing.find('.insurer span').text());
            $popup.find('.input-insurer').val($editing.find('.insurer span').text());
            $popup.find('.input-broker').prev('form').find('input').val($editing.find('.broker span').text());
            $popup.find('.input-broker').val($editing.find('.insurer span').text());
            $popup.find('.premium').val($editing.find('.premium span').text());
            $popup.find('.renewal').val($editing.find('.renewal span').text());
            $('#popupEditPolicy select').selectmenu("refresh", true);
        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_policy_info_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.policy.config.ctCls).html(html).trigger('create');
                    lead.policy.autoComplete("broker");
                    lead.policy.autoComplete("insurer");
                }
            });

        },
        autoComplete: function($field) {
            $('body').on("click", function() {
                $(".input-" + $field).children('li').each(function() {
                    $(this).addClass("ui-screen-hidden");
                });
            });

            $(".input-" + $field).on("listviewbeforefilter", function(e, data) {
                var $ul = $(this),
                        $input = $(data.input),
                        value = $input.val(),
                        html = "";

                $ul.html("");
                $input.on("keyup", function() {
                    $(this).closest('.policy-form').find('.' + $field).val(value);
                });


                if (value && value.length > 2) {
                    $ul.html("<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>");
                    $ul.listview("refresh");
                    $.ajax({
                        url: helper.baseUrl + 'leads/' + $field,
                        type: "POST",
                        dataType: "json",
                        data: {
                            q: $input.val()
                        }
                    })
                            .then(function(response) {
                        $.each(response, function(i, val) {
                            html += "<li><a class='ac-value' href='#'>" + val + "</a></li>";
                        });
                        $ul.html(html);
                        $ul.listview("refresh");
                        $ul.trigger("updatelayout");
                        $('.ac-value').on("click", function() {
                            $(this).closest('.policy-form').find('.' + $field).val($(this).text());
                            $(this).closest('ul').prev('form').find('input').val($(this).text());
                            $(this).closest('[data-role=listview]').children().addClass('ui-screen-hidden');
                        });
                    });



                }
            });
        }

    },
    docs: {
        init: function() {

            this.config = {
                ctCls: lead.pageId + ' .documents-container',
                chkbxCls: lead.pageId + ' .file-chkbx',
                ctrlCls: lead.pageId + ' .file-ctrls'
            };

            $(document).on('change', lead.docs.config.chkbxCls, function() {
                lead.docs.toggleCtrlBtns();
            });

            $(document).on('click', lead.docs.config.ctrlCls + ' .delete', function() {
                lead.docs.deleteSelected();
            });

            $(document).on('click', lead.docs.config.ctrlCls + ' .download', function() {
                /*
                 * The only way to reload the history tab when a file is
                 * downloaded is to listen for the download button click and
                 * wait a few seconds, then force the history to update.
                 */
                setTimeout(function() {
                    lead.history.loadView();
                }, 5000);
            });

            this.loadView();

        },
        loadView: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/load_documents_view',
                type: 'post',
                dataType: 'html',
                data: {
                    urn: lead.urn
                },
                success: function(html) {
                    $.mobile.loading('hide');
                    $(lead.docs.config.ctCls).html(html).trigger('create');
                }
            });

        },
        toggleCtrlBtns: function() {

            if ($(lead.docs.config.chkbxCls + ':checked').length > 0) {
                $(lead.docs.config.ctrlCls + ' .download').button('enable');
                $(lead.docs.config.ctrlCls + ' .delete').button('enable');
            } else {
                $(lead.docs.config.ctrlCls + ' .download').button('disable');
                $(lead.docs.config.ctrlCls + ' .delete').button('disable');
            }

        },
        deleteSelected: function() {

            var files = [];
            $(lead.docs.config.chkbxCls + ':checked').each(function() {
                files.push($(this).val());
            });

            if (confirm('Are you sure you want to delete the selected file(s)')) {
                $.ajax({
                    url: helper.baseUrl + 'file/delete',
                    type: 'post',
                    dataType: 'json',
                    coname: $('.company-name').text(),
                    timeout:10000,
                    saveOnError:true,
                    data: {
                        urn: lead.urn,
                        files: files
                    },
                    beforeSend: function() {
                        $.mobile.loading('show');
                    },
                    success: function(data) {
                        if (data.success) {
                            lead.docs.loadView();
                            lead.history.loadView();
                        } else {
                            alert(data.message);
                            $.mobile.loading('hide');
                        }
                    }
                }).fail(function(){ 
              lead.docs.loadView();
              });
            }

        }

    }, //End of docs

    update: {
        init: function() {

            this.config = {
                formCls: lead.pageId + ' .update-form',
                popupCls: lead.pageId + ' .update-lead-popup',
                reasonCls: lead.pageId + ' .update-form select.reason',
                nextcallCls: lead.pageId + ' .update-form .nextcall-container',
                acturisCls: lead.pageId + ' .update-form .acturis-later',
                saveCls: lead.pageId + ' .update-lead-popup .save',
                actInputCls: lead.pageId + ' .update-form input.acturis'

            };

            $(document).on('change', lead.update.config.reasonCls, function() {
                lead.update.setForm();
            });

            $(document).on("click", lead.update.config.saveCls, function() {
                lead.update.save();
            });
             
            $(document).on('change', lead.update.config.acturisCls, function() {
                lead.update.setActurisInput();
            });
        },
        setActurisInput: function() {
            if ($(lead.update.config.acturisCls).prop("checked") === true) {
                $(lead.update.config.actInputCls).val("");
            }
        },
        setForm: function() {
            if ($(lead.update.config.reasonCls).val() === "no_selection_made") {
                $(lead.update.config.formCls).children('.removeTxt-container').addClass('hidden');
            } else if ($(lead.update.config.reasonCls).val() === "Quote Given" || $(lead.update.config.reasonCls).val() === "Existing Customer") {
                $(lead.update.config.formCls).children('.nextcall-container').hide();
                $(lead.update.config.formCls).children('.acturis-container').removeClass('hidden');
                $(lead.update.config.formCls).children('.removeTxt-container').addClass('hidden');
                $(lead.update.config.formCls).find('input[name="nextcall"]').val('');

            } else if ($(lead.update.config.reasonCls).val() === "Change Next Action" || $(lead.update.config.reasonCls).val() === "Not Interested" || $(lead.update.config.reasonCls).val() === "Contact Unavailable") {
                $(lead.update.config.formCls).children('.nextcall-container').show();
                $(lead.update.config.formCls).children('.acturis-container').addClass('hidden');
                $(lead.update.config.formCls).children('.removeTxt-container').addClass('hidden');
                $(lead.update.config.formCls).find('input[name="acturis"]').val('');
                
            } else {
                $(lead.update.config.formCls).children('.acturis-container').addClass('hidden');
                $(lead.update.config.formCls).children('.nextcall-container').hide();
                $(lead.update.config.formCls).children('.removeTxt-container').removeClass('hidden');
                $(lead.update.config.formCls).find('input[name="nextcall"],input[name="acturis"]').val('');
            }
        },
        checkForm: function() {
            if ($(lead.update.config.reasonCls).val() === "no_selection_made") {
                return "Please select the update reason";
            } else if ($(lead.update.config.reasonCls).val() === "Quote Given" || $(lead.update.config.reasonCls).val() === "Existing Customer") {
                if ($(lead.update.config.acturisCls).prop("checked") === false && $(lead.update.config.formCls).find('input[name="acturis"]').val() === '') {
                    return 'Please enter the acturis reference or tick "I don\'t have the reference"';
                }

            } else if ($(lead.update.config.reasonCls).val() === "Change Next Action" || $(lead.update.config.reasonCls).val() === "Not Interested" || $(lead.update.config.reasonCls).val() === "Contact Unavailable") {
                if ($(lead.update.config.formCls).find('input[name="nextcall"]').val() === '') {
                    return "Please enter a next action date";
                }
            } else {
                return false;
            }

        },
        save: function() {

            $.ajax({
                url: helper.baseUrl + 'leads/update_company_details',
                type: 'post',
                dataType: 'json',
                data: $(lead.update.config.formCls).serialize(),
                coname: $('.company-name').text(),
                timeout:10000,
                saveOnError:true,
                beforeSend: function() {
                    var check = lead.update.checkForm();
                    if (check) {
                        alert(check);
                        $.mobile.loading('hide');
                        return false;
                    } else {
                        $.mobile.loading('show');
                    }
                },
                success: function(data) {
                    if (data.success) {
                      if(data.reset){
                       $('.reset-record').removeClass('hidden');
                       $('.update-record').addClass('hidden');
                      }
                        $('.update-lead-popup').popup('close');
                        lead.geninfo.loadView();
                        lead.history.loadView();
                    } else {
                        $.mobile.loading('hide');
                        alert(data.message);
                    }
                }
            });

        }
    } // end of update class


};  //End of lead
