define(['js/lib/controller'], function (Base) {

    Base.prototype.index = function () {
        console.info('default/index');
    };

    Base.prototype.update = function () {
        console.info('default/update');
    };

    return new Base('default');
});