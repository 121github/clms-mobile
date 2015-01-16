<div data-overlay-theme="a" data-role="popup" id="popupEditPolicy" class="policy-popup" data-position-to="window" data-clear_on_cancel="true">
    <a href="#" data-rel="back" data-role="button" data-theme="d" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
    <div class="popup-header">
        <h3>Edit Policy</h3>
    </div>
    <div class="popup-content">
        <form class="policy-form">
            <input type="hidden" name="fn" class="fn" value="edit">
            <input name="id" class="id" type="hidden" value="0">
            <input name="urn" class="urn" type="hidden" value="<?php echo $urn; ?>">
            <label for="policy">Policy</label>
            <select name="policy" class="policy">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="Property Package">Property Package</option>
                <option value="Commercial Combined">Commercial Combined</option>
                <option value="Liability">Liability</option>
                <option value="Motor Trade">Motor Trade</option>
                <option value="Fleet">Fleet</option>
                <option value="Commercial Vehicle">Commercial Vehicle</option>
                <option value="Personal Injury">Personal Injury</option>
                <option value="">Other</option>
            </select>


            <select class="other_policy ui-disabled">
                <option value="" name="other">Other...</option>
                <?php foreach ($options as $cat): ?>
                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="renewal">Renewal Date</label>
            <input name="renewal" class="renewal" type="text" data-role="datebox" data-clear-btn="true" 
                   data-options='{"mode":"calbox", "calUsePickers": true,"useNewStyle":true, "closeCallback":"showPopup", "closeCallbackArgs":["#popupEditPolicy"]}'>
            <label for="premium">Premium</label>
            <input name="premium" class="premium" type="number">
            <label for="insurer">Current Insurer</label>
            <input type="hidden" name="insurer" class="insurer">
            <p><ul  class="input-insurer" data-role="listview" data-inset="true" data-filter="true"></ul></p>
            <label for="broker">Current Broker</label>
            <input type="hidden" name="broker" class="broker">
            <p><ul class="input-broker" data-role="listview" data-inset="true" data-filter="true"></ul></p>
        </form>
    </div>
    <div class="popup-footer">
        <div data-role="controlgroup" data-type="horizontal" align="right" class="policy-ctrls">
            <a href="#" data-theme="c" data-role="button" data-rel="back" data-inline="true" data-mini="true" class="cancel-btn">Cancel</a>
            <button data-theme="b" type="button" class="save" data-inline="true" data-mini="true">Save</button>
        </div>
    </div>
</div>