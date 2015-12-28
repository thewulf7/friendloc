define(function (require) {

    var Router = require('js/lib/router');

    var Api = require('api'),
        User = require('user'),
        templater = require('templater'),
        $ = require('jquery');

    require('cookie');
    require('typewatch');

    var apiObject = new Api($.cookie('AUTH_KEY'));
    var renderer  = new templater();

    $(function () {

        var hash = (window.location.pathname === '' || window.location.pathname === '/') ? window.location.hash.replace('#/', '') :
            window.location.pathname.substr(1);

        var route = new Router(hash);

        route.getController().getAction();

        $(window).bind('hashchange', function () {

            var hash = window.location.hash.replace('#/', '');
            var route = new Router(hash);
            route.getController().getAction();

        });
    });
});