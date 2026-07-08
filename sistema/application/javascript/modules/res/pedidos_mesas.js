// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PedidoMesa = Backbone.Model.extend({
    urlRoot: "pedidos_mesas",
    defaults: {
      titulo: "", // No se persiste
      id_referencia: 0, // ID_MESA
      numero_referencia: 1, // PERSONAS
      id_tipo_comprobante: 999,
      id_cliente: 0,
      cliente: "",
      nombre: "",
      direccion: "",
      telefono: "",
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
      pagada: 0,
      tipo: "M", // M = MESA; D = DELIVERY; T = MOSTRADOR; B = BARRA
      id_tipo_estado: 0, // 0 = EN PROCESO; 1 = RESERVADA; 2 = PENDINTE DE APROBACION; 3 = PENDIENTE DE PAGO; 6 = FINALIZADO; 7 = CANCELADO
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
      last_update: 0, // Marca cuando fue la ultima vez que se actualizo el pedido
      porc_descuento: 0,
    },
  });

})( app.models );


(function ( models ) {

  models.PedidoMesaItem = Backbone.Model.extend({
    urlRoot: "pedidos_mesas",
    defaults: {
      id_articulo: 0,
      tipo_cantidad: "",
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      id_tipo_estado: 0,
      neto: 0,    // Unitario
      precio: 0,  // Unitario
      nombre: "",
      descripcion: "",
      orden: 0,
      id_rubro: 0,
      iva: 0,
      total_sin_iva: 0, // Totales (unitario * cantidad)
      total_con_iva: 0,
      porc_bonif: 0,
    }
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.PedidosMesas = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "pedidos_mesas/"
    }
    
  });

})( app.collections, app.models.Mesa, Backbone.Paginator);


(function ( app ) {

  app.views.PedidoMesaEditView = app.mixins.View.extend({

    template: _.template($("#pedido_mesa_template").html()),

    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar": "guardar",
      "click .reservar": "reservar",
      "click .reasignar": "reasignar",
      "click .unir": "unir",
      "click .eliminar_reservar":"eliminar_reservar",
      "click .cerrar_mesa": "cerrar_mesa",
      "click .cerrar_mesa_efectivo": function(){
        var self = this;
        this.total_anterior = parseFloat(this.model.get("total"));
        this.subtotal_anterior = parseFloat(this.model.get("subtotal"));
        this.model.set({
          "porc_descuento":10,
          "descuento":self.total_anterior * 0.1,
          "total":self.total_anterior * 0.9,
          "subtotal":self.subtotal_anterior * 0.9,
        });
        this.cerrar_mesa();
      },
      "click .cerrar_mesa_tarjeta": function() {
        this.cerrar_mesa();
      },
      "click .imprimir":"imprimir",
      "click .aceptar_pedido":"aceptar_pedido",
      "click .rechazar_pedido":"rechazar_pedido",

      "focusout #pedido_mesa_cliente":function(e) {
        // Si el cliente esta vacio, ponemos consumidor final
        if (isEmpty($(e.currentTarget).val())) {
          this.setear_consumidor_final();
        }
      },

      // Buscamos el cliente por codigo
      /*
      "keyup #pedido_mesa_cliente": function(e) {
          if (e.keyCode == 113) { this.ver_buscar_cliente(); }
      },
      "keypress #pedido_mesa_cliente":function(e) {
          if (e.which == 13) { this.buscar_cliente(); }
      },
      "click #pedido_mesa_buscar_cliente": "ver_buscar_cliente",
      */
      "click #pedido_mesa_buscar_articulo":"ver_buscar_articulo",
      "keypress #pedido_mesa_item_articulo": function(e) {
        if (e.which == 13) this.buscar_articulo();
      },
      "keypress #pedido_mesa_item_cantidad":function(e) {
        if (e.which == 13) {
          if (this.$("#pedido_mesa_item_precio").is(":disabled")) {
            $("#pedido_mesa_agregar_item").focus();
          } else {
            $("#pedido_mesa_item_precio").select();
          }
        }
      },
      "keypress #pedido_mesa_item_precio":function(e) {
        if (e.which == 13) this.agregar_item();
      },
      "click #pedido_mesa_agregar_item":function() {
        this.agregar_item();
      },
      "keypress #pedido_mesa_item_descripcion":function(e) {
        if (e.which == 13) this.agregar_item();
      },

      "change #pedido_mesa_porc_descuento": function() {
        this.calcular_totales();
      },
      "change #pedido_mesa_tipo_entrega":function(e) {
        var tipo = $(e.currentTarget).val();
        var titulo = $(e.currentTarget).find("option:selected").text();
        this.model.set({
          "tipo":tipo,
          "titulo":titulo,
        });
        this.render();
        this.render_tabla_items();
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
      this.guardando = 0;
      this.items_nuevos = new Array();
      this.items_cocina = new Array();
      
      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.PedidoMesaItem,
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
          model: app.models.PedidoMesaItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);                
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          p.id_tipo_estado = this.model.get("id_tipo_estado");
          var fi = new app.models.PedidoMesaItem(p);
          this.items.add(fi);
        }
        
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
        
        //this.render_tabla_items();
        //$("#tabla_items tbody").empty();
      }
    },

    setear_consumidor_final: function() {
      var cf = new app.models.Cliente({
        "id":0,
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
      var input = this.$("#pedido_mesa_cliente");
      if (this.model.get("tipo") == "M") {
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
        "hideNoResults":true,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
              if (self.model.get("tipo") == "D") {
                self.$("#pedido_mesa_direccion").focus();
              } else {
                self.$("#pedido_mesa_item_articulo").focus();
              }
            },
          });
        }
      });                

      var input = this.$("#pedido_mesa_item_articulo");
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
      self.model.set({
        "cliente":r,
        "id_cliente":r.id,
        "nombre":r.get("nombre"),
        "direccion":r.get("direccion"),
        "telefono":r.get("telefono"),
      });
      self.$("#pedido_mesa_cliente").val(r.get("nombre"));
      self.$("#pedido_mesa_direccion").val(r.get("direccion"));
      self.$("#pedido_mesa_telefono").val(r.get("telefono"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#pedido_mesa_cliente').trigger(jQuery.Event('keyup', {which: 27}));
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
        "html":app.views.importar.el,
        "width":600,
        "height":140,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#pedido_mesa_cliente").select();                    
        }
      });
      $(".search_input").select();
    },

    seleccionar_articulo: function(r){
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#pedido_mesa_item_cantidad").select();
      if (r.get("unidad") == "M") {
        this.$("#pedido_mesa_item_cantidad").data("step","0.5");
      } else {
        this.$("#pedido_mesa_item_cantidad").data("step","1");
      }

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#pedido_mesa_item_articulo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#pedido_mesa_item_id_articulo").val(this.item.get("id_articulo"));
      $("#pedido_mesa_item_articulo").val(this.item.get("nombre"));
      $("#pedido_mesa_item_cantidad").val(this.item.get("cantidad"));
      $("#pedido_mesa_item_descripcion").val(this.item.get("descripcion"));
      $("#pedido_mesa_item_precio").val(this.item.get("precio"));
      $("#pedido_mesa_item_tipo").val(this.item.get("tipo"));
      self.calcular_item();
      this.$("#pedido_mesa_item_descripcion").select();            
    },

    duplicar_articulo: function(r) {
      var self = this;
      var id_articulo = r.get("id_articulo");
      // Lo buscamos en el array
      this.articulo = window.articulos.find(function(c){
        return (c.id == id_articulo);
      });
      $("#pedido_mesa_item_id_articulo").val(r.get("id_articulo"));
      $("#pedido_mesa_item_articulo").val(r.get("nombre"));
      $("#pedido_mesa_item_cantidad").val(1);
      $("#pedido_mesa_item_descripcion").val(r.get("descripcion"));
      $("#pedido_mesa_item_precio").val(r.get("precio"));
      $("#pedido_mesa_item_tipo").val(r.get("tipo"));
      $("#pedido_mesa_item_no_totalizar_reparto").val(this.articulo.get("no_totalizar_reparto"));
      self.calcular_item();
      self.agregar_item();
      this.articulo = null;
    },

    ver_buscar_articulo: function() {
      var self = this;
      // TODO: Hacer esto configurable
      if (ID_EMPRESA == 171 || ID_EMPRESA == 599) {
        var view = new app.views.BuscarArticulosCartaCompletaView({
          collection: new app.collections.Articulos(),
        });
      } else {
        var view = new app.views.BuscarArticulosPorRubroView({
          collection: new app.collections.Articulos(),
        });
      }
      window.pedidos = new Array();
      var d = $("<div/>").append(view.el);
      crearLightboxHTML({
        "html":d,
        "width":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? 1400 : 800,
        "height":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? 700 : 500,
        "escapable":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? false : true,
        "callback":function() {
          if (window.pedidos.length > 0) {
            for(var i=0;i<window.pedidos.length;i++) {
              var pedido = window.pedidos[i];
              if (pedido.cantidad == 0) continue;
              self.articulo = pedido.articulo;
              self.mostrar_articulo();
              self.$("#pedido_mesa_item_cantidad").val(pedido.cantidad);
              self.$("#pedido_mesa_item_descripcion").val(pedido.descripcion);
              self.calcular_item();
              self.agregar_item();
            }
          }
        }
      });
    },

    buscar_articulo: function() {

      var self = this;
      var codigo = $("#pedido_mesa_item_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      
      // Lo buscamos en el array
      var r = window.articulos.find(function(c){
        return (c.get("codigo") == codigo);
      });
      if (typeof r === "undefined") {
        self.articulo = null;
        this.$("#pedido_mesa_item_cantidad").select();
      } else {
        this.seleccionar_articulo(r);
      }
    },

    mostrar_articulo : function() {
      this.$("#pedido_mesa_item_articulo").val(this.articulo.get("nombre"));
      this.$("#pedido_mesa_item_id_articulo").val(this.articulo.id);
      this.$("#pedido_mesa_item_descripcion").val(this.articulo.get("descripcion"));
      this.$("#pedido_mesa_item_no_totalizar_reparto").val(this.articulo.get("no_totalizar_reparto"));
      // El precio se forma con el adicional
      var precio = parseFloat(this.articulo.get("precio_final_dto"));
      var adicional = parseFloat(this.articulo.get("precio_final_dto_2"));
      this.$("#pedido_mesa_item_precio").val(Number(precio+adicional).toFixed(2));
      this.$("#pedido_mesa_item_tipo").val(0);
    },

    // Agrega el item a la lista
    agregar_item : function() {

      var self = this;

      var codigo = this.$("#pedido_mesa_item_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#pedido_mesa_item_articulo").focus();
        return;
      }

      var tipo = this.$("#pedido_mesa_item_tipo").val();
      var id_articulo = this.$("#pedido_mesa_item_id_articulo").val();
      var cocina = this.$("#pedido_mesa_item_no_totalizar_reparto").val();
      var descripcion = this.$("#pedido_mesa_item_descripcion").val();
      var id_rubro = (this.articulo != undefined) ? this.articulo.get("id_rubro") : 0;
      var cantidad = this.$("#pedido_mesa_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }

      var bonificacion = 0; //this.$("#pedido_mesa_item_bonificado").val();
      //var bonificacion = (this.articulo != undefined) ? this.articulo.get("porc_bonif") : 0;
      var precio = parseFloat(this.$("#pedido_mesa_item_precio").val());
      var total = precio * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":id_articulo,
        "no_totalizar_reparto":cocina,
        "precio":precio,
        "nombre":codigo,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "id_rubro":id_rubro,
        "total_con_iva":total,
        "descripcion":descripcion,
        "tipo":tipo,
      };
      
      if (this.item != undefined) {
        // Editamos un ITEM
        this.item.set(values);
      } else {
        // Agregamos un nuevo ITEM
        var item = new app.models.PedidoMesaItem(values);
        item.set({
          "orden":1,
          "tipo_cantidad":moment().format("HHmm"),
          // SON BEBIDAS, VAN A LA BARRA
          "custom_1":(((ID_EMPRESA == 162 && item.get("id_rubro")==413) || (typeof ID_CATEGORIA_BEBIDAS != "undefined" && ID_CATEGORIA_BEBIDAS == item.get("id_rubro")) )?"1":""),
          // VAN A LA COCINA
          "custom_2":((item.get("no_totalizar_reparto") == 1)?"1":""),
        });
        this.items.add(item);
      }
      
      this.item = undefined;
      this.limpiar_item();
      //this.$("#pedido_mesa_item_articulo").select();            

      var wtf    = $('#tabla_items').parent();
      var height = wtf[0].scrollHeight;
      wtf.scrollTop(height);
    },

    calcular_item: function() {
      var self = this;
      var cantidad = this.$("#pedido_mesa_item_cantidad").val();
      var precio_unit = this.$("#pedido_mesa_item_precio").val();
      var bonificado = 0; //this.$("#pedido_mesa_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
      this.$("#pedido_mesa_item_subtotal").val(subtotal);
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      var self = this;
      var view = new app.views.PedidoMesaItem({
        "model": item,
        "view":this,
        "origen":self.model.get("tipo"),
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },        

    calcular_totales : function() {

      var porc_descuento = 0; var total = 0; var costo_envio = 0;
      var descuento = 0; var subtotal = 0;
      var items = this.model.get("items");
      
      porc_descuento = parseFloat(this.$("#pedido_mesa_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      this.items.each(function(item){
        total = total + parseFloat(item.get("total_con_iva")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total_con_iva"));
      });
      
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;

      /*
      var costo_envio = parseFloat(this.$("#pedido_mesa_costo_envio").val());
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
      this.$("#pedido_mesa_total").html("$ "+Number(total).toFixed(2));
    },

    limpiar_item: function() {
      this.$("#pedido_mesa_item_id_articulo").val("0");
      this.$("#pedido_mesa_item_cantidad").val("1");
      this.$("#pedido_mesa_item_precio").val("0.00");
      this.$("#pedido_mesa_item_subtotal").val("");
      this.$("#pedido_mesa_item_articulo").val("");
      this.$("#pedido_mesa_item_descripcion").val("");
      this.$("#pedido_mesa_item_no_totalizar_reparto").val("");
      //this.$("#pedido_mesa_item_articulo").focus();
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
        this.guardar_campos();

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },	

    guardar_campos: function() {
      var self = this;
      this.model.set({
        "items":self.items.toJSON(),
        "id_usuario": ((self.$("#pedido_mesa_usuarios").length>0) ? self.$("#pedido_mesa_usuarios").val() : ID_USUARIO),
        "numero_referencia": ((self.$("#pedido_mesa_personas").length>0) ? self.$("#pedido_mesa_personas").val() : 1),
        "cliente": ((self.$("#pedido_mesa_cliente").length>0) ? self.$("#pedido_mesa_cliente").val() : ""),
        "direccion": ((self.$("#pedido_mesa_direccion").length>0) ? self.$("#pedido_mesa_direccion").val() : ""),
        "telefono": ((self.$("#pedido_mesa_telefono").length>0) ? self.$("#pedido_mesa_telefono").val() : ""),
        "observaciones": ((self.$("#pedido_mesa_observaciones").length>0) ? self.$("#pedido_mesa_observaciones").val() : ""),
      });
      if (ID_EMPRESA != 162) {
        this.model.set({
          "porc_descuento": ((self.$("#pedido_mesa_porc_descuento").length>0) ? self.$("#pedido_mesa_porc_descuento").val() : 0),
        });
      }
    },

    aceptar_pedido:function() {
      var self = this;
      if (this.validar()) {
        if (this.guardando > 0) return;
        this.guardando = 1;
        this.model.save({
          "id_tipo_estado":0,
        },{
          success: function(model,response) {
            self.guardando = 0;
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              var that = self;
              $.ajax({
                "url":"pedidos_mesas/function/aceptar_pedido/"+self.model.id,
                "dataType":"json",
                "success":function(){
                  that.cerrar_panel();
                }
              });
            }
          }
        });
      }
    },

    rechazar_pedido:function() {
      var self = this;
      if (this.validar()) {
        if (this.guardando > 0) return;
        this.guardando = 1;
        this.model.save({
          "id_tipo_estado":7,
        },{
          success: function(model,response) {
            self.guardando = 0;
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              var that = self;
              $.ajax({
                "url":"pedidos_mesas/function/rechazar_pedido/"+self.model.id,
                "dataType":"json",
                "success":function(){
                  that.cerrar_panel();
                }
              });
            }
          }
        });
      }
    },

    guardar:function() {
      var self = this;
      if (this.validar()) {

        if (this.guardando > 0) return;
        this.guardando = 1;

        if (this.model.id == null) {
          this.model.set({id:0});
        }
        // Si es una MESA
        if (this.model.get("tipo") == "M") {
          this.model.set({
            "id_tipo_estado":0, // El pedido se encuentra ABIERTO
          });

        // Si es MOSTRADOR
        } else if (this.model.get("tipo") == "T") {

          this.model.set({
          "id_tipo_estado":0, // Cerramos el pedido
        });

        // Si es DELIVERY
        } else if (this.model.get("tipo") == "D" || this.model.get("tipo") == "B") {
          var total = this.model.get("total");
          this.model.set({
            "id_tipo_estado":6, // Cerramos el pedido
            "efectivo":total, // Ponemos PAGO en EFECTIVO para que aparezca en la caja
          });
        }

        // Y GUARDAMOS
        this.model.save({},{
          success: function(model,response) {
            self.guardando = 0;
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

      var self = this;
      this.guardando = 0;

      if (this.model.get("tipo") == "M") {
        // Volvemos a buscar el salon
        $(".tab_link.active").trigger("click");                
      } else if (this.model.get("tipo") == "B") {
        this.imprimir();
        app.views.barrasTableView.buscar();
      } else {
      	app.views.deliveriesTableView.buscar();    
      	this.imprimir();
      }
      $(".modal").modal('hide');

      // Recorremos los articulos y llenamos los arrays que corresponden
      var items = this.model.get("items");
      for(var i=0;i<items.length;i++) {
        var item = items[i];
        if (item.custom_1 == 1) this.items_nuevos.push(item);
        if (item.custom_2 == 1) this.items_cocina.push(item);
      }

      if (this.items_nuevos.length > 0) {
        var pedido = {};
        pedido.empresa_nombre = NOMBRE;
        pedido.tipo = this.model.get("tipo");
        pedido.numero = self.model.get("numero");
        pedido.cliente = {
         "nombre":this.model.get("cliente"),
         "direccion":"",
         "telefono":"",
        }
        pedido.titulo = this.model.get("titulo");
        pedido.usuario = this.$("#pedido_mesa_usuarios option:selected").text();
        pedido.items = this.items_nuevos;

        var data = "pedido="+JSON.stringify(pedido);
        $.ajax({
          "url":SERVIDOR_LOCAL+"/imprimir_bebidas.php",
          "data":data,
          "dataType":"json",
          "type":"post",
        });
      }

      if (this.items_cocina.length > 0) {
        var pedido = {};
        pedido.id_empresa = ID_EMPRESA;
        pedido.empresa_nombre = NOMBRE;
        pedido.numero = self.model.get("numero");
        pedido.observaciones = self.$("#pedido_mesa_observaciones").val();
        if (ID_EMPRESA == 171 || ID_EMPRESA == 599) {
          pedido.esta_pagado = (self.$("#pedido_mesa_pagada").is(":checked") ? 1 : 0);
        }
        pedido.tipo = this.model.get("tipo");
        pedido.cliente = {
         "nombre":self.$("#pedido_mesa_cliente").val(),
         "direccion":self.$("#pedido_mesa_direccion").val(),
         "telefono":self.$("#pedido_mesa_telefono").val(),
        };
        pedido.titulo = this.model.get("titulo");
        pedido.total = this.model.get("total");
        pedido.usuario = this.$("#pedido_mesa_usuarios option:selected").text();
        pedido.items = this.items_cocina;

        var data = "pedido="+JSON.stringify(pedido);
        $.ajax({
          "url":SERVIDOR_LOCAL+"/imprimir_cocina.php",
          "data":data,
          "dataType":"json",
          "type":"post",
        });
      }
    },

    reservar:function() {
      if (!confirm("Desea reservar la mesa?")) return;
      var self = this;
      this.guardar_campos();

      if (this.guardando > 0) return;
      this.guardando = 1;

      if (this.model.id == null) {
        this.model.set({id:0});
      }
      this.model.set({
        "id_tipo_estado":1 // RESERVAMOS EL PEDIDO 
      });
      this.model.save({},{
        success: function(model,response) {
          self.guardando = 0;
          if (response.error == 1) {
            show(response.mensaje);
            return;
          } else {
            self.cerrar_panel();
          }
        }
      });
    },

    eliminar_reservar:function() {
      if (!confirm("Desea eliminar la reservar de la mesa?")) return;
      var self = this;
      if (this.guardando > 0) return;
      this.guardando = 1;
      $.ajax({
        "url":"pedidos_mesas/function/eliminar_reserva/"+self.model.id,
        "dataType":"json",
        "success":function(r) {
          self.guardando = 0;
          if (r.error == 0) self.cerrar_panel();
          else alert(r.mensaje);
        }
      })
    },

    reasignar: function() {
      var self = this;
      var v = new app.views.PedidoMesaReasignarView({
        "model": self.model,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":350,
        "height":140,
      });
    },

    unir: function() {
      var self = this;
      var v = new app.views.PedidoMesaUnirView({
        "model": self.model,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":350,
        "height":140,
      });
    },

    cerrar_mesa:function() {
      var self = this;
      if (this.validar()) {

        if (this.guardando > 0) return;
        this.guardando = 1;

        if (self.model.id == null) {
          self.model.set({id:0});
        }
        // El mozo tiene control de acceso modificable, por lo que al finalizar un pedido
        // el estado cambia a PENDIENTE DE PAGO (3)
        // En cambio el administrador lo pasa directamente a FINALIZADO (6)
        var id_tipo_estado = (control.check("pedidos_mesas") == 2) ? 3 : 6;
        this.model.set({
          "id_tipo_estado":id_tipo_estado,
        });
        if (id_tipo_estado == 6) {
          self.mostrar_form_pago();
        } else {
          this.model.save({},{
            success: function(model,response) {
              self.guardando = 0;
              if (response.error == 1) {
                show(response.mensaje);
                return;
              } else {
                self.imprimir();
                self.cerrar_panel();
              }
            }
          });
        }
      }
    },

    imprimir: function() {
      // FULARZ IMPRIME LA COMANDA
      if (ID_EMPRESA == 162 || ID_EMPRESA == 279 || ID_EMPRESA == 718 || (typeof IMPRIMIR_COMANDA != "undefined")) {
        var titulo = $("#pedido_mesa_titulo").text();
        var usuario = this.$("#pedido_mesa_usuarios option:selected").text();
        workspace.imprimir_comanda(this.model.id,titulo,usuario);
        // Imprime 2 veces
        if (ID_EMPRESA == 718 || (typeof IMPRIMIR_COMANDA_DOBLE != "undefined")) workspace.imprimir_comanda(this.model.id,titulo,usuario);
      }
    },

    mostrar_form_pago: function() {
      var self = this;
      self.model.set({
        "percepcion_ib":0,
      })
      app.views.metodoPagoView = new app.views.MetodoPagoView({
        "model": self.model,
      });
      crearLightboxHTML({
        "html":app.views.metodoPagoView.el,
        "width":600,
        "height":500,
        "callback":function() {
          self.guardando = 0;
          if (window.facturacion_guardo) {
            // El pedido ha sido guardado, cerramos el panel
            self.cerrar_panel();
          } else {
            // Volvemos a poner el pedido como que todavia no se finalizo
            // para que si el usuario hace click en "Guardar" (no en Cerrar)
            // el pedido no se cierre
            self.model.set({
              "id_tipo_estado":0,
            });
            // En FULARZ, guardamos los valores anterioes porque hace descuento
            if (ID_EMPRESA == 162) {
              self.model.set({
                "total":self.total_anterior,
                "subtotal":self.subtotal_anterior,
                "descuento":0,
                "porc_descuento":0,
              })
            }
          }                    
        }
      });
    },

  });

})(app);


(function ( app ) {
  app.views.PedidoMesaItem = app.mixins.View.extend({

    template: _.template($("#pedido_mesa_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        if (ID_EMPRESA == 171 || ID_EMPRESA == 599 || ID_EMPRESA == 718) return;
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.duplicar_articulo(this.model);
      },      
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
        return false;
      },
      "click .orden":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var o = $(e.currentTarget).text();
        o = parseInt(o)+1;
        if (o>5) o = 1;
        this.model.set({
          "orden":o,
        });
        this.render();
        return false;
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.id_tipo_estado = this.options.id_tipo_estado;
      this.origen = this.options.origen;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = { "id_tipo_estado":this.id_tipo_estado, "origen":this.origen };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);

(function ( app ) {
  app.views.PedidoMesaReasignarView = app.mixins.View.extend({
    template: _.template($("#pedido_mesa_reasignar_template").html()),
    myEvents: {
      "click .guardar":function() {
        var self = this;
        var id_mesa = self.$("#pedido_mesa_reasignar_mesas").val();
        $.ajax({
          "url":"mesas/function/reasignar/",
          "dataType":"json",
          "data":{
            "id_pedido":self.model.id,
            "id_mesa":id_mesa,
          },
          "type":"post",
          "success":function() {
            location.reload();
          }
        })
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template({}));
      new app.mixins.Select({
        modelClass: app.models.Mesa,
        url: "mesas/function/ver/",
        render: "#pedido_mesa_reasignar_mesas",
      });
      return this;
    },
  });
})(app);

(function ( app ) {
  app.views.PedidoMesaUnirView = app.mixins.View.extend({
    template: _.template($("#pedido_mesa_unir_template").html()),
    myEvents: {
      "click .guardar":function() {
        var self = this;
        var id_mesa = self.$("#pedido_mesa_unir_mesas").val();
        $.ajax({
          "url":"mesas/function/unir/",
          "dataType":"json",
          "data":{
            "id_pedido":self.model.id,
            "id_mesa":id_mesa,
          },
          "type":"post",
          "success":function() {
            location.reload();
          }
        })
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template({}));
      new app.mixins.Select({
        modelClass: app.models.Mesa,
        url: "mesas/function/ver/",
        render: "#pedido_mesa_unir_mesas",
      });
      return this;
    },
  });
})(app);