requirejs.config({
    baseUrl: 'assets',
    paths: {
        app: 'js/app',
        api: 'js/lib/api',
        templater: 'js/lib/templater',
        user: 'js/model/user',
        'jquery.cookie': 'js/lib/jquery.cookie',
        jquery: 'vendor/jquery/jquery',
        bootstrap: 'vendor/bootstrap/dist/js/bootstrap',
        twig: 'vendor/twig.js/twig',
        twigjs: 'vendor/requirejs-twig/twigjs'
    }
});

requirejs(['app/main']);