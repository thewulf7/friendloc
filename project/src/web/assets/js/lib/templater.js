define(function (require) {

    function templater() {

    }

    templater.prototype = {
        renderUserView: function (user) {
            var template = require('twigjs!templates/user');

            return template(
                {
                    name: user.getName(),
                    location: user.getLocationName(),
                }
            );
        },
        renderFriendList: function (friends) {
            var template = require('twigjs!templates/friend_line');
            var output = '';

            for (var i in friends) {
                var friend = friends[i];
                output += template({
                    friend: {
                        name: friend.getName(),
                        location: friend.getLocationName(),
                        link: friend.getLink(),
                        sign: friend.getSign()
                    }
                });
            }

            return output;
        },
        renderSearchView: function (result) {
            var template = require('twigjs!templates/friend_line');
            var output = '';

            for (var i in result) {
                var friend = friends[i];
                output += template({
                    friend: {
                        name: friend.getName(),
                        location: friend.getLocationName(),
                        link: friend.getLink(),
                        sign: friend.getSign()
                    }
                });
            }

            return output;
        }
    };

    return templater;
});