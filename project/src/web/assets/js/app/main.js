define(function (require) {

    var Router = require('js/lib/router');

    var Api = require('api'),
        User = require('user'),
        templater = require('templater')
    $ = require('jquery');

    require('cookie');
    require('typewatch');

    var apiObject = new Api($.cookie('AUTH_KEY'));
    var renderer = new templater();

    $(function () {

        var hash = window.location.hash.replace('#/', '');

        var route = new Router(hash);

        var friendList = $('.list-group.friend');

        var userId = $('#main-block').data('id');

        $('form[role=search] input').typeWatch({
            wait: 750,
            highlight: true,
            captureLength: 3,
            callback: function (string) {
                if (string.length > 2) {
                    apiObject.search(string).done(function (response) {
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
                        friendList.html(renderer.renderFriendList(friends));
                    });
                } else {

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

                            friends.push(new User(usr.user.id, usr.user.name, usr.location.locationName, usr.location.latlng, usr.user.sign));
                        }
                        friendList.html(renderer.renderFriendList(friends));
                    });
                }
            },
        });

        route.getController().getAction();

        $(window).bind('hashchange', function () {

            var hash = window.location.hash.replace('#/', '');
            var route = new Router(hash);
            route.getController().getAction();

        });
    });
});