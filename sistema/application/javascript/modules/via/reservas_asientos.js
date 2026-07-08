// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ReservaMesa = Backbone.Model.extend({
    urlRoot: "reservas_mesas",
    defaults: {
      titulo: "", // No se persiste
      id_referencia: 0, // ID_MESA
      numero_referencia: 1, // PERSONAS
      id_tipo_comprobante: 999,
      id_cliente: 0,
      cliente: "",
      direccion: "",
      observaciones: "",
      items: [],
      tarjetas: [],
      cheques: [],
      subtotal: 0,
      costo_envio: 0,
      efectivo: 0,
      cta_cte: 0,
      vuelto: 0,
      tarjeta: 0,
      cheque: 0,
      fecha: "",
      hora: "",
      total: 0,
      tipo: "M", // M = MESA; D = DELIVERY; T = MOSTRADOR
      id_tipo_estado: 0, // 0 = EN PROCESO; 6 = FINALIZADO
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
    },
  });
	  
})( app.models );


(function ( models ) {

  models.ReservaItem = Backbone.Model.extend({
    urlRoot: "reservas_mesas",
    defaults: {
      id_articulo: 0,
      tipo_cantidad: "",
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      id_tipo_estado: 0,
      neto: 0,  // Unitario
      precio: 0,  // Unitario
      nombre: "",
      orden: 0,
      id_rubro: 0,
      iva: 0,
      total_sin_iva: 0, // Totales (unitario * cantidad)
      total_con_iva: 0,
    }
  });
    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.ReservasMesas = paginator.requestPager.extend({

    model: model,
    
    paginator_core: {
      url: "reservas_mesas/"
    }
  
  });

})( app.collections, app.models.Mesa, Backbone.Paginator);


(function ( app ) {

  app.views.ReservaMesaEditView = app.mixins.View.extend({

    template: _.template($("#reserva_mesa_template").html()),
      
    myEvents: {
      "click .cerrar":function() {
        $(".modal:last").trigger('click');
      },
      "click .guardar": "guardar",
      "click .cerrar_mesa": "cerrar_mesa",

      "focusout #reserva_mesa_cliente":function(e) {
        // Si el cliente esta vacio, ponemos consumidor final
        if (isEmpty($(e.currentTarget).val())) {
          this.setear_consumidor_final();
        }
      },

      // Buscamos el cliente por codigo
      /*
      "keyup #reserva_mesa_cliente": function(e) {
        if (e.keyCode == 113) { this.ver_buscar_cliente(); }
      },
      "keypress #reserva_mesa_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); }
      },
      "click #reserva_mesa_buscar_cliente": "ver_buscar_cliente",
      */
      "click #reserva_mesa_buscar_articulo":"ver_buscar_articulo",
      "keypress #reserva_mesa_item_articulo": function(e) {
        if (e.which == 13) this.buscar_articulo();
      },
      "keypress #reserva_mesa_item_cantidad":function(e) {
        if (e.which == 13) $("#reserva_mesa_item_precio").select();
      },
      "keypress #reserva_mesa_item_precio":function(e) {
        if (e.which == 13) this.agregar_item();
      },
      "click #reserva_mesa_agregar_item":function() {
        this.agregar_item();
      },

      // Mas y menos de Input group
      "click .addon_minus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var min = $(el).attr("min");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (min != undefined && (valor-step < min)) valor = min;
        else valor = valor - step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .addon_plus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var max = $(el).attr("max");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (max != undefined && (valor+step > max)) valor = max;
        else valor = valor + step;
        $(el).val(valor);
        $(el).trigger("change");
      },

    },
        
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ReservaItem,
        });
        this.items = new ItemsCollection();
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);
        
        // Renderizamos y limpiamos
        this.render();
        this.setear_consumidor_final();

      // Estamos editando
      } else {
        
        this.render();
        
        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ReservaItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);        
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          p.id_tipo_estado = this.model.get("id_tipo_estado");
          var fi = new app.models.ReservaItem(p);
          this.items.add(fi);
        }
        
        // Buscamos el cliente y lo seteamos
        /*
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
        */
        
        //this.render_tabla_items();
        //$("#tabla_items tbody").empty();
      }
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

    render: function() {

      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#reserva_mesa_cliente");
      if (this.model.get("tipo") !== "D") {
        var form = new app.views.ClienteEditViewMini({
          "model": new app.models.Cliente(),
          "input": input,
          "onSave": self.seleccionar_cliente,
        });      
      } else {
        var form = null;
      }
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
              if (self.model.get("tipo") == "D") {
                self.$("#reserva_mesa_direccion").focus();
              } else {
                self.$("#reserva_mesa_item_articulo").focus();
              }
            },
          });
        }
      });        

      var input = this.$("#reserva_mesa_item_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "width":"300px",
        "label":"[nombre] ([codigo])",
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });

    },

    seleccionar_cliente: function(r) {
      var self = this;
      // Seteamos el cliente
      self.model.set({"cliente":r});
      self.$("#reserva_mesa_cliente").val(r.get("nombre"));
      self.$("#reserva_mesa_direccion").val(r.get("direccion"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#reserva_mesa_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);        
    },

    ver_buscar_cliente: function() {
      var self = this;
      var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
        collection: clientes,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarClientes.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#reserva_mesa_cliente").select();          
        }
      });
      $(".search_input").select();
    },

    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#reserva_mesa_item_cantidad").select();
      if (r.get("unidad") == "M") {
        this.$("#reserva_mesa_item_cantidad").data("step","0.5");
      } else {
        this.$("#reserva_mesa_item_cantidad").data("step","1");
      }

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#reserva_mesa_item_articulo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#reserva_mesa_item_id_articulo").val(this.item.get("id_articulo"));
      $("#reserva_mesa_item_articulo").val(this.item.get("nombre"));
      $("#reserva_mesa_item_cantidad").val(this.item.get("cantidad"));
      $("#reserva_mesa_item_precio").val(this.item.get("precio"));
      self.calcular_item();
      this.$("#reserva_mesa_item_cantidad").select();      
    },

    ver_buscar_articulo : function() {
      var self = this;
      var view = new app.views.BuscarArticulosPorRubroView({
        collection: new app.collections.Articulos(),
      });
      window.reservas = new Array();
      var d = $("<div/>").append(view.el);
      crearLightboxHTML({
        "html":d,
        "width":800,
        "height":500,
        "callback":function() {
          if (window.reservas.length > 0) {
            for(var i=0;i<window.reservas.length;i++) {
              var reserva = window.reservas[i];
              if (reserva.cantidad == 0) continue;
              self.articulo = reserva.articulo;
              self.mostrar_articulo();
              self.$("#reserva_mesa_item_cantidad").val(reserva.cantidad);
              self.calcular_item();
              self.agregar_item();
            }
          }
        }
      });
    },

    buscar_articulo : function() {
      
      var self = this;
      var codigo = $("#reserva_mesa_item_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      
      // Lo buscamos en el array
      var r = window.articulos.find(function(c){
        return (c.get("codigo") == codigo);
      });
      if (typeof r === "undefined") {
        self.articulo = null;
        this.$("#reserva_mesa_item_cantidad").select();
      } else {
        this.seleccionar_articulo(r);
      }
    },
    
    mostrar_articulo : function() {
      this.$("#reserva_mesa_item_articulo").val(this.articulo.get("nombre"));
      this.$("#reserva_mesa_item_id_articulo").val(this.articulo.id);
      this.$("#reserva_mesa_item_precio").val(this.articulo.get("precio_final"));
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      
      var self = this;
      
      var codigo = this.$("#reserva_mesa_item_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#reserva_mesa_item_articulo").focus();
        return;
      }
      
      var id_articulo = this.$("#reserva_mesa_item_id_articulo").val();
      var id_rubro = (this.articulo != undefined) ? this.articulo.get("id_rubro") : 0;
      var cantidad = this.$("#reserva_mesa_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }
      
      var bonificacion = 0; //this.$("#remito_item_bonificado").val();
      var precio = parseFloat(this.$("#reserva_mesa_item_precio").val());
      var total = precio * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":id_articulo,
        "precio":precio,
        "nombre":codigo,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "id_rubro":id_rubro,
        "total_con_iva":total,
      };
      
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.ReservaItem(values);
        this.items.add(item);
      }
      
      this.item = undefined;
      this.limpiar_item();
      this.$("#reserva_mesa_item_articulo").select();      
    },
    
    calcular_item: function() {
      var self = this;
      var cantidad = this.$("#reserva_mesa_item_cantidad").val();
      var precio_unit = this.$("#reserva_mesa_item_precio").val();
      var bonificado = 0; //this.$("#remito_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
      this.$("#reserva_mesa_item_subtotal").val(subtotal);
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      var view = new app.views.ReservaItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },    

    calcular_totales : function() {
      
      var porc_descuento = 0; var total = 0; var costo_envio = 0;
      var descuento = 0; var subtotal = 0;
      var items = this.model.get("items");
      
      //porc_descuento = parseFloat(this.$("#remito_porc_descuento").val());
      //if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      this.items.each(function(item){
        total = total + parseFloat(item.get("total_con_iva")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total_con_iva"));
      });
      
      /*
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;

      var costo_envio = parseFloat(this.$("#remito_costo_envio").val());
      if (isNaN(costo_envio)) costo_envio = 0;
      total = total + costo_envio;
      */

      this.model.set({
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "subtotal":subtotal,
        "costo_envio":costo_envio,
        "total":total,
      });
      this.$("#reserva_mesa_total").html("$ "+Number(total).toFixed(2));
    },

    limpiar_item: function() {
      this.$("#reserva_mesa_item_id_articulo").val("0");
      this.$("#reserva_mesa_item_cantidad").val("1");
      this.$("#reserva_mesa_item_precio").val("0.00");
      this.$("#reserva_mesa_item_subtotal").val("");
      this.$("#reserva_mesa_item_articulo").val("");
      this.$("#reserva_mesa_item_articulo").focus();
    },

    abrir:function() {
      this.model.set({"estado":"A"});
      this.render();
    },
    
    validar: function() {
      try {
        var self = this;

        if (this.items.length == 0) {
          alert("Por favor agregue algun producto antes de guardar.");
          return false;
        }
        
        this.model.set({
          "items":self.items.toJSON(),
          "id_usuario": ((self.$("#reserva_mesa_usuarios").length>0) ? self.$("#reserva_mesa_usuarios").val() : ID_USUARIO),
          "numero_referencia": ((self.$("#reserva_mesa_personas").length>0) ? self.$("#reserva_mesa_personas").val() : 1),
          //"cliente": ((self.$("#reserva_mesa_cliente").length>0) ? self.$("#reserva_mesa_cliente").val() : ""),
          "direccion": ((self.$("#reserva_mesa_direccion").length>0) ? self.$("#reserva_mesa_direccion").val() : ""),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },	
	
    guardar:function() {
      var self = this;
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
              self.cerrar_panel();
            }
          }
        });
      }
    },

    cerrar_panel: function() {
      if (this.model.get("tipo") == "M") {
        // Volvemos a buscar el salon
        $(".tab_link.active").trigger("click");        
      } else if (this.model.get("tipo") == "D") {
        app.views.deliveriesTableView.buscar();  
      } else if (this.model.get("tipo") == "T") {
        app.views.mostradoresTableView.buscar();
      }
      $(".modal:last").trigger('click');
    },

    cerrar_mesa:function() {
      var self = this;
      if (this.validar()) {

        if (self.model.id == null) {
          self.model.set({id:0});
        }
        this.model.set({
          "id_tipo_estado":6, // FINALIZAMOS EL PEDIDO
        });
        app.views.metodoPagoView = new app.views.MetodoPagoView({
          "model": self.model,
        });
        crearLightboxHTML({
          "html":app.views.metodoPagoView.el,
          "width":460,
          "height":500,
          "callback":function() {
            if (window.facturacion_guardo) {
              // El reserva ha sido guardado, cerramos el panel
              self.cerrar_panel();
            } else {
              // Volvemos a poner el reserva como que todavia no se finalizo
              // para que si el usuario hace click en "Guardar" (no en Cerrar)
              // el reserva no se cierre
              self.model.set({
                "id_tipo_estado":0,
              })
            }            
          }
        });
      }
    },
    	
  });
})(app);


(function ( app ) {
  app.views.ReservaItem = app.mixins.View.extend({
    
    template: _.template($("#reserva_item_template").html()),
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
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.id_tipo_estado = this.options.id_tipo_estado;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = { "id_tipo_estado":this.id_tipo_estado };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);