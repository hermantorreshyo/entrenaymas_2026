(function ( views, models ) {

	views.WebEditorView = app.mixins.View.extend({

		template: _.template($("#web_editor_panel_template").html()),

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
                this.model.set({
                    "texto_css":self.$("#web_editor_texto_css").val(),
                    "texto_js":self.$("#web_editor_texto_js").val(),
                });
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
