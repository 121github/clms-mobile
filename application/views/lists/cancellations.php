<ul data-inset="true" data-role="listview" class="mi-listview listview-white">
    <li>
             <div class="ui-grid-a"> <div class="ui-block-a" style="width:10%">
            <button data-rel="back" data-icon="arrow-l" class="ui-btn-right back-button">Back</button></div>
            <div class="ui-block-b"  style="width:90%">
           <h3><?php echo $description ?></h3></div>
            </div>
            </li>
            </ul>

<?php if (count($appointments) > 0): ?>
    <ul class="planner-ct" data-role="listview" data-inset="true">
        <?php foreach ($appointments as $appointment): ?>
            <?php 
                $noaddress = false;
                $attendees = '';
                $appStart = $appointment['begin_date'] . " " . str_pad($appointment['begin_hour'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($appointment['begin_mins'], 2, "0", STR_PAD_LEFT) . ":00";
               
            ?>
            <li class="planner 
              <?php  echo "appointment-cancelled"; ?>"
                appointment-id="<?php echo $appointment['id']; ?>" id="<?php echo $appointment['urn']; ?>">
                <a href="<?php echo base_url() ?>index.php/leads/detail/<?php echo $appointment['urn']; ?>">
                    <h2>
                        <?php echo $appointment['coname']; ?>
                    </h2>
                    <p class="hidden">
                        <strong class="label">URN:</strong> 
                        <span class="urn"><?php echo $appointment['urn']; ?></span>
                    </p>
                      <p>
                        <strong class="label">BDE:</strong>
                        <span class="urn"><?php echo $appointment['bde']; ?></span>
                    </p>
                                          <p>
                        <strong class="label">CAE:</strong>
                        <span class="urn"><?php echo $appointment['cae']; ?></span>
                    </p>
                    <p>
                        <strong class="label">Date:</strong> 
                        <?php echo date('l jS F', strtotime($appointment['begin_date'])); ?>
                        <span style="display:none" class="appDate"><?php echo date('d/m/y', strtotime($appointment['begin_date'])); ?></span>
                        <span style="display:none" class="sqldate"><?php echo $appointment['begin_date'] ?></span> - 
                        <span class="appStartTime"> <?php echo date("h:i A", strtotime($appStart)); ?></span>
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
                                        <p>
                        <strong class="label">Reason for cancellation:</strong>
                        <span> <?php echo (!empty($appointment['reason'])?$appointment['reason']:"Not given"); ?></span>
                    </p>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>   



<?php else: ?> <!-- There we no results returned by the search -->
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li>
            No appointments could be found with the selected options.
        </li>
    </ul>
<?php endif; ?>