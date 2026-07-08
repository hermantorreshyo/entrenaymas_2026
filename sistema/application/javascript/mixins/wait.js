(function (app) {

    app.mixins.Wait = Backbone.View.extend({
        
        template: _.template($("#wait_template").html()),
        
        initialize: function(options) {
            this.options = options;
            this.render();
        },
        
        render: function() {
            var self = this;
            var obj = {};
            obj.mensaje = (this.options.mensaje != undefined) ? this.options.mensaje : "Por favor espere...";
            $(this.el).html(this.template(obj));
            return this;
        },
        
    });

})(app);