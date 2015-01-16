<ul data-inset="true" data-role="listview" class="income-options listview-white">
  <li>
    <form>
      <div class="ui-grid-b">
        <div class="ui-block-a" style="padding: 0 4px;">
          <select name="prospector">
            <option value="">--Any prospector--</option>
            <?php foreach ($prospectors as $prospector): ?>
            <option value="<?php echo $prospector; ?>"> <?php echo $prospector; ?> </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <select name="month">
            <option value="">--Any time--</option>
            <?php foreach ($months as $monthNum => $monthName): ?>
            <option value="<?php echo $monthNum ?>"><?php echo $monthName ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <!-- /grid-bc -->
    </form>
    <button data-theme="b" type="submit">Submit</button>
  </li>
</ul>
<?php if (count($income) > 1): ?>
<ul data-inset="true" data-role="listview" class="listview-white income-container">
<li>This table shows all live polices with matching Acturis reference numbers. Click confirm to move them into CPH</li>
  <li>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table income-table">
      <thead>
        <tr>
          <th>BDE</th>
          <th>CAE</th>
          <th>Last Appointment</th>
          <th>Effective Date</th>
          <!--<th>URN</th>
          <th>Acturis</th>-->
          <th>Company</th>
          <th>Product</th>
          <th>Premium (&pound;)</th>
          <th>Options</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($income as $key => $row): 
  			if($key!=="total"): ?>
        <tr>
          <td><?php echo $row['prospector'] ?></td>
          <td><?php echo $row['cae'] ?></td>
          <td><?php echo $row['last_appointment']['date'] ?></td>
          <td><?php echo $row['effective'] ?></td>
          <!--<td><a href="<?php echo base_url()."index.php/leads/detail/". $row['urn'] ?>"><?php echo $row['urn'] ?></a></td>
          <td><?php echo $row['clientkey'] ?></td>-->
          <td><a href="<?php echo base_url()."index.php/leads/detail/". $row['urn'] ?>"><?php echo $row['company'] ?></a></td>
          <td><?php echo $row['prodtarget'] ?></td>
          <td><?php echo $row['premium'] ?></td>
          <td><a class="confirm-income" href="#">Confirm</a></td>
        </tr>
        <?php 	endif; 
  		endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6"><strong>Total</strong></td>
          <td class="premium-total"><strong><?php echo $income['total'] ?></strong></td>
          <td></td>
        </tr>
    </table>
  </li>
</ul>
<?php endif; ?>
<script type="text/javascript">
  $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
	  e.preventDefault();
    var $page = $('#' + '<?php echo $pageId; ?>');
    $tbody = $page.find('.income-container table tbody'),
	$premium_total = $page.find('.premium-total'),
            $.ajax({
      url: 'income/filter',
      type: 'post',
      dataType: 'json',
      data: $('.income-options').find('form').serialize(),
      beforeSend: function() {
        $.mobile.loading('show');
      },
      success: function(data) {
        $.mobile.loading('hide');
        if (data.success) {
          $tbody.empty();
          $.each(data.data, function(key, val) {
			  if(key!="total"){
           var results = "<tr>";
            results += '<td>' + val.prospector + '</td>';
			results += '<td>' + val.cae + '</td>';
			results += '<td>' + val.last_appointment['date'] + '</td>';
			results += '<td>' + val.effective + '</td>';
			//results += '<td><a href="'+helper.baseUrl + "leads/detail/"+val.urn+'">' + val.urn + '</a></td>';
			//results += '<td>' + val.clientkey + '</td>';
			results += '<td><a href="'+helper.baseUrl + "leads/detail/"+val.urn+'">' + val.company + '</a></td>';
			results += '<td>' + val.prodtarget + '</td>';
			results += '<td>' + val.premium + '</td>';
			results += '<td><a href="#">Confirm</a></td>';
            results+="</tr>";
            $tbody.append(results);
			  } else {
				$premium_total.html('<strong>'+val+'</strong>');  
			  }
          });
			$tbody.find('a').addClass('ui-link');
          $('.income-container table').table('refresh');
        } else {
          alert("Failed to generate income report");
        }
      }
    })
  });
</script>