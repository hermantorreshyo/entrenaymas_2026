(function ( app ) {

  app.views.DeudaTotalesResultados = app.mixins.View.extend({

    template: _.template($("#deuda_totales_resultados_template").html()),

    myEvents: {
      "click #checkTodos": "seleccionar_todos",
      "click .exportar":"exportar",
      "click .generar": "buscar",
    },

    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },

    exportar: function() {
      var fecha = this.$("#deuda_totales_fecha").val().replace(/\//g,"-");
      window.open("proveedores/function/totales_deuda/?excel=1&fecha="+fecha,"_blank");
    },

    buscar : function() {
      var self = this;
      var datos = {};
      datos.fecha = this.$("#deuda_totales_fecha").val().replace(/\//g,"-");
      if (isEmpty(datos.fecha)) {
        show("Por favor seleccione una fecha");
        this.$("#deuda_totales_fecha").focus();
        return;                
      }
      self.$("#deuda_totales_tabla thead").empty();
      self.$("#deuda_totales_tabla tbody").empty();
      $.ajax({
        "url":"proveedores/function/totales_deuda/",
        "data":datos,
        "dataType":"json",
        "type":"post",
        "success":function(r) {

          // Encabezado
          var tr = "<tr>";
          tr+="<th>Nombre</th>";
          for(var i=0; i<r.sucursales.length;i++) {
            var suc = r.sucursales[i];
            tr+="<th class='w200'>"+suc.nombre+"</th>";
          }
          tr+= "</tr>";
          self.$("#deuda_totales_tabla thead").append(tr);

          // Proveedores
          for(var i=0; i<r.proveedores.length;i++) {
            var prov = r.proveedores[i];
            var t = "<tr>";
            t +="<td>"+prov.nombre+"</td>";
            for(var j=0; j<prov.sucursales.length;j++) {
              var s = prov.sucursales[j];
              t +="<td class='tar'>"+Number(s).toFixed(2)+"</td>";
            }
            t += "</tr>";
            self.$("#deuda_totales_tabla tbody").append(t);
          }
        }
      })
    },        

    render: function() {
      var self = this;
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#deuda_totales_fecha"),new Date());
      return this;
    },

  });

})(app);