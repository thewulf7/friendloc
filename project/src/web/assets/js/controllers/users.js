define(['js/lib/controller', 'jquery', 'templater', 'api', 'user', './default', 'cookie'], function (Base, $, templater, Api, User, defaultC) {

    var renderer = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    var container = $('.blog-main');

    Base.prototype.view = function (id) {
        console.info('users/view');

        function loadUser(props) {
            var user = new User();

            user
                .setId(props.user.id)
                .setName(props.user.name)
                .setLocation(props.location.locationName)
                .setLatLng(props.location.latlng)
                .setSign(props.user.sign)
                .setIsFriend(props.isFriend);

            container.html(renderer.renderUserView(user));

            $('#addToFriends').on('click', function () {
                var friendId = $(this).data('id');

                apiObject.addToFriends(friendId).done(function (response) {
                    loadUser(response.properties);
                    defaultC.friends();
                });
            });

            $('#removeFromFriends').on('click', function () {
                var friendId = $(this).data('id');

                apiObject.removeFromFriends(friendId).done(function (response) {
                    loadUser(response.properties);
                    defaultC.friends();
                })
            });
        }

        apiObject.getUser(id).done(function (response) {
            loadUser(response.properties);
        });


    };

    return new Base('users');
});