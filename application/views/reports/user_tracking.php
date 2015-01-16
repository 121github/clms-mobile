<ul data-inset="true" data-role="listview" class="user-tracking-listview listview-white">
    <li>
        <form>
            <div class="ui-grid-b">
                <div class="ui-block-a" style="padding: 0 4px;">
                    <select name="prospector">
                        <option value="no_selection_made">Select prospector...</option>
                        <?php foreach ($prospectors as $prospector): ?>
                            <option value="<?php echo $prospector; ?>">
                                <?php echo $prospector; ?>
                            </option>
                        <?php endforeach; ?>
                    </select> 
                </div>
                <div class="ui-block-b" style="padding: 0 10px;">
                     <input name="date_from" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
                </div>
                <div class="ui-block-c" style="padding: 0 10px;">
                    <input name="date_to" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
                </div>
            </div><!-- /grid-bc -->
        </form>
        <button data-theme="b" type="submit">Submit</button>
    </li>
</ul>
<ul>
<li class="results-container hidden">
    <div class="stats">
        <b class="numloc">0</b> location(s)<br/>
        <b class="nummiles">0</b> miles total<br/><br/>
    </div>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th>Postcode</th>
                <th>Locality</th>
                <th>Date</th>
                <th>Time</th>
                <th><abbr title="Distance from last location (as the crow flies)">Dist (miles)</abbr></th>
                <th>Duration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</li>
</ul>
<script type="text/javascript">
    
    $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function (e) {
        
        e.preventDefault();
        
        //Move this crap out of here
        var platform   = navigator.platform,
            mapLink    = 'http://maps.google.com/',
            $table     = $('.results-container table tbody').empty(),
            $listview  = $('ul.user-tracking-listview'),
            $resultsCt = $('.results-container');
            
        if(platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
            mapLink = 'comgooglemaps://';
        }
                        
        $.ajax({
            url: 'load_tracking_data',
            type: 'post',
            dataType: 'json',
            data: $listview.find('form').serialize(),
            beforeSend: function() {
                $.mobile.loading('show');
            },
            success: function (data) {
                $.mobile.loading('hide');
                if (data.success) {
                    
                    $listview.append($resultsCt.removeClass('hidden')).listview('refresh');
                    $('.stats .numloc').text(data.tracking_data.total_loc);
                    $('.stats .nummiles').text(data.tracking_data.total_dist);
                    
                    for (var i in data.tracking_data.locations) {
                        var location = data.tracking_data.locations[i];
                        $table.append(
                            $('<tr/>', {
                                'class': 'location-' + location.id
                            }).append(
                                $('<td/>').text(location.postcode),
                                $('<td/>').text(location.locality),
                                $('<td/>').text(location.date),
                                $('<td/>').text(location.time),
                                $('<td/>').text(location.distance),
                                $('<td/>').text(location.duration),
                                $('<td/>').append(
                                    $('<a/>', {
                                        href: mapLink + '?q=' + location.postcode
                                    }).text('View on Map')
                                )
                            )
                        );
                    }
                } else {
                    alert(data.message);
                }
            }
        });
    });
    
</script>
