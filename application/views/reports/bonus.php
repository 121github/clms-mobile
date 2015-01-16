<ul data-inset="true" data-role="listview" class="bonus-options listview-white">
  <li>
    <form>
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
          <select name="prospector">
            <option value="">--Any prospector--</option>
            <?php foreach ($prospectors as $prospector): ?>
              <option value="<?php echo $prospector; ?>">
              <?php echo $prospector; ?>
              </option>
<?php endforeach; ?>
          </select> 
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <select name="month">
            <?php foreach ($months as $monthNum => $monthName): ?>
              <option value="<?php echo $monthNum ?>"><?php echo $monthName ?></option>
<?php endforeach; ?>
          </select>
        </div>
      </div><!-- /grid-bc -->
    </form>
    <button data-theme="b" type="submit">Submit</button>
  </li>
</ul>
<?php if (count($bonuses) > 1): ?>
  <ul data-inset="true" data-role="listview" class="listview-white bonus-container">
    <li>
      <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table bonus-table">
        <thead>
          <tr>
            <th>Prospector</th>
            <th>Renewals Captured</th>
            <th>Appointments Set</th>
            <th>Cancelled</th>
            <th>Renewals Bonus</th>
            <th>Appointment Bonus</th>
            
            <th>Avg Quality</th>
            <th>Total Bonus</th>
          </tr>
        </thead>
        <tbody>
  <?php foreach ($bonuses as $bonus): ?>
            <tr>
              <td><?php echo $bonus['prospector'] ?></td>
              <td><?php echo $bonus['renewals'] ?></td>
              <td><?php echo $bonus['appointments'] ?> </td>
              <td style="color:<?php echo ($bonus['cancelled']>0?"red":"#333") ?>"><?php echo $bonus['cancelled'] ?></td>
              <td><?php echo $bonus['renewals_bonus'] ?></td>
              <td><?php echo $bonus['appointments_bonus'] ?></td>
              
              <td><?php echo $bonus['quality'] ?></td>
              <td><?php echo $bonus['total_bonus'] ?></td>
            </tr>
  <?php endforeach; ?>
        </tbody>
      </table>
    </li>
  </ul>
<?php endif; ?>
<script type="text/javascript">
  $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
    var $page = $('#' + '<?php echo $pageId; ?>');
    $tbody = $page.find('.bonus-container table tbody'),
            $.ajax({
      url: 'report',
      type: 'post',
      dataType: 'json',
      data: $('.bonus-options').find('form').serialize(),
      beforeSend: function() {
        $.mobile.loading('show');
      },
      success: function(data) {
        $.mobile.loading('hide');
        if (data.success) {
          $tbody.empty();
          $.each(data.data, function(bonus, counts) {
			  if(counts.cancelled>0){ var color="red" } else { var color = "#333" }
           var results = "<tr>";
              results += '<td>' + counts.prospector + '</td><td>' + counts.renewals + '</td><td>' + counts.appointments + '</td><td style="color:'+color+'"><a href="'+helper.baseUrl+'appointment/cancellations/'+data.month+'/'+counts.prospector+'">' + counts.cancelled + '</a></td><td>' + counts.renewals_bonus + '</td><td>' + counts.appointments_bonus + '</td><td>' + counts.quality + '</td><td>' + counts.total_bonus + '</td>';

            results+="</tr>";
            $tbody.append(results);
          });

          $('.bonus-container table').table('refresh');
        } else {
          alert("Failed to generate bonus report");
        }
      }
    })
  });
</script>