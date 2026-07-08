(function ( app ) {

  app.views.AsistenciasTableView = app.mixins.View.extend({
    template: _.template($("#asistencias_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
      "change #asistencias_buscar_fecha":"buscar",
      "click .guardar":"guardar",
      "click .imprimir":function() {
        var self = this;
        self.id_materia = (ASISTENCIA_ALUMNO_POR_MATERIA == 1) ? this.$("#asistencias_buscar_materias").val() : 0;
        self.fecha = this.$("#asistencias_buscar_fecha").val().replace(/\//g,"-");
        var url = "asistencias/function/imprimir/";
        url+="?id_comision="+window.comision.id;
        url+="&id_materia="+self.id_materia;
        url+="&fecha="+self.fecha;
        workspace.imprimir_reporte(url);
      },
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.id_materia = (typeof this.options.id_materia != "undefined") ? this.options.id_materia : 0;
      this.id_clase = 0;
      this.render();
      this.buscar();
    },

    render: function() {
      var self = this;
      var self = this;
      var obj = window.comision.toJSON();
      obj.id = window.comision.id;
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      obj.id_clase = this.id_clase;
      $(this.el).html(this.template(obj));
      createdatepicker($(this.el).find("#asistencias_buscar_fecha"),new Date());
    },

    render_materias: function() {
      // El filtro por materias NO es obligatorio
      var self = this;
      var id_carrera = window.comision.get("id_carrera");
      var anio = window.comision.get("anio");
      $.ajax({
        "url":"materias/function/get_select/",
        "dataType":"json",
        "data": {
          "id_carrera":id_carrera,
          "anio":anio,
          //"id_docente":ID_DOCENTE, // Si esta logueado un profesor, filtramos solo por sus materias
        },
        "success":function(res) {
          var r = "";
          for(var i=0; i<res.results.length;i++) {
            var o = res.results[i];
            r += "<option "+((o.id == self.id_materia) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#asistencias_buscar_materias").html(r);
        },
      });
    },

    calcular: function() {
      var asistencias = 0;
      var inasistencias = 0;
      var total = $("#asistencias_table tbody tr").length;
      $("#asistencias_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") asistencias++;
          else if (condicion == "A" || condicion == "J") inasistencias++;
        }
      });
      var porc_in = ((total > 0) ? (inasistencias / total)*100 : 0);
      $("#asistencias_inasistencia").html(inasistencias+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (asistencias / total)*100 : 0);
      $("#asistencias_asistencia").html(asistencias+" ("+Number(porc_as).toFixed(2)+" %)");
    },

    buscar: function() {
      var self = this;
      self.id_materia = (ASISTENCIA_ALUMNO_POR_MATERIA == 1) ? this.$("#asistencias_buscar_materias").val() : 0;
      self.fecha = this.$("#asistencias_buscar_fecha").val().replace(/\//g,"-");
      $.ajax({
        "url":"asistencias/function/buscar/",
        "dataType":"json",
        "type":"get",
        "data": {
          "id_comision":window.comision.id,
          "id_materia":self.id_materia,
          "fecha":self.fecha,
        },
        "success":function(res) {
          if (res.error == 0) {
            self.id_clase = res.id_clase;
            self.render_table(res.results);
            self.calcular();
          } else {
            var tr = "<tr><td colspan='20'>No hay clases cargadas en esta fecha.</td></tr>";
            self.$("#asistencias_table .tbody").empty();
            self.$("#asistencias_table .tbody").append(tr);
          }
        },
      });
    },  

    render_table: function(res) {
      var self = this;
      this.$("#asistencias_table .tbody").empty();
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.AsistenciasItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
        });
        this.$("#asistencias_table .tbody").append(view.el);
      }
    },

    guardar: function() {
      var self = this;
      var error = false;
      var asistencias = new Array();
      $("#asistencias_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length == 0) {
          alert("Por favor seleccione un tipo de asistencia");
          error = true;
          return false;
        }
        asistencias.push({
          "id_alumno":$(e).find(".id_alumno").val(),
          "fecha":$(e).find(".fecha").val(),
          "observaciones":$(e).find(".observaciones").val(),
          "condicion":$(e).find(".condicion.active").data("valor"),
        });
      });
      if (error) return false;
      $.ajax({
        "url":"asistencias/function/guardar/",
        "dataType":"json",
        "type":"post",
        "data": {
          "asistencias":JSON.stringify(asistencias),
          "id_clase": self.id_clase,
        },
        "success":function(r) {
          if (r.error == 0) {
            window.history.back();
          } else {
            alert("Hubo un error al guardar las asistencias.");
          }
        },
        "error":function() {
          alert("Hubo un error al guardar las asistencias.");
        },
      });
    },

  });
})(app);

(function ( app ) {
  app.views.AsistenciasItemView = app.mixins.View.extend({
    template: _.template($("#asistencias_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .condicion":function(e){
        this.$(".condicion.active").removeClass("btn-primary");
        this.$(".condicion.active").removeClass("btn-danger");
        this.$(".condicion.active").removeClass("btn-warning");
        this.$(".condicion.active").removeClass("btn-success");
        this.$(".condicion").removeClass("active");
        var condicion = $(e.currentTarget).data("valor");
        $(e.currentTarget).addClass("active");
        if (condicion == "P") $(e.currentTarget).addClass('btn-success');
        else if (condicion == "T") $(e.currentTarget).addClass('btn-warning');
        else if (condicion == "A") $(e.currentTarget).addClass('btn-danger');
        else if (condicion == "J") $(e.currentTarget).addClass('btn-primary');
        this.view.calcular();
      },
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);


// ===============================================
//
// ASISTENCIAS ENTRE FECHAS


(function ( app ) {

  app.views.ReporteAsistenciasTableView = app.mixins.View.extend({
    template: _.template($("#asistencias_reporte_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
      "change .trimestre_select":function(e) {
        var s = this.$(e.currentTarget).find("option:selected");
        this.$("#asistencias_reporte_fecha_desde").val($(s).data("desde"));
        this.$("#asistencias_reporte_fecha_hasta").val($(s).data("hasta"));
        if ($(e.currentTarget).val() == 0) { 
          this.$("#asistencias_reporte_fecha_desde").parent().show();
          this.$("#asistencias_reporte_fecha_hasta").parent().show();
        } else {
          this.$("#asistencias_reporte_fecha_desde").parent().hide();
          this.$("#asistencias_reporte_fecha_hasta").parent().hide();
        }
        this.buscar();
      },
      "change #asistencias_reporte_fecha_desde":"buscar",
      "change #asistencias_reporte_fecha_hasta":"buscar",
      "click .imprimir":function() {
        var self = this;

        var fechas = new Array();
        this.$(".check_fecha:checked").each(function(i,e){
          var v = $(e).val();
          v = v.replace(/\//g,"-");
          fechas.push(v);
        });
        if (fechas.length == 0) {
          alert("Por favor marque las fechas que desea imprimir.");
          return;
        }
        if (fechas.length > 5) {
          alert("Por favor marque un maximo de hasta 5 fechas para imprimir.");
          return;          
        }
        self.id_materia = (ASISTENCIA_ALUMNO_POR_MATERIA == 1) ? this.$("#asistencias_buscar_materias").val() : 0;
        self.fecha_desde = this.$("#asistencias_reporte_fecha_desde").val();
        self.fecha_hasta = this.$("#asistencias_reporte_fecha_hasta").val();
        var url = "asistencias/function/imprimir_entre_fechas/";
        url+="?id_comision="+window.comision.id;
        url+="&id_materia="+self.id_materia;
        url+="&fechas="+fechas.join("--");
        workspace.imprimir_reporte(url);
      },
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.id_materia = (typeof this.options.id_materia != "undefined") ? this.options.id_materia : 0;
      this.clases = new Array();
      this.render();
    },

    render: function() {
      var self = this;
      var obj = window.comision.toJSON();
      obj.id = window.comision.id;
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      obj.clases = this.clases;
      obj.fecha_desde = this.fecha_desde;
      obj.fecha_hasta = this.fecha_hasta;
      $(this.el).html(this.template(obj));

      this.$(".trimestre_select").trigger("change");
      this.fecha_desde = this.$("#asistencias_reporte_fecha_desde").val();
      this.fecha_hasta = this.$("#asistencias_reporte_fecha_hasta").val();

      createdatepicker($(this.el).find("#asistencias_reporte_fecha_desde"),moment(this.fecha_desde,"DD/MM/YYYY").toDate());
      createdatepicker($(this.el).find("#asistencias_reporte_fecha_hasta"),moment(this.fecha_hasta,"DD/MM/YYYY").toDate());
    },

    render_materias: function() {
      // El filtro por materias NO es obligatorio
      var self = this;
      $.ajax({
        "url":"materias/function/get_select/",
        "dataType":"json",
        "data": {
          "id_carrera":window.comision.get("id_carrera"),
          "anio":window.comision.get("anio"),
          //"id_docente":ID_DOCENTE, // Si esta logueado un profesor, filtramos solo por sus materias
        },
        "success":function(res) {
          var r = "";
          for(var i=0; i<res.results.length;i++) {
            var o = res.results[i];
            r += "<option "+((o.id == self.id_materia) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#asistencias_reporte_materias").html(r);
        },
      });
    },

    calcular: function() {
      var asistencias = 0;
      var inasistencias = 0;
      var total = $("#asistencias_table tbody tr").length;
      $("#asistencias_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") asistencias++;
          else if (condicion == "A" || condicion == "J") inasistencias++;
        }
      });
      var porc_in = ((total > 0) ? (inasistencias / total)*100 : 0);
      $("#asistencias_inasistencia").html(inasistencias+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (asistencias / total)*100 : 0);
      $("#asistencias_asistencia").html(asistencias+" ("+Number(porc_as).toFixed(2)+" %)");
    },

    buscar: function() {
      var self = this;
      self.id_materia = (ASISTENCIA_ALUMNO_POR_MATERIA == 1) ? this.$("#asistencias_buscar_materias").val() : 0;
      self.fecha_desde = this.$("#asistencias_reporte_fecha_desde").val();
      self.fecha_hasta = this.$("#asistencias_reporte_fecha_hasta").val();
      $.ajax({
        "url":"asistencias/function/buscar_fechas/",
        "dataType":"json",
        "type":"get",
        "data": {
          "id_comision":window.comision.id,
          "id_materia":self.id_materia,
          "fecha_desde":self.fecha_desde.replace(/\//g,"-"),
          "fecha_hasta":self.fecha_hasta.replace(/\//g,"-"),
        },
        "success":function(res) {
          if (res.error == 0) {
            var table = new app.views.AsistenciasReporteTablaView({
              model: new app.models.AbstractModel(res),
              clases: res.clases,
            });
            self.$("#asistencias_reporte_tabla").html(table.el);

            var t = "";
            for(var i=0; i<res.results.length;i++) {
              var alumno = res.results[i];
              t+='<tr class="h64">';
              t+='<td class="ver hidden-xs">'+alumno.numero+'</td>';
              t+='<td class="ver hidden-xs">';
              if (!isEmpty(alumno.path)) {
                t+='<img src="/sistema/'+alumno.path+'" class="customcomplete-image"/>';
              } else {
                t+='<span class="avatar xs avatar-texto bg-info %> pull-left">';
                t+= isEmpty(alumno.nombre) ? "" : alumno.nombre.toUpperCase().substr(0,1);
                t+='</span>';
              }
              t+='</td>';
              t+='<td class="ver nowrap pl0"><span class="text-info">'+alumno.nombre.ucwords()+'</span></td>';
              t+='</tr>';
            }
            self.$("#reporte_asistencias_table_nombres tbody").html(t);

            $('[data-toggle="tooltip"]').tooltip(); 
          }
        },
      });
    },  

  });
})(app);

(function ( app ) {
  app.views.AsistenciasReporteTablaView = app.mixins.View.extend({
    template: _.template($("#asistencias_reporte_tabla_template").html()),
    initialize : function (options) {
      this.view = options.view;
      this.clases = options.clases;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template({
        "clases":self.clases,
      }));

      this.$("#reporte_asistencias_table .tbody").empty();
      var res = this.model.get("results");
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.AsistenciasReporteItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
          "clases":self.clases,
        });
        this.$("#reporte_asistencias_table .tbody").append(view.el);
      }
    },
  });
})(app);

(function ( app ) {
  app.views.AsistenciasReporteItemView = app.mixins.View.extend({
    template: _.template($("#asistencias_reporte_item_template").html()),
    tagName: "tr",
    className: "h64",
    myEvents: {
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);