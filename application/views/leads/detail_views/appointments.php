<?php if (!empty($appointments)): ?>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th class="hidden">URN</th>
                <th class="hidden">Begin Date</th>
                <th class="hidden">Start Time</th>
                <th class="hidden">Finish Time</th>
                <th data-priority="1">Type</th>
                <th data-priority="1">Date</th>
                <th data-priority="1">Title</th>
                <th data-priority="1">Comments</th>
                <th data-priority="1">Set By</abbr></th>
                <th data-priority="1">Attendee(s)</th>
                <th data-priority="1"></th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($appointments as $item): ?>
                <tr  class="appointment-row <?php if($item['cancelled']): echo "appointment-cancelled"; endif; ?>" data-appointment_id="<?php echo $item['id']; ?>">
                    <td class="urn hidden">
                        <span><?php echo $item['urn'] ?></span>
                    </td>
                   <td class="app_type">
                        <span <?php if(count($item['attendees'])<2):echo "style='color:red'";endif; ?>><?php echo ($item['status']=="BDE"?"BDE":"CAE") ?></span>
                    </td>
                    <td class="begin_date hidden">
                        <span><?php echo date('d/m/Y', strtotime($item['begin_date'])) ?></span>
                    </td>
                    <td class="start_time hidden">
                        <span><?php echo date('h:i A', strtotime($item['begin_hour'] . ":" . $item['begin_mins'])) ?></span>
                    </td>
                    <td class="end_time hidden">
                        <span><?php echo date('h:i A', strtotime($item['end_hour'] . ":" . $item['end_mins'])) ?></span>
                    </td>
                    <td class="date">
                        <span><?php echo date('d/m/Y H:i', strtotime($item['begin_date'] . " " . $item['begin_hour'] . ":" . $item['begin_mins'])); ?></span>
                    </td>
                    <td class="title">
                        <span><?php echo $item['title']; ?></span>
                    </td>
                    <td class="text">
                        <span><?php echo $item['text']; ?></span>
                    </td>
                    <td class="setby">
                        <span><?php echo $item['set_by']; ?></span>
                    </td>
<td class="attendees"><!--<span data-role="button" data-icon="edit" data-mini="true" class="attendee-btn">Attendees</span>-->
   
        <select class="select-attendees" app="<?php echo $item['id']; ?>" name="attendees[]" data-native-menu="false" data-mini="true" multiple="multiple"><option>Select Attendees</option>
                <?php foreach ($generalInfo['manager']['options'] as $attendee) { ?>
                    <option <?php if(in_array($attendee,$item['attendees'])): echo "selected"; endif; ?> value="<?php echo $attendee ?>"><?php echo $attendee ?></option>
                <?php } ?>
            </select>

</td>
<td class="chkbx-fixed">
                        <fieldset data-role="controlgroup" class="chkbx">
                            <input type="checkbox" name="appointments-chkbx" 
                                id="appointments-chkbx-<?php echo $item['id']; ?>" class="appointments-chkbx" data-iconpos="notext" data-appointment_id="<?php echo $item['id']; ?>">
                            <label for="appointments-chkbx-<?php echo $item['id']; ?>" data="test"></label>
                        </fieldset>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-msg">No appointments have been set for this record</div>
<?php endif; ?>

<div data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right appointments-ctrls">
    <a href="#popupAddApp" data-rel="popup" data-role="button" data-theme="b" class="add">Add</a>
    <a href="#" data-role="button" data-theme="b" class="ui-disabled delete">Delete</a>
    <a href="#popupEditApp" data-rel="popup" data-role="button" data-theme="b" class="ui-disabled edit">Edit</a>
</div>
<div class="float-push"></div>