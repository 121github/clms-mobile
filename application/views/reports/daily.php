<?php if (count($stats) > 0): ?>   <h3>This table shows any <?php echo $type ?> changes on <?php echo $date ?></h3>


    <table data-role="table" data-mode="none" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th>URN</th>
                <th><?php echo ucfirst($type) ?></th>
                <th>Inserts</th>
                <th>Updates</th>
                <th>Deletes</th>
            </tr>
        </thead>
        <tbody>


            <?php foreach ($stats as $v) { ?>
                <tr>
                    <td><a href="<?php echo base_url(); ?>index.php/leads/detail/<?php echo $v['urn'] ?>"><?php echo $v['urn'] ?></a></td>
                    <td><?php echo substr($v["name"], 0, 50); ?></td>
                    <td><?php echo $v["inserted"]; ?>
                    </td>
                    <td><?php echo $v["updated"]; ?>
                    </td>            
                    <td><?php echo $v["deleted"]; ?></td>
                </tr>
            <?php } ?>
        </tbody>

    <?php else: ?>
        
         <ul data-inset="true" data-role="listview" class="listview-white">
        <li>
          There was no activity on <?php echo $date ?>
        </li>
        <li>
            <form action="../../activity"><button data-theme="b" type="submit" data-icon="back">Back to activity </button></form>
        </li>
    </ul>
 


    <?php endif; ?>