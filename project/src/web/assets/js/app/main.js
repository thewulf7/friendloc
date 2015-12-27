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
                        var html = '';
                        for (var type in response.properties) {
                            var friends = [];
                            if(type==='other' || (type==='my' && response.properties[type].length > 0)) {
                                for (var i in response.properties[type]) {
                                    var user = new User();

                                    user
                                        .setId(response.properties[type][i].user.id)
                                        .setName(response.properties[type][i].user.name)
                                        .setLocation(response.properties[type][i].location.locationName)
                                        .setLatLng(response.properties[type][i].location.latlng)
                                        .setSign(response.properties[type][i].user.sign)
                                        .setIsFriend(response.properties[type][i].user.isFriend);

                                    friends.push(user);
                                }

                                html += renderer.renderFriendsList(type, friends);
                            }
                        }

                        friendList.html(html);
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

                            friends.push(user);
                        }

                        friendList.html(renderer.renderFriendsList('my', friends));
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