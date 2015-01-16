<div data-overlay-theme="a" data-role="popup" id="popupUpdateLead" class="update-lead-popup" data-position-to="window" data-clear_on_cancel="true">
    <a href="#" data-rel="back" data-role="button" data-theme="d" data-icon="delete" data-iconpos="notext" class="ui-btn-right close-btn">Close</a>
    <div class="popup-header">
        <h3>Update Record</h3>
    </div>
    <div class="popup-content">
        <form class="update-form">
            <input type="hidden" name="urn" value="<?php echo $urn; ?>">
            <label for="ul-costatus">Update Type</label>
            <select id="ul-costatus" class="reason" name="costatus">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="Change Next Action">Change Next Action</option>
                <option value="Quote Given">Quote Given</option>
                <option value="Existing Customer">Existing Customer</option>
                <option value="Duplicate Record">Duplicate Record</option>
                <option value="Contact Unavailable">Contact Unavilable</option>
                <option value="Not Interested">Not Interested</option>
                <option value="Supression Request">Supression Request</option>
                <option value="Ceased Trading">Ceased Trading</option>
                <option value="Bankrupt">Bankrupt</option>
                <option value="Telemarketing Only">Telemarketing Only</option>
            </select>
            
               <label for="ul-comments">Comments</label>
            <textarea name="comments" cols="40" rows="8"></textarea>
            <div class="nextcall-container">
            <label for="ul-nextcall">Next action date</label>
            <input id="ul-nextcall" name="nextcall" data-role="datebox" 
                data-clear-btn="true" data-options='{"mode":"calbox","calUsePickers": true,"useNewStyle":true, "closeCallback":"showPopup", "closeCallbackArgs":["#popupUpdateLead"]}' />
            <div class="error-txt nextcall_date pull-right">* Date is a required field</div>
            </div>
            <div class="acturis-container hidden">
            <label for="ul-acturis">Enter Acturis Reference</label>
            <input class="acturis" name="acturis" type="text" />
            
            <div data-role="fieldcontain">
            <fieldset data-role="controlgroup">
            <input class='acturis-later' id="acturis-later" name="acturis_later" type="checkbox" /><label for="acturis-later">I don't have the reference</label>
            </fieldset>  
            </div>
            <div class="error-txt acturis pull-right">* Acturis reference is required</div>
            </div>
            
            <div class="removeTxt-container red pull-right hidden">
            <p>This will remove the record from the prospector system</p>
            </div>
           <div style="clear:both"></div>
        </form>
    </div>
            
    <div class="popup-footer">
        <div align="right">
            <button data-theme="b" type="button" class="save" data-inline="true" data-mini="true">Update</button>
        </div>
    </div>
</div>