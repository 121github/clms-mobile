<?php if (!empty($leads)): ?>
  <ul id="results-ct" data-role="listview" data-inset="true">
    <?php foreach ($leads as $lead): ?>
      <li class="result <?php if (!is_numeric($lead['prospector']) && !is_null($lead['prospector']) && $lead['prospector'] <> $_SESSION['login']): echo 'lead-is-owned';
    elseif (!is_numeric($lead['prospector']) && !is_null($lead['prospector']) && $lead['prospector'] == $_SESSION['login']): echo 'lead-is-mine';
    endif; ?>">
        <a href="detail/<?php echo $lead['urn']; ?>" class="hreflink" id="<?php echo $lead['urn']; ?>">
          <h2><?php echo $lead['coname']; ?></h2>
            <?php if (!empty($lead['nextcontact'])): ?>
            <p><strong class="label">Next Action:</strong> <?php
              echo $lead['nextcontact_formatted'];
              echo " [" . $lead['nextcall_days'] . "]";
              ?></p>
            <?php endif; ?>
            <?php if (!empty($lead['date_updated'])): ?>
            <p><strong class="label">Last Action:</strong> <?php
              echo $lead['date_updated_formatted'];
              echo " [" . $lead['lastcall_days'] . "]";
              ?></p>
            <?php endif; ?>
            <?php if (!empty($lead['nearest_renewal'])): ?>
            <p><strong class="label">Closest Renewal:</strong> <?php
        echo date('jS M', strtotime($lead['nearest_renewal']));
        ?></p><?php endif; ?>
          <p><strong class="label">Postcode:</strong> <?php echo $lead['postcode']; ?></p>

          <!-- If we have a distance, show it -->
          <div class="ui-li-aside">
                  <?php if (isset($lead['distance'])): ?>
              <p><strong><?php echo number_format($lead['distance'], 2); ?></strong> (miles)</p>
                  <?php endif; ?>
              <p class="in-jplanner">
                <?php if (!is_null($lead['plan_id'])): ?>
                  Available in Journey Planner
              <?php endif; ?>
              </p>

          </div>
        </a>
        <a href="#results-popup" data-rel="popup" data-icon="gear" class="action-btn"
           data-urn="<?php echo $lead['urn']; ?>" data-plan_id="<?php echo $lead['plan_id']; ?>">Action</a>
      </li>
  <?php endforeach; ?>
  </ul>

  <div data-role="popup" id="results-popup">
    <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="b">
      <li><a class="plannerBtn">Add to Planner</a></li>
      <li><a href="#popupAddApp" data-rel="popup">Create Appointment</a></li>
    </ul>
  </div>

  <?php $this->view('popups/add_appointment_popup'); ?>





<?php else: ?> <!-- There we no results returned by the search -->

  <ul data-inset="true" data-role="listview" class="listview-white">
    <li>
      Sorry, there are no results matching your search criteria.
    </li>
    <li>
      <form action="search"><button data-theme="b" type="submit" data-icon="back">Back to Search Leads</button></form>
    </li>
  </ul>

<?php endif; ?>
<?php if ($total > 50): //only show pager if we need it  ?>
  </div><!--this </div> is important because it puts the footer outside the body in the template -->
  <div data-role="footer" class="footer" data-tap-toggle="false" data-position="fixed">
    <div class="ui-grid-b">
      <div class="ui-block-a" >
        <a class="prev-page"  <?php if ($pagenum < 2): ?>style="display:none"<?php endif ?> href="#" page ="<?php echo $pagenum - 1 ?>" data-role="button" data-icon="arrow-l">Previous Page</a> 
      </div>
      <div class="ui-block-b">
        <select id="page-selector" name="page">
          <?php foreach ($page_array as $page => $desc) { ?>
            <option value="<?php echo $page + 1 ?>">Page <?php echo $page + 1 . ". Showing " . $desc ?></option>
  <?php } ?>
        </select>
      </div>
      <div class="ui-block-c">
        <a href="#" page ="<?php echo $pagenum + 1 ?>" data-role="button" data-iconpos="right" data-icon="arrow-r" class="next-page">Next Page</a> 
      </div>
    </div>
<?php endif; ?>
  <script type="text/javascript">
    $(document).on('pageinit', '#search-results', function() {
      paginator.init('<?php echo $total; ?>');
      $('[data-position=fixed]').fixedtoolbar({tapToggle: false});
      /**
       * Listener for the search_results page action button for each row.
       * On clicking the button for a row, clear down the add appointment popup & add
       * the urn. Also set the planner button text and add the urn property to it.
       * This is done with the assumption that they will click either the planner btn
       * or the add appointment btn.
       */
      $(document).on('click', '#results-ct .action-btn', function() {
        var $actionBtn = $(this),
                $popup = $('#search-results .add-appointment-popup'),
                urn = $actionBtn.attr('data-urn'),
                $plannerBtn = $('.plannerBtn');

        $popup.find('form')[0].reset();
        $popup.find('.error-txt').hide();
        $popup.find('.urn').val(urn);
        //Set up the planner button
        $plannerBtn.attr('data-urn', urn);

        if ($.trim($actionBtn.closest('li.result').find('.in-jplanner').text()) != '') {
          $plannerBtn
                  .text('Remove from Planner')
                  .addClass('removeFromPlanner')
                  .removeClass('addToPlanner');
        } else {
          $plannerBtn
                  .text('Add to Planner')
                  .addClass('addToPlanner')
                  .removeClass('removeFromPlanner');
        }
      });

    });
  </script>
