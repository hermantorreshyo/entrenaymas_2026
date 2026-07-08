// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TransferenciaStock = Backbone.Model.extend({
    urlRoot: "transferencias_stock/",
    defaults: {
      fecha: "",
      id_empresa: ID_EMPRESA,
      total: 0,
      neto: 0,
      items: [],
      observaciones: "",
      numero_remito: "",
      id_origen: 0,
      id_destino: 0,
      estado: 0, // 0 = PENDIENTE, 1 = CONFIRMADO
    }
  });
      
})( app.models );


(function (collections, model, paginator) {
  collections.TransferenciasStock = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "transferencias_stock/function/ver/?id_sucursal="+ID_SUCURSAL,
    }
  });
})( app.collections, app.models.TransferenciaStock, Backbone.Paginator);


(function ( models ) {

  models.TransferenciaStockItem = Backbone.Model.extend({
    urlRoot: "transferencias_stock/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      costo_neto: 0,    // Unitario
      costo_final: 0,  // Unitario
      precio_neto: 0,
      precio_final: 0,
      nombre: "",
      orden: 0,
      codigo: "",
      codigo_barra: "",
      total_neto: 0, // Totales (unitario * cantidad)
      total_final: 0,
    }
  });
      
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.TransferenciaStockEditView = app.mixins.View.extend({

    template: _.template($("#transferencia_stock_edit_panel_template").html()),

    myEvents: {
      "click .aceptar": function() {
        this.confirmar = 0;
        this.aceptar();
      },
      "click .confirmar": function() {
        this.confirmar = 1;
        this.aceptar();
      },
      "click #transferencia_stock_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #transferencia_stock_agregar_item": "agregar_item",

      "click .imprimir": function(){
        this.imprimir(this.model.id);
      },
      "keypress #transferencia_stock_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #transferencia_stock_item_cantidad": function(e) {
        if (e.which == 13) { 
          if (control.check("transferencias_stock")<3) {
            this.agregar_item();
          } else {
            this.$("#transferencia_stock_agregar_item").focus(); 
          }
        }
      },
      // ACCIONES SOBRE EL FORMULARIO
      "keyup .action":function(e) {
        if (e.which == 120) { e.preventDefault(); e.stopPropagation(); this.ver_buscar_articulo(); return false; } // F9
      },
    },

    imprimir: function(id) {
      workspace.imprimir_reporte("transferencias_stock/function/imprimir/"+id);
    },
        

    buscar_articulo : function() {
      var self = this;

      // Primero controlamos que haya seleccionado la sucursal que quiere
      var id_sucursal = this.$("#transferencia_stock_almacen_origen").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#transferencia_stock_almacen_origen").focus();
        return;
      }

      var codigo = $("#transferencia_stock_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }

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
      this.$("#transferencia_stock_item_cantidad").select();
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#transferencia_stock_id_articulo").val(this.item.get("id_articulo"));
      $("#transferencia_stock_codigo_articulo").val(this.item.get("codigo"));
      $("#transferencia_stock_item_nombre").val(this.item.get("nombre"));
      $("#transferencia_stock_item_cantidad").val(this.item.get("cantidad"));
      $("#transferencia_stock_item_costo_neto").val(this.item.get("costo_neto"));
      $("#transferencia_stock_item_costo_final").val(this.item.get("costo_final"));
      $("#transferencia_stock_precio_final").val(this.item.get("precio_final"));
      $("#transferencia_stock_alicuotas_iva").val(this.item.get("id_tipo_alicuota_iva"));
      $("#transferencia_stock_item_descripcion").val(this.item.get("descripcion"));
      self.calcular_item();
      this.$("#transferencia_stock_item_cantidad").select();            
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
            self.$("#transferencia_stock_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.buscar_articulo();
          } else {
            self.$("#transferencia_stock_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    mostrar_articulo : function() {
      // TODO: REVISAR EL TEMA DE LISTAS
      console.log(this.articulo);
      this.$("#transferencia_stock_item_nombre").val(this.articulo.get("nombre"));
      this.$("#transferencia_stock_alicuotas_iva").val(this.articulo.get("id_tipo_alicuota_iva"));
      this.$("#transferencia_stock_id_articulo").val(this.articulo.id);
      this.$("#transferencia_stock_item_costo_neto").val(Number(this.articulo.get("costo_neto")).toFixed(2));
      this.$("#transferencia_stock_item_costo_final").val(Number(this.articulo.get("costo_final")).toFixed(2));
      this.$("#transferencia_stock_item_porc_ganancia").val(Number(this.articulo.get("porc_ganancia")).toFixed(2));
      this.$("#transferencia_stock_precio_final").val(Number(this.articulo.get("precio_final")).toFixed(2));
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;

      if (typeof this.articulo == "undefined") {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#transferencia_stock_codigo_articulo").focus();
        return;
      }
      
      var cantidad = this.$("#transferencia_stock_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES); }
      
      // El precio que figura es el FINAL
      var bonificacion = 0;
      var porc_iva = parseFloat(this.articulo.get("porc_iva"));
      var costo_final = parseFloat(this.articulo.get("costo_final"));
      var costo_neto = parseFloat(this.articulo.get("costo_neto"));
      var total_neto = costo_neto * ((100-bonificacion)/100) * cantidad;
      var total_final = costo_final * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":this.articulo.id,
        "costo_neto":costo_neto,
        "costo_final":costo_final,
        "codigo":this.articulo.get("codigo"),
        "nombre":this.articulo.get("nombre"),
        "cantidad":cantidad,
        "porc_iva":porc_iva,
        "precio_neto":this.articulo.get("precio_neto"),
        "precio_final":this.articulo.get("precio_final"),
        "total_neto":total_neto,
        "total_final":total_final,
        "id_tipo_alicuota_iva":this.articulo.get("id_tipo_alicuota_iva"),
      };            

      // Actualizamos o agregamos el item
      console.log(this.item);
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.TransferenciaStockItem(values);
        this.items.add(item);
      }
        
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#transferencia_stock_codigo_articulo").select();              
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#transferencia_stock_item_cantidad").val();
      var precio_unit = this.$("#transferencia_stock_item_costo_final").val();
      var bonificado = this.$("#transferencia_stock_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(FACTURACION_CANTIDAD_DECIMALES);
      this.$("#transferencia_stock_item_subtotal").val(subtotal);
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
          model: app.models.TransferenciaStockItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.TransferenciaStockItem(p);
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
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      if (isEmpty(this.model.get("fecha"))) this.model.set("fecha",moment().format("DD/MM/YYYY"));
      createdatepicker(this.$("#transferencia_stock_fecha"),this.model.get("fecha"));
  
      this.limpiar_item();
      var input = this.$("#transferencia_stock_codigo_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "width":"300px",
        "label":"[nombre] ([codigo])",
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });
      return this;
    },
    
    calcular_totales : function() {
        
      var neto = 0; var porc_descuento = 0; var total = 0; var iva = 0;
      var descuento = 0; var subtotal_neto = 0; var subtotal_final = 0;
      var items = this.model.get("items");
        
      var porc_descuento = 0; /*parseFloat(this.$("#transferencia_stock_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);*/
      var pdesc = 1;
        
      this.items.each(function(item){
        neto = neto + item.get("total_neto") * pdesc;
        total = total + item.get("total_final") * pdesc;
        subtotal_neto = subtotal_neto + parseFloat(item.get("total_neto"));
        subtotal_final = subtotal_final + parseFloat(item.get("total_final"));
        iva = iva + item.get("iva") * pdesc;
      });
      
      subtotal = subtotal_final;
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;
        
      this.model.set({
        "iva":iva,
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "neto":neto,
        "subtotal":subtotal,
        "total":total,
      });
    },

    render_view: function() {
      var self = this;
      self.$("#transferencia_stock_subtotal_neto").val(Number(self.model.get("neto")).toFixed(2));
      self.$("#transferencia_stock_total").val(Number(self.model.get("total")).toFixed(2));
    },
    
    limpiar_item: function() {
      this.$("#transferencia_stock_id_articulo").val("0");
      this.$("#transferencia_stock_item_nombre").val("");
      this.$("#transferencia_stock_item_descripcion").val("");
      this.$("#transferencia_stock_item_cantidad").val("1");
      this.$("#transferencia_stock_item_bonificado").val("0");
      this.$("#transferencia_stock_item_costo_neto").val("0.00");
      this.$("#transferencia_stock_item_costo_final").val("0.00");
      this.$("#transferencia_stock_precio_final").val("0.00");
      this.$("#transferencia_stock_item_porc_ganancia").val("");
      this.$("#transferencia_stock_item_subtotal").val("");
      this.$("#transferencia_stock_codigo_articulo").val("");
      this.$("#transferencia_stock_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },
    
    addItem : function ( item ) {
      var view = new app.views.TransferenciaStockItemTabla({
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

      var id_origen = this.$("#transferencia_stock_almacen_origen").val();
      var id_destino = this.$("#transferencia_stock_almacen_destino").val();
      if (id_origen == id_destino) {
        throw "ERROR: La sucursal de destino es la misma que la de origen.";
      }
    },
    
    limpiar: function() {
      this.model = new app.models.TransferenciaStock({
        "items":[],
      });

      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.TransferenciaStockItem,
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
        "confirmar":self.confirmar,
        "items":self.items.toJSON(),
        "id_origen":self.$("#transferencia_stock_almacen_origen").val(),
        "id_destino":self.$("#transferencia_stock_almacen_destino").val(),
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
            location.href = "app/#transferencias_stock";
          }
        },
      });
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.TransferenciaStockItemTabla = app.mixins.View.extend({
    template: _.template($("#transferencia_stock_item_tabla_template").html()),
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
          $("#transferencia_stock_codigo_articulo").focus();
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
      $("#transferencia_stock_codigo_articulo").focus();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.TransferenciaStockItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#transferencias_stock_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .imprimir":function() {
        var id = this.model.id;
        workspace.imprimir_reporte("transferencias_stock/function/imprimir/"+id);
      },
      "click .imprimir_sin_costo": function(){
        var id = this.model.id;
        workspace.imprimir_reporte("transferencias_stock/function/imprimir/"+id+"?con_precio=0");
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
      location.href="app/#transferencia_stock/"+this.model.id;
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

  app.views.TransferenciasStockTableView = app.mixins.View.extend({

   template: _.template($("#transferencias_stock_panel_template").html()),

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
      var view = new app.views.TransferenciaStockItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);
