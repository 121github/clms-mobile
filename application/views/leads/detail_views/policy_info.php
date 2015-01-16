<?php if (!empty($policies)): ?>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th class="hidden">URN</th>
                <th data-priority="1">Policy Type</th>
                <th data-priority="1">Insurer</th>
                <th data-priority="1">Broker</th>
                <th data-priority="1">Premium</th>
                <th data-priority="1">Renewal Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($policies as $policy): ?>
                <tr class="policy-row" data-policy_id="<?php echo $policy['id']; ?>">
                    <td class="urn hidden">
                        <span><?php echo $policy['urn'] ?></span>
                    </td>
                    <td class="policy">
                        <span><?php echo $policy['type']; ?></span>
                    </td>
                    <td class="insurer">
                        <span><?php echo $policy['insurer']; ?></span>
                    </td>
                    <td class="broker">
                        <span><?php echo $policy['broker']; ?></span>
                    </td>
                    <td class="premium">
                        <span><?php echo $policy['premium']; ?></span>
                    </td>
                    <td class="renewal">
                        <span><?php echo $policy['date']; ?></span>
                    </td>
                    
                    <td class="chkbx-fixed">
                        <fieldset data-role="controlgroup" class="chkbx">
                            <input type="checkbox" name="policy-chkbx" id="policy-chkbx-<?php echo $policy['id']; ?>"
                                class="policy-chkbx" data-iconpos="notext" />
                            <label for="policy-chkbx-<?php echo $policy['id']; ?>" data="test"></label>
                        </fieldset>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-msg">No policies have been added for this record</div>
<?php endif; ?>

<div data-role="controlgroup" data-type="horizontal" data-mini="true" align="right" class="policy-ctrls">
    <a href="#popupAddPolicy" data-rel="popup" data-role="button" data-theme="b" class="add">Add</a>
    <a href="#" data-role="button" data-theme="b" class="ui-disabled delete">Delete</a>
    <a href="#popupEditPolicy" data-rel="popup" data-role="button" data-theme="b" class="ui-disabled edit">Edit</a>
</div>
    
<?php $this->view('popups/add_policy_popup', array('urn' => $urn, 'options' => $options)); ?>
<?php $this->view('popups/edit_policy_popup', array('urn' => $urn, 'options' => $options)); ?>