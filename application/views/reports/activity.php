<ul data-inset="true" data-role="listview" class="activity-listview listview-white">
    <li>
        <form>
            <div class="ui-grid-b">
                <div class="ui-block-a" style="padding: 0 4px;">
                    <select name="prospector">
                        <option value="no_selection_made">Select prospector...</option>
                        <?php foreach ($prospectors as $prospector): ?>
                            <option <?php if ($_SESSION['login'] === $prospector): echo "selected";
                        endif;
                            ?> value="<?php echo $prospector; ?>">
                            <?php echo $prospector; ?>
                            </option>
<?php endforeach; ?>
                    </select> 
                </div>
                <div class="ui-block-b" style="padding: 0 10px;">
                    <input value="<?php echo date('d/m/Y', strtotime('-1 week')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
                </div>
                <div class="ui-block-c" style="padding: 0 10px;">
                    <input value="<?php echo date('d/m/Y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
                </div>
            </div><!-- /grid-bc -->
        </form>
        <button data-theme="b" type="submit">Submit</button>
    </li>
</ul>
<ul data-inset="true" data-role="listview" class="listview-white">
    <li class="activity-description">This table shows your activity over the last 7 days</li>
    <li class="activity-results">
        <table data-role="table" data-mode="none" class="table-stroke table-stripe responsive-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Company Updates</th>
                    <th>Contact Updates</th>
                    <th>Policy Updates</th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </li>
</ul>

<script type="text/javascript">
    $(document).ready(function() {
        reload_activity();


        $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
            e.preventDefault();
            reload_activity();
        });

        function reload_activity() {
            //Move this crap out of here
            var $table = $('.activity-results table tbody').empty(),
                    $listview = $('ul.activity-listview'),
                    $resultsCt = $('.activity-results'),
                    $link = helper.baseUrl + 'reports/daily/';
            $companyVal = '';
            $contactVal = '';
            $policyVal = '';
            $.ajax({
                url: 'load_activity_data',
                type: 'post',
                dataType: 'json',
                data: $listview.find('form').serialize(),
                beforeSend: function() {
                    $.mobile.loading('show');
                },
                success: function(data) {
                    $.mobile.loading('hide');
                    if (data.success) {
						$agent = $listview.find("select[name='prospector']").val();
                        $('.activity-description').text('Showing all activity for ' + $agent + ' between ' + $listview.find("input[name='date_from']").val() + ' and ' + $listview.find("input[name='date_to']").val());
                        $.each(data.activity_data, function(i, val) {
                            //show links if there are any updates found
                            $companyVal = (val.leads === "0" ? val.leads : $('<a/>', {href: $link + 'companies/' + i+'/'+$agent}).text(val.leads));

                            $contactVal = (val.contacts === "0" ? val.contacts : $('<a/>', {href: $link + 'contacts/' + i+'/'+$agent}).text(val.contacts));

                            $policyVal = (val.renewals === "0" ? val.renewals : $('<a/>', {href: $link + 'policies/' + i+'/'+$agent}).text(val.renewals));

                            $table.append(
                                    $('<tr/>', {
                                'class': 'row'
                            }).append(
                                    $('<td/>').text(val.formatted_date),
                                    $('<td/>').append($companyVal),
                                    $('<td/>').append($contactVal),
                                    $('<td/>').append($policyVal)
                                    )
                                    );
                        });
                    } else {
                        alert(data.message);
                    }
                }
            });

        }

    });
</script>
