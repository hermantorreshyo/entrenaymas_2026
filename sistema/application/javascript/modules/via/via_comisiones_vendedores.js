(function ( app ) {

  app.views.ViaComisionesVendedoresResultados = app.mixins.View.extend({

    template: _.template($("#via_comisiones_vendedores_resultados_template").html()),
      
    myEvents: {
      "click .buscar":"buscar",
      "click .editar_comision":function(e) {
        var valor = prompt("Comision: ");
        if (!valor) return;
        valor = parseFloat(valor);
        if (isNaN(valor)) return;
        var id = $(e.currentTarget).data("id");
        var self = this;
        $.ajax({
          "url":"viajes/function/editar_comision/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":id,
            "valor":valor,
          },
          "success":function() {
            self.buscar();
          }
        });
      }
    },
    
    render_tabla: function() {
			var self = this;
      $("#via_comisiones_vendedores_tabla tbody").empty();
      for(var i=0;i<this.resultados.length;i++) {
        var item = self.resultados[i];
        var view = new app.views.ViaComisionesVendedoresItemResultados({
          model: item,
          resultados: self
        });
        $(this.el).find("#via_comisiones_vendedores_tabla .tbody").append(view.render().el);
      }
    },

    initialize: function() {
      var self = this;
      _.bindAll(this);
			$(this.el).html(this.template(this.model.toJSON()));
      createdatepicker(this.$("#via_comisiones_vendedores_fecha_desde"),moment().format("DD/MM/YYYY"));
      createdatepicker(this.$("#via_comisiones_vendedores_fecha_hasta"),moment().format("DD/MM/YYYY"));
    },
    
    buscar: function() {
      this.resultados = new Array();
      var self = this;
      var fecha_desde = $("#via_comisiones_vendedores_fecha_desde").val();
      var fecha_hasta = $("#via_comisiones_vendedores_fecha_hasta").val();
      var id_vendedor = $("#via_comisiones_vendedores_vendedores").val();
	    $.ajax({
        "url":"viajes/function/ver_comisiones/",
        "data": {
          "fecha_desde":fecha_desde,
          "fecha_hasta":fecha_hasta,
          "id_vendedor":id_vendedor,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          var saldo = Number(r.saldo_inicial).toFixed(2);
          self.$("#via_comisiones_vendedores_saldo_inicial").val(saldo);
          for(var i=0;i<r.datos.length;i++) {
            var item = r.datos[i];
            saldo += Number(item.comision_vendedor);
            item.saldo = saldo;
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
  app.views.ViaComisionesVendedoresItemResultados = Backbone.View.extend({
    
    template: _.template($("#via_comisiones_vendedores_item_resultados_template").html()),
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
