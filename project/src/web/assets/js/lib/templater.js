define(function (require) {

    function templater() {

    }

    templater.prototype = {
        renderUserView: function (user) {

            var template = require('twigjs!templates/userProfile');

            return template(user);
        },
        renderFriendsList: function (type, friends) {

            var templateHeader = require('twigjs!templates/friends_list');
            var templateLine = require('twigjs!templates/friend_line');
            var output = '';

            output += templateHeader({title: type === 'my' ? 'My friends' : 'Search result'});

            if (friends.length === 0) {
                output += type === 'my' ? 'You have 0 friends. To add them use the search bar.' : 'No users found';
            }

            for (var i in friends) {
                output += templateLine({
                    friend: friends[i]
                });
            }

            return output;
        },
        renderUpdate: function (user) {
            var template = require('twigjs!templates/updateme');
            return template(user);
        },
        renderNearest: function (result) {
            var template = require('twigjs!templates/friend_line_small');
            var output = '';

            for (var i in result) {
                output += template(result[i]);
            }

            return output;
        }
    };

    return templater;
});