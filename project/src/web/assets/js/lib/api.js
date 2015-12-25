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
                    'Auth-Key': authKey,
                },
                dataType: 'json'
            });
        },

        getUser: function (id) {

            return this.sendRequest('GET', '/v1/users/' + id, {});

        },

        getFriends: function (id) {
            return this.sendRequest('GET', '/v1/users/' + id + '/getFriendsList', {})
        },

        updateUser: function (id, uname, uemail, upassword) {
            return this.sendRequest('PUT', '/v1/users/' + id, { name: uname, email: uemail,password: upassword})
        },

        addToFriends: function (friendId) {
            return this.sendRequest('GET', '/v1/users/addToFriends', { id: friendId})
        },

        removeFromFriends: function (friendId) {
            return this.sendRequest('GET', '/v1/users/removeFromFriends', { id: friendId})
        },

        search: function (string) {
            return this.sendRequest('GET', '/v1/search/', {q: string})
        }
    };

    return Api;
});