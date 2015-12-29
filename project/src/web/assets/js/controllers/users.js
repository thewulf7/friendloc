define(['js/lib/controller', 'jquery', 'templater', 'api', 'user', './default', 'cookie'], function (Base, $, templater, Api, User, defaultC) {

    var renderer = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    var container = $('.blog-main');

    Base.prototype.view = function (id) {
        console.info('users/view');
        defaultC.index();

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
                }).fail(function(e){
                     console.info(e)
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