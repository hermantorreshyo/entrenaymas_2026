// -----------------------------------------
//   VISTA DE PARAMETROS DE VENTAS DIARIAS
// -----------------------------------------
(function ( app ) {

    app.views.IvaVentasParametros = Backbone.View.extend({

        template: _.template($("#iva_ventas_parametros_template").html()),

        events: {
            "click .generar": "imprimir",
            "click .citi": "citi",
            "click #iva_ventas_por_concepto": "ventas_por_concepto",
        },

        initialize: function(options) {
            _.bindAll(this);
            this.options = options;
            this.render();
            
            var fecha_desde = new Date();
            var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
            fecha_desde = new Date(y, m, 1);
            fecha_hasta = new Date(y, m+1, 0);	            
            createdatepicker($(this.el).find("#iva_ventas_fecha_desde"),fecha_desde);
            createdatepicker($(this.el).find("#iva_ventas_fecha_hasta"),fecha_hasta);
            this.resultados = this.options.resultados;
        },
        
        render: function() {
            var self = this;
            $(this.el).html(this.template());
            return this;
        },
                
        imprimir: function() {
            var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
            var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
            var desde = $(this.el).find("#iva_ventas_desde").val();
            workspace.imprimir_reporte("iva/function/ventas/"+fecha_desde+"/"+fecha_hasta+"/"+desde+"/");
        },
        
        citi: function() {
            var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
            var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
            window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/cbte/","_blank");
            window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/alicuotas/","_blank");
        },        

        ventas_por_concepto: function() {
            var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
            var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
            workspace.imprimir_reporte("ventas/function/por_concepto/?desde="+fecha_desde+"&hasta="+fecha_hasta+"/");
        },
		
    });

})(app);