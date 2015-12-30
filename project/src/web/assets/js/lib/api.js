define(['jquery'], function ($) {

    function Api(authKey) {
        this.authKey = authKey;
    }

    Api.prototype = {
        sendRequest: function (method, url, data) {

            var authKey = this.authKey;

            return $.ajax({
                method: method,
                url: url,
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'HTTP_AUTH_KEY': authKey,
                },
                dataType: 'json'
            });
        },

        getUser: function (id) {

            return this.sendRequest('GET', '/v1/users/' + id, {});

        },

        getFriends: function (id) {
            return this.sendRequest('GET', '/v1/users/' + id + '/getFriendList', {})
        },

        updateUser: function (id, params) {
            return this.sendRequest('PUT', '/v1/users/' + id, params)
        },

        addToFriends: function (friendId) {
            return this.sendRequest('PUT', '/v1/users/addToFriends', { id: friendId })
        },

        removeFromFriends: function (friendId) {
            return this.sendRequest('DELETE', '/v1/users/removeFromFriends', { id: friendId})
        },

        search: function (string) {
            return this.sendRequest('GET', '/v1/search/', {q: string})
        },

        nearest: function () {
            return this.sendRequest('GET', '/v1/nearest/', {})
        }
    };

    return Api;
});
