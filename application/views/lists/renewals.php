<ul data-inset="true" data-role="listview" class="mi-listview listview-white">
    <li>
             <div class="ui-grid-a"> <div class="ui-block-a" style="width:10%">
            <button data-rel="back" data-icon="arrow-l" class="ui-btn-right back-button">Back</button></div>
            <div class="ui-block-b"  style="width:90%">
           <h3><?php echo $description ?></h3></div>
            </div>
            </li>
            </ul>

<?php if (count($renewals) > 0): ?>
    <ul class="planner-ct" data-role="listview" data-inset="true">
        <?php foreach ($renewals as $renewal): ?>

            <li>
                <a href="<?php echo base_url() ?>index.php/leads/detail/<?php echo $renewal['urn']; ?>">
                    <h2>
                        <?php echo $renewal['coname']; ?>
                    </h2>
                    <p class="hidden">
                        <strong class="label">URN:</strong> 
                        <span class="urn"><?php echo $renewal['urn']; ?></span>
                    </p>
                    <p>
                        <strong class="label">Product:</strong>
                        <span><?php echo $renewal['type']; ?></span>
                    </p>
                      <p>
                        <strong class="label">Renewal Date:</strong>
                        <span><?php echo $renewal['date']; ?></span>
                    </p>
                    <p>
                        <strong class="label">Captured by:</strong>
                        <span> <?php echo $renewal['prospector']; ?></span> 
                    </p>
                    <p>
                        <strong class="label">Date captured:</strong>
                        <span> <?php echo $renewal['date_added']; ?></span>
                    </p>
                    <?php if(!empty($renewal['broker'])): ?>
                                        <p>
                        <strong class="label">Broker:</strong>
                        <span> <?php echo $renewal['broker']; ?></span>
                    </p>
                      <?php endif; ?>
                     <?php if(!empty($renewal['insurer'])): ?>
                                        <p>
                        <strong class="label">Insurer:</strong>
                        <span> <?php echo $renewal['insurer']; ?></span>
                    </p>
                     <?php endif; ?>
                    <?php if(!empty($renewal['premium'])): ?>
                                                  <p>
                        <strong class="label">Premium:</strong>
                        <span> <?php echo $renewal['premium']; ?></span>
                    </p>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>   
    
    <?php endif; ?>