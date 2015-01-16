<style type="text/css">
  table.mi-results {
    font-size: 0.8em;
  }
</style>

<ul data-inset="true" data-role="listview" class="mi-listview listview-white">
  <li>
    <form>
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
          <select name="miFilter" class="prospector">
            <option value="no_selection_made">Select a prospector...</option>
            <?php foreach ($miFilters as $prospector): ?>
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
    </form>
    <button data-theme="b" type="submit">Submit</button>
  </li>
  <li>Showing first visits between <span class="text_from"><?php echo date('d/m/Y', strtotime('-7 days')) ?></span> to <span class="text_to"><?php echo date('d/m/Y') ?></span> and figures relating to those visits <span class="pull-right"><a rel="external" data-ajax="false" href="export_mi">Export</a></span></li>
  <li class="mi-container">
    <table data-role="table" class="table-stroke table-stripe responsive-table mi-results tablesorter">
      <thead>
        <tr>
          <th>Prospector</th>
          <th>First Visit</th>
          <th>Renewal Date</th>
          <th>Policy Type</th>
          <th>Broker</th>
          <th>Insurer</th>
          <th>Premium</th>
          <th>Turnover</th>
          <th>Turnover Validated</th>
          <th>Employees</th>
          <th>Employees Validated</th>
          <th>Consent to Contact</th>
          <th>BDE App</th>
          <th>CAE App</th>
          <th>Total Visits</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr class="total_row">
          <th>Totals</th>
          <td class="col1">0</td>
          <td class="col2">0</td>
          <td class="col3">0</td>
          <td class="col4">0</td>
          <td class="col5">0</td>
          <td class="col6">0</td>
          <td class="col7">0</td>
          <td class="col8">0</td>
          <td class="col9">0</td>
          <td class="col10">0</td>
          <td class="col11">0</td>
          <td class="col12">0</td>
          <td class="col13">0</td>
          <td class="col14">0</td>
        </tr>
      </tfoot>
    </table>
  </li>
</ul>

<script type="text/javascript">

  $(document).on('pageshow', '#' + '<?php echo $pageId; ?>', function() {
    $('table.mi-results').tablesorter();
    get_prospectors();
    
    function get_prospectors() {
      reset_export_session();
      var filter = $('select[name="miFilter"]').val(), prospectors = [];
      $('.total_row').children('td').text('0');
      if (filter === "no_selection_made") {

        $.getJSON('get_prospectors', function(response) {
          prospectors = response;
          $.each(prospectors, function(i, val) {
            get_mi_data(val, i + 1, prospectors.length);
          });
        });
      } else {
        get_mi_data(filter, 1, 1);
        $('table.mi-results').table('refresh');
      }
    }
    
    function reset_export_session(){
    $.ajax({
        url: 'reset_export_session',
        type: 'post'});
    }

$.xhrPool = [];
$.xhrPool.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0;
};

    function get_mi_data(prospector, progress, total) {
      var $table = $('table.mi-results'),
              $tbody = $table.find('tbody'),
              $from = $('.mi-listview form').find('.date_from').val(),
              $to = $('.mi-listview form').find('.date_to').val();
      $.ajax({
        url: 'load_management_information',
        type: 'post',
        dataType: 'json',
        data: {
          prospector: prospector,
          date_from: $from,
          date_to: $to
        },
        beforeSend: function(jqXHR) {
             $.xhrPool.push(jqXHR);
          $.mobile.loading('show', {
            text: 'Loading report...',
            textVisible: true,
            theme: 'a'});
        },
        complete: function(jqXHR) {
        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.splice(index, 1);
            }
        },
        success: function(data) {
          if (data.success) {
            $('.text_from').text(data.date_from);
            $('.text_to').text(data.date_to);
            $tbody.append(
                    $('<tr/>').append(
                    $('<th/>').text(data.miResults.prospector),
                    $('<td/>').text(data.miResults.first_visits),
                    $('<td/>').text(data.miResults.renewal_dates),
                    $('<td/>').text(data.miResults.policy_types),
                    $('<td/>').text(data.miResults.policy_brokers),
                    $('<td/>').text(data.miResults.policy_insurers),
                    $('<td/>').text(data.miResults.policy_premiums),
                    $('<td/>').html(data.miResults.turnover),
                    $('<td/>').html(data.miResults.turnover_validations),
                    $('<td/>').html(data.miResults.employees),
                    $('<td/>').html(data.miResults.employee_validations),
                    $('<td/>').html(data.miResults.consent_to_contacts),
                    $('<td/>').text(data.miResults.bde_appointments),
                    $('<td/>').text(data.miResults.cae_appointments),
                    $('<td/>').text(data.miResults.total_visits)
                    )
                    );
            $('.col1').text(Number($('.col1').text().replace(/\D/g,'')) + data.miResults.first_visits);
            $('.col2').text(Number($('.col2').text().replace(/\D/g,'')) + data.miResults.renewal_dates);
            $('.col3').text(Number($('.col3').text().replace(/\D/g,'')) + data.miResults.policy_types);
            $('.col4').text(Number($('.col4').text().replace(/\D/g,'')) + data.miResults.policy_brokers);
            $('.col5').text(Number($('.col5').text().replace(/\D/g,'')) + data.miResults.policy_insurers);
            $('.col6').text(Number($('.col6').text().replace(/\D/g,'')) + data.miResults.policy_premiums);
            $('.col7').text(Number($('.col7').text().replace(/\D/g,'')) + data.miResults.turnover);
            $('.col8').text(Number($('.col8').text().replace(/\D/g,'')) + data.miResults.turnover_validations);
            $('.col9').text(Number($('.col9').text().replace(/\D/g,'')) + data.miResults.employees);
            $('.col10').text(Number($('.col10').text().replace(/\D/g,'')) + data.miResults.employee_validations);
            $('.col11').text(Number($('.col11').text().replace(/\D/g,'')) + data.miResults.consent_to_contacts);
            $('.col12').text(Number($('.col12').text().replace(/\D/g,'')) + data.miResults.bde_appointments);
            $('.col13').text(Number($('.col13').text().replace(/\D/g,'')) + data.miResults.cae_appointments);
            $('.col14').text(Number($('.col14').text().replace(/\D/g,'')) + data.miResults.total_visits);

            if (progress === total) {
              $('table.mi-results').table('refresh');
            }
            if (progress === total) {
              $.mobile.loading('hide');
            }
          } else {
            alert(data.message);
          }
        }
      });
    }

    $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
      e.preventDefault();
      $.xhrPool.abortAll;
      $('table.mi-results').find('tbody').empty();
      get_prospectors();
    });
    
 });
</script>
