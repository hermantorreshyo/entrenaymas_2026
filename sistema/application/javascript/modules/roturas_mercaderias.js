// -----------
//   MODELO
// -----------

(function ( models ) {

  models.RoturaMercaderia = Backbone.Model.extend({
    urlRoot: "roturas_mercaderias/",
    defaults: {
      fecha: "",
      id_empresa: ID_EMPRESA,
      total: 0,
      items: [],
      observaciones: "",
      almacen: "",
      id_almacen: 0,
      estado: 0, // 0 = PENDIENTE, 1 = CONFIRMADO
      numero_remito: "",
    }
  });
      
})( app.models );

(function (collections, model, paginator) {
  collections.RoturasMercaderias = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 10,
      order_by: 'fecha',
      order: 'desc',
    },
    paginator_core: {
      url: "roturas_mercaderias/function/buscar/",
    },
  });

})( app.collections, app.models.RoturaMercaderia, Backbone.Paginator);


(function ( models ) {

  models.RoturaMercaderiaItem = Backbone.Model.extend({
    urlRoot: "roturas_mercaderias_items/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      costo_final: 0,  // Unitario
      precio_final: 0,
      nombre: "",
      orden: 0,
      codigo: "",
      codigo_barra: "",
      total_final: 0,
      no_editar_stock: 0,
    }
  });
      
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.RoturaMercaderiaEditView = app.mixins.View.extend({

    template: _.template($("#rotura_mercaderia_edit_panel_template").html()),

    myEvents: {
      "click .aceptar": function() {
        this.model.set({
          "estado":0,
        });
        this.aceptar();
      },
      "click .confirmar": function() {
        if (!confirm("Desea confirmar el comprobante?")) return;
        this.model.set({
          "estado":1,
        })
        this.aceptar();
      },
      "click #rotura_mercaderia_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #rotura_mercaderia_agregar_item": "agregar_item",

      "click .imprimir": function(){
        this.imprimir(this.model.id);
      },

      "focusin #rotura_mercaderia_codigo_articulo":function() {
        $("#tabla_items tbody tr.seleccionado").removeClass('seleccionado');
        $("#tabla_items tbody tr .radio").prop("checked",false);
      },
      "keypress #rotura_mercaderia_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #rotura_mercaderia_item_cantidad": function(e) {
        if (e.which == 13) { this.$("#rotura_mercaderia_agregar_item").focus(); }
      },
    },

    imprimir: function(id) {
      workspace.imprimir_reporte("roturas_mercaderias/function/imprimir/"+id);
    },

    buscar_articulo : function() {
      var self = this;

      // Primero controlamos que haya seleccionado la sucursal que quiere
      var id_sucursal = this.$("#rotura_mercaderia_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#rotura_mercaderia_almacenes").focus();
        return;
      }
      var codigo = $("#rotura_mercaderia_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }

      var tipo_codigo = this.$("input[name=tipo_codigo]:checked").val();
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
    },
    
    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#rotura_mercaderia_item_cantidad").select();
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#rotura_mercaderia_id_articulo").val(this.item.get("id_articulo"));
      $("#rotura_mercaderia_codigo_articulo").val(this.item.get("codigo"));
      $("#rotura_mercaderia_item_nombre").val(this.item.get("nombre"));
      $("#rotura_mercaderia_item_cantidad").val(this.item.get("cantidad"));
      $("#rotura_mercaderia_item_costo_final").val(Number(this.item.get("costo_final")).toFixed(2));
      $("#rotura_mercaderia_precio_final").val(Number(this.item.get("precio_final")).toFixed(2));
      $("#rotura_mercaderia_item_descripcion").val(this.item.get("descripcion"));
      $("#rotura_mercaderia_item_no_editar_stock").val(this.item.get("no_editar_stock"));

      this.articulo = new app.models.AbstractModel({
        "id":this.item.get("id_articulo"),
        "nombre":this.item.get("nombre"),
        "costo_final":this.item.get("costo_final"),
        "precio_final":this.item.get("precio_final"),
        "codigo":this.item.get("codigo"),
        "no_editar_stock":this.item.get("no_editar_stock"),
      });

      self.calcular_item();
      this.$("#rotura_mercaderia_item_cantidad").select();            
    },
    
    ver_buscar_articulo : function() {
      var self = this;
      var id_sucursal = this.$("#rotura_mercaderia_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#rotura_mercaderia_almacenes").focus();
        return;
      }
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: new app.collections.Articulos(),
        habilitar_seleccion: true,
        id_sucursal: id_sucursal,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            self.$("#rotura_mercaderia_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#rotura_mercaderia_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    mostrar_articulo : function() {
      this.$("#rotura_mercaderia_item_nombre").val(this.articulo.get("nombre"));
      this.$("#rotura_mercaderia_id_articulo").val(this.articulo.id);
      this.$("#rotura_mercaderia_item_costo_final").val(Number(this.articulo.get("costo_final")).toFixed(2));
      this.$("#rotura_mercaderia_precio_final").val(Number(this.articulo.get("precio_final")).toFixed(2));
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;

      var codigo = this.$("#rotura_mercaderia_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#rotura_mercaderia_codigo_articulo").focus();
        return;
      }                

      var cantidad = this.$("#rotura_mercaderia_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES); }

      // Si ya existe el codigo ingresado, tenemos que t

      var bonificacion = 0;
      var id_articulo = this.$("#rotura_mercaderia_id_articulo").val();
      var costo_final = parseFloat(this.$("#rotura_mercaderia_item_costo_final").val());
      var total_final = costo_final * ((100-bonificacion)/100) * cantidad;
      var precio_final = parseFloat(this.$("#rotura_mercaderia_precio_final").val());
      
      var values = {
        "id_articulo":id_articulo,
        "costo_final":costo_final,
        "codigo":codigo,
        "nombre":this.$("#rotura_mercaderia_item_nombre").val(),
        "cantidad":cantidad,
        "precio_final":precio_final,
        "total_final":total_final,
        "no_editar_stock":this.$("#rotura_mercaderia_item_no_editar_stock").val(),
      };            

      // Actualizamos o agregamos el item
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.RoturaMercaderiaItem(values);
        this.items.add(item);
      }
        
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#rotura_mercaderia_codigo_articulo").select();              

      this.$('#tabla_items').parent().scrollTop(self.$('#tabla_items').parent()[0].scrollHeight);
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#rotura_mercaderia_item_cantidad").val();
      var precio_unit = this.$("#rotura_mercaderia_item_costo_final").val();
      var bonificado = 0; //this.$("#rotura_mercaderia_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(FACTURACION_CANTIDAD_DECIMALES);
      this.$("#rotura_mercaderia_item_subtotal").val(subtotal);
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
          model: app.models.RoturaMercaderiaItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.RoturaMercaderiaItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);

        this.render_tabla_items();
      }
    },

    render: function() {
        
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        edicion: edicion, 
        id:this.model.id,
        permiso: this.options.permiso 
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      if (isEmpty(this.model.get("fecha"))) this.model.set("fecha",moment().format("DD/MM/YYYY"));
      createdatepicker(this.$("#rotura_mercaderia_fecha"),this.model.get("fecha"));
  
      this.limpiar_item();
      return this;
    },

    render_view: function() {
      var self = this;
      self.$("#rotura_mercaderia_subtotal_neto").val(Number(self.model.get("neto")).toFixed(2));
      self.$("#rotura_mercaderia_total").val(Number(self.model.get("total")).toFixed(2));
    },
    
    calcular_totales : function() {
      var total = 0;
      var items = this.model.get("items");
      this.items.each(function(item){
        total = total + item.get("total_final");
      });
      this.model.set({
        "total":total,
      });
    },
    
    limpiar_item: function() {
      this.$("#rotura_mercaderia_id_articulo").val("0");
      this.$("#rotura_mercaderia_item_nombre").val("");
      this.$("#rotura_mercaderia_item_descripcion").val("");
      this.$("#rotura_mercaderia_item_cantidad").val("1");
      this.$("#rotura_mercaderia_item_no_editar_stock").val("0");
      this.$("#rotura_mercaderia_item_costo_final").val("0.00");
      this.$("#rotura_mercaderia_precio_final").val("0.00");
      this.$("#rotura_mercaderia_item_subtotal").val("");
      this.$("#rotura_mercaderia_codigo_articulo").val("");
      this.$("#rotura_mercaderia_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },
    
    addItem : function ( item ) {
      var view = new app.views.RoturaMercaderiaItemTabla({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.el);
      this.calcular_totales();
    },
    
    validar: function() {
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al comprobante antes de guardar.";
      }
    },
    
    limpiar: function() {
      this.model = new app.models.RoturaMercaderia({
        "items":[],
      });
      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.RoturaMercaderiaItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
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
      this.model.save({
        "items":self.items.toJSON(),
        "fecha":self.$("#rotura_mercaderia_fecha").val(),
        "id_almacen":((self.$("#rotura_mercaderia_almacenes").length > 0) ? self.$("#rotura_mercaderia_almacenes").val() : 0),
        "numero_remito":self.$("#rotura_mercaderia_numero").val(),
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
            location.href = "app/#roturas_mercaderias";
          }
        },
      });
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.RoturaMercaderiaItemTabla = app.mixins.View.extend({
    template: _.template($("#rotura_mercaderia_item_tabla_template").html()),
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
          $("#rotura_mercaderia_codigo_articulo").focus();
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
      $("#rotura_mercaderia_codigo_articulo").focus();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.RoturaMercaderiaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#roturas_mercaderias_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .imprimir":function() {
        var id = this.model.id;
        workspace.imprimir_reporte("roturas_mercaderias/function/imprimir/"+id);
      },
      "click .imprimir_sin_costo": function(){
        var id = this.model.id;
        workspace.imprimir_reporte("roturas_mercaderias/function/imprimir/"+id+"?con_precio=0");
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
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#rotura_mercaderia/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    duplicar: function(e) {
      var clonado = this.model.clone();
      clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
      clonado.save({},{
        success: function(model,response) {
          model.set({id:response.id});
        }
      });
      this.model.collection.add(clonado);
      e.stopPropagation();
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.RoturasMercaderiasTableView = app.mixins.View.extend({

    template: _.template($("#roturas_mercaderias_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.roturas_mercaderias_filter = (typeof window.roturas_mercaderias_filter != "undefined") ? window.roturas_mercaderias_filter : "";
      window.roturas_mercaderias_id_sucursal = (typeof window.roturas_mercaderias_id_sucursal != "undefined") ? window.roturas_mercaderias_id_sucursal : 0;
      window.roturas_mercaderias_desde = (typeof window.roturas_mercaderias_desde != "undefined") ? window.roturas_mercaderias_desde : "";
      window.roturas_mercaderias_hasta = (typeof window.roturas_mercaderias_hasta != "undefined") ? window.roturas_mercaderias_hasta : "";
      window.roturas_mercaderias_page = (typeof window.roturas_mercaderias_page != "undefined") ? window.roturas_mercaderias_page : 1;
      this.permiso = this.options.permiso;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker($(this.el).find("#roturas_mercaderias_desde"),window.roturas_mercaderias_desde);
      createdatepicker($(this.el).find("#roturas_mercaderias_hasta"),window.roturas_mercaderias_hasta);
      return this;
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.roturas_mercaderias_filter != this.$("#roturas_mercaderias_buscar").val().trim()) {
        window.roturas_mercaderias_filter = this.$("#roturas_mercaderias_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#roturas_mercaderias_desde").length > 0) {
        if (window.roturas_mercaderias_desde != this.$("#roturas_mercaderias_desde").val().trim()) {
          window.roturas_mercaderias_desde = this.$("#roturas_mercaderias_desde").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#roturas_mercaderias_hasta").length > 0) {
        if (window.roturas_mercaderias_hasta != this.$("#roturas_mercaderias_hasta").val().trim()) {
          window.roturas_mercaderias_hasta = this.$("#roturas_mercaderias_hasta").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#roturas_mercaderias_buscar_sucursales").length > 0) {
        if (window.roturas_mercaderias_id_sucursal != this.$("#roturas_mercaderias_buscar_sucursales").val().trim()) {
          window.roturas_mercaderias_id_sucursal = this.$("#roturas_mercaderias_buscar_sucursales").val().trim();
          cambio_parametros = true;
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.roturas_mercaderias_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.roturas_mercaderias_filter),
        "desde": (isEmpty(window.roturas_mercaderias_desde)) ? "" : window.roturas_mercaderias_desde.replace(/\//g,"-"),
        "hasta": (isEmpty(window.roturas_mercaderias_hasta)) ? "" : window.roturas_mercaderias_hasta.replace(/\//g,"-"),
        "id_sucursal":window.roturas_mercaderias_id_sucursal,
        "estado":((control.check("roturas_mercaderias")==3)?-1:1),
      };
      if (!isEmpty(window.roturas_mercaderias_desde)) {
        this.collection.setManyPer(99999999999);
      }
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.roturas_mercaderias_page);
    },
        
    addAll : function () {
      this.total = 0;
      window.roturas_mercaderias_page = this.pagination.getPage();
      this.$("#roturas_mercaderias_table tbody").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
      this.$("#roturas_mercaderias_total").html(Number(this.total).toFixed(2));
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.RoturaMercaderiaItem({
        model: item,
        collection: self.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
        permiso: this.permiso,
      });
      this.total += parseFloat(item.get("total"));
      this.$("#roturas_mercaderias_table tbody").append(view.render().el);
    },
  });
})(app);
