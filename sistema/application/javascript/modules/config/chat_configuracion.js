(function ( models ) {

  models.ChatConfiguracion = Backbone.Model.extend({
    urlRoot: "chat_configuracion/",
    defaults: {
      id_empresa: ID_EMPRESA,
      chat_nombre: "",
      chat_color: "",
      chat_idioma: "",
      chat_pregunta: "",
    },
  });

});


(function ( views, models ) {

	views.ChatConfiguracionEditView = app.mixins.View.extend({

		template: _.template($("#chat_configuracion_edit_panel_template").html()),

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
      $(".colorpicker-component").colorpicker({
        format: "rgb"
      });
      return this;
    },
        
    validar: function() {
      var self = this;
      try {

        var color = this.$(".color").colorpicker('getValue');
        this.model.set({
          "chat_color":color,
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