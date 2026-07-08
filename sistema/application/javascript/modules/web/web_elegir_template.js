(function ( models ) {

    models.WebElegirTemplate = Backbone.Model.extend({
        urlRoot: "web_configuracion/",
        defaults: {
            opciones: [],
        }
    });
        
})( app.models );

(function ( views, models ) {

	views.WebElegirTemplateView = app.mixins.View.extend({

		template: _.template($("#web_elegir_template_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
            "click .elegir_disenio":"elegir_disenio",
		},

        initialize: function() {
            _.bindAll(this);
            this.render();
        },

        elegir_disenio: function(e) {
            if (!confirm("Realmente desea cambiar de template?")) return;
            var id = $(e.currentTarget).data("id");
            $.ajax({
                "url":"web_templates/function/elegir_template/",
                "dataType":"json",
                "data":{
                    "id_template":id,
                    "id_empresa":ID_EMPRESA,
                },
                "type":"post",
                "success":function(r) {
                    if (r.error == 0) window.location.reload();
                }
            });
        },

        render: function() {
            var self = this;
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
        
        validar: function() {
            var self = this;
            try {
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },

        guardar: function() {
            var self = this;
            if (this.validar()) {
                this.model.save({},{
                    success: function(model,response) {
                        location.reload();
                    }
                });
            }
		},		
	});

})(app.views, app.models);
