define(['js/lib/controller', 'jquery', 'templater', 'api', 'user'], function (Base, $, templater, Api, User) {

    var renderer = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    Base.prototype.login = function(){
        console.info('auth/login');
    };

    Base.prototype.signup = function () {
        console.info('auth/signup');

        window.loadGoogle = function () {
            load_ivory_google_place();
            load_ivory_google_map();

            location_autocomplete.bindTo('bounds', location_map);

            var infowindow = new google.maps.InfoWindow();
            var geocoder = new google.maps.Geocoder;
            var marker = new google.maps.Marker({
                map: location_map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                anchorPoint: new google.maps.Point(0, -29)
            });

            var place_changed = function (place) {

                if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                }

                if (place.geometry.viewport) {
                    location_map.fitBounds(place.geometry.viewport);
                } else {
                    location_map.setCenter(place.geometry.location);
                    location_map.setZoom(17);
                }

                marker.setIcon(({
                    url: place.icon ? place.icon : "https://maps.gstatic.com/mapfiles/place_api/icons/geocode-71.png",
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                $('input[name="location[lat]"]').val(place.geometry.location.lat());
                $('input[name="location[lng]"]').val(place.geometry.location.lng());

                var address = place.formatted_address;

                var name = place.name ? place.name : place.address_components[0]['short_name']+' '+place.address_components[1]['short_name'];

                infowindow.setContent('<div><strong>' + name + '</strong><br>' + address);
                infowindow.open(location_map, marker);
            };

            location_autocomplete.addListener('place_changed', function () {

                infowindow.close();
                marker.setVisible(false);

                var place = location_autocomplete.getPlace();

                place_changed(place);
            });

            google.maps.event.addListener(marker, 'dragend', function (event) {

                var latlng = {lat: event.latLng.lat(), lng: event.latLng.lng()};

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            place_changed(results[0]);
                            document.getElementById('location_input').value = results[0].formatted_address;
                        } else {
                            window.alert('No results found');
                        }
                    } else {
                        window.alert('Geocoder failed due to: ' + status);
                    }
                });
            });
        };

        google.load("maps", "3", {"other_params": "libraries=places&language=en&sensor=false", "callback": loadGoogle});
    };

    return new Base('auth');
});