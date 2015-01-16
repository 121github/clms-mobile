<?php echo validation_errors(); ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="error"><?php echo $this->session->flashdata('error'); ?></div>
<?php endif; ?>
    
    <?php if($this->session->flashdata('success')): ?>
    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
<?php endif; ?>
    
<?php echo form_open('user/account', array('data-ajax' => 'false', 'onsubmit' => "$.mobile.loading( 'show')")); ?>

    
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li data-role="fieldcontain">
            <label for="current_pass">Current Password:</label>
            <input type="password" name="current_pass" id="current_pass" data-clear-btn="true"/>
        </li>
        <li data-role="fieldcontain">
            <label for="new_pass">New Password:</label>
            <input type="password" data-clear-btn="true" name="new_pass" id="new_pass" />
        </li>
              <li data-role="fieldcontain">
            <label for="conf_pass">Confirm Password:</label>
            <input type="password" data-clear-btn="true" name="conf_pass" id="conf_pass" />
        </li>
        <li>
            <input type="submit" value="Change Password" id="change-pass" name="change-pass" data-theme="b">
        </li>
    </ul>
    
<?php echo form_close(); ?>

<script type="text/javascript">
    
    $(document).on('pageshow', '#my-account', function () {
        getLocation();
    });

    
</script>
