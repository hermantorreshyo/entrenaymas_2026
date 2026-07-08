(function (collections, paginator) {

  collections.DeudaSucursales = paginator.requestPager.extend({

    paginator_ui: {
      perPage: 999999,
      order_by: 'ultimo_pago',
      order: 'desc',
    },
    paginator_core: {
      url:"proveedores/function/deuda_por_sucursal",
    }

  });

})( app.collections, Backbone.Paginator);


(function ( app ) {

  app.views.DeudaSucursalesResultados = app.mixins.View.extend({

    template: _.template($("#deuda_sucursales_resultados_template").html()),

    myEvents: {
      "click #checkTodos": "seleccionar_todos",
      "click .exportar":"exportar",
      "click .generar": "buscar",
    },

    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.collection.on('sync', this.addAll, this);
      this.render();
    },

    buscar : function() {
      var self = this;
      var datos = {};
      datos.id_proveedor = this.$("#deuda_sucursales_id_proveedor").val();
      if (isEmpty(datos.id_proveedor)) {
        show("Por favor seleccione un proveedor");
        this.$("#deuda_sucursales_proveedor").focus();
        return;        
      }
      datos.fecha_desde = this.$("#deuda_sucursales_fecha_desde").val().replace(/\//g,"-");
      if (isEmpty(datos.fecha_desde)) {
        show("Por favor seleccione una fecha");
        this.$("#deuda_sucursales_fecha_desde").focus();
        return;                
      }
      datos.fecha_hasta = this.$("#deuda_sucursales_fecha_hasta").val().replace(/\//g,"-");
      if (isEmpty(datos.fecha_hasta)) {
        show("Por favor seleccione una fecha");
        this.$("#deuda_sucursales_fecha_hasta").focus();
        return;                
      }
      this.collection.server_api = datos;
      this.collection.pager();
    },        

    render: function() {
      var self = this;
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#deuda_sucursales_fecha_hasta"),new Date());
      createdatepicker($(this.el).find("#deuda_sucursales_fecha_desde"),new Date());

      // AUTOCOMPLETE DE PROVEEDORES
      // ---------------------------
      var input = this.$("#deuda_sucursales_proveedor");
      $(input).customcomplete({
        "url":"proveedores/function/get_by_nombre/",
        "width":300,
        "form":null,
        "onSelect":function(item){
          var proveedor = new app.models.Proveedor({"id":item.id});
          proveedor.fetch({
            "success":function(){
              self.seleccionar_proveedor(proveedor);
            },
          });
        }
      });
      return this;
    },

    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      this.$("#deuda_sucursales_id_proveedor").val(self.proveedor.id);
      this.$("#deuda_sucursales_proveedor").val(self.proveedor.get("nombre"));
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#deuda_sucursales_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    seleccionar_todos : function(e) {
      var checked = $(e.currentTarget).is(":checked");
      if (checked) {
        $(this.el).find(".tbody .fila_roja .checkbox").parents("tr").addClass("seleccionado");
      } else {
        $(this.el).find(".tbody .fila_roja .checkbox").parents("tr").removeClass("seleccionado");
      }
      $(this.el).find(".tbody .fila_roja .checkbox").attr("checked",checked);
    },

    exportar : function() {

      var self = this;
      var fecha = $("#deuda_sucursales_fecha").val();
      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "nombre":$.trim($(e).find("td:eq(0) span").html()),
          "saldo":$(e).find("td:eq(1)").html(),
          "compra":$(e).find("td:eq(2)").html(),
          "fecha_ult_compra":$(e).find("td:eq(3)").html(),
          "pago":$(e).find("td:eq(4)").html(),
          "fecha_ult_pago":$(e).find("td:eq(5)").html(),
          "total":$(e).find("td:eq(6)").html(),
        });
      });
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });

      var footer = new Array();
      footer[0] = "";
      footer[1] = this.$("#deuda_sucursales_total_saldo").html();
      footer[2] = this.$("#deuda_sucursales_total_compras").html();
      footer[4] = this.$("#deuda_sucursales_total_pagos").html();
      footer[5] = this.$("#deuda_sucursales_total").html();

      this.exportar_excel({
        "filename":"deuda",
        "title":"Deuda con sucursales",
        "date":fecha,
        "data":array,
        "header":header,
        "footer":footer,
      });         
    },


    addAll : function () {
      $(this.el).find("tbody").empty();
      this.saldo = 0;
      this.total_compras = 0;
      this.total_pagos = 0;
      if (this.collection.size() == 0) {
        $(this.el).find("tbody").append("<tr><td colspan='10'>No se encontraron resultados.</td></tr>");    
      } else {
        this.collection.each(this.addOne);        
      }
      this.$("#deuda_sucursales_total_saldo").text(Number(this.saldo).toFixed(2));
      this.$("#deuda_sucursales_total_compras").text(Number(this.total_compras).toFixed(2));
      this.$("#deuda_sucursales_total_pagos").text(Number(this.total_pagos).toFixed(2));
      var total = Number(this.saldo) + Number(this.total_compras) + Number(this.total_pagos);
      this.$("#deuda_sucursales_total").text(Number(total).toFixed(2));
    },        

    addOne : function ( item ) {
      var self = this;

      var saldo = parseFloat(item.get("saldo"));
      if (isNaN(saldo)) saldo = 0;
      this.saldo += saldo;

      var total_compras = parseFloat(item.get("total_compras"));
      if (isNaN(total_compras)) total_compras = 0;
      this.total_compras += total_compras;

      var total_pagos = parseFloat(item.get("total_pagos"));
      if (isNaN(total_pagos)) total_pagos = 0;
      this.total_pagos += total_pagos;

      var view = new app.views.DeudaSucursalesItemResultados({
        model: item,
        resultados: self
      });
      $(self.el).find(".tbody").append(view.el);
    },        

  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.DeudaSucursalesItemResultados = Backbone.View.extend({
    template: _.template($("#deuda_sucursales_item_resultados_template").html()),
    events: {
      "click":function() {
        window.open("app/#cuentas_corrientes_sucursales/"+this.model.id,"_blank");
      }
    },
    tagName: "tr",
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);
