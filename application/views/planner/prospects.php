<?php if (count($prospects) > 0): ?>
  <div data-role="popup" id="plan-top-options-popup" class="options-popup">
    <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="b">
      <li><a id="use-my-location" class="locate-postcode">Find my location</a></li>
      <li data-iconpos="right">
        <a href="<?php echo base_url() . "index.php/planner/printable/$sqldate"; ?>" data-ajax="false">Print View</a>
      </li>
      <li><a class="clear-planner">Clear Planner</a></li>
    </ul>
  </div>
  <ul id="location-panel" data-inset="true" data-role="listview" class="listview-white">
    <li style="padding: 8px 12px">
      <strong>Current Location:</strong> 
      <span class="current_postcode">
        <?php echo $_SESSION['current_postcode']; ?>
      </span>
    </li>
    <li style="padding: 5px">
      <fieldset class="ui-grid-b">
        <div class="ui-block-a">
          <a data-role="button" href="#plan-top-options-popup" data-rel="popup" data-icon="gear" data-theme="d" class="action-btn">Options</a>
        </div>
        <div class="ui-block-b">
          <button class="show-on-map" data-fn="show" type="submit" 
                  data-icon="map-ico-btn" data-iconpos="right">View all on Map</button>
        </div>

        <div class="ui-block-c">
          <input <?php if (!empty($date)): echo "value='$date'";
      endif ?> name="date" id="plan-date" class="plan-date" type="text" data-role="datebox" data-clear-btn="true" 
                                                                      data-options='{"mode":"calbox", "useNewStyle":true,"usePlaceholder":"Select Date...","useClearButton":true}'>
        </div>
      </fieldset>
      <div class="error geolocation-error"></div>
    </li>
  </ul>

  <ul id="planner-ct" data-role="listview" data-inset="true">
  <?php foreach ($prospects as $prospect): $noaddress = false; ?>
      <li class="planner" id="<?php echo $prospect['urn']; ?>" data-postcode="<?php echo $prospect['p_postcode']; ?>">
        <a href="<?php echo base_url() ?>index.php/leads/detail/<?php echo $prospect['urn']; ?>">
          <h2 class="coname"><?php echo $prospect['coname']; ?></h2>
          <p><strong class="label">URN:</strong> <span class="urn"><?php echo $prospect['urn']; ?></span></p>
          <p><strong class="label">Date:</strong> <span class="date">
    <?php if ($prospect['date']): ?>
        <?php echo date('d/m/y', strtotime($prospect['date'])); ?>
    <?php else: ?>
        Not Set
    <?php endif; ?></span></p>

          <p>
            <strong class="label">Address:</strong> 
              <?php if ($prospect['p_postcode'] && $prospect['p_add1']): ?>
              <span class="address">
                <?php
                echo $prospect['p_add1'] . ', ' .
                $prospect['p_town'] . ', ' .
                $prospect['p_postcode'];
                ?>
              </span>
              <span class="postcode hidden"><?php echo $prospect['p_postcode']; ?></span>
          <?php else: $noaddress = true; ?>
              <span style='color:red'>No Address</span>
    <?php endif; ?>
          </p>
          <?php if (isset($prospect['distance'])): ?>
            <p class="lv-rt">
              <strong><?php echo number_format($prospect['distance'], 2); ?> (miles)</strong>
            </p>
    <?php endif; ?>

          <div class="prospect-sort-btns" data-role="controlgroup" data-type="horizontal" data-mini="true">
            <button data-icon="arrow-u" data-iconpos="notext" class="sort-up">Move Up</button>
            <button  data-icon="arrow-d" data-iconpos="notext" class="sort-down">Move Down</button>
          </div>

        </a>
        <?php if ($noaddress): ?>
          <a class="plannerBtn removeFromPlanner" href="#" data-urn="<?php echo $prospect['urn']; ?>" data-icon="delete" data-theme="d">Remove from planner</a>
      <?php else: ?>
          <a href="#plan-options-popup" data-rel="popup" data-icon="map-ico" data-theme="d" class="action-btn">Action</a>
    <?php endif; ?>
      </li>
  <?php endforeach; ?>
  </ul>

  <div data-role="popup" id="plan-options-popup" class="options-popup">
    <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="b">
      <li><a id="map">View on map</a></li>
      <li><a id="directions">Get directions</a></li>
      <li><a class="plannerBtn setDate">Set date</a>
      </li>
      <li><a class="plannerBtn removeFromPlanner">Remove from planner</a></li>
    </ul>
  </div>

<?php else: ?> <!-- There we no results returned by the search -->

  <ul data-inset="true" data-role="listview" class="listview-white">
    <li>Sorry, there is nothing in your journey planner on this day.</li>
    <li>
      <form data-ajax="false" data-rel="<?php echo "/index.php/planner/prospects"; ?>">
        <button data-theme="b" type="submit" data-icon="back">Go Back</button>
      </form>
    </li>
  </ul>

<?php endif; ?>
<div class="hidden">
  <input id="date-holder" data-role="datebox" <?php if (!empty($sqldate)): echo "value='$sqldate'";
      endif ?>
         data-options='{"mode":"calbox","noButton": true,"centerHoriz": true, "closeCallback":"setDate"}'>
</div>
<script type="text/javascript">

  $(document).on('pageinit', '#prospect-planner', function() {

    plansort.init();

    $('#plan-date').on('datebox', function(e, p) {
      if (p.method === 'set') {
        window.location.href = helper.baseUrl + 'planner/prospects/' + $('#plan-date').datebox('callFormat', '%Y-%m-%d', p.date);
      }
      if (p.method === 'clear') {
        window.location.href = helper.baseUrl + 'planner/prospects';
      }
    });

    if ($.trim($('.current_postcode').text()) === '') {
      getLocation();
    }

    $('.action-btn').click(function() {
      var $row = $(this).closest('.planner'),
              daddr = $.trim($row.find('.postcode').text()),
              saddr = $.trim($('.current_postcode').text()),
              platform = navigator.platform,
              mapLink = 'http://maps.google.com/';
      $('.options-popup .plannerBtn').attr('data-urn', $row.attr('id'));
      if (platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
        mapLink = 'comgooglemaps://';
      }
      $('#map').attr('href', mapLink + '?q=' + daddr);
      $('#directions').attr('href', mapLink + '?zoom=2&saddr=' + saddr + '&daddr=' + daddr);
    });

    $('.show-on-map').click(function() {
      var $btn = $(this);
      if ($btn.attr('data-fn') === 'show') {
        gmap.init();
        $btn.attr('data-fn', 'hide').siblings('.ui-btn-inner').children('.ui-btn-text').text('Hide Map');
      } else {
        $('.prospect-map-ct').remove();
        $('#location-panel').listview('refresh');
        $btn.attr('data-fn', 'show').siblings('.ui-btn-inner').children('.ui-btn-text').text('View all on Map');
      }
      return false;
    });

    return false;
  });

</script>