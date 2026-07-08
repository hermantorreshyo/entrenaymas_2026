// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PedidoProveedor = Backbone.Model.extend({
    urlRoot: "pedidos_proveedores/",
    defaults: {
      fecha: "",
      id_proveedor: 0,
      proveedor: "",
      id_empresa: ID_EMPRESA,
      id_tipo_estado: 0,
      numero: 0,
      subtotal: 0,
      total: 0,
      items: [],
      observaciones: "",
      direccion: "",
      id_localidad: 0,
      localidad: "",
      id_sucursal: 0,
      sucursal: "",
      sucursal_direccion: "",
    }
  });

})( app.models );



(function ( models ) {

  models.PedidoProveedorItem = Backbone.Model.extend({
    urlRoot: "pedidos_proveedores/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      precio: 0,
      nombre: "",
      orden: 0,
      bonificacion: 0,
      total: 0,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.PedidosProveedores = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 10,
    },        

    paginator_core: {
      url: "pedidos_proveedores/function/consulta/",
    },
  });

})( app.collections, app.models.PedidoProveedor, Backbone.Paginator);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.PedidoProveedorEditView = app.mixins.View.extend({

    template: _.template($("#pedidos_proveedores_edit_panel_template").html()),

    myEvents: {
      "click .aceptar": "aceptar",
      "click .anular": "anular",
      "click .imprimir": function(){
        this.imprimir(this.model.id,null,false);
      },
      "click #pedidos_proveedores_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      
      "change #pedidos_proveedores_item_cantidad": "calcular_item",
      "keypress #pedidos_proveedores_item_cantidad": function(e) {
        if (e.which == 13) { this.agregar_item(); }
      },

      "change #pedidos_proveedores_item_precio": "calcular_item",
      "click #pedidos_proveedores_agregar_item": "agregar_item",
      
      // Buscamos el proveedor por codigo
      "keyup #pedidos_proveedores_codigo_proveedor": function(e) {
        if (e.keyCode == 113) { this.ver_buscar_proveedor(); }
      },
      "keypress #pedidos_proveedores_codigo_proveedor":function(e) {
        if (e.which == 13) { this.buscar_proveedor(); $("#pedidos_proveedores_codigo_articulo").select(); }
      },
      
      "click #pedidos_proveedores_buscar_proveedor": "ver_buscar_proveedor",

      "keypress #pedidos_proveedores_codigo_articulo": function(e) {
        if (e.which == 13)  {
          this.buscar_articulo();    
        }
      },
      "keyup #pedidos_proveedores_codigo_articulo": function(e) {
        if (e.which == 113) { $("#pedidos_proveedores_codigo_proveedor").select(); e.preventDefault(); } // F2
        if (e.which == 45) { this.aceptar(); this.$("#pedidos_proveedores_codigo_articulo").val(""); }
      },

      // CARTELES DE AYUDA
      
      "click .buscar_proveedores_ayuda":function() {
        var ayuda = new app.views.AyudaView({
          model: new app.models.AbstractModel()
        });
        var html = "Es posible asignar un proveedor a un comprobante de diferentes maneras: <br/>";
        html+= "<ul style='padding-left: 30px'>";
        html+= "<li>A trav&eacute;s de su c&oacute;digo interno, y luego presionar la tecla Enter.</li>";
        html+= "<li>Escribiendo parte de su nombre y seleccion&aacute;ndolo luego en la lista de sugerencias.</li>";
        html+= "<li>Si es un proveedor nuevo, que a&uacute;n no esta cargado en su lista de contactos, puede escribir parte de su nombre y luego hacer click en el bot&oacute;n Nuevo que aparece en la lista de sugerencias. De esta manera podr&aacute; cargar r&aacute;pidamente un nuevo proveedor sin tener que salir de la pantalla del comprobante.</li>";
        html+= "</ul>";
        ayuda.setText(html);
        crearLightboxHTML({
          "html":ayuda.el,
          "width":600,
          "height":300,
        });
      },
            
      // ACCIONES SOBRE EL FORMULARIO
      "keyup .action":function(e) {
        if (e.which == 120) { this.ver_buscar_articulo(); e.preventDefault(); }
        if (e.which == 118) { this.anular(); } // F7
      },

      "keypress #pedidos_proveedores_item_precio": function(e) {
        if (e.keyCode == 13) { this.agregar_item(); }
      },
    },

    imprimir: function(id,limpiar_despues) {
      var self = this;
      workspace.imprimir_reporte("pedidos_proveedores/function/imprimir/"+id,function(){
        if (limpiar_despues == true) {
          self.limpiar();
          $("#pedidos_proveedores_codigo_articulo").val("");
          $("#pedidos_proveedores_codigo_proveedor").select();                                                                
        }
      });
    },

    ver_buscar_proveedor: function() {
      var self = this;
      var proveedores = new app.collections.Proveedores();
      app.views.buscarProveedores = new app.views.ProveedoresTableView({
        collection: proveedores,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarProveedores.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_proveedor_seleccionado != undefined && window.codigo_proveedor_seleccionado != -1) {
            $("#pedidos_proveedores_codigo_proveedor").val(window.codigo_proveedor_seleccionado);
          }
          $("#pedidos_proveedores_codigo_proveedor").select();                    
        }
      });
      $(".search_input").select();
    },

    buscar_proveedor : function() {
      var self = this;

      var codigo = this.$("#pedidos_proveedores_codigo_proveedor").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#pedidos_proveedores_codigo_proveedor").val(codigo);
      }
      // Es consumidor final, creamos el proveedor directamente
      if (codigo != 0) {
        // Buscamos el proveedor por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
        codigo = parseInt(codigo);
        if (!isNaN(codigo)) {
          $.ajax({
            "url":"proveedores/function/get_by_codigo/",
            "data":{
              "codigo":codigo,
            },
            "dataType":"json",
            "success":function(r) {
              if (r.length == 0) {
                show("No existe un proveedor con el codigo: '"+codigo+"'");
                self.$("#pedidos_proveedores_codigo_proveedor").select();
                self.$("#pedidos_proveedores_codigo_proveedor").focus();
                return;
              }
              var proveedor = new app.models.Proveedor(r);
              self.seleccionar_proveedor(proveedor);
            }
          });
        }
      }
      this.$("#pedidos_proveedores_codigo_articulo").focus();    
    },

    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.render_view();
      self.$("#pedidos_proveedores_codigo_articulo").focus();
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#pedidos_proveedores_codigo_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);      
    },

    // Actualizamos la vista con los datos del modelo
    render_view: function() {

      var self = this;

      // Mostamos los datos del proveedor
      if (self.proveedor != null) {

        self.$("#pedidos_proveedores_id_proveedor").val(self.proveedor.id);

        var id_tipo_iva = self.proveedor.get("id_tipo_iva");
        if (id_tipo_iva == 1) self.$("#pedidos_proveedores_proveedor_iva").html("Responsable Inscripto");
        else if (id_tipo_iva == 2) self.$("#pedidos_proveedores_proveedor_iva").html("Monotributo");
        else if (id_tipo_iva == 3) self.$("#pedidos_proveedores_proveedor_iva").html("Exento");
        else if (id_tipo_iva == 4) self.$("#pedidos_proveedores_proveedor_iva").html("Consumidor Final");
        self.$("#pedidos_proveedores_codigo_proveedor").val(self.proveedor.get("nombre"));
        self.$("#pedidos_proveedores_proveedor_pedido").html(self.proveedor.get("nombre"));
        self.$("#pedidos_proveedores_proveedor_cuit").html(self.proveedor.get("cuit"));
        if (!isEmpty(self.model.get("localidad"))) {
          self.$("#pedidos_proveedores_proveedor_localidad").html(self.model.get("localidad"));
          self.$("#pedidos_proveedores_proveedor_direccion").html(self.model.get("direccion"));
        } else {
          self.$("#pedidos_proveedores_proveedor_localidad").html(self.proveedor.get("localidad"));
          self.$("#pedidos_proveedores_proveedor_direccion").html(self.proveedor.get("direccion"));                    
        }
      }
      
      // Fecha
      self.$("#pedidos_proveedores_fecha_pedido").html(self.model.get("fecha"));
      self.$("#pedidos_proveedores_subtotal").val(Number(self.model.get("subtotal")).toFixed(2));
      self.$("#pedidos_proveedores_total").val(Number(self.model.get("total")).toFixed(2));
    },

    buscar_articulo : function() {
      var self = this;

      var id_sucursal = (this.$("#pedidos_proveedores_sucursales").length > 0) ? this.$("#pedidos_proveedores_sucursales").val() : 0;

      var codigo = $("#pedidos_proveedores_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }

      var tipo_codigo = this.$("input[name=tipo_codigo]:checked").val();
      if (tipo_codigo == "INTERNO") {
        $.ajax({
          "url":"articulos/function/get_by_codigo/"+codigo,
          "type":"post",
          "data":{
            "id_sucursal":id_sucursal,
          },
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
      } else if (tipo_codigo == "PROVEEDOR") {
        // Lo buscamos por codigo de proveedor
        if (typeof self.proveedor == "undefined") {
          alert("Por favor seleccione un proveedor");
          return;
        }
        $.ajax({
          "url":"articulos/function/get_by_codigo_proveedor/",
          "type":"post",
          "dataType":"json",
          "data":{
            "codigo":codigo,
            "id_proveedor":self.proveedor.id,
          },
          "success":function(r) {
            if (r.error == 1) {
              alert(r.mensaje);
            } else {
              var a = new app.models.Articulo(r.articulo);
              self.seleccionar_articulo(a);
            }
          }
        });
      }
    },
    
    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#pedidos_proveedores_item_cantidad").select();
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#pedidos_proveedores_codigo_articulo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#pedidos_proveedores_codigo_articulo").val(this.item.get("nombre"));
      $("#pedidos_proveedores_tipo_item").val(this.item.get("tipo"));
      $("#pedidos_proveedores_item_precio").val(this.item.get("precio"));
      $("#pedidos_proveedores_item_cantidad").val(this.item.get("cantidad"));
      $("#pedidos_proveedores_item_tipo_cantidad").val(this.item.get("tipo_cantidad"));
      $("#pedidos_proveedores_item_uxb").val(this.item.get("uxb"));
      self.calcular_item();
      this.$("#pedidos_proveedores_item_cantidad").select();            
    },

    ver_buscar_articulo : function() {
      var self = this;
      app.views.buscarArticulosResultados = new app.views.ArticulosTableView({
        collection: articulos,
        habilitar_seleccion: true,
      });    
      var d = $("<div/>").append(app.views.buscarArticulosResultados.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            $("#pedidos_proveedores_codigo_articulo").val($("#pedidos_proveedores_codigo_articulo").val()+window.codigo_articulo_seleccionado);
                  //self.buscar_articulo();
                  $("#pedidos_proveedores_codigo_articulo").focus();
                } else {
                  $("#pedidos_proveedores_codigo_articulo").focus();
                }                    
              }
            });
      $("#articulos_texto").select();
    },

    mostrar_articulo : function() {
      $("#pedidos_proveedores_codigo_articulo").val(this.articulo.get("codigo"));
      $("#pedidos_proveedores_item_nombre").val(this.articulo.get("nombre"));
      $("#pedidos_proveedores_item_neto").val(this.articulo.get("costo_neto"));
      $("#pedidos_proveedores_item_precio").val(this.articulo.get("costo_final"));
      var uxb = this.articulo.get("uxb");
      uxb = (uxb == 0)?1:uxb;
      $("#pedidos_proveedores_item_tipo_cantidad").val((uxb>1)?"B":"U");
      $("#pedidos_proveedores_item_uxb").val(uxb);
    },

    // Agrega el item a la lista
    agregar_item : function() {

      var self = this;

      var codigo = this.$("#pedidos_proveedores_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#pedidos_proveedores_codigo_articulo").focus();
        return;
      }

      var id_articulo = (this.articulo != undefined) ? this.articulo.id : ((this.item != undefined) ? (this.item.get("id_articulo")) : 0);
      var nombre = (this.articulo != undefined) ? this.articulo.get("nombre") : ((this.item != undefined) ? (this.item.get("nombre")) : "");
      var cantidad = this.$("#pedidos_proveedores_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      var tipo_cantidad = this.$("#pedidos_proveedores_item_tipo_cantidad").val();
      var uxb = this.$("#pedidos_proveedores_item_uxb").val();

      var precio = parseFloat(this.$("#pedidos_proveedores_item_precio").val());
      if (tipo_cantidad == "B") {
        var total = precio * cantidad * uxb;  
      } else {
        var total = precio * cantidad;
      }
      
      var values = {
        "id_articulo":id_articulo,
        "precio":precio,
        "codigo":codigo,
        "nombre":nombre,
        "cantidad":cantidad,
        "tipo_cantidad":tipo_cantidad,
        "total":total,
        "uxb":uxb,
      };
      
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.PedidoProveedorItem(values);
        this.items.add(item);
      }
      
      this.item = undefined;
      this.limpiar_item();
      this.$("#pedidos_proveedores_codigo_articulo").select();              
    },

    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#pedidos_proveedores_item_cantidad").val();
      var precio_unit = this.$("#pedidos_proveedores_item_precio").val();
      var tipo_cantidad = this.$("#pedidos_proveedores_item_tipo_cantidad").val();
      var uxb = this.$("#pedidos_proveedores_item_uxb").val();
      if (tipo_cantidad == "B") {
        var subtotal = Number(cantidad * precio_unit * uxb).toFixed(2);
      } else {
        var subtotal = Number(cantidad * precio_unit).toFixed(2);  
      }
      this.$("#pedidos_proveedores_item_subtotal").val(subtotal);
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
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
          model: app.models.PedidoProveedorItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.PedidoProveedorItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);                
        
        // Buscamos el proveedor y lo seteamos
        var id_proveedor = self.model.get("id_proveedor");
        if (id_proveedor != 0) {
          var proveedor = new app.models.Proveedor({"id":id_proveedor});
          proveedor.fetch({
            "success":function() {
              self.seleccionar_proveedor(proveedor);        
            },
          });
        }
        
        this.render_tabla_items();
        $("#tabla_items tbody").empty();
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

      if (isEmpty(this.model.get("fecha"))) {
        this.model.set("fecha",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#pedidos_proveedores_fecha"),this.model.get("fecha"));

      this.limpiar_item();

      if (control.check("sucursales")>0) {
        new app.mixins.Select({
          modelClass: app.models.Sucursal,
          url: "sucursales/",
          render: "#pedidos_proveedores_sucursales",
          selected: self.model.get("id_sucursal"),
        });
      }

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#pedidos_proveedores_codigo_proveedor");
      var form = new app.views.ProveedorEditViewMini({
        "model": new app.models.Proveedor(),
        "input": input,
        "onSave": self.seleccionar_proveedor,
      });            
      $(input).customcomplete({
        "url":"proveedores/function/get_by_nombre/",
        "form":form,
        "width":"300px",
        "onSelect":function(item){
          var proveedor = new app.models.Proveedor({"id":item.id});
          proveedor.fetch({
            "success":function(){
              self.seleccionar_proveedor(proveedor);
            },
          });
        }
      });
      
      var input = this.$("#pedidos_proveedores_codigo_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });
      return this;
    },

    calcular_totales : function() {
      var total = 0;
      var subtotal = 0;
      var items = this.model.get("items");
      var pdesc = 1;
      this.items.each(function(item){
        total = total + parseFloat(item.get("total")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total"));
      });
      this.model.set({
        "subtotal":subtotal,
        "total":total,
      });
    },

    limpiar_item: function() {
      this.$("#pedidos_proveedores_item_cantidad").val("1");
      this.$("#pedidos_proveedores_item_uxb").val("1");
      this.$("#pedidos_proveedores_item_precio").val("0.00");
      this.$("#pedidos_proveedores_item_subtotal").val("");
      this.$("#pedidos_proveedores_codigo_articulo").val("");
      this.$("#pedidos_proveedores_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      var view = new app.views.PedidoProveedorItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },        

    validar: function() {
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al pedido antes de guardar.";
      }
    },

    anular: function() {
      if (confirmar("Desea limpiar el pedido?")) {
        this.limpiar();
        $("#pedidos_proveedores_codigo_articulo").focus();
      }                
    },

    limpiar : function() {

      // Guardamos las variables antes de renderizar
      var lista = 0;
      
      this.model = new app.models.PedidoProveedor();
      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.PedidoProveedorItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
      this.buscar_proveedor();
    },

    aceptar: function() {

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

      if (this.model.id == null) {
        this.model.set({
          id:0,
          id_usuario: ID_USUARIO,
        });
      }
      this.model.set({
        "items":self.items.toJSON(),
        "fecha":self.$("#pedidos_proveedores_fecha").val(),
        "observaciones":self.$("#pedidos_proveedores_observaciones").val(),
        "id_empresa": ID_EMPRESA,
        "id_proveedor":self.$("#pedidos_proveedores_id_proveedor").val(),
        "id_tipo_estado":self.$("#pedidos_proveedores_tipo_estado").val(),
        "id_sucursal": ((self.$("#pedidos_proveedores_sucursales").length > 0) ? self.$("#pedidos_proveedores_sucursales").val() : "0"),
      });
      this.model.save({},{
        success: function(model,response) {
          self.guardando = 0; // Habilitamos el boton
          if (response.id != undefined) {
            self.model.id = response.id;
          }
          location.href="app/#pedidos_proveedores";
        },
      });                     
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.PedidoProveedorItem = app.mixins.View.extend({

    template: _.template($("#pedido_proveedor_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);




// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.PedidosProveedoresTableView = app.mixins.View.extend({

    template: _.template($("#pedidos_proveedores_resultados_template").html()),

    myEvents: {
      "click .buscar":"buscar",
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;            

      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);

      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
            
      createdatepicker(this.$("#pedidos_proveedores_desde"));
      createdatepicker(this.$("#pedidos_proveedores_hasta"));
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var filtros = {};
      filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : 0;
      if (!isEmpty(this.$("#pedidos_proveedores_listado_numero").val())) 
        filtros.numero = this.$("#pedidos_proveedores_listado_numero").val();
      
      var fecha_desde = this.$("#pedidos_proveedores_desde").val();
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) filtros.desde = fecha_desde;
      
      var fecha_hasta = this.$("#pedidos_proveedores_hasta").val();
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) filtros.hasta = fecha_hasta;
      
      this.collection.server_api = filtros;
      this.collection.pager();            
    },

    addAll : function () {
      this.$("#pedidos_proveedores_tabla tbody tr").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PedidosProveedoresItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#pedidos_proveedores_tabla tbody").append(view.render().el);
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.PedidosProveedoresItemResultados = Backbone.View.extend({

    template: _.template($("#pedidos_proveedores_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click .edit":"seleccionar",
      "click .delete":"borrar",
      "click .imprimir":function(e) {
        var id = this.model.id;
        workspace.imprimir_reporte("pedidos_proveedores/function/imprimir/"+id);
      },
    },
    seleccionar : function(e) {
      if (this.options.seleccionar && this.parent != undefined) {
        $('.modal:last').modal('hide');
        this.parent.importar(this.model);
      } else {
        location.href="app/#pedido_proveedor/"+this.model.id;
      }
    },
    borrar : function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (confirmar("Realmente desea eliminar este pedido?")) {
        $.ajax({
          "url":"pedidos_proveedores/function/delete/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.pedidos_proveedoresTableView.buscar();
          }
        });                
      }
    },
    imprimir: function() {
      window.open("pedidos_proveedores/function/imprimir/"+this.model.id,"_blank");
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);