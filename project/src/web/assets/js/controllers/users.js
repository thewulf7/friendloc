define(['js/lib/controller', 'jquery', 'templater', 'api', 'user','cookie'], function (Base, $, templater, Api, User) {

    var renderer  = new templater();
    var apiObject = new Api($.cookie('AUTH_KEY'));

    var container = $('.blog-main');

    Base.prototype.view = function (id) {
        console.info('users/view');

        apiObject.getUser(id).done(function(response){
            var user = new User();

            user
                .setId(response.properties.user.id)
                .setName(response.properties.user.name)
                .setLocation(response.properties.location.locationName)
                .setLatLng(response.properties.location.latlng)
                .setSign(response.properties.user.sign)
                .setIsFriend(response.properties.user.isFriend);

            container.html(renderer.renderUserView(user))
        });

    };

    return new Base('users');
});