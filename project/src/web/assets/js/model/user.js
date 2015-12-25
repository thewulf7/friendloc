define(function () {
    function user(id, username, location, latlng) {
        this.id = id;
        this.username = username;
        this.location = location;
        this.latlng = latlng;
    }

    user.prototype = {
        getId: function () {
            return this.id;
        },
        getName: function () {
            return this.username;
        },
        getLocationName: function () {
            return this.location;
        },
        getLatLng: function () {
            return this.latlng;
        },
        getLink: function(){
            return '/users/' + this.id;
        },
        getSign: function(){
            return this.username;
        }
    };

    return user;
});