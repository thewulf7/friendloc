define(function (require) {

    function router(link) {

        this.link = link === '' ? ['/'] : link.split('/');

        this.controllers = {
            '/': require('js/controllers/default'),
            'users': require('js/controllers/users')
        };
    }

    router.prototype = {

        getController: function () {

            var controller = null;

            for (var i in this.controllers) {
                if (i === this.link[0]) {
                    controller = this.controllers[i];
                    break;
                }
            }

            this.controller = controller;

            return this;
        },

        getAction: function () {

            var action = '';
            var params = [];

            if (parseInt(this.link[1]) > 0) {
                action = 'view';
                params.push(this.link[1]);
            } else {
                action = this.link[1] ? this.link[1] : 'index';
                params.push(this.link.splice(2));
            }

            return this.controller.run(action, params);
        }

    };

    return router;
});