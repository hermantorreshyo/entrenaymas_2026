(function ( models ) {

  models.RecorridoCliente = Backbone.Model.extend({
    urlRoot: "recorridos_clientes/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      dia: 0,
      cantidad_clientes: 0,
      clientes: [],
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.RecorridosClientes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "recorridos_clientes/"
    }
  });

})( app.collections, app.models.RecorridoCliente, Backbone.Paginator);


(function ( app ) {

  app.views.RecorridoClienteItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#recorridos_clientes_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .imprimir":"imprimir",
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
      location.href="app/#recorrido_cliente/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    imprimir: function() {
      workspace.imprimir_reporte("recorridos_clientes/function/imprimir/"+this.model.id);
    },
  });

})( app );

(function ( app ) {

  app.views.RecorridosClientesTableView = app.mixins.View.extend({
    template: _.template($("#recorridos_clientes_panel_template").html()),
    myEvents:{
      "click .buscar":"buscar",
      "keypress #recorridos_clientes_buscar":function(e) {
        if (e.which == 13) this.buscar();
      }
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.recorridos_clientes_filter = (typeof window.recorridos_clientes_filter != "undefined") ? window.recorridos_clientes_filter : "";
      window.recorridos_clientes_page = (typeof window.recorridos_clientes_page != "undefined") ? window.recorridos_clientes_page : 1;
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
      if (window.recorridos_clientes_filter != this.$("#recorridos_clientes_buscar").val().trim()) {
        window.recorridos_clientes_filter = this.$("#recorridos_clientes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.recorridos_clientes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.recorridos_clientes_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.recorridos_clientes_page);
    },

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.RecorridoClienteItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.RecorridoClienteEditView = app.mixins.View.extend({

    template: _.template($("#recorridos_clientes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #recorrido_cliente_cliente_agregar":"agregar_cliente",
      "click .eliminar_cliente":function(e){
        $(e.currentTarget).parents("tr").remove();
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

      var input = this.$("#recorrido_cliente_cliente_nombre");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#recorrido_cliente_cliente_id").val(item.id);
          self.$("#recorrido_cliente_cliente_nombre").val(item.label);
          self.$("#recorrido_cliente_cliente_codigo").val(item.value);
          self.$("#recorrido_cliente_cliente_info").val(item.info);
          self.agregar_cliente();
        }
      });
      self.$("#recorridos_clientes_clientes_tabla").sortable();
    },

    agregar_cliente: function() {
      var id = this.$("#recorrido_cliente_cliente_id").val();
      // Controlamos que no se haya agregado antes
      var encontro = false;
      $("#recorridos_clientes_clientes_tabla tr").each(function(i,e){
        if (id == $(e).find(".id").val()) {
          encontro = true; return;
        }
      });
      if (!encontro) {
        var nombre = this.$("#recorrido_cliente_cliente_nombre").val();
        var codigo = this.$("#recorrido_cliente_cliente_codigo").val();
        var info = this.$("#recorrido_cliente_cliente_info").val();
        if (id == 0) {
          alert("Por favor busque un cliente y luego seleccionelo de la lista.");
          return;
        }
        var tr = "<li><table class='table m-b-none default' style='width: 100%'><tr>";
        tr+="<input type='hidden' class='id' value='"+id+"'/>";
        tr+="<td style='width: 50%'>";
        tr+="<span class='btn fs14 btn-default m-r-xs'><i class='fa fa-arrows'></i></span>";
        tr+="<span class='text-info nombre'>"+nombre+"</span></td>";
        tr+="<td style='width: 10%'><span class='codigo'>"+codigo+"</span></td>";
        tr+="<td style='width: 30%'><span class='info'>"+info+"</span></td>";
        tr+='<td style="width: 10%" class="tar">';
        tr+='<button class="btn btn-sm btn-white eliminar_cliente"><i class="fa fa-trash"></i></button>';
        tr+='</td>';
        tr+="</table></li></tr>";
        this.$("#recorridos_clientes_clientes_tabla").append(tr);
      }
      this.$("#recorrido_cliente_cliente_id").val("0");
      this.$("#recorrido_cliente_cliente_nombre").val("");
      this.$("#recorrido_cliente_cliente_codigo").val("");
      this.$("#recorrido_cliente_cliente_info").val("");
    },

    validar: function() {
      var self = this;
      try {
        self.model.set({
          "nombre":self.$("#recorrido_cliente_nombre").val(),
        });

        if (this.$("#recorridos_clientes_clientes_tabla").length > 0) {
          var clientes = new Array();
          $("#recorridos_clientes_clientes_tabla tr").each(function(i,e){
            clientes.push({
              "id":$(e).find(".id").val(),
            });
          });
          this.model.set({"clientes":clientes});
        }

        // No hay ningun error
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
            // Se refresca la pagina porque tenemos cacheado un array de recorridos_clientes
            location.reload();
            //location.href="app/#recorridos_clientes";
          }
        });
      }
    },

  });

})(app.views, app.models);