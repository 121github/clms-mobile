<ul data-inset="true" data-role="listview" class="mi-listview listview-white">
  <li>
  <h4>Regional Appointments Export</h4>
    <form method="post" data-ajax="false" action="<?php echo base_url() ?>index.php/exports/regional_appointments">
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
          <select name="bde" class="prospector">
            <option value="">Select a prospector...</option>
            <?php foreach ($bde as $prospector): ?>
              <option value="<?php echo $prospector; ?>"><?php echo $prospector; ?></option>
            <?php endforeach; ?>
          </select> 
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <input class="date_from" value="<?php echo date('d/m/Y', strtotime('-1 days')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
        </div>
        <div class="ui-block-c" style="padding: 0 10px;">
          <input class="date_to" value="<?php echo date('d/m/Y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
        </div>
      </div><!-- /grid-bc -->
        <button data-theme="b" type="submit">Submit</button>
    </form>
  
  </li>
    <li>
  <h4>Renewals Export</h4>
    <form method="post" data-ajax="false" action="<?php echo base_url() ?>index.php/exports/renewals_export">
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
        A list of renewels captured by BDE's
        </div>
        
        <div class="ui-block-b" style="padding: 0 10px;">
          <input disabled class="date_from" value="<?php echo date('d/m/Y', strtotime('-1 days')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
        </div>
        <div class="ui-block-c" style="padding: 0 10px;">
          <input  disabled class="date_to" value="<?php echo date('d/m/Y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
        </div>
      </div><!-- /grid-bc -->
        <button data-theme="b"  value="1" type="submit">Export</button>
    </form>
  
  </li>
  
    <li>
  <h4>Renewals Without Address</h4>
    <form method="post" data-ajax="false" action="<?php echo base_url() ?>index.php/exports/renewals_noadd_export">
  
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
        An export of records that have renewals but incomplete addresses
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <input disabled class="date_from" value="<?php echo date('d/m/Y', strtotime('-1 days')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
        </div>
        <div class="ui-block-c" style="padding: 0 10px;">
          <input disabled class="date_to" value="<?php echo date('d/m/Y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
        </div>
      </div><!-- /grid-bc -->
      
        <button data-theme="b" value="1" type="submit">Export</button>
    </form>
  
  </li>
  
 
     <li>
  <h4>Renewals Without Telephone</h4>
    <form method="post" data-ajax="false" action="<?php echo base_url() ?>index.php/exports/renewals_notel_export">
  
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
        An export of records that have renewals but no telephone number
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <input disabled class="date_from" value="<?php echo date('d/m/Y', strtotime('-1 days')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
        </div>
        <div class="ui-block-c" style="padding: 0 10px;">
          <input disabled class="date_to" value="<?php echo date('d/m/Y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" 
                 data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
        </div>
      </div><!-- /grid-bc -->
      
        <button data-theme="b" value="1" type="submit">Export</button>
    </form>
  
  </li>
  </ul>