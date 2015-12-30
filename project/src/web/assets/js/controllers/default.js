define(['js/lib/controller', 'jquery', 'templater', 'api', 'user'], function (Base, $, templater, Api, User) {

    var renderer  = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    Base.prototype.init = function()
    {
        var friendList = $('.list-group.friend');
        $('form[role=search] input').typeWatch({
            wait: 750,
            highlight: true,
            captureLength: 3,
            callback: function (string) {
                if (string.length > 2) {
                    apiObject.search(string).done(function (response) {
                        var html = '';
                        for (var type in response.properties.result) {
                            var friends = [];
                            if((type==='other' && response.properties.result['my'].length === 0) || (type==='my' && response.properties.result[type].length > 0)) {

                                var isFriend = type === 'my';

                                for (var i in response.properties.result[type]) {

                                    var userResp = response.properties.result[type][i];

                                    var user = new User();

                                    user
                                        .setId(userResp.id)
                                        .setName(userResp.name)
                                        .setLocation(userResp.locationName)
                                        .setLatLng(userResp.latlng)
                                        .setSign(userResp.sign)
                                        .setIsFriend(isFriend);

                                    friends.push(user);
                                }

                                html += renderer.renderFriendsList(type, friends);
                            }
                        }

                        friendList.html(html);
                    });
                } else {
                    require('js/controllers/default').friends();
                }
            },
        });
    };

    Base.prototype.index = function () {
        console.info('default/index');
        Base.prototype.init();

        var container = $('.blog-main');

        apiObject.nearest().done(function(response){
            container.html('<h2 style="text-align: center">People near you (100 miles)</h2>');
            if(response.properties.result.length > 0 ) {
                container.append(renderer.renderNearest(response.properties.result));
            } else {
                container.append('<p>Not found</p>')
            }
        });
    };

    Base.prototype.update = function () {
        console.info('default/update');
        Base.prototype.init();

        var userId = $('#main-block').data('id');

        var container = $('.blog-main');

        apiObject.getUser(userId).done(function(response){

            var user = new User();

            user
                .setId(response.properties.user.id)
                .setName(response.properties.user.name)
                .setEmail(response.properties.user.email)
                .setLocation(response.properties.user.locationName)
                .setLatLng(response.properties.user.latlng)
                .setSign(response.properties.user.sign)
                .setIsFriend(response.properties.isFriend);

            var text = $('<textarea />').html(response.properties.location.html).text();
            var textA = $('<textarea />').html(response.properties.location.htmlA).text();
            var js = $('<textarea />').html(response.properties.location.js).text();
            var jsA = $('<textarea />').html(response.properties.location.jsA).text();

            container.html(renderer.renderUpdate(user)).find('.mapper').append(textA).append(text).append(jsA).append(js);

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

                    $('input[name="lat"]').val(place.geometry.location.lat());
                    $('input[name="lng"]').val(place.geometry.location.lng());

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

            $('#updatePersonal').submit(function(e){
                e.preventDefault();

                var params = $(this).serializeArray();
                var userparams = {};

                for(var i in params)
                {
                    userparams[params[i]['name']] = params[i]['value'];
                }

                apiObject.updateUser(userId, userparams).done(function(){
                    $('#inputNewPassword,#inputPassword,#inputRNewPassword').val('');
                    alert('Personal info updated');
                }).fail(function(response){
                    console.info(response)
                });
            })
        });
    };

    Base.prototype.friends = function () {
        console.info('default/friends');

        var userId = $('#main-block').data('id');
        var friendList = $('.list-group.friend');

        apiObject.getFriends(userId).done(function (response) {
            var friends = [];

            for (var i in response.properties.friends) {
                var user = new User();

                var usr = response.properties.friends[i];

                user
                    .setId(usr.id)
                    .setName(usr.name)
                    .setLocation(usr.locationName)
                    .setLatLng(usr.latlng)
                    .setSign(usr.sign)
                    .setIsFriend(true);

                friends.push(user);
            }

            friendList.html(renderer.renderFriendsList('my', friends));
        });
    };

    return new Base('default');
});