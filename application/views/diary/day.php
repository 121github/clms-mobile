<div class="day-desc">
    <?php $dayDesc = $today ? $dayDesc . ' (today)' : $dayDesc; echo $dayDesc; ?>

</div>
<div class="day-container"></div>

<div data-role="popup" id="popupAppDetail" data-theme="d" class="ui-corner-all">
    <a href="#" data-rel="back" data-role="button" data-theme="d" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
    <div class="popup-header">
        <h3>Appointment Detail</h3>
    </div>
    <div class="popup-content">
        <p class="urn"></p>
        <p class="coname"></p>
        <p class="timeStr"></p>
        <p class="title"></p>
        <p class="comments"></p>
    </div>
    <div class="popup-footer">
        <div data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right">
            <a href="#" class="delete" data-role="button" data-inline="true" data-theme="b">Delete</a>
            <a href="#popupEditApp" data-rel="popup" data-role="button" data-inline="true" data-theme="b">Edit</a>
            <a href="#" class="view" data-role="button" data-inline="true" data-theme="b">View Lead</a>
        </div>
        <div class="float-push"></div>
    </div>
</div>

<?php $this->view('popups/edit_appointment_popup'); ?>

<script type="text/javascript">
    
    $(document).on('pageinit', '#' + '<?php echo $pageId; ?>', function () {
        
        var todayUrl, 
            today    = '<?php echo $today; ?>', 
            diaryUrl = helper.baseUrl + 'diary/', 
            pageId   = '<?php echo $pageId; ?>';
        
        todayUrl = today ? null : diaryUrl + 'day';
        
        day.init(today, $.parseJSON('<?php echo str_replace("\\r\\n","<br>",$appointments); ?>'), $(this));

        footer.addFooterButtons(pageId, [
            {text: 'Today', callback: 'helper.switchDiaryView', args: todayUrl},
            {text: 'Month', callback: 'helper.switchDiaryView', args: diaryUrl + 'month'}
        ]);
        
    });
    
</script>
