var gmap = {
        
    init: function () {
        
        var geocoder = new google.maps.Geocoder(), 
            myPostcode = $.trim($('.current_postcode').text());
        
        $('#location-panel').append('<li class="prospect-map-ct"><div id="map-canvas"/></li>').listview('refresh');
        gmap.$canvas = $('#map-canvas').height($(window).height() - 240);
        gmap.getPostcodeLocation(geocoder, myPostcode, 'You are here!', true);

        $('#planner-ct .planner').each(function (i) {
            var $row = $(this),
                coname = $.trim($row.find('.coname').text()),
                pcode = $row.attr('data-postcode');
            if (pcode !== '') {
                (function(i) {
                    setTimeout(function() {
                        gmap.getPostcodeLocation(geocoder, pcode, coname, false);
                    }, 500 * i);
                })(i);
            }
        });

    },

    getPostcodeLocation: function (geocoder, pcode, info, init) {
        var location, lat, lng;
        geocoder.geocode({
            address: pcode,
            region: 'GB'
        },
        function(data, status) {
            if (status === 'OK') {
                location = data[0].geometry.location;
                lat = location.lat();
                lng = location.lng();
                if (init) {
                    gmap.initialize(lat, lng);
                }
                gmap.addMarker(lat, lng, info, init);
            } else {
                console.log('Location error: ' + status);
            }
        });
    },

    initialize: function (lat, lng) {
        var mapOptions = {
            center: new google.maps.LatLng(lat, lng),
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        gmap.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    },

    addMarker: function (lat, lng, info, currLoc) {
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            info: new google.maps.InfoWindow({
                content: '<div class="marker-info">' + info + '</div>'
            })
        });
        if (currLoc) {
            marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
        }
        google.maps.event.addListener(marker, 'click', function() {
            marker.info.open(gmap.map, marker);
        });
        marker.setMap(gmap.map);
    }

};