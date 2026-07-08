(function ( app ) {

  app.views.IvaComprasParametros = Backbone.View.extend({

    template: _.template($("#iva_compras_parametros_template").html()),

    events: {
      "click .imprimir":"imprimir",
      "click .citi":"citi",
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var d = new Date();
      $(this.el).html(this.template({
        "anio":d.getFullYear(),
        "mes":d.getMonth(),
      }));

      if (control.check("razones_sociales")>0) {
        new app.mixins.Select({
          modelClass: app.models.RazonSocial,
          url: "razones_sociales/",
          render: "#iva_compras_razones_sociales",
        });
      }

      return this;
    },

    validar: function() {
      try {
        validate_input("iva_compras_movimiento_anio",IS_EMPTY,"Por favor ingrese un año.");
        return true;
      } catch(e) {
        return false;
      }
    },

    imprimir: function() {
      if (this.validar()) {
        var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
        var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
        anio = anio.replace("20","");
        var numero = $(this.el).find("#iva_compras_desde").val();
        var cerrar = ($(this.el).find("#iva_compras_cerrar").is(":checked") ? 1:0);
        var id_razon_social = 0;
        if (control.check("razones_sociales")>0) {
          id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
        }
        workspace.imprimir_reporte("iva/function/compras/"+mes+anio+"/"+cerrar+"/"+numero+"/"+id_razon_social);
      }
    },

    citi: function() {
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      window.open("compras/function/regimen_informacion/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
      window.open("compras/function/regimen_informacion_alicuotas/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
    },        

  });

})(app);