define(['js/lib/controller', 'jquery', 'templater', 'api', 'user', './default', 'cookie'], function (Base, $, templater, Api, User, defaultC) {

    var renderer = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    var container = $('.blog-main');

    Base.prototype.view = function (id) {
        console.info('users/view');
        defaultC.init();

        function loadUser(props) {

            var user = new User();

            user
                .setId(props.user.id)
                .setName(props.user.name)
                .setLocation(props.user.locationName)
                .setLatLng(props.user.latlng)
                .setSign(props.user.sign)
                .setIsFriend(props.isFriend);

            var text = $('<textarea />').html(props.location.html).text();
            var js = $('<textarea />').html(props.location.js).text();

            container.html(renderer.renderUserView(user)).find('.map').append(text).append(js);

            $('#addToFriends').on('click', function () {
                var friendId = $(this).data('id');

                apiObject.addToFriends(friendId).done(function (response) {
                    loadUser(response.properties);
                    defaultC.friends();
                }).fail(function (response) {
                    console.warn(response);
                });
            });

            $('#removeFromFriends').on('click', function () {

                var friendId = $(this).data('id');

                apiObject.removeFromFriends(friendId).done(function (response) {
                    loadUser(response.properties);
                    defaultC.friends();
                }).fail(function (e) {
                    console.info(e)
                });
            });

            $('#getDirections').on('click', function () {
                var friendId = $(this).data('id');
                var self = this;

                apiObject.getDirections(friendId).done(function (response) {
                    if(!window.directionsService) {
                        window.directionsService = new google.maps.DirectionsService;
                    }
                    if(!window.directionsDisplay) {
                        window.directionsDisplay = new google.maps.DirectionsRenderer;
                    }

                    var origin = new google.maps.LatLng(response.properties.user.lat, response.properties.user.lng);
                    var destination = new google.maps.LatLng(response.properties.friend.lat, response.properties.friend.lng);

                    if ($(self).hasClass('btn-info')) {
                        window.directionsDisplay.set('directions', null);
                        location_map.setCenter(destination);
                        location_map.setZoom(13);
                        $(self).text('Get directions').removeClass('btn-info');
                    } else {
                        window.directionsDisplay.setMap(location_map);
                        window.directionsService.route({
                            origin: origin,
                            destination: destination,
                            travelMode: google.maps.TravelMode.DRIVING
                        }, function (response, status) {
                            if (status === google.maps.DirectionsStatus.OK) {
                                directionsDisplay.setDirections(response);
                            } else {
                                window.alert('Directions request failed due to ' + status);
                            }
                        });
                        $(self).text('Remove directions').addClass('btn-info');
                    }
                });

            });

            load_ivory_google_map_api();
        }

        apiObject.getUser(id).done(function (response) {
            loadUser(response.properties);
        });
    };

    return new Base('users');
});