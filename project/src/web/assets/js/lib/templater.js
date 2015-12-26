define(function (require) {

    function templater() {

    }

    templater.prototype = {
        renderUserView: function (user) {
            var template = require('twigjs!templates/friend');
            return template(user);
        },
        renderFriendList: function (friends) {
            var template = require('twigjs!templates/friend_line');
            var output = '';

            for (var i in friends) {
                output += template({
                    friend: friends[i]
                });
            }

            return output;
        },
        renderSearchView: function (result) {
            var template = require('twigjs!templates/friend_line');
            var output = '';

            for (var i in result) {
                output += template({
                    friend: friends[i]
                });
            }

            return output;
        }
    };

    return templater;
});