// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ProcesarPagosCampaniasResultados = app.mixins.View.extend({

        template: _.template($("#procesar_pagos_campanias_resultados_template").html()),
            
        myEvents: {
            "click .buscar":"buscar",
            "click .guardar":"guardar",
        },
        
        render_tabla: function() {
			var self = this;
			var total = 0, total_dif = 0, total_com = 0;
            var total_cobrado = 0, total_com_cobrado = 0, total_dif_cobrado = 0;
            var total_por_cobrar = 0, total_dif_por_cobrar = 0, total_com_por_cobrar = 0;
            $("#procesar_pagos_campanias_tabla tbody").empty();
            for(var i=0;i<this.resultados.length;i++) {
                var item = self.resultados[i];
                var view = new app.views.ProcesarPagosCampaniasItemResultados({
                    model: item,
                    resultados: self
                });
                $(this.el).find("#procesar_pagos_campanias_tabla .tbody").append(view.render().el);
                total += parseFloat(item.get("total"));
                total_com += parseFloat(item.get("comision"));
                total_dif += parseFloat(item.get("diferencia"));
                if (item.get("pagada") == 1) {
                    total_cobrado += parseFloat(item.get("total"));
                    total_com_cobrado += parseFloat(item.get("comision"));
                    total_dif_cobrado += parseFloat(item.get("diferencia"));
                } else {
                    total_por_cobrar += parseFloat(item.get("total"));
                    total_com_por_cobrar += parseFloat(item.get("comision"));
                    total_dif_por_cobrar += parseFloat(item.get("diferencia"));
                }
            }
            $("#procesar_pagos_campanias_total_comisiones").html("$ "+Number(total_com).toFixed(2));
            $("#procesar_pagos_campanias_total_resto").html("$ "+Number(total_dif).toFixed(2));
            $("#procesar_pagos_campanias_total").html("$ "+Number(total).toFixed(2));

            $("#procesar_pagos_campanias_total_comisiones_cobrado").html("$ "+Number(total_com_cobrado).toFixed(2));
            $("#procesar_pagos_campanias_total_resto_cobrado").html("$ "+Number(total_dif_cobrado).toFixed(2));
            $("#procesar_pagos_campanias_total_cobrado").html("$ "+Number(total_cobrado).toFixed(2));

            $("#procesar_pagos_campanias_total_comisiones_por_cobrar").html("$ "+Number(total_com_por_cobrar).toFixed(2));
            $("#procesar_pagos_campanias_total_resto_por_cobrar").html("$ "+Number(total_dif_por_cobrar).toFixed(2));
            $("#procesar_pagos_campanias_total_por_cobrar").html("$ "+Number(total_por_cobrar).toFixed(2));
        },

        guardar: function() {
            if (!confirm("Desea cerrar la cobranza?")) return;
            $("#procesar_pagos_campanias_tabla tbody tr .factura").each(function(i,e){

            });
        },
        
        initialize: function() {
            var self = this;
            _.bindAll(this);
			$(this.el).html(this.template(this.model.toJSON()));
            createdatepicker(this.$("#procesar_pagos_campanias_fecha"),moment().format("DD/MM/YYYY"));
        },
        
        buscar: function() {
            this.resultados = new Array();
            var self = this;
            var fecha = $("#procesar_pagos_campanias_fecha").val();
            var id_vendedor = $("#procesar_pagos_campanias_vendedores").val();
	        $.ajax({
                "url":"campanias/function/ver_pagos/",
                "data": {
                    "fecha":fecha,
                    "id_vendedor":id_vendedor,
                },
                "type":"post",
                "dataType":"json",
                "success":function(r) {
                    for(var i=0;i<r.results.length;i++) {
                        var item = r.results[i];
                        var modelo = Backbone.Model.extend({
                            defaults: item,
                        });
                        self.resultados.push(new modelo());
                    }
                    self.render_tabla();
                }
            });
        },
        
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.ProcesarPagosCampaniasItemResultados = Backbone.View.extend({
        
        template: _.template($("#procesar_pagos_campanias_item_resultados_template").html()),
        tagName: "tr",
        events: {
            "change .comision":function(e) {
                var self = this;
                var v = parseFloat($(e.currentTarget).val());
                var total = parseFloat(this.model.get("total"));
                var comision = parseFloat(total * v / 100);
                var diferencia = total - comision;
                $.ajax({
                    "url":"facturas/function/cambiar_comision/"+self.model.id+"/"+v+"/",
                    "dataType":"json",
                    "success":function(r){
                        if (r.error == "0") {
                            self.model.set({
                                "comision_vendedor":v,
                                "comision":comision,
                                "diferencia":diferencia,
                            });
                            self.render();
                            self.options.resultados.render_tabla();
                        }
                    }
                });
            },
			"change input[type=checkbox]":function(e) {
                var self = this;
                var pagada = ($(e.currentTarget).is(":checked")?1:0);
                $.ajax({
                    "url":"facturas/function/marcar_pagada/"+self.model.id+"/"+self.model.get("id_punto_venta")+"/"+pagada+"/",
                    "dataType":"json",
                    "success":function(r){
                        if (r.error == "0") {
                            self.model.set({
                                "pagada":pagada,
                            });
                            self.render();
                            self.options.resultados.render_tabla();
                        }
                    }
                });
			},
        },
        initialize: function(options) {
            var self = this;
            this.options = options;
            _.bindAll(this);
            this.render();
        },
        render: function() {
            var obj = this.model.toJSON();
            obj.id = this.model.id;
            $(this.el).html(this.template(obj));
            return this;
        },
    });
})(app);
