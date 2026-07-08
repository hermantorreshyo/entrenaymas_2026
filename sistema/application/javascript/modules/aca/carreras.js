// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Carrera = Backbone.Model.extend({
    urlRoot: "carreras",
    defaults: {
      nombre: "",
      materias: [],
      id_empresa: ID_EMPRESA,
    },
  });
      
})( app.models );


(function ( models ) {

  models.Materia = Backbone.Model.extend({
    urlRoot: "materias/",
    defaults: {
      nombre: "",
      anio: 0,
      cuatrimestre: 0,
      id_carrera: 0,
      id_empresa: ID_EMPRESA,
    }
  });
        
})( app.models );


(function ( models ) {

  models.Clase = Backbone.Model.extend({
    urlRoot: "clases/",
    defaults: {
      id_comision: 0,
      id_materia: 0,
      id_docente: 0,
      duracion_cantidad: 1,
      duracion_tipo: "H", // H (Hora) o M (Minutos)
      fecha: "",
      hora: "",
      id_clase_padre: 0,
      id_empresa: ID_EMPRESA,
    }
  });
        
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Carreras = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
      order_by: 'nombre',
      order: 'asc',
    },        
    paginator_core: {
      url: "carreras/function/ver",
    }
  });

})( app.collections, app.models.Carrera, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CarrerasTableView = app.mixins.View.extend({

    template: _.template($("#carreras_resultados_template").html()),
        
    myEvents: {
      "change #carreras_buscar":"buscar",
      "click .eliminar_lote":"eliminar_lote",
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;

      this.render();
      this.collection.on('sync', this.addAll, this);

      this.collection.server_api = {
      "filter":this.filter,
      };            
      this.collection.goTo(this.pagina);
    },
    
    render: function() {
      var self = this;
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
        "filter":this.filter,
      }));
            
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);            
    },
        
    buscar: function() {
      var self = this;
      var filter = this.$("#carreras_buscar").val();
      self.filter = (typeof filter != "undefined") ? filter.trim : "";            
      this.collection.server_api = {
        "filter":self.filter,
      };
      this.collection.pager();
    },
        
    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.CarrerasItemResultados({
        model: item,
        collection: this.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    eliminar_lote: function() {
      var checks = this.$("#carreras_tabla .check-row:checked");
      if (checks.length == 0) return;
      if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
        $(checks).each(function(i,e){
          var id = $(e).val();
          var art = carreras.get(id);
          art.destroy();  // Eliminamos el modelo
          $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
        });
      }            
    },
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.CarrerasItemResultados = app.mixins.View.extend({
        
        template: _.template($("#carreras_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            "click .data":"seleccionar",
            "keyup .radio":function(e) {
                if (e.which == 13) { this.seleccionar(); }
                e.stopPropagation();
            },
            "focus .radio":function(e) {
                $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
                e.stopPropagation();
                e.preventDefault();
                return false;
            },
      "blur .radio":function(e) {
        $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
                e.stopPropagation();
                e.preventDefault();
                return false;
      },
            "click .duplicar":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                if (confirm("Desea duplicar el elemento?")) {
                    $.ajax({
                        "url":"carreras/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            carreras.add(d);
                        },
                    });                    
                }
                return false;
            },
            "click .eliminar":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                if (confirm("Realmente desea eliminar el elemento?")) {
                    this.model.destroy();  // Eliminamos el modelo
                    $(this.el).remove();  // Lo eliminamos de la vista
                }
                return false;
            },
        },
        seleccionar: function() {
            if (this.habilitar_seleccion) {
                window.codigo_carrera_seleccionado = this.model.get("codigo");
                window.carrera_seleccionado = this.model;
                $('.modal:last').modal('hide');                
            } else {
                location.href="app/#carrera/"+this.model.id;
            }
        },
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.render();
        },
        render: function() {
          var obj = { seleccionar: this.habilitar_seleccion };
          $.extend(obj,this.model.toJSON());
            $(this.el).html(this.template(obj));
            return this;
        },
    });
})(app);



// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.CarreraEditView = app.mixins.View.extend({

    template: _.template($("#carrera_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click #carrera_materia_agregar":"agregar_materia",
      "click .eliminar_materia":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keydown #carrera_nombre":function(e){
        if (e.which == 13) this.$("#carrera_materia_nombre").focus();
      },
      "keydown #carrera_materia_nombre":function(e){
        if (e.which == 13) this.$("#carrera_materia_anio").focus();
      },
      "keydown #carrera_materia_anio":function(e){
        if (e.which == 13) this.$("#carrera_materia_cuatrimestre").focus();
      },
      "keydown #carrera_materia_cuatrimestre":function(e){
        if (e.which == 13) this.agregar_materia();
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
        
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },

    agregar_materia: function() {
      
      var nombre = $("#carrera_materia_nombre").val();
      if (isEmpty(nombre)) {
        alert("Por favor ingrese un nombre");
        $("#carrera_materia_nombre").focus();
        return;
      }
      var anio = $("#carrera_materia_anio").val();
      if (isEmpty(anio)) {
        alert("Por favor ingrese un anio");
        $("#carrera_materia_anio").focus();
        return;
      }
      var cuatrimestre = $("#carrera_materia_cuatrimestre").val();
      if (isEmpty(cuatrimestre)) {
        alert("Por favor ingrese un cuatrimestre");
        $("#carrera_materia_cuatrimestre").focus();
        return;
      }

      var tr = "<tr>";
      tr+='<input type="hidden" class="dn id" value="0"/>';
      tr+='<td><input type="text" class="form-control no-model nombre" value="'+nombre+'"/></td>';
      tr+='<td><input type="text" class="form-control no-model anio" value="'+anio+'"/></td>';
      tr+='<td>';
      tr+='<select class="form-control cuatrimestre no-model">';
      tr+='<option '+((cuatrimestre == 0)?"selected":"")+' value="0">Anual</option>';
      tr+='<option '+((cuatrimestre == 1)?"selected":"")+' value="1">1º Cuatrimestre</option>';
      tr+='<option '+((cuatrimestre == 2)?"selected":"")+' value="2">2º Cuatrimestre</option>';
      tr+='</select>';
      tr+='</td>';
      tr+='<td>';
      tr+='<input type="color" id="carrera_materia_color" />';
      tr+='</td>';
      tr+='<td class="tar">';
      tr+='<button class="btn btn-sm btn-white eliminar_materia"><i class="fa fa-trash"></i></button>';
      tr+='</td>';

      // Buscamos el lugar donde insertarlo
      anio = parseInt(anio);
      cuatrimestre = parseInt(cuatrimestre);
      if ($("#carrera_materias_tabla tbody tr").length == 0) {
        $("#carrera_materias_tabla tbody").append(tr);  
      } else {
        var inserto = false;
        $("#carrera_materias_tabla tbody tr").each(function(i,e){
          var o1 = parseInt($(e).find(".anio").val());
          var o2 = parseInt($(e).find(".cuatrimestre").val());
          if (o1 < anio) return true; // Continue
          else if (o1 == anio) {
            if (o2 < cuatrimestre) return true; // Continue
            else {
              inserto = true;
              $(e).before(tr);
              return false; // BREAK
            }
          } else {
            inserto = true;
            $(e).before(tr);
            return false; // BREAK
          }
        });
        if (!inserto) $("#carrera_materias_tabla tbody").append(tr);
      }
      $("#carrera_materia_nombre").val("");
      $("#carrera_materia_anio").val("");
      $("#carrera_materia_cuatrimestre").val(0);
      $("#carrera_materia_color").val("");
      $("#carrera_materia_nombre").focus();
    },
    
    validar: function() {
      try {
        var self = this;
        validate_input("carrera_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");

        if (this.$("#carrera_materias_tabla").length > 0) {
          var materias = new Array();
          $("#carrera_materias_tabla tbody tr").each(function(i,e){
            materias.push({
              "id": $(e).find(".id").val(),
              "nombre": $(e).find(".nombre").val(),
              "anio": $(e).find(".anio").val(),
              "color": $(e).find(".color").val(),
              "cuatrimestre": $(e).find(".cuatrimestre").val(),
              "id_empresa":ID_EMPRESA,
            });
          });
          this.model.set({"materias":materias});
        }

        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },  
  
    guardar:function() {
      var es_nuevo = false;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              location.href="app/#carreras";
            }
          }
        });
      }      
    },
  
  });
})(app);


(function ( app ) {

  app.views.ClaseEditView = app.mixins.View.extend({

    template: _.template($("#clase_edit_panel_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar":"eliminar",
    },
      
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.id_carrera = (typeof this.options.id_carrera != "undefined") ? this.options.id_carrera : 0;
      this.anio = (typeof this.options.anio != "undefined") ? this.options.anio : 0;
      this.mostrar_docentes = (typeof this.options.mostrar_docentes != "undefined") ? this.options.mostrar_docentes : false;
      this.mostrar_comisiones = (typeof this.options.mostrar_comisiones != "undefined") ? this.options.mostrar_comisiones : false;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        "edicion": edicion,
        "id":this.model.id,
        "mostrar_comisiones":this.mostrar_comisiones,
        "mostrar_docentes":this.mostrar_docentes,
      }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      createdatepicker($(this.el).find("#clase_fecha"),this.model.get("fecha"));
      createdatepicker($(this.el).find("#clase_fecha_hasta"),moment().endOf("year").toDate());
      this.$("#clase_hora").mask("99:99");

      // SELECT DE MATERIAS
      $.ajax({
        "url":"materias/function/get_select/",
        "data": {
          id_carrera: self.id_carrera,
          anio: self.anio,
        },
        "type":"get",
        "dataType":"json",
        "success":function(response) {
          var r = "";
          for(var i=0; i<response.results.length;i++) {
            var o = response.results[i];
            r += "<option "+((o.id == self.model.get("id_materia")) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#clase_materias").html(r);
        }
      });

      // SELECT DE DOCENTES
      if (this.mostrar_docentes) {
        new app.mixins.Select({
          modelClass: app.models.Docente,
          url: "docentes/",
          render: "#clase_docentes",
          firstOptions: ["<option value='0'>Seleccione</option>"],
          name : "id_docente",
          id_field: "id_cliente",
          selected: self.model.get("id_docente"),
        });                
      }

      // SELECT DE COMISIONES
      if (this.mostrar_comisiones) {
        new app.mixins.Select({
          modelClass: app.models.Comision,
          url: "comisiones/",
          render: "#clase_comisiones",
          firstOptions: ["<option value='0'>Seleccione</option>"],
          name : "id_comision",
          selected: self.model.get("id_comision"),
          onComplete: function() {
            self.$("#clase_comisiones").select2();
          },
        });                
      }
    },

    validar: function() {
      try {
        var self = this;

        if (this.mostrar_docentes) {
          var id_docente = self.$("#clase_docentes").val();
          if (id_docente == 0) {
            alert("Por favor seleccione un docente.");
            self.$("#clase_docentes").focus();
            return false;
          }
        }

        if (isEmpty(self.$("#clase_fecha").val())) {
          alert("Por favor seleccione una fecha");
          self.$("#clase_fecha").focus();
          return false;
        }

        if (isEmpty(self.$("#clase_hora").val())) {
          alert("Por favor seleccione una hora");
          self.$("#clase_hora").focus();
          return false;
        }

        if (this.model.id == undefined) {
          this.model.set({
            "repetir":self.$("#clase_repeticion").val(),
            "fecha_hasta":self.$("#clase_fecha_hasta").val(),
          });
        }

        this.model.set({
          "id_comision":self.$("#clase_comisiones").val(),
          "id_materia":self.$("#clase_materias").val(),
          "id_docente":self.$("#clase_docentes").val(),
          "duracion_cantidad":self.$("#clase_duracion_cantidad").val(),
          "duracion_tipo":self.$("#clase_duracion_tipo").val(),
          "fecha":self.$("#clase_fecha").val(),
          "hora":self.$("#clase_hora").val(),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },  
  
    guardar:function() {
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              $(".modal").last().trigger("click");
            }
          }
        });
      }       
    },

    eliminar: function() {
      if (confirm("Realmente desea eliminar el elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        $(".modal").last().trigger("click");
      }
    },
  
  });
})(app);


