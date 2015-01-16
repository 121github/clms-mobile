<ul id="location-panel" data-inset="true" data-role="listview" class="listview-white">
  <li class="prospect-map-ct"><div id="map-canvas"/></div></li> 
</ul>

<ul id="results-ct" data-role="listview" data-inset="true">
  <?php foreach ($prospects as $prospect): $noaddress = false; ?>
    <li class="results" id="<?php echo $prospect['urn']; ?>" data-postcode="<?php echo $prospect['p_postcode']; ?>">
      <a href="<?php echo base_url() ?>index.php/leads/detail/<?php echo $prospect['urn']; ?>">
        <h2 class="coname"><?php echo $prospect['coname']; ?></h2>
        <p><strong class="label">URN:</strong> <span class="urn"><?php echo $prospect['urn']; ?></span></p>

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



      </a>
    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">

  $(document).on('pageshow', '#view-regions', function() {
    $('#map-canvas').height($(window).height() - 340);

    function initialize() {
      var manchester = new google.maps.LatLng(53.479324, -2.248485);
      var mapOptions = {
        zoom: 8,
        center: manchester
      };

      var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);



      var ctaLayer = new google.maps.KmlLayer('http://demo.smartprospector.com/mapdemo/England.kmz');
      ctaLayer.setMap(map);
      google.maps.event.addListener(ctaLayer, 'click', function(kmlEvent) {
        var region = kmlEvent.featureData.name;
        $.ajax({url: helper.baseUrl + 'regions/filter', type: 'POST', dataType: 'JSON', data: {region: region}
        }).done(function(response) {
          if (response.success) {
            //console.log(response.data);
            $('#results-ct').empty();
            $.each(response.data, function(i, data) {
              var address = data.p_add1;
              if (data.p_add3 && data.p_add3 !== data.p_town) {
                address = address + "," + data.p_add3;
              }
              ;
              if (data.p_town) {
                address = address + "," + data.p_town;
              }
              ;
              if (data.p_county && data.p_county !== data.p_town) {
                address = address + "," + data.p_county;
              }
              ;

              $('#results-ct').append(
                      $('<li/>', {'class': 'result'}).append(
                      $('<a/>', {
                href: '<?php echo base_url() ?>' + 'index.php/leads/detail/' + data.urn,
                'class': 'hreflink',
                'id': data.urn
              }).append(
                      $('<h2/>').text(data.coname),
                      $('<p/>').html('<strong class="label">URN:</strong> ' + data.urn),
                      $('<p/>').html('<strong class="label">Address:</strong><span class="address"> ' + address + '</span>'),
                      $('<p/>').html('<strong class="label">Postcode:</strong> ' + data.p_postcode)
                      )
                      )
                      );
            });
            $('#results-ct').listview('refresh');
          } else {
            alert(response.message);
          }

        });
      });

      var listener = google.maps.event.addListener(map, "idle", function() {
        if (map.getZoom() < 6)
          map.setZoom(6);

        google.maps.event.removeListener(listener);
      });
    }


    initialize();




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