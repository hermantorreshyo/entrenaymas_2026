(function (collections, paginator) {

  collections.DeudaProveedores = paginator.requestPager.extend({

    paginator_ui: {
      perPage: 999999,
      order_by: 'ultimo_pago',
      order: 'desc',
    },
    paginator_core: {
      url:"proveedores/function/listado_deuda",
    }

  });

})( app.collections, Backbone.Paginator);


(function ( app ) {

  app.views.DeudaProveedoresResultados = app.mixins.View.extend({

    template: _.template($("#deuda_proveedores_resultados_template").html()),

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
      datos.filtrar_en_cero = this.$("#deuda_filtrar_en_cero").is(":checked") ? 1 : 0;
      datos.tipo_proveedor = this.$("#deuda_tipo_proveedor").val();
      datos.fecha_desde = this.$("#deuda_proveedores_fecha_desde").val().replace(/\//g,"-");
      if (isEmpty(datos.fecha_desde)) {
        show("Por favor seleccione una fecha");
        this.$("#deuda_proveedores_fecha_desde").focus();
        return;                
      }
      datos.id_sucursal = this.$("#deuda_proveedores_sucursales").val();
      /*
      datos.fecha_hasta = this.$("#deuda_proveedores_fecha_hasta").val().replace(/\//g,"-");
      if (isEmpty(datos.fecha_hasta)) {
        show("Por favor seleccione una fecha");
        this.$("#deuda_proveedores_fecha_hasta").focus();
        return;                
      }
      */
      this.collection.server_api = datos;
      this.collection.pager();
    },        

    render: function() {
      $(this.el).html(this.template());
      //createdatepicker($(this.el).find("#deuda_proveedores_fecha_hasta"),new Date());
      createdatepicker($(this.el).find("#deuda_proveedores_fecha_desde"),new Date());
      return this;
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
      var fecha = $("#deuda_proveedores_fecha").val();
      var array = new Array();

      $(".table tbody tr").each(function(i,e){

        var saldo_mas_90 = $(e).find("td:eq(2)").html();
        saldo_mas_90 = saldo_mas_90.replace(/\./g,"");
        saldo_mas_90 = saldo_mas_90.replace(/\,/g,".");
        var saldo_90 = $(e).find("td:eq(3)").html();
        saldo_90 = saldo_90.replace(/\./g,"");
        saldo_90 = saldo_90.replace(/\,/g,".");
        var saldo_60 = $(e).find("td:eq(4)").html();
        saldo_60 = saldo_60.replace(/\./g,"");
        saldo_60 = saldo_60.replace(/\,/g,".");
        var saldo_30 = $(e).find("td:eq(5)").html();
        saldo_30 = saldo_30.replace(/\./g,"");
        saldo_30 = saldo_30.replace(/\,/g,".");
        var saldo = $(e).find("td:eq(6)").html();
        saldo = saldo.replace(/\./g,"");
        saldo = saldo.replace(/\,/g,".");
        var compra = $(e).find("td:eq(7)").html();
        compra = compra.replace(/\./g,"");
        compra = compra.replace(/\,/g,".");
        var pago = $(e).find("td:eq(9)").html();
        pago = pago.replace(/\./g,"");        
        pago = pago.replace(/\,/g,".");

        array.push({
          "id":$.trim($(e).find("td:eq(0)").html()),
          "nombre":$.trim($(e).find("td:eq(1) span").html()),
          "saldo_mas_90":saldo_mas_90,
          "saldo_90":saldo_90,
          "saldo_60":saldo_60,
          "saldo_30":saldo_30,
          "saldo":saldo,
          "compra":compra,
          "fecha_ult_compra":$(e).find("td:eq(8)").html(),
          "pago":pago,
          "fecha_ult_pago":$(e).find("td:eq(10)").html(),
        });
      });
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });

      var footer = new Array();
      footer[0] = "";
      footer[1] = "";
      footer[2] = this.$("#deuda_proveedores_total_saldo_mas_90").html();
      footer[3] = this.$("#deuda_proveedores_total_saldo_90").html();
      footer[4] = this.$("#deuda_proveedores_total_saldo_60").html();
      footer[5] = this.$("#deuda_proveedores_total_saldo_30").html();
      footer[6] = this.$("#deuda_proveedores_total_saldo").html();
      footer[7] = this.$("#deuda_proveedores_total_compras").html();
      footer[8] = "";
      footer[9] = this.$("#deuda_proveedores_total_pagos").html();
      footer[10] = "";

      this.exportar_excel({
        "filename":"deuda",
        "title":"Deuda con proveedores",
        "date":fecha,
        "data":array,
        "header":header,
        "footer":footer,
      });         
    },


    addAll : function () {
      $(this.el).find("tbody").empty();
      this.saldo = 0;
      this.saldo_mas_90 = 0;
      this.saldo_90 = 0;
      this.saldo_60 = 0;
      this.saldo_30 = 0;
      this.total_compras = 0;
      this.total_pagos = 0;
      if (this.collection.size() == 0) {
        $(this.el).find("tbody").append("<tr><td colspan='10'>No se encontraron resultados.</td></tr>");    
      } else {
        this.collection.each(this.addOne);        
      }
      this.$("#deuda_proveedores_total_saldo").text(Number(this.saldo).format(2));
      //TODO: POR EL MOMENTO ESTA DESACTIVADO
      //this.$("#deuda_proveedores_total_saldo_mas_90").text(Number(this.saldo_mas_90).format(2));
      //this.$("#deuda_proveedores_total_saldo_90").text(Number(this.saldo_90).format(2));
      //this.$("#deuda_proveedores_total_saldo_60").text(Number(this.saldo_60).format(2));
      //this.$("#deuda_proveedores_total_saldo_30").text(Number(this.saldo_30).format(2));
      //this.$("#deuda_proveedores_total_compras").text(Number(this.total_compras).format(2));
      //this.$("#deuda_proveedores_total_pagos").text(Number(this.total_pagos).format(2));
      //var total = Number(this.saldo) + Number(this.total_compras) + Number(this.total_pagos);
      //this.$("#deuda_proveedores_total").text(Number(total).format(2));
    },

    addOne : function ( item ) {
      var self = this;

      var saldo = parseFloat(item.get("saldo"));
      if (isNaN(saldo)) saldo = 0;
      this.saldo += saldo;

      var saldo_mas_90 = parseFloat(item.get("saldo_mas_90"));
      if (isNaN(saldo_mas_90)) saldo_mas_90 = 0;
      this.saldo_mas_90 += saldo_mas_90;

      var saldo_90 = parseFloat(item.get("saldo_90"));
      if (isNaN(saldo_90)) saldo_90 = 0;
      this.saldo_90 += saldo_90;

      var saldo_60 = parseFloat(item.get("saldo_60"));
      if (isNaN(saldo_60)) saldo_60 = 0;
      this.saldo_60 += saldo_60;

      var saldo_30 = parseFloat(item.get("saldo_30"));
      if (isNaN(saldo_30)) saldo_30 = 0;
      this.saldo_30 += saldo_30;

      /*
      var total_compras = parseFloat(item.get("total_compras"));
      if (isNaN(total_compras)) total_compras = 0;
      this.total_compras += total_compras;

      var total_pagos = parseFloat(item.get("total_pagos"));
      if (isNaN(total_pagos)) total_pagos = 0;
      this.total_pagos += total_pagos;
      */

      var view = new app.views.DeudaProveedoresItemResultados({
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

  app.views.DeudaProveedoresItemResultados = Backbone.View.extend({
    template: _.template($("#deuda_proveedores_item_resultados_template").html()),
    events: {
      "click":function() {
        window.open("app/#cuentas_corrientes_proveedores/"+this.model.id,"_blank");
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
