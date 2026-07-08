(function ( models ) {

  models.Oferta = Backbone.Model.extend({
    urlRoot: "ofertas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      desde: "",
      hasta: "",
      activo: 1,
      lunes: 1,
      martes: 1,
      miercoles: 1,
      jueves: 1,
      viernes: 1,
      sabado: 1,
      domingo: 1,
      articulos: [],
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Ofertas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "ofertas/"
    }
  });

})( app.collections, app.models.Oferta, Backbone.Paginator);


(function ( app ) {

  app.views.OfertaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#ofertas_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
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
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#oferta/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );

(function ( app ) {

  app.views.OfertasTableView = app.mixins.View.extend({
    template: _.template($("#ofertas_panel_template").html()),
    myEvents:{
      "click .buscar":"buscar",
      "keypress #ofertas_buscar":function(e) {
        if (e.which == 13) this.buscar();
      }
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.ofertas_filter = (typeof window.ofertas_filter != "undefined") ? window.ofertas_filter : "";
      window.ofertas_page = (typeof window.ofertas_page != "undefined") ? window.ofertas_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(pagination.el);
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      if (window.ofertas_filter != this.$("#ofertas_buscar").val().trim()) {
        window.ofertas_filter = this.$("#ofertas_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.ofertas_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.ofertas_filter),
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.ofertas_page);
    },

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.OfertaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.OfertaEditView = app.mixins.View.extend({

    template: _.template($("#ofertas_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #oferta_articulo_agregar":"agregar_articulo",
      "click .eliminar_articulo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      // BUSQUEDA DE ARTICULOS
      "keypress #oferta_articulo_codigo": function(e) {
        if (e.keyCode == 13) { this.buscar_articulo(); }
      },
      "keypress #oferta_articulo_cantidad": function(e) {
        if (e.keyCode == 13) { this.$("#oferta_articulo_").select(); }
      },    

    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var input = this.$("#oferta_articulo_codigo");
      $(input).customcomplete({
        "url":"articulos/function/get_by_nombre/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#oferta_articulo_id").val(item.id);
          self.$("#oferta_articulo_nombre").val(item.label);
          self.$("#oferta_articulo_codigo").val(item.value);
          self.$("#oferta_articulo_info").val(item.info);
          self.agregar_articulo();
        }
      });
      self.$("#ofertas_articulos_tabla").sortable();
    },

    buscar_articulo : function() {
      var self = this;    
      var codigo = $("#oferta_articulo_codigo").val();
      if (isEmpty(codigo)) return;

      if (CACHE_ARTICULOS == 1 || FACTURACION_USA_CACHE_ARTICULOS == 1) { 
      
        // Lo buscamos en el array
        var r = window.articulos.find(function(c){
          // Si tenemos codigo de barra
          var encontro_codigo_barra = false;
          var codigos = c.get("codigos");
          for(var cc = 0; cc < codigos.length; cc++) {
            var codigo_barra = codigos[cc];
            if (codigo_barra == codigo) {
              encontro_codigo_barra = true;
              break;
            }
          }
          if (encontro_codigo_barra) return true;

          // Sino buscamos por codigo o codigo de barra
          return (c.get("codigo") == codigo);
        });
        if (typeof r === "undefined") {
          self.articulo = null;
          alert("No se encuentra el articulo con codigo '"+codigo+"'.");
          this.$("#oferta_articulo_codigo").select();
        } else {
          this.seleccionar_articulo(r);
        }

      // Los articulos no se encuentran cacheados en un array de JS, por lo que hay que buscarlo con AJAX
      } else {

        $.ajax({
          "url":"articulos/function/get_by_codigo/"+codigo,
          "dataType":"json",
          "type":"post",
          "data":{
            "id_sucursal":ID_SUCURSAL,
          },
          "success":function(result) {
            if (result.error == 1) {
              self.articulo = null;
              alert("No se encuentra el articulo con codigo '"+codigo+"'.");
            } else {
              var art = new app.models.Articulo(result.articulo);
              self.seleccionar_articulo(art);
            }
          }
        });
      }
    },

    seleccionar_articulo: function(res) {
      this.articulo = res;
      this.$("#oferta_articulo_nombre").val(res.get("nombre"));
      this.$("#oferta_articulo_cantidad").select();
    },

    ver_buscar_articulo : function() {
      var self = this;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: articulos,
        habilitar_seleccion: true,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            $("#oferta_articulo_codigo").val($("#oferta_articulo_codigo").val()+window.codigo_articulo_seleccionado);
            self.buscar_articulo();
          } else {
            $("#oferta_articulo_codigo").focus();
          }                    
        }
      });
      $("#oferta_articulo_codigo").focus();
    }, 


    agregar_articulo: function() {
      var id = this.$("#oferta_articulo_id").val();
      // Controlamos que no se haya agregado antes
      var encontro = false;
      $("#ofertas_articulos_tabla tr").each(function(i,e){
        if (id == $(e).find(".id").val()) {
          encontro = true; return;
        }
      });
      if (!encontro) {
        var nombre = this.$("#oferta_articulo_nombre").val();
        var codigo = this.$("#oferta_articulo_cantidad").val();
        var info = this.$("#oferta_articulo_descuento").val();
        if (id == 0) {
          alert("Por favor busque un articulo y luego seleccionelo de la lista.");
          return;
        }
        var tr = "<li><table class='table m-b-none default' style='width: 100%'><tr>";
        tr+="<input type='hidden' class='id_articulo' value='"+id+"'/>";
        tr+="<td>";
        tr+="<span class='text-info nombre editar'>"+nombre+"</span></td>";
        tr+="<td><span class='cantidad editar'>"+cantidad+"</span></td>";
        tr+="<td><span class='descuento editar'>"+descuento+"</span></td>";
        tr+='<td class="tar">';
        tr+='<button class="btn btn-sm btn-white eliminar_articulo"><i class="fa fa-trash"></i></button>';
        tr+='</td>';
        tr+="</table></li></tr>";
        this.$("#ofertas_articulos_tabla").append(tr);
      }
      this.$("#oferta_articulo_id").val("0");
      this.$("#oferta_articulo_nombre").val("");
      this.$("#oferta_articulo_codigo").val("");
      this.$("#oferta_articulo_info").val("");
    },

    validar: function() {
      var self = this;
      try {
        self.model.set({
          "nombre":self.$("#oferta_nombre").val(),
        });
        if (this.$("#ofertas_articulos_tabla").length > 0) {
          var articulos = new Array();
          $("#ofertas_articulos_tabla tr").each(function(i,e){
            articulos.push({
              "id_articulo":$(e).find(".id").val(),
              "cantidad":$(e).find(".cantidad").val(),
              "descuento":$(e).find(".descuento").val(),
            });
          });
          this.model.set({"articulos":articulos});
        }
        $(".error").removeClass("error");
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
            // Se refresca la pagina porque tenemos cacheado un array de ofertas
            location.reload();
            //location.href="app/#ofertas";
          }
        });
      }
    },

  });

})(app.views, app.models);