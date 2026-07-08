// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Zeta = Backbone.Model.extend({
    urlRoot: "zetas/",
    defaults: {
      id_punto_venta: 0,
      punto_venta: "",
      id_empresa: ID_EMPRESA,
      numero: "",
      fecha: "",
      comp_desde: "",
      comp_hasta: "",
      neto: 0,
      iva: 0,
      total: 0,
      neto_105: 0,
      iva_105: 0,
      neto_0: 0,
      id_tipo_comprobante: 82,
      id_cliente: 0,
      cliente: "",
      anulada: 0,
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Zetas = paginator.requestPager.extend({
		model: model,
    paginator_ui: {
      perPage: 100,
    },
		paginator_core: {
			url: "zetas/function/buscar/"
		}
	});

})( app.collections, app.models.Zeta, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ZetaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#zetas_item').html()),
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
    	location.href="app/#zeta/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();	// Eliminamos el modelo
      	$(this.el).remove();	// Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.ZetasTableView = app.mixins.View.extend({

  	template: _.template($("#zetas_panel_template").html()),

    myEvents: {
      "click .imprimir": "imprimir",
      "click .citi": "citi",
      "click .exportar_excel": "exportar_excel",
      "click .buscar":"buscar",
    },

		initialize : function (options) {
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
			this.permiso = this.options.permiso;

			var pagination = new app.mixins.PaginationView({
				collection: self.collection
			});

			var search = new app.mixins.SearchView({
				collection: self.collection
			});

      this.collection.on('sync', this.addAll, this);
			
      window.zetas_page = (typeof window.zetas_page != "undefined") ? window.zetas_page : 1;
      window.zetas_fecha_desde = (typeof window.zetas_fecha_desde != "undefined") ? window.zetas_fecha_desde : "";
      window.zetas_fecha_hasta = (typeof window.zetas_fecha_hasta != "undefined") ? window.zetas_fecha_hasta : "";
      window.zetas_id_sucursal = (typeof window.zetas_id_sucursal != "undefined") ? window.zetas_id_sucursal : 0;
      window.zetas_id_razon_social = (typeof window.zetas_id_razon_social != "undefined") ? window.zetas_id_razon_social : 0;
      window.zetas_id_punto_venta = (typeof window.zetas_id_punto_venta != "undefined") ? window.zetas_id_punto_venta : 0;
      var obj = { permiso: this.permiso };
			$(this.el).html(this.template(obj));
			$(this.el).find(".pagination_container").html(pagination.el);
			$(this.el).find(".search_container").html(search.el);
      createdatepicker($(this.el).find("#zetas_fecha_desde"));
      createdatepicker($(this.el).find("#zetas_fecha_hasta"));

      this.buscar();
		},

    buscar : function() {
      var self = this;
      var cambio_parametros = false;
      if (this.$("#zetas_sucursales").length > 0) {
        if (window.zetas_id_sucursal != this.$("#zetas_sucursales").val()) {
          window.zetas_id_sucursal = this.$("#zetas_sucursales").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#zetas_razones_sociales").length > 0) {
        if (window.zetas_id_razon_social != this.$("#zetas_razones_sociales").val()) {
          window.zetas_id_razon_social = this.$("#zetas_razones_sociales").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#zetas_puntos_venta").length > 0) {
        if (window.zetas_id_punto_venta != this.$("#zetas_puntos_venta").val()) {
          window.zetas_id_punto_venta = this.$("#zetas_puntos_venta").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#zetas_fecha_desde").length > 0) {
        if (window.zetas_fecha_desde != this.$("#zetas_fecha_desde").val().trim()) {
          window.zetas_fecha_desde = this.$("#zetas_fecha_desde").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#zetas_fecha_hasta").length > 0) {
        if (window.zetas_fecha_hasta != this.$("#zetas_fecha_hasta").val().trim()) {
          window.zetas_fecha_hasta = this.$("#zetas_fecha_hasta").val().trim();
          cambio_parametros = true;
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.zetas_page = 1;
      this.collection.server_api = {
        "id_sucursal":window.zetas_id_sucursal,
        "id_razon_social":window.zetas_id_razon_social,
        "id_punto_venta":window.zetas_id_punto_venta,
        "fecha_desde":window.zetas_fecha_desde.replace(/\//g,"-"),
        "fecha_hasta":window.zetas_fecha_hasta.replace(/\//g,"-"),
      }
      this.collection.goTo(window.zetas_page);
    },  

		addAll : function () {
			$(this.el).find("tbody").empty();
      this.neto = 0;
      this.iva = 0;
      this.total = 0;
			this.collection.each(this.addOne);
      $(this.el).find("#zetas_panel_neto").html("$ "+Number(this.neto).toFixed(2));
      $(this.el).find("#zetas_panel_iva").html("$ "+Number(this.iva).toFixed(2));
      $(this.el).find("#zetas_panel_total").html("$ "+Number(this.total).toFixed(2));
		},

		addOne : function ( item ) {
      this.neto += ((item.get("anulada") == 0) ? parseFloat(item.get("neto")) : 0);
      this.iva += ((item.get("anulada") == 0) ? parseFloat(item.get("iva")) : 0);
      this.total += ((item.get("anulada") == 0) ? parseFloat(item.get("total")) : 0);
			var view = new app.views.ZetaItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		},

    imprimir: function() {
      var fecha_desde = $(this.el).find("#zetas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#zetas_fecha_hasta").val().replace(/\//g,"-");
      var id_sucursal = (this.$("#zetas_sucursales").length > 0) ? this.$("#zetas_sucursales").val() : 0;
      var id_razon_social = (this.$("#zetas_razones_sociales").length > 0) ? this.$("#zetas_razones_sociales").val() : 0;
      var id_punto_venta = (this.$("#zetas_puntos_venta").length > 0) ? this.$("#zetas_puntos_venta").val() : 0;
      var desde = $(this.el).find("#zetas_desde").val();
      desde = (isEmpty(desde) ? 0 : desde);
      workspace.imprimir_reporte("iva/function/ventas/"+fecha_desde+"/"+fecha_hasta+"/"+desde+"/"+id_razon_social+"/?id_punto_venta="+id_punto_venta);
    },
    
    citi: function() {
      var fecha_desde = $(this.el).find("#zetas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#zetas_fecha_hasta").val().replace(/\//g,"-");
      var id_sucursal = (this.$("#zetas_sucursales").length > 0) ? this.$("#zetas_sucursales").val() : 0;
      var id_razon_social = (this.$("#zetas_razones_sociales").length > 0) ? this.$("#zetas_razones_sociales").val() : 0;
      var id_punto_venta = (this.$("#zetas_puntos_venta").length > 0) ? this.$("#zetas_puntos_venta").val() : 0;
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/cbte/"+id_razon_social+"/?id_punto_venta="+id_punto_venta,"_blank");
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/alicuotas/"+id_razon_social+"/?id_punto_venta="+id_punto_venta,"_blank");
    }, 

    exportar_excel: function() {
      var fecha_desde = $(this.el).find("#zetas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#zetas_fecha_hasta").val().replace(/\//g,"-");
      var id_sucursal = (this.$("#zetas_sucursales").length > 0) ? this.$("#zetas_sucursales").val() : 0;
      var id_razon_social = (this.$("#zetas_razones_sociales").length > 0) ? this.$("#zetas_razones_sociales").val() : 0;
      var id_punto_venta = (this.$("#zetas_puntos_venta").length > 0) ? this.$("#zetas_puntos_venta").val() : 0;
      var url = "zetas/function/exportar/?";
      url+="fecha_desde="+fecha_desde;
      url+="&fecha_hasta="+fecha_hasta;
      url+="&id_sucursal="+id_sucursal;
      url+="&id_razon_social="+id_razon_social;
      url+="&id_punto_venta="+id_punto_venta;
      url+="&limit=0";
      url+="&offset=9999999";
      window.open(url,"_blank");
    }, 

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.ZetaEditView = app.mixins.View.extend({

		template: _.template($("#zetas_edit_panel_template").html()),

		myEvents: {
      "click #zetas_buscar_cliente": "ver_buscar_cliente",
      "click #zetas_nuevo_cliente":"nuevo_cliente",
      "keypress #zetas_codigo_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); }
      },
      "focusout #zetas_codigo_cliente":function(e){
        if (typeof this.cliente != "undefined") {
          var nombre = this.cliente.get("nombre");
          var texto = $(e.currentTarget).val();
          if (nombre != texto) {
          // Blanqueamos el cliente para que no haya confusion
          $(e.currentTarget).val("");
          }
        }
      },
      "click #zetas_buscar_cliente": "ver_buscar_cliente",

			"click .guardar": "guardar",
      "change #zetas_punto_venta":function() {
        if (MEGASHOP == 1 || ID_EMPRESA == 421) this.buscar_proximo();
      },
      "change #zetas_comprobantes":function() {
        this.buscar_proximo();
      },
      "keypress #zetas_punto_venta":function(e) {
        if (e.which == 13) { this.$("#zetas_fecha").focus(); }
      },
      "keypress #zetas_fecha":function(e) {
        if (e.which == 13) { this.$("#zetas_numero").select(); }
      },
      "keypress #zetas_numero":function(e) {
        if (e.which == 13) { 
          if (MEGASHOP == 1 || ID_EMPRESA == 421) {
            this.$("#zetas_total").select(); 
          } else {
            this.$("#zetas_comp_desde").select(); 
          }
        }
      },
      "keypress #zetas_comp_desde":function(e) {
        if (e.which == 13) { this.$("#zetas_comp_hasta").select(); }
      },
      "keypress #zetas_comp_hasta":function(e) {
        if (e.which == 13) { this.$("#zetas_neto").select(); }
      },
      "keypress #zetas_neto":function(e) {
        if (e.which == 13) {
          var neto = parseFloat($(e.currentTarget).val());
          var id_tipo_comprobante = this.$("#zetas_comprobantes").val();
          if (ID_SUCURSAL == 23) { //|| id_tipo_comprobante == 6 || id_tipo_comprobante == 7 || id_tipo_comprobante == 8) {
            var iva = 0;
            var total = neto;
          } else {
            var iva = (neto * 0.21);
            var total = (neto * 1.21);
          }
          this.$("#zetas_iva").val(Number(iva).toFixed(2));
          this.$("#zetas_total").val(Number(total).toFixed(2));
          this.$("#zetas_iva").select();
        }
      },
      "keypress #zetas_neto_105":function(e) {
        if (e.which == 13) {
          var neto = parseFloat($(e.currentTarget).val());
          var iva = (neto * 0.105);
          this.$("#zetas_iva_105").val(Number(iva).toFixed(2));
          this.calcular_total();
          this.$("#zetas_neto_0").select();
        }
      },      
      "keypress #zetas_neto_0":function(e) {
        if (e.which == 13) {
          this.calcular_total();
        }
      },      
      "keypress #zetas_iva":function(e) {
        if (e.which == 13) { this.$("#zetas_total").select(); }
      },
      "keypress #zetas_total":function(e) {
        if (e.which == 13) {
          var total = parseFloat($(e.currentTarget).val());
          var id_tipo_comprobante = this.$("#zetas_comprobantes").val();
          if (ID_SUCURSAL == 23) { //|| id_tipo_comprobante == 6 || id_tipo_comprobante == 7 || id_tipo_comprobante == 8) {
            var iva = 0;
            var neto = total;
          } else {
            var neto = (total / 1.21);
            var iva = (neto * 0.21);
          }
          this.$("#zetas_iva").val(Number(iva).toFixed(2));
          this.$("#zetas_neto").val(Number(neto).toFixed(2));
          this.$(".guardar").focus();
        }
      },
		},

    calcular_total: function() {
      var total = 0;
      total += parseFloat(this.$("#zetas_neto").val());
      total += parseFloat(this.$("#zetas_neto_105").val());
      total += parseFloat(this.$("#zetas_neto_0").val());
      total += parseFloat(this.$("#zetas_iva").val());
      total += parseFloat(this.$("#zetas_iva_105").val());
      this.$("#zetas_total").val(Number(total).toFixed(2));
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    buscar_proximo: function() {
      var self = this;
      var id_punto_venta = this.$("#zetas_punto_venta").val();
      var id_tipo_comprobante = (this.$("#zetas_comprobantes").length > 0) ? this.$("#zetas_comprobantes").val() : 82;
      $.ajax({
        "url":"zetas/function/next/"+id_punto_venta+"/"+id_tipo_comprobante,
        "dataType":"json",
        "success":function(r) {
          self.$("#zetas_numero").val(r.proximo);
        }
      });
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

      var fecha = (isEmpty(this.model.get("fecha"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha");
      createdatepicker(this.$("#zetas_fecha"),fecha);

      if (MEGASHOP == 1 || ID_EMPRESA == 421) {

        // AUTOCOMPLETE DE CLIENTES
        // ------------------------
        var input = this.$("#zetas_codigo_cliente");
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

        if (typeof this.model.id == "undefined") this.buscar_proximo();
      }

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("zetas_numero",IS_EMPTY,"Por favor, ingrese un numero.");

        // Si es una Factura, tiene que tener si o si CLIENTE
        var id_tipo_comprobante = ((self.$("#zetas_comprobantes").length > 0) ? self.$("#zetas_comprobantes").val() : 0);
        var id_cliente = ((self.$("#zetas_id_cliente").length > 0) ? self.$("#zetas_id_cliente").val() : 0);
        if (!(id_tipo_comprobante == 82 || id_tipo_comprobante == 6) && id_cliente == 0) {
          alert("Por favor seleccione un cliente.");
          return false;
        }

        // No hay ningun error
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
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
      var codigo = this.$("#zetas_codigo_cliente").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#zetas_codigo_cliente").val(codigo);
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
              self.$("#zetas_codigo_cliente").select();
              self.$("#zetas_codigo_cliente").focus();
              return;
            }
            var cliente = new app.models.Cliente(r);
            self.seleccionar_cliente(cliente);
          }
        });
      }
      }
      this.$("#zetas_codigo_articulo").focus();  
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
      self.$("#zetas_codigo_cliente").val(r.get("nombre"));
      self.$("#zetas_id_cliente").val(r.id);
      setTimeout(function(){
        self.$('#zetas_codigo_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }

        if (MEGASHOP == 1 || ID_EMPRESA == 421) {
          this.model.set({
            "comp_desde":self.$("#zetas_numero").val(),
            "comp_hasta":self.$("#zetas_numero").val(),
          });
        }

        this.model.save({
            "id_empresa":ID_EMPRESA,
            "id_cliente":((self.$("#zetas_id_cliente").length > 0) ? self.$("#zetas_id_cliente").val() : 0),
            "id_tipo_comprobante":((self.$("#zetas_comprobantes").length > 0) ? self.$("#zetas_comprobantes").val() : 0),
            "id_punto_venta":self.$("#zetas_punto_venta").val(),
            "punto_venta":self.$("#zetas_punto_venta option:selected").data("numero_fiscal"),
            "anulada":(self.$("#zetas_anulada").is(":checked")?1:0),
            "numero":self.$("#zetas_numero").val(),
            "fecha":self.$("#zetas_fecha").val(),
            "neto":self.$("#zetas_neto").val(),
            "neto_105":self.$("#zetas_neto_105").val(),
            "neto_0":self.$("#zetas_neto_0").val(),
            "iva":self.$("#zetas_iva").val(),
            "iva_105":self.$("#zetas_iva_105").val(),
            "total":self.$("#zetas_total").val(),
          },{
          success: function(model,response) {
            if (MEGASHOP == 1 || ID_EMPRESA == 421) location.reload();
            var id_punto_venta = self.model.get("id_punto_venta");
            var numero = parseInt(self.model.get("numero"))+1;
            var comp_desde = parseInt(self.model.get("comp_hasta"))+1;
            self.model = new app.models.Zeta({
              "id_punto_venta":id_punto_venta,
              "numero":numero,
            });
            self.render();
          }
        });
      }
		},
		
	});

})(app.views, app.models);