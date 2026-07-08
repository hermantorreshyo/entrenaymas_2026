// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Reparacion = Backbone.Model.extend({
    urlRoot: "reparaciones/",
    defaults: {
      fecha: "",
      fecha_entrega: "",
      id_empresa: ID_EMPRESA,
      id_punto_venta: 0,
      total: 0,
      items: [],
      numero: 0,
      id_cliente: 0,
      cliente: "",
      observaciones_equipo: "Marca/Modelo: \nTrae cargador/fuente: \nTrae bateria: \nTrae funda: \nPassword: \nOtro: ",
      observaciones: "",
      falla_declarada: "",
      requerimientos_cliente: "",
      diagnostico: "",
      id_usuario: 0,
      tecnico: "",
      domicilio: 0,
      id_estado: 0,
    }
  });
      
})( app.models );


(function (collections, model, paginator) {
  collections.Reparaciones = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "reparaciones/"
    }
  });
})( app.collections, app.models.Reparacion, Backbone.Paginator);


(function ( models ) {

  models.ReparacionItem = Backbone.Model.extend({
    urlRoot: "reparaciones/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      nombre: "",
      precio_final: 0,
      total: 0,
      orden: 0,
      observaciones: "",
    }
  });
      
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.ReparacionEditView = app.mixins.View.extend({

    template: _.template($("#reparacion_edit_panel_template").html()),

    myEvents: {
      "click .guardar":"guardar",
      "click .cerrar":"cerrar",

      "click #reparacion_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #reparacion_agregar_item": "agregar_item",

      "keypress #reparacion_codigo_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); $("#reparacion_codigo_articulo").select(); }
      },
      "focusout #reparacion_codigo_cliente":function(e){
        if (typeof this.cliente != "undefined") {
          var nombre = this.cliente.get("nombre");
          var texto = $(e.currentTarget).val();
          if (nombre != texto) {
          // Blanqueamos el cliente para que no haya confusion
          $(e.currentTarget).val("");
          }
        }
      },
      "click #reparacion_buscar_cliente": "ver_buscar_cliente",


      "click .imprimir": function(){
        this.imprimir(this.model.id);
      },
      "keypress #reparacion_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #reparacion_item_cantidad": function(e) {
        if (e.which == 13) { this.$("#reparacion_precio_final").select(); }
      },
      "keypress #reparacion_precio_final":function(e) {
        if (e.which == 13) this.agregar_item();
      },
      // ACCIONES SOBRE EL FORMULARIO
      "keyup .action":function(e) {
        if (e.which == 120) { e.preventDefault(); e.stopPropagation(); this.ver_buscar_articulo(); return false; } // F9
      },
    },

    imprimir: function(id) {
      workspace.imprimir_reporte("reparaciones/function/imprimir/"+id);
    },

    ver_buscar_cliente: function() {
      var self = this;
      var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
      collection: clientes,
      habilitar_seleccion: true,
      });
      delete window.codigo_cliente_seleccionado;
      var d = $("<div/>").append(app.views.buscarClientes.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#reparacion_codigo_articulo").select();          
        }
      });
      $("#clientes_buscar").focus();
    },

    nuevo_cliente: function() {
      var self = this;
      var c = new app.views.ClienteEditViewMini({
        model: new app.models.Cliente({
          id_tipo_documento: 80,
          id_tipo_iva: 1,
          id_sucursal: ID_SUCURSAL,
        }),
        onSave: function(cli){
          self.seleccionar_cliente(cli);
          $('.modal:last').modal('hide');
        }
      });
      crearLightboxHTML({
        "html":c.el,
        "width":600,
        "height":500,
      });
      $("#clientes_mini_nombre").focus();
    },
      
    buscar_cliente : function() {
      var self = this;
      
      var codigo = this.$("#reparacion_codigo_cliente").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#reparacion_codigo_cliente").val(codigo);
      }
      // Es consumidor final, creamos el cliente directamente
      if (codigo == 0) {
        this.setear_consumidor_final();
      } else {
        // Buscamos el cliente por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
        codigo = parseInt(codigo);
        if (!isNaN(codigo)) {
          $.ajax({
            "url":"clientes/function/get_by_codigo/",
            "data":{
              "codigo":codigo,
            },
            "dataType":"json",
            "success":function(r) {
              if (r.length == 0) {
                show("No existe un cliente con el codigo: '"+codigo+"'");
                self.$("#reparacion_codigo_cliente").select();
                self.$("#reparacion_codigo_cliente").focus();
                return;
              }
              var cliente = new app.models.Cliente(r);
              self.seleccionar_cliente(cliente);
            }
          });
        }
      }
      this.$("#reparacion_codigo_articulo").focus();  
    },
      
    setear_consumidor_final: function() {
      var cf = new app.models.Cliente({
      "id_tipo_iva":4,
      "nombre":"Consumidor Final",
      "cuit":"",
      "saldo":0,
      "email":"",
      "direccion":"",
      "percibe_ib":0,
      "descuento":0,
      "error":0,
      "id_vendedor":0,
      "lista":0,
      "tipo_pago":"E",
      });      
      this.seleccionar_cliente(cf);
    },
      
    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      self.$("#reparacion_id_cliente").val(self.cliente.id);
      self.$('#reparacion_codigo_cliente').val(self.cliente.get("nombre"));
      
      if (isEmpty(self.model.get("direccion"))) {
        self.$("#reparacion_cliente_direccion").html(self.cliente.get("direccion"));
        self.$("#reparacion_cliente_telefono").html(self.cliente.get("telefono"));
      }
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#reparacion_codigo_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    buscar_articulo : function() {
      var self = this;

      var codigo = $("#reparacion_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }

      $.ajax({
        "url":"articulos/function/get_by_codigo/"+codigo,
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            var a = new app.models.Articulo(r.articulo);
            self.seleccionar_articulo(a);
          }
        }
      });
    },

    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#reparacion_item_cantidad").select();
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#reparacion_id_articulo").val(this.item.get("id_articulo"));
      $("#reparacion_codigo_articulo").val(this.item.get("codigo"));
      $("#reparacion_item_nombre").val(this.item.get("nombre"));
      $("#reparacion_item_cantidad").val(this.item.get("cantidad"));
      $("#reparacion_precio_final").val(this.item.get("precio_final"));
      self.calcular_item();
      this.$("#reparacion_item_cantidad").select();
    },
    
    ver_buscar_articulo : function() {
      var self = this;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: new app.collections.Articulos(),
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
            self.$("#reparacion_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#reparacion_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    mostrar_articulo : function() {
      // TODO: REVISAR EL TEMA DE LISTAS
      this.$("#reparacion_item_nombre").val(this.articulo.get("nombre"));
      this.$("#reparacion_id_articulo").val(this.articulo.id);
      this.$("#reparacion_precio_final").val(Number(this.articulo.get("precio_final")).toFixed(2));
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;

      if (typeof this.articulo == "undefined") {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#reparacion_codigo_articulo").focus();
        return;
      }
      var precio_final = parseFloat(this.$("#reparacion_precio_final").val());
      if (isNaN(precio_final)) precio_final = 0;

      var cantidad = this.$("#reparacion_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES); }
      var bonificacion = 0;
      var total = precio_final * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":this.articulo.id,
        "codigo":this.articulo.get("codigo"),
        "nombre":this.articulo.get("nombre"),
        "cantidad":cantidad,
        "precio_final":precio_final,
        "total":total,
      };            

      // Actualizamos o agregamos el item
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.ReparacionItem(values);
        this.items.add(item);
      }
        
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#reparacion_codigo_articulo").select();              
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#reparacion_item_cantidad").val();
      var precio_unit = this.$("#reparacion_item_costo_final").val();
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.agregando = 0;
      this.options = options;
      _.bindAll(this);
      this.bind("limpiar",this.limpiar);

      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {
        this.limpiar();

      // Estamos editando
      } else {

        this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
        
        this.render();
        
        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ReparacionItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.ReparacionItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);   
        this.render_tabla_items();

        // Buscamos el cliente y lo seteamos
        var id_cliente = self.model.get("id_cliente");
        if (id_cliente == 0) {
          this.setear_consumidor_final();
        } else {
          var cliente = new app.models.Cliente({"id":id_cliente});
          cliente.fetch({
            "success":function() {
              self.seleccionar_cliente(cliente);
            },
          });
        }
                             
      }
    },

    render: function() {
        
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      if (isEmpty(this.model.get("fecha"))) this.model.set("fecha",moment().format("DD/MM/YYYY HH:mm"));
      createtimepicker(this.$("#reparacion_fecha"),this.model.get("fecha"));
      createtimepicker(this.$("#reparacion_fecha_entrega"),this.model.get("fecha_entrega"));
  
      this.limpiar_item();
      var input = this.$("#reparacion_codigo_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "width":"300px",
        "label":"[nombre] ([codigo])",
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#reparacion_codigo_cliente");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });      
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });

      return this;
    },
    
    calcular_totales : function() {
      var total = 0;
      var descuento = 0;
      var items = this.model.get("items");
      var pdesc = 1;
      this.items.each(function(item){
        total = total + item.get("total") * pdesc;
      });
      this.model.set({
        "total":total,
      });
    },

    render_view: function() {
      var self = this;
      self.$("#reparacion_total").html(Number(self.model.get("total")).toFixed(2));
    },
    
    limpiar_item: function() {
      this.$("#reparacion_id_articulo").val("0");
      this.$("#reparacion_item_nombre").val("");
      this.$("#reparacion_item_cantidad").val("1");
      this.$("#reparacion_precio_final").val("0.00");
      this.$("#reparacion_codigo_articulo").val("");
      this.$("#reparacion_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },
    
    addItem : function ( item ) {
      var view = new app.views.ReparacionItemTabla({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.el);
      this.calcular_totales();
    },
    
    validar: function() {
      this.model.set({
        "id_cliente": (self.$("#reparacion_id_cliente").length > 0) ? self.$("#reparacion_id_cliente").val() : 0,
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },
    
    limpiar: function() {
      this.model = new app.models.Reparacion({
        "items":[],
      });

      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.ReparacionItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
    },

    guardar: function() {
      var self = this;
      try {
        this.validar(); // Primero validamos
      } catch(e) {
        alert(e);
        return;
      }

      // Desactivamos el doble submiteo
      if (this.guardando == 1) return;
      this.guardando = 1;

      this.model.save({
        "items":self.items.toJSON(),
      },{
        success: function(model,response) {
          $('.modal:last').modal('hide');
          self.guardando = 0; // Habilitamos el boton
          if (response.id != undefined) {
            self.model.id = response.id;
          }
          if (response.error == 1) {
            show(response.mensaje);
          } else {
            location.href = "app/#reparaciones";
          }
        },
      });
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.ReparacionItemTabla = app.mixins.View.extend({
    template: _.template($("#reparacion_item_tabla_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .eliminar_flechita":"do_eliminar",
      "keydown .eliminar":function(e) {
        if (e.which == 13) {
          var self = this;
          e.stopPropagation();
          e.preventDefault();
          this.do_eliminar();
          return false;
        } else if (e.which == 27) {
          $("#reparacion_codigo_articulo").focus();
        }
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    do_eliminar: function() {
      this.model.destroy();  // Eliminamos el modelo
      $(this.el).remove();  // Lo eliminamos de la vista
      $("#reparacion_codigo_articulo").focus();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.ReparacionItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#reparaciones_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .imprimir":function() {
        var id = this.model.id;
        workspace.imprimir_reporte("reparaciones/function/imprimir/"+id);
      },
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
      var self = this;
      this.model.fetch({
        "success":function(){
          var view = new app.views.ReparacionEditView({
            model: self.model
          });
          crearLightboxHTML({
            "html":view.el,
            "width":700,
            "height":400,
            "escapable":false,
          });
        }
      });
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



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.ReparacionesTableView = app.mixins.View.extend({

    template: _.template($("#reparaciones_panel_template").html()),

    myEvents: {
      "click .nuevo":function() {
        var view = new app.views.ReparacionEditView({
          model: new app.models.AbstractModel({
            items: [],
          })
        });
        crearLightboxHTML({
          "html":view.el,
          "width":700,
          "height":400,
          "escapable":false,
        });
      },
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: lista
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ReparacionItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);
