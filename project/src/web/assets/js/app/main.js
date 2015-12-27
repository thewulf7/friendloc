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

        route.getController().getAction();

        $(window).bind('hashchange', function () {

            var hash = window.location.hash.replace('#/', '');
            var route = new Router(hash);
            route.getController().getAction();

        });
    });
});