<ul data-inset="true" data-role="listview" class="feedback-listview listview-white">
    <li>
        <form>
            <div class="ui-grid-b">
                <div class="ui-block-a" style="padding: 0 4px;">
                    <select name="prospector">
                        <option value="">--Any prospector--</option>
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
                    <input value="<?php echo date('d/m/y', strtotime('-1 month')) ?>" name="date_from" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date from...">
                </div>
                <div class="ui-block-c" style="padding: 0 10px;">
                    <input value="<?php echo date('d/m/y') ?>" name="date_to" type="text" data-role="datebox" data-clear-btn="true" data-options='{"mode":"calbox", "useNewStyle":true}' placeholder="Date to...">
                </div>
            </div><!-- /grid-bc -->
        </form>
        <button data-theme="b" type="submit">Submit</button>
    </li>
</ul>

<?php if (count($feedback) > 0): ?>
    <ul class="feedback-ct" data-role="listview" data-inset="true">
        <?php foreach ($feedback as $row): ?>
            <?php 
                $noaddress = false;
                $appStart = $row['begin_date'] . " " . str_pad($row['begin_hour'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($row['begin_mins'], 2, "0", STR_PAD_LEFT) . ":00";
                $appEnd = $row['begin_date'] . " " . str_pad($row['end_hour'], 2, "0", STR_PAD_LEFT) . ":" . str_pad($row['end_mins'], 2, "0", STR_PAD_LEFT) . ":00";
            ?>
            <li>
                <a href="<?php echo base_url() ?>index.php/feedback/answers/<?php echo $row['id']; ?>">
                    <h2>
                        <?php echo $row['coname']; ?>
                    </h2>
                     
                                                              <p>
                        <strong class="label">Feedback for:</strong>
                        <span><?php echo $row['bde']; ?></span>
                    </p>
                                          <p>
                        <strong class="label">Feedback left by:</strong>
                        <span><?php echo $row['cae']; ?></span>
                    </p>
                                                              <p>
                        <strong class="label">Feedback left on:</strong>
                        <span><?php echo $row['feedback_date']; ?></span>
                    </p>

                    <p>
                        <strong class="label">Appointment Date:</strong> 
                        <?php echo $row['app_date']; ?>
                    </p>

                    <p>
                        <strong class="label">Details:</strong>
                        <span class="appTitle"> <?php echo $row['title']; ?></span> - 
                        <span class="appText"> <?php echo $row['text']; ?></span>
                    </p>
                    <div class="ui-li-aside"> 
                        <strong class="label">Score:</strong>
                        <span><?php echo $row['score']; ?></span>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>   
<? endif; ?>
<script type="text/javascript">
     $('#' + '<?php echo $pageId; ?> button[type="submit"]').click(function(e) {
       $feedback = $('#' + '<?php echo $pageId; ?>').find('.feedback-ct');
       
       $.ajax({url:"load_view",
         type:"POST",
         dataType:"JSON",
         data: $('.feedback-listview').find('form').serialize(),
         beforeSend: function() {
        $.mobile.loading('show');
      },
          success: function(data) {
        $feedback.empty();
        $.mobile.loading('hide');
        $.each(data,function(i,val){
        $('.feedback-ct').append($('<li/>').append($('<a/>').attr('href',helper.baseUrl + "feedback/answers/"+val.id).html("<h2>"+val.coname+"</h2><p><strong class='label'>Feedback for: </strong><span>"+val.bde+"</span></p><p><strong class='label'>Feedback left by: </strong><span>"+val.cae+"</span></p><p><strong class='label'>Feedback left on: </strong><span>"+val.feedback_date+"</span></p><p><strong class='label'>Appointment Date: </strong>"+val.app_date+"</p><p><strong class='label'>Details: </strong><span class='appTitle'>"+val.title+"</span> - <span class='appText'> "+val.text+"</span></p><div class='ui-li-aside'><strong class='label'>Score: </strong><span>"+val.score+"</span></div>")
      )
      )
          });
        $('.feedback-ct').listview('refresh')
      }
});
     });
  
</script>