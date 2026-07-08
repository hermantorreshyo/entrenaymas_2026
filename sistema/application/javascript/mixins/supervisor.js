(function ( views, models ) {

  views.CodigoSupervisorView = Backbone.View.extend({

    template: _.template($("#codigo_supervisor_template").html()),

    events: {
      "click .guardar": "guardar",
      "keyup #codigo_supervisor_texto":function(e) {
        if (e.which == 13) { this.guardar(); }
        else if (e.which == 27) {
          window.acepto_supervisor = 0;
          $('.modal:last').modal('hide');
        }
      }
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
    
    validar: function() {
      try {
        validate_input("codigo_supervisor_texto",IS_EMPTY,"Por favor, ingrese un codigo.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },
    
    guardar: function() {
      var self = this;
      var codigo = $("#codigo_supervisor_texto").val();
      if (this.validar()) {
        // Arreglo para MEGASHOP: pasar el codigo de barra a mayuscula

        // Tarjeta de los locales
        // MD5: d66fe0d25446cdc89d95f5a8df64063f
        // String: KVZ7ZbaN09

        if (MEGASHOP == 1) {
          if ((ID_SUCURSAL == 56 && codigo == "1") || (ID_SUCURSAL == 1037 && codigo == "1") || ((ID_SUCURSAL == 223 || ID_SUCURSAL == 224) && codigo == "8561220356")) {
            window.acepto_supervisor = 1;
            $('.modal:last').modal('hide');
            return;
          }
        }
        if (MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421) codigo = codigo.toUpperCase();
        codigo = hex_md5(codigo);
        if (codigo == SUPERVISOR) {
          window.acepto_supervisor = 1;
          $('.modal:last').modal('hide');
        } else {
          show("ERROR: Codigo incorrecto.");
        }
      }
    },
	
  });

})(app.views, app.models);