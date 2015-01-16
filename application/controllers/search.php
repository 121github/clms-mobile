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
            <button id="use-my-location" class="locate-postcode" type="submit" data-icon="location" data-iconpos="right">Find my location</button>
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
            <?php foreach ($options['call_status'] as $call_status): ?>
                <option value="<?php echo $call_status; ?>" <?php echo set_select('call_status', $call_status); ?>>
                    <?php echo $call_status; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="lead_status" class="select">Lead Status</label>
        <select name="lead_status" id="lead_status">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['lead_status'] as $lead_status): ?>
                <option value="<?php echo $lead_status; ?>" <?php echo set_select('lead_status', $lead_status); ?>>
                    <?php echo $lead_status; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="employees">Employees</label>
        <select name="employees" id="employees">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['employees'] as $employees): ?>
                <option value="<?php echo $employees; ?>" <?php echo set_select('employees', $employees); ?>>
                    <?php echo $employees; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="turnover">Turnover</label>
        <select name="turnover" id="turnover">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['turnover'] as $turnover): ?>
                <option value="<?php echo $turnover; ?>" <?php echo set_select('turnover', $turnover); ?>>
                    <?php echo $turnover; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="insurance_type">Insurance Type</label>
        <select name="insurance_type" id="insurance_type">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['insurance_type'] as $insurance_type): ?>
                <option value="<?php echo $insurance_type; ?>" <?php echo set_select('insurance_type', $insurance_type); ?>>
                    <?php echo $insurance_type; ?>
                </option>
            <?php endforeach; ?>
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
            <?php foreach ($options['region'] as $region): ?>
                <option value="<?php echo $region; ?>" <?php echo set_select('region', $region); ?>>
                    <?php echo $region; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="manager">Manager</label>
        <select name="manager" id="manager">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['manager'] as $manager): ?>
                <option value="<?php echo $manager; ?>" <?php echo set_select('manager', $manager); ?>>
                    <?php echo $manager; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="activity_origin">Activity Origin</label>
        <select name="activity_origin" id="activity_origin">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['activity_origin'] as $activity_origin): ?>
                <option value="<?php echo $activity_origin; ?>" <?php echo set_select('activity_origin', $activity_origin); ?>>
                    <?php echo $activity_origin; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="sector">Sector / Industry</label>
        <select name="sector" id="sector">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['sector'] as $sector): ?>
                <option value="<?php echo $sector; ?>" <?php echo set_select('sector', $sector); ?>>
                    <?php echo $sector; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="lead_source">Lead Source</label>
        <select name="lead_source" id="lead_source">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['lead_source'] as $lead_source): ?>
                <option value="<?php echo $lead_source; ?>" <?php echo set_select('lead_source', $lead_source); ?>>
                    <?php echo $lead_source; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>

    <li data-role="fieldcontain" class="adv-search">
        <label for="quote_status">Quote Status</label>
        <select name="quote_status" id="quote_status">
            <option value="no_selection_made">Please make a selection...</option>
            <?php foreach ($options['quote_status'] as $quote_status): ?>
                <option value="<?php echo $quote_status; ?>" <?php echo set_select('quote_status', $quote_status); ?>>
                    <?php echo $quote_status; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>
    
        <li data-role="fieldcontain" class="adv-search">
        <label for="order_by">Order By</label>
        <select name="order_by" id="order_by">
            <?php foreach ($options['order_by'] as $order_by): ?>
                <option value="<?php echo $order_by; ?>" <?php echo set_select('order_by', $order_by); ?>>
                    <?php echo $order_by; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </li>
    
    <li>
        <button data-theme="b" type="submit">Search</button>
    </li>

</ul>

<?php echo form_close(); ?>
