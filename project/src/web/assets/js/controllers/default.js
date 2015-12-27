define(['js/lib/controller', 'jquery', 'templater', 'api', 'user'], function (Base, $, templater, Api, User) {

    var renderer  = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    Base.prototype.index = function () {
        console.info('default/index');
    };

    Base.prototype.update = function () {
        console.info('default/update');
    };

    Base.prototype.friends = function () {
        console.info('default/friends');

        var userId = $('#main-block').data('id');
        var friendList = $('.list-group.friend');

        apiObject.getFriends(userId).done(function (response) {

            var friends = [];
            for (var i in response.properties) {
                var user = new User();

                user
                    .setId(response.properties[i].user.id)
                    .setName(response.properties[i].user.name)
                    .setLocation(response.properties[i].location.locationName)
                    .setLatLng(response.properties[i].location.latlng)
                    .setSign(response.properties[i].user.sign)
                    .setIsFriend(response.properties[i].user.isFriend);

                friends.push(user);
            }

            friendList.html(renderer.renderFriendsList('my', friends));
        });
    };

    return new Base('default');
});