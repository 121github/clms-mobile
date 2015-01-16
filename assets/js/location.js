/* ==========================================================================
   LOCATION
 ========================================================================== */

function getLocation() {
    if (navigator.geolocation) {
        return navigator.geolocation.getCurrentPosition(getLocationSuccess, getLocationError);
    }
    alert('Geolocation is not enabled on this device');
    return false;
}

function getLocationSuccess(position) {
    
    $('.geolocation-error').hide();
    
    var postcode = null,
        locality = null,
        exit     = 0,
        geocoder;
    
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
        region: 'GB'
    },
    function(result, status) {
        if (status === 'OK') {
            for(var i = 0, length = result.length; i < length; i++) {
                //each result has an address with multiple parts (it's all in the reference)
                for(var j = 0; j < result[i].address_components.length; j++) {
                    var component = result[i].address_components[j];
                    //if the address component has postal code then write it out
                    if(component.types[0] === 'postal_code') {
                        postcode = component.long_name;
                        exit++;
                    }
                    if(component.types[0] === 'locality') {
                        locality = component.long_name;
                        exit++;
                    }
                    if (exit === 2) {
                        break;
                    }
                }
            }
            if(postcode !== null){
                $('.current_postcode_input').val(postcode).trigger('change');
                $('.current_postcode').text(postcode);
                $.ajax({
                    url: helper.baseUrl + 'planner/store_postcode',
                    type: 'post',
                    data: {
                        lat      : position.coords.latitude,
                        lng      : position.coords.longitude,
                        postcode : postcode,
                        locality : locality
                    }
                });  
            } else {
                alert('Cannot find your location');
            }
        } else {
            alert('Location error: ' + status);
        }
    });
}

function getLocationError(error){
    var errMsg = 'Unknown Error';
    switch (error.code) {
        case 0:
            errMsg = 'Unknown Error';
            break;
        case 1:
            errMsg = 'Location permission denied by user.';
            break;
        case 2:
            errMsg = 'Position is not available';
            break;
        case 3:
            errMsg = 'Request timeout';
            break;
    }
    $('.geolocation-error').text(errMsg).show();
}

$(document).on('click', '.locate-postcode', function(e) {
    e.preventDefault();
    getLocation();
});