define(function () {
    function user() {
        this.id = null;
        this.name = null;
        this.location = null;
        this.lat = null;
        this.lng = null;
        this.sign = null;
        this.isFriend = false;
        this.link = null;
        this.email = null;
    }

    user.prototype = {
        setId: function (id) {
            this.id = id;
            this.link = '/#/users/' + this.id;
            return this;
        },
        setName: function (name) {
            this.name = name;
            return this;
        },
        setLocation: function (loc) {
            this.location = loc;
            return this;
        },
        setLatLng: function (latlng) {
            this.lat = latlng.lat;
            this.lng = latlng.lon;
            return this;
        },
        setSign: function (sign) {
            this.sign = sign;
            return this;
        },
        setIsFriend: function (friend) {
            this.isFriend = friend === undefined ? false : friend;
            return this;
        },
        setEmail: function (email) {
            this.email = email;
            return this;
        }
    };

    return user;
});