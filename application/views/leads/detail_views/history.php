<?php if(!empty($history)): ?>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th data-priority="1">Date</th>
                <th data-priority="1"><abbr title="Description">Desc</abbr></th>
                <th data-priority="1">Comments</th>
                <th data-priority="1">Outcome</abbr></th>
                <th data-priority="1">Next Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $item): ?>
                <tr>
                    <td><?php echo $item['fmtcontact']; ?></td>
                    <td><?php echo $item['description']; ?></td>
                    <td><?php if($item['log_id']){ ?><a href="#" class="show-log" id="<?php echo $item['log_id'] ?>">Details</a><?php } else { echo $item['comments']; } ?></td>
                    <td><?php echo $item['status']; ?></td>
                         <td><?php if(!empty($item['nextcall'])){ echo $item['nextcall']; } else { echo "-"; } ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-msg">No history available for this record</div>
<?php endif; ?>