<div class="hidden company-name"><?php echo $coname ?></div>
<div class="detail-options">
    <span data-role="button" data-urn="<?php echo $urn; ?>"
    data-inline="true" data-icon="planner-grey" class="plannerBtn addToPlanner 
    <?php if ($inPlanner) { echo 'hidden'; } ?>">Add to Planner</span>

<span data-role="button" data-urn="<?php echo $urn; ?>"
    data-inline="true" data-icon="planner" class="plannerBtn removeFromPlanner 
    <?php if (!$inPlanner) { echo 'hidden'; } ?>">Remove from Planner</span>

<a data-role="button" class="local-info" data-urn="<?php echo $urn; ?>" data-inline="true" data-icon="local-info">Local Customers</a>

<a data-role="button"  class="duplicate-info" data-urn="<?php echo $urn; ?>" data-inline="true" data-icon="duplicate-info">Duplicate Checker</a>

<a data-role="button" data-urn="<?php echo $urn; ?>" class="update-record <?php if($generalInfo['lead_status']['value']<>"Live"): ?>hidden<?php endif; ?>" data-inline="true" data-icon="update" href="#popupUpdateLead" data-rel="popup">Update</a>

<a data-role="button" data-urn="<?php echo $urn; ?>" data-inline="true" class="reset-record <?php if($generalInfo['lead_status']['value']==="Live"): ?>hidden<?php endif; ?>" data-icon="update">Reset</a>

</div>
<?php $this->view('popups/update_record_popup', array("urn" => $urn)); ?>

<div data-demo-html="true">
    <div data-role="collapsible-set" data-theme="c" data-content-theme="d">

        <div data-role="collapsible" <?php if (!isset($_SESSION['open_file_tab'])) { echo 'data-collapsed="false"'; } ?>>
            <h3>General Info</h3>
            <div class="general-info-container">
                <?php $this->view('leads/detail_views/general_info', array('generalInfo' => $generalInfo)); ?>
            </div>
        </div>

        <div data-role="collapsible">
            <h3>Company Details</h3>
            <div class="company-detail-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

        <div data-role="collapsible">
            <h3>Contacts</h3>
            <div class="contacts-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

        <div data-role="collapsible">
            <h3>History</h3>
            <div class="history-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

       <div data-role="collapsible">
            <h3>Appointments</h3>
            <div class="appointments-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

        <div data-role="collapsible">
            <h3>Policy Information</h3>
            <div class="policy-info-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

        <div class="filetab" data-role="collapsible" <?php if (isset($_SESSION['open_file_tab'])) { echo 'data-collapsed="false"'; unset($_SESSION['open_file_tab']); } ?>>
            <h3>Documentation / Attachments</h3>
            <div class="documents-container">
                <?php echo img('assets/img/ajax-loader-bar.gif'); ?>
            </div>
        </div>

    </div>
</div>

<?php 

    $this->view('popups/history_detail_popup', array('urn' => $urn));
    $this->view('popups/add_appointment_popup',  array('urn' => $urn,'attendees'=>$generalInfo['manager']['options']));
    $this->view('popups/edit_appointment_popup',  array('urn' => $urn,'attendees'=>$generalInfo['manager']['options']));
    $this->view('popups/local_info_popup', array('urn' => $urn));
    $this->view('popups/duplicate_info_popup', array('urn' => $urn));
?>

<script type="text/javascript">
    $(document).on('pageshow', '#' + '<?php echo $pageId; ?>', function () {   
               /* 
         * Only initialise the lead object if this page is not already in the DOM.
         * This means that the listeners will not be added multiple times for
         * the same page.
         */
        var urn = '<?php echo $urn; ?>';
        if (lead.pageId !== '#' + $.mobile.activePage.attr('id')) {
            lead.init(urn);
        }
    });
</script>