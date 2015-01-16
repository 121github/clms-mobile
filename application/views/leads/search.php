<?php echo validation_errors(); ?>

<?php echo form_open('leads/search', array('data-ajax' => 'false', 'onsubmit' => "$.mobile.loading( 'show')")); ?>

<ul data-inset="true" data-role="listview" class="listview-white">

    <li data-role="fieldcontain">
        <label for="urn">URN</label>
        <input type="text" name="urn" id="urn" data-clear-btn="true" 
               value="<?php echo set_value('urn'); ?>"/>
    </li>

    <li data-role="fieldcontain">
        <label for="coname">Company Name</label>
        <input type="text" name="coname" id="coname" data-clear-btn="true" 
               value="<?php echo set_value('coname'); ?>"/>
    </li>

    <li>
        <div data-role="fieldcontain">
            <label class="ui-input-text" for="postcode">Postcode</label>
            <input type="text" name="postcode" id="postcode_input" class="current_postcode_input" data-clear-btn="true" />
        </div>
        <div data-role="fieldcontain">
            <label class="ui-input-text" for="use-my-location">Current Location</label>
            <a id="use-my-location" href="#" class="ui-submit locate-postcode" type="button" data-icon="location" data-iconpos="right">Find my location</a>
            <div class="error geolocation-error"></div>
        </div>
        <div data-role="fieldcontain">
            <label for="postrange" class="select">Search Radius (miles)</label>
            <select name="postrange" id="postrange">
                <option value="no_selection_made">Please make a selection...</option>
                <?php foreach ($options['postrange'] as $postrange): ?>
                    <option value="<?php echo $postrange; ?>" <?php echo set_select('postrange', $postrange); ?>>
                        <?php echo $postrange; ?>
                    </option>
                <?php endforeach; ?>
            </select> 
        </div>
    </li>

    <!-- Advanced search switch -->
    <li data-role="fieldcontain">
        <label class="ui-slider" for="adv-search-switch">Advanced Search</label>
        <select id="adv-search-switch" name="adv_search_switch" class="ui-slider-switch" data-role="slider">
            <option value="off">Off</option>
            <option value="on">On</option>
        </select>
    </li>

    <!-- Advanced search fields -->

    <li data-role="fieldcontain" class="adv-search">
        <label for="acturis">Acturis Reference</label>
        <input type="text" name="acturis" id="acturis" data-clear-btn="true" 
               value="<?php echo set_value('acturis'); ?>"/>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname" data-clear-btn="true" 
               value="<?php echo set_value('lastname'); ?>"/>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="call_status" class="select">Call Status</label>
        <select name="call_status" id="call_status">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="lead_status" class="select">Lead Status</label>
        <select name="lead_status" id="lead_status">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="employees">Employees</label>
        <select name="employees" id="employees">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="turnover">Turnover</label>
        <select name="turnover" id="turnover">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="insurance_type">Insurance Type</label>
        <select name="insurance_type" id="insurance_type">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <div data-role="fieldcontain">
            <label class="fordatefield" for="lastcontact_from">Last Contacted (from)</label>
            <input name="lastcontact_from" id="lastcontact_from" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
        <div data-role="fieldcontain">
            <label class="fordatefield" for="lastcontact_to">Last Contacted (to)</label>
            <input name="lastcontact_to" id="lastcontact_to" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <div data-role="fieldcontain">
            <label class="fordatefield" for="renewal_from">Renewal Date (from)</label>
            <input name="renewal_from" id="renewal_from" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
        <div data-role="fieldcontain">
            <label class="fordatefield" for="renewal_to">Renewal Date (to)</label>
            <input name="renewal_to" id="renewal_to" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <div data-role="fieldcontain">
            <label class="fordatefield" for="leadadd_from">Lead Added (from)</label>
            <input name="leadadd_from" id="leadadd_from" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
        <div data-role="fieldcontain">
            <label class="fordatefield" for="leadadd_to">Lead Added (to)</label>
            <input name="leadadd_to" id="leadadd_to" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <div data-role="fieldcontain">
            <label class="fordatefield" for="nextcontact_from">Next Contact (from)</label>
            <input name="nextcontact_from" id="nextcontact_from" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
        <div data-role="fieldcontain">
            <label class="fordatefield" for="nextcontact_to">Next Contact (to)</label>
            <input name="nextcontact_to" id="nextcontact_to" type="text" data-role="datebox" data-clear-btn="true"
                   data-options='{"mode":"calbox", "useNewStyle":true}'/>
        </div>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="region">Region</label>
        <select name="region" id="region">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>
    <li data-role="fieldcontain" class="adv-search">
        <label for="manager">Prospector</label>
        <select name="prospector" id="prospector">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>
    <li data-role="fieldcontain" class="adv-search">
        <label for="manager">Manager</label>
        <select name="manager" id="manager">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="activity_origin">Activity Origin</label>
        <select name="activity_origin" id="activity_origin">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="sector">Sector / Industry</label>
        <select name="sector" id="sector">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="lead_source">Lead Source</label>
        <select name="lead_source" id="lead_source">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="quote_status">Quote Status</label>
        <select name="quote_status" id="quote_status">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>
    
        <li data-role="fieldcontain" class="adv-search">
        <label for="order_by">Order By</label>
        <select name="order_by" id="order_by">
            <option value="no_selection_made">Please make a selection...</option>
        </select>
    </li>
    
    <li>
        <button data-theme="b" type="submit">Search</button>
    </li>

</ul>
 
<?php echo form_close(); ?>
 </div>