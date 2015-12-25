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

        $('form[role=search] input').on('keyup', function(){

            var string = $(this).val();

            if(string.length > 3)
            {
                apiObject.search(string).done(function(response){
                    renderer.renderFriendList(response.properties.friendList);
                });
            }
        });

        //apiObject.getUser(25).done(function(response){
        //    var model = new User(response.id, response.properties.name, '', '');
        //    console.info(model);
        //})
    });
});