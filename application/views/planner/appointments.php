<ul data-inset="true" data-role="listview" class="mi-listview listview-white">
    <li>
        <form data-ajax="false">
            <div class="ui-grid-c">
                <div class="ui-block-a" style="padding: 0 4px;">
                    <select name="set_by">
                        <option value="">Set by...</option>
                        <?php foreach ($prospectors as $prospector): ?>
                            <option <?php if($options['set_by']===$prospector){ echo "selected"; } ?> value="<?php echo $prospector; ?>"><?php echo $prospector; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </div>
                <div class="ui-block-b" style="padding: 0 4px;">
                    <input type="hidden" id="attendee_val" value="<?php echo $options['attendee'] ?>">
                    <select name="attendee">
                        <option value="">Attendees...</option>
                    </select> 
                </div>
                <div class="ui-block-c" style="padding: 0 10px;">
                     <input name="date_from" type="text" data-role="datebox" data-clear-btn="true" 
                        data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from..." value="<?php echo $options['date_from'] ?>">
                </div>
                <div class="ui-block-d" style="padding: 0 10px;">
                    <input name="date_to" type="text" data-role="datebox" data-clear-btn="true" 
                        data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to..." value="<?php echo $options['date_to'] ?>">
                </div>
            </div><!-- /grid-bc -->
        
        <button data-theme="b" type="submit">Submit</button>
        </form>
    </li>

<?php if (count($appointments) > 0): ?>
    <ul class="planner-ct" data-role="listview" data-inset="true">
        <?php foreach ($appointments as $appointment): ?>
            <?php 
                $noaddress = false;
                $attendees = '';
                $appStart = $appointment['begin_date'] . " " . str_pad($appointment['begin_hour'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($appointment['begin_mins'], 2, "0", STR_PAD_LEFT) . ":00";
                $appEnd = $appointment['begin_date'] . " " . str_pad($appointment['end_hour'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($appointment['end_mins'], 2, "0", STR_PAD_LEFT) . ":00";
            ?>
            <li class="planner 
              <?php if($appointment['type']=="CAE"): echo "cae-app"; endif; ?>
              <?php if($appointment['cancelled']=="1"): echo "appointment-cancelled"; endif; ?>"
                appointment-id="<?php echo $appointment['event_id']; ?>" id="<?php echo $appointment['urn']; ?>">
                <a href="<?php echo base_url() ?>index.php/leads/detail/<?php echo $appointment['urn']; ?>">
                    <h2>
                        <?php echo $appointment['coname']; ?>
                    </h2>
                    <p class="hidden">
                        <strong class="label">URN:</strong> 
                        <span class="urn"><?php echo $appointment['urn']; ?></span>
                    </p>
                      <p>
                        <strong class="label">Attendees:</strong>
                        <span><?php if(!empty($appointment['attendees'][0])): foreach($appointment['attendees'] as $attendee){ $attendees .= $attendee.", "; } echo rtrim($attendees,", "); else: echo $appointment['appointment_owner']; endif; 
                        if(!empty($appointment['tba'])):
                          echo ", <span style='color:red'>CAE to be allocated</span>";
                          endif;
                        ?></span>
                    </p>
                    <p>
                        <strong class="label">Date:</strong> 
                        <?php echo date('l jS F', strtotime($appointment['begin_date'])); ?>
                        <span style="display:none" class="appDate"><?php echo date('d/m/y', strtotime($appointment['begin_date'])); ?></span>
                        <span style="display:none" class="sqldate"><?php echo $appointment['begin_date'] ?></span> - 
                        <span class="appStartTime"> <?php echo date("h:i A", strtotime($appStart)); ?></span> - 
                        <span class="appEndTime"><?php echo date("h:i A", strtotime($appEnd)); ?></span>
                    </p>
                    <p>
                        <strong class="label">Address:</strong> 
                        <?php //if we have an address show all the parts or show no address
                        if(empty($appointment['p_add1'])):echo "No valid address details"; else:
                        echo $appointment['p_add1'];
                       if(!empty($appointment['p_town'])): echo ", ".$appointment['p_town']; endif;
                       if(!empty($appointment['p_postcode'])): echo ", ".$appointment['p_postcode']; endif;
                       endif; ?>
                    </p>
                    <p>
                        <strong class="label">Details:</strong>
                        <span class="appTitle"> <?php echo $appointment['title']; ?></span> - 
                        <span class="appText"> <?php echo $appointment['text']; ?></span>
                    </p>
  <div class="ui-li-aside">
                  <?php if (isset($appointment['distance'])): ?>
              <p><strong><?php echo number_format($appointment['distance'], 2); ?></strong> (miles)</p>
                  <?php endif; ?>
              <p class="in-jplanner">
                <?php if (!is_null($appointment['plan_id'])): ?>
                  Available in Journey Planner
              <?php endif; ?>
              </p>

          </div>
                </a>
                <a href="#app-options-popup" data-rel="popup" data-icon="gear" data-theme="d" class="action-btn">Action</a>
            </li>
        <?php endforeach; ?>
    </ul>   

    <div data-role="popup" id="app-options-popup" class="options-popup">
        <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="b">
            <li><a href="#popupEditApp" data-rel="popup">Edit Appointment</a></li>
            <li><a class="plannerBtn">Add to planner</a></li>
            <li><a id="diaryBtn">View in diary</a></li>
        </ul>
    </div>

    <?php $this->view('popups/edit_appointment_popup'); ?>

<?php else: ?> <!-- There we no results returned by the search -->
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li>
            No appointments could be found with the selected options.
        </li>
    </ul>
<?php endif; ?>

<script type="text/javascript">
     $(document).on('pageshow', '#' + '<?php echo $pageId; ?>', function () {
        update_attendee_options();    
        
        function update_attendee_options(){
   var set_by = $("select[name='set_by']").val();
   $.ajax({url:helper.baseUrl + "planner/get_attendee_options",
       type:"POST",
       dataType:"JSON",
       data:{value:set_by},
       beforeSend:function(){
        $("select[name='attendee']").html("<option value=''>Loading...</option>").selectmenu('refresh');
       },
       success:function(response){
        $("select[name='attendee']").html("<option value=''>Attendees...</option>");
   if(response.length===0){
       $("select[name='attendee']").append("<option value=''>None Found...</option>");
   } else {
       $.each(response,function(i,row){ $("select[name='attendee']").append("<option value='"+row.attendee+"'>"+row.attendee+"</option>");
       });
   }
   $("select[name='attendee']").selectmenu('refresh');
   $("select[name='attendee']").val($('#attendee_val'));
    }
    });
  }
  
    $(document).on("change","select[name='set_by']",function(){ update_attendee_options() });
    
    $(document).on('click', '.action-btn', function() {
        var $li         = $(this).closest('li.planner'), 
            urn         = $li.attr('id'),
            $popup      = $('.edit-appointment-popup'),
            $plannerBtn = $('#app-options-popup .plannerBtn').attr('data-urn', urn);
            
        console.log($li);
        
        if ($.trim($li.find('.in-jplanner').text()) !== '') {
            $plannerBtn
                .text('Remove from Planner')
                .addClass('removeFromPlanner')
                .removeClass('addToPlanner');
        } else {
            $plannerBtn
                .text('Add to Planner')
                .addClass('addToPlanner')
                .removeClass('removeFromPlanner');
        }
        $('#diaryBtn').attr('href', helper.baseUrl + 'diary/day/' + $li.find('.sqldate').text());
        //Setup the edit appointment popup values.
        $popup.find('.appointment_id').val($li.attr('appointment-id'));
        $popup.find('.urn').val(urn);
        $popup.find('.title').val($.trim($li.find('.appTitle').text()));
        $popup.find('.comments').val($.trim($li.find('.appText').text())).trigger('change');
        $popup.find('.begin_date').val($li.find('.appDate').text());
        $popup.find('.start_time').val($li.find('.appStartTime').text());
        $popup.find('.finish_time').val($li.find('.appEndTime').text());
    });
        
    });
    
  
</script>