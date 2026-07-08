// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ProcesarPedidosResultados = app.mixins.View.extend({

    template: _.template($("#procesar_pedidos_resultados_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "click .crear_pedido":"crear_pedido",
    },

    crear_pedido: function() {
      var self = this;
      if (self.id_proveedor == 0) {
        alert("Por favor seleccione un proveedor");
        this.$("#procesar_pedidos_proveedores").focus();
        return;
      }
      var items = new Array();
      var total_general = 0;
      $(this.el).find("#procesar_pedidos_tabla .tbody .cantidad").each(function(i,e){
        var cantidad = parseFloat($(e).val());
        if (!(isNaN(cantidad) || cantidad <= 0)) {
          var id_articulo = $(e).data("id");
          var costo_final = parseFloat($(e).data("costo"));
          var nombre = $(e).parents("tr").find(".nombre").html();
          var total = cantidad * costo_final;
          total_general += total;
          items.push({
            "id_articulo":id_articulo,
            "precio":costo_final,
            "nombre":nombre,
            "cantidad":cantidad,
            "bonificacion":0,
            "total":total,
          });
        }
      });
      if (items.length==0) return;
      var pedido = new app.models.PedidoProveedor({
        "id_proveedor":self.id_proveedor,
        "fecha":moment().format("DD/MM/YYYY"),
        "proveedor":self.$("#procesar_pedidos_proveedores").val(),
        "items":items,
        "subtotal":total_general,
        "total":total_general,
        "id_usuario":ID_USUARIO,
        "id_empresa":ID_EMPRESA,
      });
      pedido.save({},{
        success: function(model,response) {
          if (response.error == 0) {
            window.open("/sistema/app/#pedido_proveedor/"+response.id,"_blank");
          }
        }
      });
    },

    render_tabla: function() {
     var self = this;
     $("#procesar_pedidos_tabla tbody").empty();
     for(var i=0;i<this.resultados.length;i++) {
      var item = self.resultados[i];
      var view = new app.views.ProcesarPedidosItemResultados({
        model: item,
        resultados: self
      });
      $(this.el).find("#procesar_pedidos_tabla .tbody").append(view.render().el);
    }
  },

  guardar: function() {
    if (!confirm("Desea cerrar el pedido?")) return;
    $("#procesar_pedidos_tabla tbody tr .cantidad").each(function(i,e){

    });
  },

  initialize: function() {
    var self = this;
    _.bindAll(this);
    self.id_proveedor = 0;
    $(this.el).html(this.template(this.model.toJSON()));
    var fecha = moment().subtract(1,'months').format("DD/MM/YYYY");
    createdatepicker(this.$("#procesar_pedidos_fecha"),fecha);

    var input = this.$("#procesar_pedidos_proveedores");
    $(input).customcomplete({
      "url":"proveedores/function/get_by_nombre/",
      "width":300,
      "onSelect":function(item){
        self.$("#procesar_pedidos_proveedores").val(item.label);
        self.id_proveedor = item.id;
      }
    });

  },

  buscar: function() {
    this.resultados = new Array();
    var self = this;
    if (self.id_proveedor == 0) {
      alert("Por favor seleccione un proveedor");
      this.$("#procesar_pedidos_proveedores").focus();
      return;
    }
    var fecha = $("#procesar_pedidos_fecha").val();
    $.ajax({
      "url":"pedidos/function/ver_procesar/",
      "data": {
        "fecha":fecha,
        "id_sucursal":1,
        "id_proveedor":self.id_proveedor,
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
  app.views.ProcesarPedidosItemResultados = Backbone.View.extend({

    template: _.template($("#procesar_pedidos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "change .comision":function(e) {
        var self = this;
        var v = parseFloat($(e.currentTarget).val());
        $.ajax({
          "url":"pedidos/function/cambiar_comision/"+self.model.id+"/"+v+"/",
          "dataType":"json",
          "success":function(r){
            if (r.error == "0") {
              self.model.set({
                "cantidad":v,
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
