(function (collections, paginator) {

  collections.ListadoSaldosProveedores = paginator.requestPager.extend({

    paginator_ui: {
      perPage: 999999,
      order_by: 'nombre',
      order: 'asc',
    },
    paginator_core: {
      url:"proveedores/function/listado_saldos",
    }

  });

})( app.collections, Backbone.Paginator);


(function ( app ) {

  app.views.ListadoSaldosProveedoresResultados = app.mixins.View.extend({

    template: _.template($("#listado_saldos_proveedores_resultados_template").html()),

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
      var fecha = this.$("#listado_saldos_proveedores_fecha").val().replace(/\//g,"-");
      if (isEmpty(fecha)) {
        show("Por favor seleccione una fecha");
        this.$("#listado_saldos_proveedores_fecha").focus();
        return;                
      }
      var datos = {};
      datos.fecha = fecha;
      datos.filtrar_en_cero = this.$("#listado_saldos_filtrar_en_cero").is(":checked") ? 1 : 0;
      datos.tipo_proveedor = this.$("#listado_saldos_tipo_proveedor").val();
      datos.fecha_desde = this.$("#listado_saldos_proveedores_fecha_desde").val();
      this.collection.server_api = datos;
      this.collection.pager();
    },        

    render: function() {
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#listado_saldos_proveedores_fecha"),new Date());
      createdatepicker($(this.el).find("#listado_saldos_proveedores_fecha_desde"));
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
      var fecha = $("#listado_saldos_proveedores_fecha").val();

      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "id":$.trim($(e).find("td:eq(0)").html()),
          "nombre":$.trim($(e).find("td:eq(1)").html()),
          "saldo":$(e).find("td:eq(2)").html(),
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
      footer[2] = $("#listado_saldos_proveedores_total").html();

      this.exportar_excel({
        "filename":"listado_saldos",
        "title":"Listado de Saldos",
        "date":fecha,
        "data":array,
        "header":header,
        "footer":footer,
      });         
    },


    addAll : function () {
      $(this.el).find("tbody").empty();
      this.total = 0;
      if (this.collection.size() == 0) {
        $(this.el).find("tbody").append("<tr><td colspan='10'>No se encontraron resultados.</td></tr>");    
      } else {
        this.collection.each(this.addOne);        
      }
      this.$("#listado_saldos_proveedores_total").text(Number(this.total).toFixed(2));
    },        

    addOne : function ( item ) {
      var self = this;
      var total = parseFloat(item.get("saldo"));
      if (isNaN(total)) total = 0;
      this.total += total;
      var view = new app.views.ListadoSaldosProveedoresItemResultados({
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

  app.views.ListadoSaldosProveedoresItemResultados = Backbone.View.extend({

    template: _.template($("#listado_saldos_proveedores_item_resultados_template").html()),

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
