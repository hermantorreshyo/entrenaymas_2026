// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Presupuesto = Backbone.Model.extend({
    urlRoot: "presupuestos/",
    defaults: {
      fecha: "",
      fecha_hasta: "",
      id_cliente: 0,
      id_vendedor: 0,
      id_empresa: 0,
      cliente: "Consumidor Final",
      numero: 0,
      subtotal: 0,
      comision_vendedor: 0,
      porc_descuento: 0,
      descuento: 0,
      total: 0,
      items: [],
      observaciones: "",
      enviado: 0,
      visto: 0,
      id_sucursal: 0,
      stock: 0, // Indica si se proceso el stock o no
      forma_pago: "E",
      id_tarjeta: 0,
      cuotas: 0,
      recargo: 0,
      porc_iva: 0,
      iva: 0,
      moneda: ((ID_EMPRESA == 224 || ID_EMPRESA == 1325) ? 2 : 1),
    }
  });
      
})( app.models );



(function ( models ) {

  models.PresupuestoItem = Backbone.Model.extend({
    urlRoot: "presupuestos/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      precio: 0,
      nombre: "",
      orden: 0,
      bonificacion: 0,
      total: 0,
      porc_iva: 0,
      neto: 0,
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {
  collections.Presupuestos = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },        
    paginator_core: {
      url: "presupuestos/function/consulta/",
    },
  });
})( app.collections, app.models.Presupuesto, Backbone.Paginator);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.PresupuestoEditView = app.mixins.View.extend({

    template: _.template($("#presupuestos_edit_panel_template").html()),

    myEvents: {
      "change #presupuesto_forma_pago":function() {
        this.habilitar_tarjetas();
        this.calcular_intereses();
      },
      "change #presupuesto_tarjetas":"calcular_intereses",
      "change #presupuesto_cuotas":"calcular_intereses",
      "click .aceptar": "aceptar",
      "click .anular": "anular",
      "click .imprimir": function(){
        workspace.imprimir_reporte("presupuestos/function/imprimir/"+this.model.id);
      },
      "click #presupuestos_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
            
      "change #presupuestos_lista":function(e) {
        if (this.articulo != null && this.articulo != undefined) {
          var lista = parseInt($(e.currentTarget).val());
          var valor = 0;
          if (lista == 0) {
            valor = this.model.get("precio_final");
          } else if (lista == 1) {
            valor = this.model.get("precio_final_2");
          } else if (lista == 2) {
            valor = this.model.get("precio_final_3");
          }
          $("#presupuestos_item_precio").val(valor);
          this.calcular_item();
        }
      },
      
      "change #presupuestos_item_cantidad": "calcular_item",
      "change #presupuestos_item_precio": "calcular_item",
      "change #presupuestos_item_alicuotas_iva": "calcular_item",
      "change #presupuestos_item_bonificado": "calcular_item",
      "click #presupuestos_agregar_item": "agregar_item",
      
      "click .importar_presupuesto": "ver_buscar_comprobantes",
      
      // Buscamos el cliente por codigo
      "keyup #presupuestos_codigo_cliente": function(e) {
        if (e.keyCode == 113) { this.ver_buscar_cliente(); }
      },
      "keypress #presupuestos_codigo_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); $("#presupuestos_codigo_articulo").select(); }
      },
      
      "click #presupuestos_buscar_cliente": "ver_buscar_cliente",

      "keypress #presupuestos_codigo_articulo": function(e) {
        if (e.which == 13)  {
            this.buscar_articulo();    
          }
        },
      "keyup #presupuestos_codigo_articulo": function(e) {
        if (e.which == 113) { $("#presupuestos_codigo_cliente").select(); e.preventDefault(); } // F2
        if (e.which == 45) { this.aceptar(); this.$("#presupuestos_codigo_articulo").val(""); }
      },
      "keypress #presupuestos_item_cantidad":function(e) {
        if (e.which == 13)  {
          $("#presupuestos_item_precio").select();
        }                
      },
      "click .buscar_clientes_ayuda":function() {
        var ayuda = new app.views.AyudaView({
          model: new app.models.AbstractModel()
        });
        var html = "Es posible asignar un cliente a un presupuesto de diferentes maneras: <br/>";
        html+= "<ul style='padding-left: 30px'>";
        html+= "<li>A trav&eacute;s de su c&oacute;digo interno, y luego presionar la tecla Enter.</li>";
        html+= "<li>Escribiendo parte de su nombre y seleccion&aacute;ndolo luego en la lista de sugerencias.</li>";
        html+= "<li>Si es un cliente nuevo, que a&uacute;n no esta cargado en su lista de contactos, puede escribir parte de su nombre y luego hacer click en el bot&oacute;n Nuevo que aparece en la lista de sugerencias. De esta manera podr&aacute; cargar r&aacute;pidamente un nuevo cliente sin tener que salir de la pantalla del comprobante.</li>";
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
      "keypress #presupuestos_item_precio": function(e) {
        if (e.keyCode == 13) { this.$("#presupuestos_item_bonificado").select(); }
      },
      "keypress #presupuestos_item_bonificado": function(e) {
        if (e.keyCode == 13) { this.agregar_item(); }
      },
      "change #presupuestos_porc_descuento": function() {
        this.calcular_totales();
      },
      "change #presupuestos_porc_iva": function() {
        this.calcular_totales();
      },

      "change #presupuestos_lista":function(e) {
        if (typeof this.articulo == undefined) return;
        e.stopPropagation();
        var lista = this.$("#presupuestos_lista").val();
        // Dependiendo de la lista que estamos usando
        if (lista == 0) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto"));  
        } else if (lista == 1) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto_2"));
        } else if (lista == 2) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto_3"));
        } else if (lista == 3) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto_4"));
        } else if (lista == 4) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto_5"));
        } else if (lista == 5) {
          this.$("#presupuestos_item_precio").val(this.articulo.get("precio_final_dto_6"));
        }
      }

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
        "callback":function(){
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            $("#presupuestos_codigo_cliente").val(window.codigo_cliente_seleccionado);
          }
          $("#presupuestos_codigo_cliente").select();
        }
      });
      $(".search_input").select();
    },

    habilitar_tarjetas: function() {
      var forma_pago = this.$("#presupuesto_forma_pago").val();
      if (forma_pago == "T") {
        this.$(".habilitar_tarjeta").removeAttr("disabled");
      } else {
        this.$(".habilitar_tarjeta").attr("disabled","disabled");
      }
    },
    
    ver_buscar_comprobantes: function() {
      var self = this;
      app.views.presupuestosTableView = new app.views.PresupuestosTableView({
        collection: new app.collections.Presupuestos(),
        habilitar_seleccion: true,
        parent: self,
      });
      var d = $("<div/>").append(app.views.presupuestosTableView.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
      });
    },
    
    importar: function(model) {
      var self = this;
      this.model = new app.models.Presupuesto({
        "id": model.id,
      });
      this.model.fetch({
        "success":function(){
          console.log(self.model);
          self.model.id = 0;
          self.listenTo(self.model,"change",self.render_view); // Si el modelo cambia, renderizamos la vista
          
          // Creamos una nueva coleccion de items
          var ItemsCollection = Backbone.Collection.extend({
            model: app.models.PresupuestoItem
          });
          var productos = self.model.get("items");
          self.items = new ItemsCollection();
          for(var i=0;i<productos.length;i++) {
            var p = productos[i];
            var fi = new app.models.PresupuestoItem(p);
            self.items.add(fi);
          }
          self.items.on('all', self.render_tabla_items, self);
          self.items.on('add', self.addItem, self);
          
          self.render();
          self.render_view();
          self.render_tabla_items();
        }
      });
    },
    
    buscar_cliente : function() {
      var self = this;
      
      var codigo = this.$("#presupuestos_codigo_cliente").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#presupuestos_codigo_cliente").val(codigo);
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
                self.$("#presupuestos_codigo_cliente").select();
                self.$("#presupuestos_codigo_cliente").focus();
                return;
              }
              var cliente = new app.models.Cliente(r);
              self.seleccionar_cliente(cliente);
            }
          });
        }
      }
      this.$("#presupuestos_codigo_articulo").focus();  
    },
    
    setear_consumidor_final: function() {
      var cf = new app.models.Cliente({
        "id_tipo_iva":4,
        "nombre":"Consumidor Final",
        "cuit":"",
        "email":"",
        "direccion":"",
        "localidad":"",
        "descuento":0,
        "id_vendedor":0,
        "lista":0,
      });      
      this.seleccionar_cliente(cf);
    },
    
    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      
      if (self.model.id == null) {
        // Si es un comprobante nuevo, tomamos el descuento del cliente
        self.$("#presupuestos_porc_descuento").val(self.cliente.get("descuento"));
      } else {
        // Sino tomamos el descuento del comprobante, ya que puede haber sido cambiado
        self.$("#presupuestos_porc_descuento").val(self.model.get("porc_descuento"));
      }
      
      self.render_view();
      self.$("#presupuestos_codigo_articulo").focus();

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#presupuestos_codigo_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },
    
    // Actualizamos la vista con los datos del modelo
    render_view: function() {
      
      var self = this;
      
      // Mostamos los datos del cliente
      if (self.cliente != null) {
        
        self.$("#presupuestos_id_cliente").val(self.cliente.id);
        
        var id_tipo_iva = self.cliente.get("id_tipo_iva");
        if (id_tipo_iva == 1) self.$("#presupuestos_cliente_iva").html("Responsable Inscripto");
        else if (id_tipo_iva == 2) self.$("#presupuestos_cliente_iva").html("Monotributo");
        else if (id_tipo_iva == 3) self.$("#presupuestos_cliente_iva").html("Exento");
        else if (id_tipo_iva == 4) self.$("#presupuestos_cliente_iva").html("Consumidor Final");
        self.$("#presupuestos_codigo_cliente").val(self.cliente.get("nombre"));
        self.$("#presupuestos_cliente_presupuesto").html(self.cliente.get("nombre"));
        self.$("#presupuestos_cliente_cuit").html(self.cliente.get("cuit"));
        self.$("#presupuestos_cliente_localidad").html(self.cliente.get("localidad"));
        self.$("#presupuestos_cliente_direccion").html(self.cliente.get("direccion"));
        
        self.$("#presupuestos_lista").val(self.cliente.get("lista"));
        self.$("#presupuestos_vendedores").val(self.cliente.get("id_vendedor"));
      }
      
      // Fecha
      self.$("#presupuestos_fecha_presupuesto").html(self.model.get("fecha"));
      
      self.$("#presupuestos_subtotal").val(Number(self.model.get("subtotal")).toFixed(2));
      self.$("#presupuestos_descuento").val(Number(self.model.get("descuento")).toFixed(2));
      self.$("#presupuestos_iva").val(Number(self.model.get("iva")).toFixed(2));
      self.$("#presupuestos_total").val(Number(self.model.get("total")).toFixed(2));
    },
    
    buscar_articulo : function() {
      
      var self = this;
      var codigo = $("#presupuestos_codigo_articulo").val();
      var lista_precios = $("#presupuestos_lista").val();
      codigo = codigo.trim();
      
      if (codigo == FACTURACION_CODIGO_FINALIZAR) {
        this.aceptar(); return;
      }
        
      // Si el codigo del articulo esta vacio, simplemente saltamos a que escriba la descripcion
      if (isEmpty(codigo)) {
        //$("#presupuestos_item_descripcion").select();
        return;
      }        

      if (MEGASHOP == 1 || ID_EMPRESA == 421) {
        try {
          codigo = String(codigo).replace("]C1","");
          // Si comienza con un numero, parseamos a entero
          if ($.isNumeric(codigo.substr(0,1))) {

            // Si comienza con 0000 y tiene 13 digitos, tenemos que sacar el ultimo
            if (codigo.substr(0,4) == "0000" && codigo.length == 13) {
              codigo = codigo.substr(0,12);
            }

            var codigo_ant = parseFloat(codigo);
            codigo = codigo_ant;
          }
        } catch(e){
          console.log("Error al convertir INT el codigo: '"+codigo+"'.");
        }
      }

      codigo = encodeURIComponent(codigo);
      $.ajax({
        "url":"articulos/function/get_by_codigo/"+codigo,
        "dataType":"json",
        "type":"post",
        "data":{
          "id_sucursal":((self.$("#presupuestos_sucursales").length > 0) ? self.$("#presupuestos_sucursales").val() : 0),
          "lista_precios":lista_precios,
        },
        "success":function(result) {
          if (result.error == 1) {
            self.articulo = null;
            alert("No se encuentra el articulo con codigo '"+codigo+"'.");
            self.$("#presupuestos_codigo_articulo").select();
          } else {
            var art = new app.models.Articulo(result.articulo);
            self.seleccionar_articulo(art);
          }
        }
      });
    },
    
    seleccionar_articulo : function(r) {
      var self = this;

      // MEGASHOP, CLIENTE TORANCIO GRACIELA TIENE EL COSTO + 15%
      if (MEGASHOP == 1 && typeof self.cliente != undefined && typeof self.cliente.id != undefined && 
        (self.cliente.id == 6008066 || self.cliente.id == 6008067 || self.cliente.id == 1005770)) {
        r.set({
          "precio_neto":Number(r.get("costo_neto") * 1.15).toFixed(2),
          "precio_final":Number(r.get("costo_final") * 1.15).toFixed(2),
          "precio_final_dto":Number(r.get("costo_final") * 1.15).toFixed(2),
        });
      }

      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#presupuestos_item_cantidad").select();
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#presupuestos_codigo_articulo').trigger(jQuery.Event('keyup', {which: 27}));
      },1000);
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#presupuestos_codigo_articulo").val(this.item.get("nombre"));
      $("#presupuestos_item_cantidad").val(this.item.get("cantidad"));
      $("#presupuestos_tipo_item").val(this.item.get("tipo"));
      $("#presupuestos_item_precio").val(this.item.get("precio"));
      $("#presupuestos_item_bonificado").val(this.item.get("bonificacion"));
      $("#presupuestos_item_neto").val(this.item.get("neto"));
      $("#presupuestos_item_alicuotas_iva").val(this.item.get("porc_iva"));
      self.calcular_item();
      this.$("#presupuestos_item_cantidad").select();      
    },
    
    ver_buscar_articulo : function() {
      if (typeof FACTURACION_OCULTAR_BUSCADOR != "undefined" && FACTURACION_OCULTAR_BUSCADOR == 1) return;
      var self = this;
      window.articulos_buscar_activo = 1;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: ((CACHE_ARTICULOS == 1) ? articulos : new app.collections.Articulos()),
        habilitar_seleccion: true,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":((ID_EMPRESA == 342)?1100:860),
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            $("#presupuestos_codigo_articulo").val($("#presupuestos_codigo_articulo").val()+window.codigo_articulo_seleccionado);
            self.buscar_articulo();
            $("#presupuestos_codigo_articulo").focus();
          } else {
            $("#presupuestos_codigo_articulo").focus();
          }       
        }
      });
      $("#articulos_texto").select();
    },
    
    mostrar_articulo : function() {
      if (typeof this.articulo == undefined) return;
      this.$("#presupuestos_codigo_articulo").val(this.articulo.get("nombre"));

      var lista = this.$("#presupuestos_lista").val();
      var precio = 0;
      // Dependiendo de la lista que estamos usando
      if (lista == 0) {
        precio = this.articulo.get("precio_final_dto");  
      } else if (lista == 1) {
        precio =  this.articulo.get("precio_final_dto_2");
      } else if (lista == 2) {
        precio =  this.articulo.get("precio_final_dto_3");
      } else if (lista == 3) {
        precio =  this.articulo.get("precio_final_dto_4");
      } else if (lista == 4) {
        precio =  this.articulo.get("precio_final_dto_5");
      } else if (lista == 5) {
        precio =  this.articulo.get("precio_final_dto_6");
      }

      // Dependiendo de la moneda que estamos mostrando el presupuesto
      var moneda = this.$("#presupuesto_monedas").val();
      if (this.articulo.get("moneda") == 2 && moneda == 1) {
        // El articulo esta en USD, y el presupuesto esta en ARS
        precio = Number(precio * COTIZACION_DOLAR).toFixed(2);
      } else if (this.articulo.get("moneda") == 1 && moneda == 2 && COTIZACION_DOLAR != 0) {
        // El articulo esta en ARS, pero el presupuesto esta en USD
        precio = Number(precio / COTIZACION_DOLAR).toFixed(2);
      }
      this.$("#presupuestos_item_precio").val(precio);
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      
      var self = this;
      
      var codigo = this.$("#presupuestos_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#presupuestos_codigo_articulo").focus();
        return;
      }
      
      var id_articulo = (this.articulo != undefined) ? this.articulo.id : 0;
      var codigo_real = (this.articulo != undefined) ? this.articulo.get("codigo") : "";
      var cantidad = this.$("#presupuestos_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }
      
      var bonificacion = this.$("#presupuestos_item_bonificado").val();
      
      // El precio que figura es el FINAL
      var precio = parseFloat(this.$("#presupuestos_item_precio").val());      

      // Si tiene algun tipo de alicuota de IVA
      var porc_iva = parseFloat(this.$("#presupuestos_item_alicuotas_iva").val());
      var neto = precio;
      if (porc_iva > 0) neto = (precio * ((100-bonificacion)/100)) / (1+(porc_iva/100));  

      var total = precio * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":id_articulo,
        "precio":precio,
        "neto":neto,
        "porc_iva":porc_iva,
        "codigo":codigo_real,
        "nombre":codigo,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "total":total,
      };
      
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.PresupuestoItem(values);
        this.items.add(item);
      }
      
      this.item = undefined;
      this.limpiar_item();
      this.$("#presupuestos_codigo_articulo").select();        
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#presupuestos_item_cantidad").val();
      var precio_unit = this.$("#presupuestos_item_precio").val();
      var bonificado = this.$("#presupuestos_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
      this.$("#presupuestos_item_subtotal").val(subtotal);
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
          model: app.models.PresupuestoItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.PresupuestoItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);        
        
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
        
        this.render_tabla_items();
        $("#tabla_items tbody").empty();
      }
      this.habilitar_tarjetas();
    },

    calcular_intereses: function() {
      var self = this;
      var id_tarjeta = this.$("#presupuesto_tarjetas").val();
      var cuotas = this.$("#presupuesto_cuotas").val()
      $.ajax({
        "url":"tarjetas/function/calcular_intereses/"+id_tarjeta+"/"+cuotas,
        "dataType":"json",
        "success":function(r) {
          var importe = parseFloat($("#presupuestos_total").val());
          var importe_con_interes = Number(importe * r.interes).toFixed(2);
          var interes = Number(importe_con_interes - importe).toFixed(2);
          $("#presupuesto_recargo").val(interes);
        }
      });
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
      createdatepicker(this.$("#presupuestos_fecha"),this.model.get("fecha"));

      if (isEmpty(this.model.get("fecha_hasta"))) {
        this.model.set("fecha_hasta",moment().add(1,'months').format("DD/MM/YYYY"));
      }    
      createdatepicker(this.$("#presupuestos_fecha_hasta"),this.model.get("fecha_hasta"));
    
      this.limpiar_item();
      
      if (control.check("vendedores")>0) {
        new app.mixins.Select({
          modelClass: app.models.Vendedor,
          url: "vendedores/",
          render: "#presupuestos_vendedores",
          name : "id_vendedor",
          firstOptions: ["<option value='0'>Vendedor</option>"],
          selected: this.model.get("id_vendedor"),
        });        
      }
      
      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#presupuestos_codigo_cliente");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });      
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });
      
      var input = this.$("#presupuestos_codigo_articulo");
      $(input).customcomplete({
        "url":"articulos/function/get_by_descripcion/",
        "hideNoResults":true,
        "onSelect":function(item){
          var that = self;
          var codigo = encodeURIComponent(item.codigo);
          $.ajax({
            "url":"articulos/function/get_by_codigo/"+codigo+((that.$("#presupuestos_sucursales").length > 0 ? ("?id_sucursal="+that.$("#presupuestos_sucursales").val()) : "")),
            "dataType":"json",
            "success":function(res) {
              if (res.error == 0) {
                var art = new app.models.Articulo(res.articulo);
                that.seleccionar_articulo(art);
              }
            }
          });
        }
      });
      return this;
    },
    
    calcular_totales : function() {
      
      var porc_descuento = 0; var total = 0;
      var descuento = 0; var subtotal = 0;
      var items = this.model.get("items");
      
      var porc_descuento = parseFloat(this.$("#presupuestos_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      this.items.each(function(item){
        total = total + parseFloat(item.get("total")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total"));
      });
      
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;
      
      var porc_iva = parseFloat(this.$("#presupuestos_porc_iva").val());
      var iva = subtotal * parseFloat(porc_iva / 100);
      if (isNaN(iva)) iva = 0;
      total = total + iva;

      this.model.set({
        "porc_iva":porc_iva,
        "iva":iva,
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "subtotal":subtotal,
        "total":total,
      });

      this.calcular_intereses();
    },
    
    limpiar_item: function() {
      this.$("#presupuestos_item_cantidad").val("1");
      this.$("#presupuestos_item_bonificado").val("0");
      this.$("#presupuestos_item_precio").val("0.00");
      this.$("#presupuestos_item_subtotal").val("");
      this.$("#presupuestos_codigo_articulo").val("");
      this.$("#presupuestos_codigo_articulo").focus();
    },
    
    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },
    
    addItem : function ( item ) {
      var view = new app.views.PresupuestoItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },    
    
    validar: function() {
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al presupuesto antes de guardar.";
      }
    },
    
    anular: function() {
      if (confirmar("Desea limpiar el presupuesto?")) {
        this.limpiar();
        $("#presupuestos_codigo_articulo").focus();
      }        
    },
  
    limpiar : function() {
    
      // Guardamos las variables antes de renderizar
      var lista = $("#presupuestos_lista").val();
      var id_vendedor = this.model.get("id_vendedor");
      var id_cliente = this.model.get("id_cliente");
      
      this.model = new app.models.Presupuesto({
        "id_cliente":id_cliente,
        "id_vendedor":id_vendedor,
      });
      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.PresupuestoItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
      this.buscar_cliente();
      $("#presupuestos_lista").val(lista);
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
        this.model.set({id:0});
      }

      var forma_pago = self.$("#presupuesto_forma_pago").val();

      this.model.set({
        "items":self.items.toJSON(),
        "fecha":self.$("#presupuestos_fecha").val(),
        "forma_pago":forma_pago,
        "moneda":(self.$("#presupuesto_monedas").length > 0 ? self.$("#presupuesto_monedas").val() : 1),
        "recargo":((forma_pago == "T") ? self.$("#presupuesto_recargo").val() : 0),
        "cuotas":((forma_pago == "T") ? self.$("#presupuesto_cuotas").val() : 0),
        "id_tarjeta":((forma_pago == "T") ? self.$("#presupuesto_tarjetas").val() : 0),
        "fecha_hasta":self.$("#presupuestos_fecha_hasta").val(),
        "observaciones":self.$("#presupuestos_observaciones").val(),
        "id_empresa": ID_EMPRESA,
        "id_cliente":self.$("#presupuestos_id_cliente").val(),
        "id_vendedor":(control.check("vendedores")>0)?self.$("#presupuestos_vendedores").val():0,
        "id_sucursal":((self.$("#presupuestos_sucursales").length > 0) ? self.$("#presupuestos_sucursales").val() : 0),
      });
      this.model.save({},{
        success: function(model,response) {
          self.guardando = 0; // Habilitamos el boton
          if (response.id != undefined) {
            self.model.id = response.id;
          }
          location.href="app/#presupuestos";
        },
      });           
    },
  
  });

})(app.views, app.models);



(function ( app ) {
  app.views.PresupuestoItem = app.mixins.View.extend({
    
    template: _.template($("#presupuesto_item_template").html()),
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

  app.views.PresupuestosTableView = app.mixins.View.extend({

    template: _.template($("#presupuestos_resultados_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar",
      "change .buscar":"buscar",
      "click .buscar-btn":"buscar",
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
    
      this.collection.on('sync', this.addAll, this);

      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
      
      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        render: "#presupuestos_listado_cliente",
        firstOptions: ["<option value='0'>Clientes</option>"],
      });
      
      if (control.check("vendedores")>0) {
        new app.mixins.Select({
          modelClass: app.models.Vendedor,
          url: "vendedores/",
          render: "#presupuestos_listado_vendedor",
          firstOptions: ["<option value='0'>Vendedores</option>"],
        });        
      }
    
      createdatepicker(this.$("#presupuestos_desde"));
      createdatepicker(this.$("#presupuestos_hasta"));
      
      this.buscar();
    },
    
    exportar_csv: function(obj) {
      var desde = $("#presupuestos_desde").val();
      desde = desde.replace(/\//g,"-");
      var hasta = $("#presupuestos_hasta").val();
      hasta = hasta.replace(/\//g,"-");
      window.open("presupuestos/function/exportar_csv/"+desde+"/"+hasta,"_blank");
    },
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"presupuestos"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },
    
    buscar: function() {
      var self = this;
      var filtros = {};
      
      /*
      if (!isEmpty(this.$("#presupuestos_listado_cliente").val())) 
        filtros.id_cliente = this.$("#presupuestos_listado_cliente").val();
      if (!isEmpty(this.$("#presupuestos_listado_vendedor").val())) 
        filtros.id_vendedor = this.$("#presupuestos_listado_vendedor").val();
      if (!isEmpty(this.$("#presupuestos_listado_numero").val())) 
        filtros.numero = this.$("#presupuestos_listado_numero").val();
      */
      var fecha_desde = this.$("#presupuestos_desde").val();
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) filtros.desde = fecha_desde;
      
      var fecha_hasta = this.$("#presupuestos_hasta").val();
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) filtros.hasta = fecha_hasta;

      filtros.id_sucursal = ID_SUCURSAL;
      
      this.collection.server_api = filtros;
      this.collection.pager();      
    },
  
    exportar : function() {
    
      var self = this;
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });
      // Acomodamos los datos
      var array = new Array();
      _.each(self.collection.models,function(m){
        array.push({
          "fecha": m.get("fecha"),
          "cliente": (isEmpty(m.get("cliente"))?"Consumidor Final":m.get("cliente"))+(m.get("anulada") == 1?"(ANULADA)":""),
          "vendedor":m.get("vendedor"),
          "total":Number(m.get("total")).toFixed(2),
        });
      });
      this.exportar_excel({
        "filename":"presupuestos",
        "title":"Listado de Presupuestos",
        "date":$("#presupuestos_desde").val()+" - "+$("#presupuestos_hasta").val(),
        "data":array,
        "header":header,
      });    
    },
    
    addAll : function () {
      this.$("#presupuestos_tabla tbody tr").empty();
      this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.PresupuestosItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#presupuestos_tabla tbody").append(view.render().el);
    },
  
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.PresupuestosItemResultados = app.mixins.View.extend({
    
    template: _.template($("#presupuestos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .delete":"borrar",
      "click .imprimir":"imprimir",
      "click .procesar_stock":"procesar_stock",
    },
    seleccionar : function(e) {
      if (this.options.seleccionar && this.parent != undefined) {
        $('.modal:last').modal('hide');
        this.parent.importar_presupuesto(this.model);
      } else location.href="app/#presupuesto/"+this.model.id;
    },
    borrar : function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (confirmar("Realmente desea eliminar este presupuesto?")) {
        $.ajax({
          "url":"presupuestos/function/delete/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.presupuestosTableView.buscar();
          }
        });        
      }
    },
    procesar_stock: function(e) {
      var self = this;
      e.preventDefault();
      e.stopPropagation();
      if (confirmar("Desea pasar los items del presupuesto al stock?")) {
        $.ajax({
          "url":"presupuestos/function/procesar_stock/"+self.model.id+"/"+self.model.get("id_sucursal"),
          "dataType":"json",
          "success":function(r){
            if (r.error == 1) alert("Ocurrio un error al procesar el stock");
            app.views.presupuestosTableView.buscar();
          }
        });        
      }
    },
    imprimir: function() {
      workspace.imprimir_reporte("presupuestos/function/imprimir/"+this.model.id);
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