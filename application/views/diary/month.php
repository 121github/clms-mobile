<div id="calendar-container">
    <div class="calendar">
      <div class="cal-select">
<select class="select-regions" name="region" data-mini="true">
  <option>Select Region</option>
                <?php foreach ($regions as $region) { ?>
                    <option <?php if($region==$_SESSION['diary_region']): echo "selected"; endif; ?> value="<?php echo $region ?>"><?php echo $region ?></option>
                <?php } ?>
            </select>          
      </div>
       <div class="cal-select">     
<select class="select-attendees" name="attendees[]" data-native-menu="false" data-mini="true" multiple="multiple"><option>Select Attendees</option>
                <?php foreach ($managers as $attendee) { ?>
                    <option <?php if(in_array($attendee,$_SESSION['diary_attendees'])): echo "selected"; endif; ?> value="<?php echo $attendee ?>"><?php echo $attendee ?></option>
                <?php } ?>
            </select>
</div>
      <div class="cal-select">
        <button class="update-calender" data-mini="true" data-role="button">Update Calendar</button>
      </div>
        <div data-inline="true" id="calendar-controls" data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right">
			<a href="#" class="prev" data-role="button" data-theme="b"><?php echo date('M', strtotime('-1 month')); ?></a>
			<a href="#popupChangeMonth" class="curr" data-rel="popup" data-role="button" data-theme="b"><?php echo $dateStr; ?></a>
			<a href="#" class="next" data-role="button" data-theme="b"><?php echo date('M', strtotime('+1 month')); ?></a>
		</div>
        
        <div class="float-push"></div>
        <table>
            <thead><tr><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th></tr></thead>
            <tbody>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
                <tr><td></td><td></td><td></td><td></td><td></td><td class="weekend"></td><td class="weekend"></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div data-role="popup" id="popupChangeMonth" data-theme="d" data-position-to="window" class="ui-corner-all">
    <a href="#" data-rel="back" data-role="button" data-theme="d" data-icon="delete" 
       data-iconpos="notext" class="ui-btn-right">Close</a>
    <div style="padding: 10px; width: 150px;">
        <select name="select-month" id="select-month" data-mini="true">
        <?php foreach ($dateSelection['months'] as $key => $val): ?>
            <option <?php if ($key == $dateSelection['currentMonth']) echo 'selected="selected"'; ?> 
                value="<?php echo $key; ?>"><?php echo $val; ?></option>
        <?php endforeach; ?>
        </select>
         <select name="select-year" id="select-year" data-mini="true">
        <?php foreach ($dateSelection['years'] as $year): ?>
            <option <?php if ($year == $dateSelection['currentYear']) echo 'selected="selected"'; ?> 
                value="<?php echo $year; ?>"><?php echo $year; ?></option>
        <?php endforeach; ?>
        </select>
        <button id="change-month-btn" data-theme="b" data-mini="true">Select</button>
    </div>
</div>

<script type="text/javascript">
    $(document).on('pageinit', '#diary-manager-month',  function () {

        calendar.init($.parseJSON('<?php echo $appointments; ?>'), '<?php echo $today; ?>');
        return false;
    });
</script>
