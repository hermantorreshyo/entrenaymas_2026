(function (collections, paginator) {

  collections.ListadoSaldosClientes = paginator.requestPager.extend({

    paginator_ui: {
      perPage: 999999,
      order_by: 'id',
      order: 'asc',
    },
    paginator_core: {
      url:"clientes/function/listado_saldos",
    }

  });

})( app.collections, Backbone.Paginator);


(function ( app ) {

  app.views.ListadoSaldosClientesResultados = app.mixins.View.extend({

    template: _.template($("#listado_saldos_clientes_resultados_template").html()),

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
      var fecha = this.$("#listado_saldos_clientes_fecha").val().replace(/\//g,"-");
      if (isEmpty(fecha)) {
        show("Por favor seleccione una fecha");
        this.$("#listado_saldos_clientes_fecha").focus();
        return;                
      }
      var filtrar_en_cero = this.$("#listado_saldos_filtrar_en_cero").is(":checked") ? 1 : 0;
      var agrupado_por = this.$("#listado_saldos_clientes_agrupado_por").val();
      var id_etiqueta = this.$("#listado_saldos_clientes_etiquetas").val();
      var fecha_desde = this.$("#listado_saldos_clientes_fecha_desde").val();
      this.collection.server_api = {
        fecha: fecha,
        agrupado_por: agrupado_por,
        filtrar_en_cero: filtrar_en_cero,
        id_etiqueta: id_etiqueta,
        fecha_desde: fecha_desde,
      };
      this.collection.pager();
    },        

    render: function() {
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#listado_saldos_clientes_fecha"),new Date());
      createdatepicker($(this.el).find("#listado_saldos_clientes_fecha_desde"));

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.ClienteEtiqueta,
        url: "clientes_etiquetas/",
        firstOptions: ["<option value='0'>Etiqueta</option>"],
        render: "#listado_saldos_clientes_etiquetas",
        onComplete:function(c) {
          crear_select2("listado_saldos_clientes_etiquetas");
        }
      });

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
      var fecha = $("#listado_saldos_clientes_fecha").val();

      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "id":$.trim($(e).find("td:eq(0)").html()),
          "nombre":$.trim($(e).find("td:eq(1)").html()),
          "observaciones":$.trim($(e).find("td:eq(2)").html()),
          "saldo":$(e).find("td:eq(3)").html(),
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
      footer[2] = "";
      footer[3] = $("#listado_saldos_clientes_total").html();

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
      this.$("#listado_saldos_clientes_total").text(Number(this.total).toFixed(2));
    },        

    addOne : function ( item ) {
      var self = this;
      var total = parseFloat(item.get("saldo"));
      if (isNaN(total)) total = 0;
      if (item.get("codigo")>=0) this.total += total;
      var view = new app.views.ListadoSaldosClientesItemResultados({
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

  app.views.ListadoSaldosClientesItemResultados = Backbone.View.extend({

    template: _.template($("#listado_saldos_clientes_item_resultados_template").html()),

    events: {
      "click":function() {
        window.open("app/#cuentas_corrientes_clientes/"+this.model.id,"_blank");
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
