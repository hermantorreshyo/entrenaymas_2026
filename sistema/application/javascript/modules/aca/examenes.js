(function ( models ) {

  models.Examen = Backbone.Model.extend({
    urlRoot: "examenes/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      id_comision: 0,
      id_materia: 0,
      id_docente: 0,
      fecha: moment().format("DD/MM/YYYY"),
      numerico: 1,
      aprueba_con: 7,
      utilizada_en_promedio: 1,
      cerrada: 0,
      notas: [],
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Examenes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "examenes/"
    }
  });

})( app.collections, app.models.Examen, Backbone.Paginator);


(function ( app ) {

  app.views.ExamenView = app.mixins.View.extend({
    template: _.template($("#examen_panel_template").html()),
    myEvents: {
      "click .guardar":"guardar",
      "click .imprimir":function() {
        workspace.imprimir_reporte("examenes/function/imprimir/"+this.model.id);
      }
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.render();
    },

    render: function() {
      var self = this;
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      $(this.el).html(this.template(obj));

      createdatepicker($(this.el).find("#examen_fecha"),this.model.get("fecha"));

      this.render_table();
      //this.calcular();
    },

    /*
    calcular: function() {
      var examenes = 0;
      var inexamenes = 0;
      var total = $("#examenes_table tbody tr").length;
      $("#examenes_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") examenes++;
          else if (condicion == "A" || condicion == "J") inexamenes++;
        }
      });
      var porc_in = ((total > 0) ? (inexamenes / total)*100 : 0);
      $("#examenes_inasistencia").html(inexamenes+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (examenes / total)*100 : 0);
      $("#examenes_asistencia").html(examenes+" ("+Number(porc_as).toFixed(2)+" %)");
    },
    */

    render_table: function() {
      var self = this;
      this.$("#examenes_table .tbody").empty();
      var res = this.model.get("notas");
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.ExamenItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
        });
        this.$("#examenes_table .tbody").append(view.el);
      }
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("examen_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var notas = new Array();
        $("#examenes_table tbody tr").each(function(i,e){
          notas.push({
            "id_alumno":$(e).find(".id_alumno").val(),
            "valor":$(e).find(".valor").val(),
            "observaciones":$(e).find(".observaciones").val(),
          });
        });
        this.model.set({
          "notas":notas,
        });

        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
            "id_empresa":ID_EMPRESA,
          },{
          success: function(model,response) {
            location.href="app/#examenes/"+window.comision.id;
          }
        });
      }
    },

  });
})(app);

(function ( app ) {
  app.views.ExamenItemView = app.mixins.View.extend({
    template: _.template($("#examen_item_template").html()),
    tagName: "tr",
    myEvents: {
      "keypress .valor":function(e) {
        if (e.which == 13) {
          // Con enter pasamos a la proxima nota
          $(e.currentTarget).parents("tr").next("tr").find(".valor").select();
        }
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

  app.views.ReporteExamenesTableView = app.mixins.View.extend({
    template: _.template($("#examenes_reporte_panel_template").html()),
    myEvents: {
      "change .trimestre_select":function(e) {
        var s = this.$(e.currentTarget).find("option:selected");
        this.$("#examenes_reporte_fecha_desde").val($(s).data("desde"));
        this.$("#examenes_reporte_fecha_hasta").val($(s).data("hasta"));
        if ($(e.currentTarget).val() == 0) { 
          this.$("#examenes_reporte_fecha_desde").parent().show();
          this.$("#examenes_reporte_fecha_hasta").parent().show();
        } else {
          this.$("#examenes_reporte_fecha_desde").parent().hide();
          this.$("#examenes_reporte_fecha_hasta").parent().hide();
        }
        this.buscar();
      },
      "change #examenes_reporte_materias":"buscar",
      "change #examenes_reporte_fecha_desde":"buscar",
      "change #examenes_reporte_fecha_hasta":"buscar",
      "click .ver_examen":function(e) {
        var id_materia = $(e.currentTarget).data("id_materia");
        this.$("#examenes_reporte_materias").val(id_materia);
        this.buscar();
      },
      "click .nuevo":function() {
        var self = this;
        window.id_materia = this.$("#examenes_reporte_materias").val();
        if (isEmpty(window.id_materia) || window.id_materia == 0) {
          alert("Por favor seleccione una materia");
          this.$("#examenes_reporte_materias").focus();
          return;
        }
        location.href="app/#examen";
      },
      "click .imprimir":function() {
        var self = this;
        window.id_materia = this.$("#examenes_reporte_materias").val();
        if (isEmpty(window.id_materia) || window.id_materia == 0) {
          alert("Por favor seleccione una materia");
          this.$("#examenes_reporte_materias").focus();
          return;
        }
        window.fecha_desde = this.$("#examenes_reporte_fecha_desde").val();
        window.fecha_hasta = this.$("#examenes_reporte_fecha_hasta").val();
        var url = "examenes/function/imprimir_entre_fechas/";
        url+="?id_comision="+window.comision.id;
        url+="&id_materia="+window.id_materia;
        url+="&fecha_desde="+window.fecha_desde.replace(/\//g,"-");
        url+="&fecha_hasta="+window.fecha_hasta.replace(/\//g,"-");
        workspace.imprimir_reporte(url);
      },
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.fecha_desde = (typeof window.fecha_desde == "undefined") ? moment().subtract(3,"months").format("DD/MM/YYYY") : window.fecha_desde;
      window.fecha_hasta = (typeof window.fecha_hasta == "undefined") ? moment().format("DD/MM/YYYY") : window.fecha_hasta;
      console.log(window.id_materia);
      this.render();
    },

    render: function() {
      var self = this;
      var obj = window.comision.toJSON();
      obj.id = window.comision.id;
      obj.fecha_desde = window.fecha_desde;
      obj.fecha_hasta = window.fecha_hasta;
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      $(this.el).html(this.template(obj));

      this.$(".trimestre_select").trigger("change");
      window.fecha_desde = this.$("#examenes_reporte_fecha_desde").val();
      window.fecha_hasta = this.$("#examenes_reporte_fecha_hasta").val();
      createdatepicker($(this.el).find("#examenes_reporte_fecha_desde"),moment(window.fecha_desde,"DD/MM/YYYY").toDate());
      createdatepicker($(this.el).find("#examenes_reporte_fecha_hasta"),moment(window.fecha_hasta,"DD/MM/YYYY").toDate());
      this.render_materias();
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
          r += "<option "+((window.id_materia == 0) ? "selected":"")+" value='0'>Todas las materias</option>";
          for(var i=0; i<res.results.length;i++) {
            var o = res.results[i];
            r += "<option "+((o.id == window.id_materia) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#examenes_reporte_materias").html(r);
          self.buscar();
        },
      });
    },

    calcular: function() {
      var examenes = 0;
      var inexamenes = 0;
      var total = $("#examenes_table tbody tr").length;
      $("#examenes_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") examenes++;
          else if (condicion == "A" || condicion == "J") inexamenes++;
        }
      });
      var porc_in = ((total > 0) ? (inexamenes / total)*100 : 0);
      $("#examenes_inasistencia").html(inexamenes+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (examenes / total)*100 : 0);
      $("#examenes_asistencia").html(examenes+" ("+Number(porc_as).toFixed(2)+" %)");
    },

    buscar: function() {
      var self = this;
      window.id_materia = this.$("#examenes_reporte_materias").val();
      window.fecha_desde = this.$("#examenes_reporte_fecha_desde").val();
      window.fecha_hasta = this.$("#examenes_reporte_fecha_hasta").val();
      $.ajax({
        "url":"examenes/function/buscar_fechas/",
        "dataType":"json",
        "type":"get",
        "data": {
          "id_comision":window.comision.id,
          "id_materia":window.id_materia,
          "fecha_desde":window.fecha_desde.replace(/\//g,"-"),
          "fecha_hasta":window.fecha_hasta.replace(/\//g,"-"),
        },
        "success":function(res) {
          if (res.error == 0) {
            self.render_table(res);
            //self.calcular();
          }
        },
      });
    },  

    render_table: function(res) {
      var self = this;
      if (window.id_materia == 0) {
        // Reporte de materias
        var table = new app.views.ReporteMateriasTable({
          model: new app.models.AbstractModel(res),
        });
      } else {
        // Reporte de examenes
        var table = new app.views.ReporteExamenesTable({
          model: new app.models.AbstractModel(res),
        });        
      }
      this.$("#reporte_examenes_table").html(table.el);
      $('[data-toggle="tooltip"]').tooltip(); 
    },

  });
})(app);


// Tabla de Examenes
(function ( app ) {
  app.views.ReporteExamenesTable = app.mixins.View.extend({
    template: _.template($("#reporte_examenes_table_template").html()),
    tagName: "div",
    className:"b-a table-responsive",
    myEvents: {
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      var res = this.model.get("results");
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.ExamenesReporteItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
        });
        this.$(".tbody").append(view.el);
      }
    },
  });
})(app);


(function ( app ) {
  app.views.ExamenesReporteItemView = app.mixins.View.extend({
    template: _.template($("#examenes_reporte_item_template").html()),
    tagName: "tr",
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


// Tabla de Materias
(function ( app ) {
  app.views.ReporteMateriasTable = app.mixins.View.extend({
    template: _.template($("#reporte_materias_table_template").html()),
    tagName: "div",
    className:"b-a table-responsive",
    myEvents: {
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      var res = this.model.get("alumnos");
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.ReporteMateriasItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
        });
        this.$(".tbody").append(view.el);
      }
    },
  });
})(app);


(function ( app ) {
  app.views.ReporteMateriasItemView = app.mixins.View.extend({
    template: _.template($("#reporte_materias_item_template").html()),
    tagName: "tr",
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