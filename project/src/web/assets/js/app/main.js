define(function (require) {

    var Api = require('api'),
        User = require('user'),
        templater = require('templater')
        $ = require('jquery');

    require('jquery.cookie');

    var apiObject = new Api($.cookie('AUTH_KEY'));
    var renderer  = new templater();

    $(function () {

        var friendList = $('.list-group.friend');

        var userId = $('#main-block').data('id');

        $('form[role=search] input').on('keyup', function(){

            var string = $(this).val();

            if(string.length > 2)
            {
                apiObject.search(string).done(function(response){
                    var friends = [];
                    for(var i in response.properties)
                    {
                        var usr = response.properties[i];
                        friends.push(new User(usr.user.id,usr.user.name,usr.location.locationName,usr.location.latlng,usr.user.sign));
                    }
                    friendList.html(renderer.renderFriendList(friends));
                });
            } else {
                if(string.length === 0)
                {
                    apiObject.getFriends(userId).done(function(response){
                        var friends = [];
                        for(var i in response.properties)
                        {
                            var usr = response.properties[i];
                            friends.push(new User(usr.user.id,usr.user.name,usr.location.locationName,usr.location.latlng,usr.user.sign));
                        }
                        friendList.html(renderer.renderFriendList(friends));
                    });
                }
            }
        });

        //apiObject.getUser(25).done(function(response){
        //    var model = new User(response.id, response.properties.name, '', '');
        //    console.info(model);
        //})
    });
});