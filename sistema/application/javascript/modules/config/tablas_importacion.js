// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TablaImportacion = Backbone.Model.extend({
    urlRoot: "tablas_importacion",
    defaults: {
      nombre: "",
      campos: [],
      id_empresa: ID_EMPRESA,
      activo: 1,
    },
  });
	  
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.TablasImportacion = paginator.requestPager.extend({

    model: model,
    
    paginator_core: {
      url: "tablas_importacion/"
    }
  
  });

})( app.collections, app.models.TablaImportacion, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.TablasImportacionTableView = app.mixins.View.extend({

    template: _.template($("#tablas_importacion_resultados_template").html()),
      
    myEvents: {
      "change #tablas_importacion_buscar":"buscar",
      "click .buscar":"buscar",
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
			this.permiso = this.options.permiso;

      // Filtros de la tabla_importacion
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
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      var search = new app.mixins.SearchView({
        collection: this.collection
      });

      $(this.el).html(this.template({
        "permiso":this.permiso,
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);

      this.$(".search_container").html(search.el);

      return this;
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.TablasImportacionItemResultados({
        model: item,
      });
      this.$(".tbody").append(view.render().el);
    },
        
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.TablasImportacionItemResultados = app.mixins.View.extend({
    
    template: _.template($("#tablas_importacion_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"tablas_importacion/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
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
          this.model.destroy();	// Eliminamos el modelo
          $(this.el).remove();	// Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_tabla_importacion_seleccionado = this.model.get("codigo");
        window.tabla_importacion_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#tabla_importacion/"+this.model.id;
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.TablaImportacionEditView = app.mixins.View.extend({

    template: _.template($("#tabla_importacion_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "click #tabla_importacion_campo_agregar":"agregar_campo",
      "click .editar_campo":"editar_campo",
      "click .eliminar_campo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
    },		
        
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      this.options = options;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },
    
    agregar_campo: function() {
      // Controlamos los valores
      var campo = $("#tabla_importacion_campo").val();
      var columna = $("#tabla_importacion_columna").val();
      if (isEmpty(columna) || columna <= 0) {
        alert("Por favor ingrese una columna fecha");
        $("#tabla_importacion_columna").focus();
        return;
      }
      var tr = "<tr>";
      tr+="<td>"+campo+"</td>";
      tr+="<td>"+columna+"</td>";
      tr+="<td><i class='fa fa-pencil editar_campo cp'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_campo text-danger cp'></i></td>";
      tr+="</tr>";
      
      if (this.item == null) {
        $("#tabla_importacion_campos_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      
      $("#campo_monto").val("");
    },

    editar_campo: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#tabla_importacion_campo").val($(this.item).find("td:eq(0)").text());
      $("#tabla_importacion_columna").val($(this.item).find("td:eq(1)").text());
    },

    validar: function() {
      try {
        var self = this;
        
        validate_input("tabla_importacion_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
        
        // Guardamos los campos
        var campos = new Array();
        $("#tabla_importacion_campos_tabla tbody tr").each(function(i,e){
          campos.push({
            "campo": $(e).find("td:eq(0)").html(),
            "columna": $(e).find("td:eq(1)").html(),
          });
        });
        this.model.set({"campos":campos});
        
        return true;
      } catch(e) {
        console.log(e);
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
              history.back();
            }
          }
        });
      }	  
    },
    	
  });
})(app);