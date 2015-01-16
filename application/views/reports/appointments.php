<ul data-inset="true" data-role="listview" class="report-options listview-white">
  <li>
    <form>
      <div class="ui-grid-a">
        <div class="ui-block-a" style="padding: 0 4px;">
          <select name="type">
            <option value="">What to look at..</option>
              <option selected value="date_added">Creation date</option>
              <option value="begin_date">Appointment Date</option>
          </select> 
        </div>
        <div class="ui-block-b" style="padding: 0 10px;">
          <select name="year">
            <?php foreach($years as $year): ?>
             <option value="<?php echo $year['year'] ?>"><?php echo $year['year'] ?></option>
             <?php endforeach; ?>
          </select>
        </div>
      </div><!-- /grid-bc -->
    </form>
    <button data-theme="b" type="submit">Submit</button>
  </li>
</ul>
<ul data-inset="true" data-role="listview" class="listview-white">
  <li class="results-description">Showing the number of appointments created each month<span class="pull-right"><a rel="external" data-ajax="false" href="export_appointments">Export</a></span></li>
  <li class="results-container">
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table appointment-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Jan<span style="display:none">1</span></th>
          <th>Feb<span style="display:none">2</span></th>
          <th>Mar<span style="display:none">3</span></th>
          <th>Apr<span style="display:none">4</span></th>
          <th>May<span style="display:none">5</span></th>
          <th>Jun<span style="display:none">6</span></th>
          <th>Jul<span style="display:none">7</span></th>
          <th>Aug<span style="display:none">8</span></th>
          <th>Sep<span style="display:none">9</span></th>
          <th>Oct<span style="display:none">10</span></th>
          <th>Nov<span style="display:none">11</span></th>
          <th>Dec<span style="display:none">12</span></th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $prospector => $months) {
          $prospector_total = 0; ?>
          <tr>
            <th><?php echo $prospector ?></th>
            <?php foreach ($months as $count) {
               ?>
              <td><?php echo $count ?></td>
            <?php } ?>
<?php } ?>
        </tr>
      </tbody>
    </table>
  </li>
</ul>
<script type="text/javascript">

     $(document).on('pageshow', '#' + '<?php echo $pageId; ?>', function () {
    
    $('#' + '<?php echo $pageId; ?>').find('td').on('click',function(){ 
      var month = $(this).closest('table').find('th').eq($(this).index()).find('span').text().replace('Total','');
      var bde = $(this).closest('tr').find('th').text().replace('Name','').replace('Total','');
	  var year = $(this).closest('page').find('select[name="year"]').val();
	  var type = $(this).closest('page').find('select[name="type"]').val();
window.location.href = helper.baseUrl+'appointment/view/'+month+'/'+bde;
    })

    
  $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
    e.preventDefault();
    var $page = $('#' + '<?php echo $pageId; ?>');
    $tbody = $page.find('.results-container table tbody').empty(),
            $table = $page.find('.results-container table');
            $options = $page.find('ul.report-options'),
            $resultsCt = $page.find('.results-container');
            $title = $page.find('.results-description');
    

    $.ajax({
      url: 'load_appointment_data',
      type: 'post',
      dataType: 'json',
      data: $options.find('form').serialize(),
      beforeSend: function() {
        $.mobile.loading('show');
      },
      success: function(data) {
        $.mobile.loading('hide');
        if (data.success) {
          $title.text(data.description);
          $.each(data.data, function(prospector, months) {
            var result = "<tr><th>"+prospector+"</th>";
            $.each(months, function(month, count) {
             result += '<td>'+count+'</td>';
            });
            result += '</tr>';
            $tbody.append(result);			
    })
	    $('#' + '<?php echo $pageId; ?>').find('td').on('click',function(){ 
      var month = $(this).closest('table').find('th').eq($(this).index()).find('span').text().replace('Total','');
      var bde = $(this).closest('tr').find('th').text().replace('Name','').replace('Total','');
	 $('.appointment-table').table('refresh');
	 window.location.href = helper.baseUrl+'appointment/view/'+month+'/'+bde;
          });
        } else {
          alert(data.message);
        }
      }
    })
    
   
      })
  });
</script>