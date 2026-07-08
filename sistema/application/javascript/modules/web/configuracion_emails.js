(function ( views, models ) {

	views.WebConfiguracionEmailEditView = app.mixins.View.extend({

		template: _.template($("#web_configuracion_email_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
		},

        initialize: function() {
            _.bindAll(this);
            this.render();
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
                        show("Los datos han sido guardados correctamente.");
                        location.reload();
                    }
                });
            }
		},		
	});

})(app.views, app.models);