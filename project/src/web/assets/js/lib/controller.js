define(function () {
    function controllerBase(id) {
        this.id = id;
    }

    controllerBase.prototype = {
        run: function (actionName, params) {
            this[actionName].apply(null,params);
        }
    };

    return controllerBase;
});