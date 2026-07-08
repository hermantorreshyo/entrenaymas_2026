(function ( app ) {

  app.views.EstadisticasComprasVentasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_compras_ventas_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",
      "click .exportar_solo_ventas":"exportar_solo_ventas",
      "click .cerrar":"cerrar",
      "change #estadisticas_compras_ventas_fecha_filter":"buscar",
      "click .sorting":function(e) {
        var asc = $(e.currentTarget).hasClass("sorting_asc");
        var desc = $(e.currentTarget).hasClass("sorting_desc");
        $(".sorting").removeClass("sorting_asc");
        $(".sorting").removeClass("sorting_desc");
        if (asc) $(e.currentTarget).addClass("sorting_desc");
        else if (desc) $(e.currentTarget).addClass("sorting_asc");
        else $(e.currentTarget).addClass("sorting_desc");

        var sort_by = $(e.currentTarget).data("sort-by");
        if (sort_by == undefined) return;
        var sort = (desc)?"desc":"asc";
        this.order_by = sort_by;
        this.order = sort;
        this.buscar();
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.desde = (typeof options.desde != "undefined") ? moment(options.desde).toDate() : moment().startOf("month").toDate();
      this.hasta = (typeof options.hasta != "undefined") ? moment(options.hasta).toDate() : new Date();
      this.order_by = (typeof options.order_by != "undefined") ? options.order_by : "nombre";
      this.order = (typeof options.order != "undefined") ? options.order : "asc";
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_compras_ventas_fecha_desde").val();
      params.hasta = self.$("#estadisticas_compras_ventas_fecha_hasta").val();
      params.filter = self.$("#estadisticas_compras_ventas_fecha_filter").val();
      params.id_sucursal = self.$("#estadisticas_compras_ventas_sucursales").val();
      if (params.id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        self.$("#estadisticas_compras_ventas_sucursales").focus();
        return;
      }
      params.id_proveedor = self.$("#estadisticas_compras_ventas_proveedores").val();
      if (params.id_proveedor == 0) {
        alert("Por favor seleccione un proveedor");
        self.$("#estadisticas_compras_ventas_proveedores").focus();
        return;
      }
      params.order = this.order;
      params.order_by = this.order_by;
      $.ajax({
        "url":"estadisticas/function/compras_ventas_periodo/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.$("#estadisticas_compras_ventas_table tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var elem = r.results[i];
            var item = new app.views.EstadisticasComprasVentasItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_compras_ventas_table tbody").append(item.el);
          }
        },
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      createdatepicker($(this.el).find("#estadisticas_compras_ventas_fecha_desde"),this.desde);
      createdatepicker($(this.el).find("#estadisticas_compras_ventas_fecha_hasta"),this.hasta);

      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#estadisticas_compras_ventas_proveedores",
        firstOptions: ["<option value='0'>Proveedor</option>"],
        onComplete:function(c) {
          crear_select2("estadisticas_compras_ventas_proveedores");
        }
      });
    },

    exportar: function() {
      var array = new Array();
      $("#estadisticas_compras_ventas_table tbody tr").each(function(i,e){
        array.push({
          "codigo":$(e).find("td:eq(0)").html(),
          "ean":$(e).find("td:eq(1)").html().replaceAll('<br>'," | "),
          "codigo_prov":$(e).find("td:eq(2)").html(),
          "nombre":$(e).find("td:eq(3) span").text(),
          "costo":$(e).find("td:eq(4)").html(),
          "precio":$(e).find("td:eq(5)").html(),
          "margen":$(e).find("td:eq(6)").html(),
          "compra":$(e).find("td:eq(7)").html(),
          "ult_compra":$(e).find("td:eq(8)").html(),
          "venta":$(e).find("td:eq(9)").html(),
          "ult_venta":$(e).find("td:eq(10)").html(),
          "diferencia":$(e).find("td:eq(11)").html(),
          "porcentaje":$(e).find("td:eq(12)").html(),
          "stock":$(e).find("td:eq(13)").html(),
        });
      });
      var titulo = "";
      if ($("#estadisticas_compras_ventas_proveedores").length > 0) {
        titulo += $("#estadisticas_compras_ventas_proveedores option:selected").text();
      }
      if ($("#estadisticas_compras_ventas_sucursales").length > 0) {
        var sucursal = $("#estadisticas_compras_ventas_sucursales option:selected").text();
        if (ID_EMPRESA == 249 && sucursal.indexOf("-")>0) {
          var v = sucursal.split("-");
          sucursal = v[1];
          sucursal = sucursal.trim();
        }        
        titulo += ((isEmpty(titulo)) ? "" : " - ") + sucursal+" ";
      }
      var header = new Array("Codigo","EAN","Cod. Prov.","Nombre","Costo","Precio","Margen","Compra","Ult. Compra","Venta","Ult. Venta","Diferencia","% Vendido","Stock");
      this.exportar_excel({
        "filename":titulo,
        "title":titulo,
        "data":array,
        "header":header,
      });
    },

    exportar_solo_ventas: function() {
      var array = new Array();
      $("#estadisticas_compras_ventas_table tbody tr").each(function(i,e){
        var venta = $(e).find("td:eq(9)").html();
        var compra = $(e).find("td:eq(7)").html();
        if (venta != 0 || compra != 0) {
          array.push({
            "codigo":$(e).find("td:eq(0)").html(),
            "ean":$(e).find("td:eq(1)").html().replaceAll('<br>'," | "),
            "codigo_prov":$(e).find("td:eq(2)").html(),
            "nombre":$(e).find("td:eq(3) span").text(),
            "costo":$(e).find("td:eq(4)").html(),
            "precio":$(e).find("td:eq(5)").html(),
            "margen":$(e).find("td:eq(6)").html(),
            "compra":$(e).find("td:eq(7)").html(),
            "ult_compra":$(e).find("td:eq(8)").html(),
            "venta":$(e).find("td:eq(9)").html(),
            "ult_venta":$(e).find("td:eq(10)").html(),
            "diferencia":$(e).find("td:eq(11)").html(),
            "porcentaje":$(e).find("td:eq(12)").html(),
            "stock":$(e).find("td:eq(13)").html(),
          });
        }
      });
      var titulo = "";
      if ($("#estadisticas_compras_ventas_proveedores").length > 0) {
        titulo += $("#estadisticas_compras_ventas_proveedores option:selected").text();
      }
      if ($("#estadisticas_compras_ventas_sucursales").length > 0) {
        var sucursal = $("#estadisticas_compras_ventas_sucursales option:selected").text();
        if (ID_EMPRESA == 249 && sucursal.indexOf("-")>0) {
          var v = sucursal.split("-");
          sucursal = v[1];
          sucursal = sucursal.trim();
        }        
        titulo += ((isEmpty(titulo)) ? "" : " - ") + sucursal+" ";        
      }
      var header = new Array("Codigo","EAN","Cod. Prov.","Nombre","Costo","Precio","Margen","Compra","Ult. Compra","Venta","Ult. Venta","Diferencia","% Vendido","Stock");
      this.exportar_excel({
        "filename":titulo,
        "title":titulo,
        "data":array,
        "header":header,
      });
    },    
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasComprasVentasPorArticulosView = app.mixins.View.extend({

    template: _.template($("#estadisticas_compras_ventas_por_articulos_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",
      "click .cerrar":"cerrar",
      "change #estadisticas_compras_ventas_por_articulos_filter":"buscar",
      "click .sorting":function(e) {
        var asc = $(e.currentTarget).hasClass("sorting_asc");
        var desc = $(e.currentTarget).hasClass("sorting_desc");
        $(".sorting").removeClass("sorting_asc");
        $(".sorting").removeClass("sorting_desc");
        if (asc) $(e.currentTarget).addClass("sorting_desc");
        else if (desc) $(e.currentTarget).addClass("sorting_asc");
        else $(e.currentTarget).addClass("sorting_desc");

        var sort_by = $(e.currentTarget).data("sort-by");
        if (sort_by == undefined) return;
        var sort = (desc)?"desc":"asc";
        this.order_by = sort_by;
        this.order = sort;
        this.buscar();
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.desde = (typeof options.desde != "undefined") ? moment(options.desde).toDate() : moment().startOf("month").toDate();
      this.hasta = (typeof options.hasta != "undefined") ? moment(options.hasta).toDate() : new Date();
      this.order_by = (typeof options.order_by != "undefined") ? options.order_by : "nombre";
      this.order = (typeof options.order != "undefined") ? options.order : "asc";
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_compras_ventas_por_articulos_fecha_desde").val();
      params.hasta = self.$("#estadisticas_compras_ventas_por_articulos_fecha_hasta").val();
      params.codigos_articulos = self.$("#estadisticas_compras_ventas_por_articulos_filter").val().replace(/\,/g,"-");
      params.order = this.order;
      params.order_by = this.order_by;
      $.ajax({
        "url":"estadisticas/function/compras_ventas_por_articulos/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.$("#estadisticas_compras_ventas_por_articulos_table tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var elem = _.extend({
              cantidad_compra:0,
              fecha_compra:"",
              cantidad_venta:0,
              fecha_venta:"",
              costo_final:0, 
              precio_final_dto:0,
              stock:0,
              porcentaje:0,
              id_articulo: -1, // Para indicar que es un subtitulo
              id_proveedor: 0,
              codigo: "",
              nombre: "",
              codigo_barra: "",
              codigo_prov: "",
            },r.results[i]);
            var item = new app.views.EstadisticasComprasVentasItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_compras_ventas_por_articulos_table tbody").append(item.el);
          }
        },
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      createdatepicker($(this.el).find("#estadisticas_compras_ventas_por_articulos_fecha_desde"),this.desde);
      createdatepicker($(this.el).find("#estadisticas_compras_ventas_por_articulos_fecha_hasta"),this.hasta);
    },

    exportar: function() {
      var array = new Array();
      $("#estadisticas_compras_ventas_por_articulos_table tbody tr").each(function(i,e){
        array.push({
          "codigo":$(e).find("td:eq(0)").html(),
          "ean":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(1)").html().replaceAll('<br>'," | ") : "",
          "codigo_prov":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(2)").html() : "",
          "nombre":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(3) span").text() : "",
          "costo":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(4)").html() : "",
          "precio":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(5)").html() : "",
          "margen":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(6)").html() : "",
          "compra":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(7)").html() : "",
          "ult_compra":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(8)").html() : "",
          "venta":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(9)").html() : "",
          "ult_venta":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(10)").html() : "",
          "diferencia":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(11)").html() : "",
          "porcentaje":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(12)").html() : "",
          "stock":($(e).find("td:eq(1)").length > 0) ? $(e).find("td:eq(13)").html() : "",
        });
      });
      var header = new Array("Codigo","EAN","Cod. Prov.","Nombre","Costo","Precio","Margen","Compra","Ult. Compra","Venta","Ult. Venta","Diferencia","% Vendido","Stock");
      this.exportar_excel({
        "filename":"estadisticas",
        "title":"Resumen",
        "data":array,
        "header":header,
      });
    },
        
  });
})(app);



(function ( app ) {

  app.views.EstadisticasComprasVentasItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_compras_ventas_item_template').html()),
    myEvents: {
      "click .ver_detalle":"ver_detalle",
    },
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },
    ver_detalle: function() {
      var self = this;
      var detalle = new app.views.StockDetalleView({
        tab_default: "grafico",
        model:new app.models.AbstractModel({
          "desde":$("#estadisticas_compras_ventas_fecha_desde").val(),
          "hasta":$("#estadisticas_compras_ventas_fecha_hasta").val(),
          "nombre":self.model.get("nombre"),
          "codigo":self.model.get("codigo"),
          "id_articulo":self.model.get("id_articulo"),
          "id_sucursal":self.model.get("id_almacen"),
        }),
      });        
      crearLightboxHTML({
        "html":detalle.el,
        "width":800,
        "height":500,
        "escapable":false,
      });
    },
    render: function() {
      var obj = { 
        permiso: this.permiso,
        total_general: this.total_general,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );