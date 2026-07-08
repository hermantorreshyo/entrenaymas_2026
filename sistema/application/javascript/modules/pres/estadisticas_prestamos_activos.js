(function ( models ) {

  models.EstadisticasPrestamosActivos = Backbone.Model.extend({
    urlRoot: "estadisticas_prestamos_activos/",
  });

})( app.models );


(function (collections, model, paginator) {
  collections.EstadisticasPrestamosActivos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "estadisticas/function/prestamos_activos/"
    },
    paginator_ui: {
      perPage: 99999,
    },
  });
})( app.collections, app.models.EstadisticasPrestamosActivos, Backbone.Paginator);


(function ( app ) {
  app.views.EstadisticasPrestamosActivosItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_prestamos_activos_item').html()),
    events: {
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );


(function ( app ) {

  app.views.EstadisticasPrestamosActivosTableView = app.mixins.View.extend({

    template: _.template($("#estadisticas_prestamos_activos_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #estadisticas_prestamos_activos_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
      "change #estadisticas_prestamos_activos_sucursales":"buscar",
      /*
      "click .imprimir":function() {
        var url = "pres_prestamos/function/buenos_clientes/?imprimir=1";
        url += "&id_sucursal="+window.estadisticas_prestamos_activos_id_sucursal;
        url += "&texto="+encodeURIComponent(window.estadisticas_prestamos_activos_filter);
        workspace.imprimir_reporte(url);
      },
      */
      "click .exportar":function() {
        var self = this;
        var array = new Array();
        $("#estadisticas_prestamos_activos_table tbody tr").each(function(i,e){
          array.push({
            "plan":$.trim($(e).find("td:eq(0) a").text()),
            "activos":$.trim($(e).find("td:eq(1) span").text()),
            "mora_30":$.trim($(e).find("td:eq(2) span").text()),
            "mora_60":$.trim($(e).find("td:eq(3) span").text()),
            "mora_90":$.trim($(e).find("td:eq(4) span").text()),
            "mora_mas_90":$.trim($(e).find("td:eq(5) span").text()),
            "mora":$.trim($(e).find("td:eq(6) span").text()),
            "porc_mora":$.trim($(e).find("td:eq(7) span").text()),
            "cuota_mas_elegida":$.trim($(e).find("td:eq(8) span").text()),
            "cantidad_veces":$.trim($(e).find("td:eq(9) span").text()),
          });
        });
        // Totales
        $("#estadisticas_prestamos_activos_table tfoot tr").each(function(i,e){
          array.push({
            "plan":$.trim($(e).find("td:eq(0)").text()),
            "activos":$.trim($(e).find("td:eq(1)").text()),
            "mora_30":$.trim($(e).find("td:eq(2)").text()),
            "mora_60":$.trim($(e).find("td:eq(3)").text()),
            "mora_90":$.trim($(e).find("td:eq(4)").text()),
            "mora_mas_90":$.trim($(e).find("td:eq(5)").text()),
            "mora":$.trim($(e).find("td:eq(6)").text()),
            "porc_mora":$.trim($(e).find("td:eq(7)").text()),
            "cuota_mas_elegida":$.trim($(e).find("td:eq(8)").text()),
            "cantidad_veces":$.trim($(e).find("td:eq(9)").text()),
          });
        });
        var header = new Array("Plan","Activos","1 a 30","31 a 60","61 a 90","Mas 90","Total","% Mora","Cuota mas elegida","Cantidad de veces");
        this.exportar_excel({
          "filename":"Prestamos Activos",
          "title":"Prestamos Activos",
          "data":array,
          "header":header,
        }); 
      },
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;
      this.collection.on('sync', this.addAll, this);

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(this.pagination.el);
      this.buscar();
    },

    buscar: function() {
      if (window.estadisticas_prestamos_activos_id_sucursal != this.$("#estadisticas_prestamos_activos_sucursales").val()) {
        window.estadisticas_prestamos_activos_id_sucursal = this.$("#estadisticas_prestamos_activos_sucursales").val();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.estadisticas_prestamos_activos_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "id_sucursal":window.estadisticas_prestamos_activos_id_sucursal,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.estadisticas_prestamos_activos_page);
    },

    addAll : function () {
      this.$("tfoot").empty();
      this.total_activos = 0;
      this.total_mora = 0;
      this.total_mora_30 = 0;
      this.total_mora_60 = 0;
      this.total_mora_90 = 0;
      this.total_mora_mas_90 = 0;
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
      var tr = "<tr>";
      tr+="<td></td>";
      tr+="<td>"+Number(this.total_activos).toFixed(0)+"</td>";
      tr+="<td>"+Number(this.total_mora_30).toFixed(0)+"</td>";
      tr+="<td>"+Number(this.total_mora_60).toFixed(0)+"</td>";
      tr+="<td>"+Number(this.total_mora_90).toFixed(0)+"</td>";
      tr+="<td>"+Number(this.total_mora_mas_90).toFixed(0)+"</td>";
      tr+="<td>"+Number(this.total_mora).toFixed(0)+"</td>";
      tr+="<td>"+((this.total_activos > 0) ? Number(this.total_mora / this.total_activos * 100).toFixed(2) : Number(0).toFixed(2))+" %</td>";
      tr+="<td></td>";
      tr+="<td></td>";
      tr+="</tr>";
      this.$("tfoot").append(tr);
    },

    addOne : function ( item ) {
      var view = new app.views.EstadisticasPrestamosActivosItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
      this.total_activos += parseFloat(item.get("cantidad"));
      this.total_mora += parseFloat(item.get("cantidad_mora"));
      this.total_mora_30 += parseFloat(item.get("cantidad_mora_30"));
      this.total_mora_60 += parseFloat(item.get("cantidad_mora_60"));
      this.total_mora_90 += parseFloat(item.get("cantidad_mora_90"));
      this.total_mora_mas_90 += parseFloat(item.get("cantidad_mora_mas_90"));
    }

  });
})(app);