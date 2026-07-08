(function ( models ) {

  models.ConfiguracionFacturacion = Backbone.Model.extend({
    urlRoot: "configuracion_facturacion/",
    idAttribute: "id_empresa",
    defaults: {
      id_empresa: 0,
      supervisor: "",
      numero_ib: "",
      fecha_inicio: "",
      percibe_ib: 0,
      retiene_ib: 0,
      retiene_ganancias: 0,
      ultimo_movimiento_cerrado_compras: "",
      ultimo_movimiento_cerrado_ventas: "",
      observaciones: "",
      cotizacion_dolar:0,
      facturacion_template_factura: "",
      facturacion_testing: 1,
      facturacion_usa_cache_clientes: 1,
      facturacion_usa_cache_articulos: 1,
      facturacion_conservar_cliente_al_guardar: 1,
      facturacion_consultar_eliminar_item: 1,
      facturacion_codigo_finalizar: "0",
      facturacion_mostrar_fecha: 1,
      facturacion_mostrar_numero: 1,
      facturacion_modificar_precio: 1,
      facturacion_modificar_item: 1,
      facturacion_modificar_descripcion: 1,
      facturacion_permite_anular_producto: 1,
      facturacion_editar_descuento: 1,
      facturacion_usa_nplu: 0,
      facturacion_gestiona_stock_default: 0,
      facturacion_cantidad_copias: 3,
      facturacion_identificador_plu: "",
      facturacion_largo_plu: "",
      facturacion_controlar_caja_abierta: 0,
      facturacion_forma_pago: "E",
      facturacion_cantidad_items: 15,
      facturacion_numeracion_tipo_comprobante: 0,
      facturacion_abrir_dialogo_imprimir: 0,
      facturacion_mostrar_logo_en_comprobante: 1,
      facturacion_imprimir_item_al_final: 0,
      remitos_tomar_precio_neto: 0,
      facturacion_ocultar_cuenta_corriente: 1, // No es ocultar sino USA
      facturacion_usa_creditos_personales: 0,
      facturacion_crear_cliente: 1,
    }
  });

})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.ConfiguracionFacturacionView = app.mixins.View.extend({

		template: _.template($("#configuracion_facturacion_template").html()),

		myEvents: {
			"click .guardar": "guardar",

      // Si se cambia que usa o no NPLU
      "change input[name=facturacion_usa_nplu]": function(e) {
        var v = $("input[name=facturacion_usa_nplu]:checked").val();
        $("#facturacion_identificador_plu").prop("disabled",(v != 1));
        $("#facturacion_largo_plu").prop("disabled",(v != 1));
      },            
      
      // Si se cambia el tipo de impresion
      "change input[name=facturacion_tipo_impresion]": function(e) {
        var v = $("input[name=facturacion_tipo_impresion]:checked").val();
        $("#facturacion_imp_fiscal").prop("disabled",(v != "F"));
      },            
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
  	   // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      createdatepicker($(this.el).find("#facturacion_fecha_inicio"),self.model.get("fecha_inicio"));
      return this;
    },        

    validar: function() {
      try {
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() 
    {
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