<?php echo validation_errors(); ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="error"><?php echo $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<?php echo form_open('user/login', array('data-ajax' => 'false', 'onsubmit' => "$.mobile.loading( 'show')")); ?>
    
    <?php if($redirect): ?>
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
    <?php endif; ?>
    <input type="hidden" name="lat">
    <input type="hidden" name="lng">
    <input type="hidden" name="postcode">
    <input type="hidden" name="locality">
    
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li data-role="fieldcontain">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" data-clear-btn="true" value="<?php echo set_value('username') ?>"/>
        </li>
        <li data-role="fieldcontain">
            <label for="password">Password:</label>
            <input type="password" data-clear-btn="true" name="password" id="password" />
        </li>
        <li>
            <input type="submit" value="Login" id="login" name="login" data-theme="b">
        </li>
    </ul>
    
<?php echo form_close(); ?>

<script type="text/javascript">
    
    $(document).on('pageshow', '#login', function () {
        getLocation();
    });
    
    function getLocation() {
        if (navigator.geolocation) {
            return navigator.geolocation.getCurrentPosition(extractLocationData);
        }
        alert('Geolocation is not enabled on this device');
        return false;
    }

    function extractLocationData(position) {
        
        $('input[name="lat"]').val(position.coords.latitude);
        $('input[name="lng"]').val(position.coords.longitude);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            location : new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
            region   : 'GB'
        },
        function(result, status) {
            var exit = 0;
            if (status === 'OK') {
                for(var i = 0, length = result.length; i < length; i++) {
                    //each result has an address with multiple parts (it's all in the reference)
                    for(var j = 0; j < result[i].address_components.length; j++) {
                        var component = result[i].address_components[j];
                        //if the address component has postal code then write it out
                        if(component.types[0] === 'postal_code') {
                            $('input[name="postcode"]').val(component.long_name);
                            exit++;
                        }
                        if(component.types[0] === 'locality') {
                            $('input[name="locality"]').val(component.long_name);
                            exit++;
                        }
                        if (exit === 2) {
                            break;
                        }
                    }
                }
            } else {
                alert('Location error: ' + status);
            }
        });
    }
    
</script>
