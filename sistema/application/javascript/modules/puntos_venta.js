// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PuntoVenta = Backbone.Model.extend({
    urlRoot: "puntos_venta/",
    defaults: {
      nombre: "",
      numero: 0,
      numero_fiscal: 0, 
      activo: 1,
      tipo_impresion: "",
      imp_fiscal: "",
      enviar_email: 1,
      texto_email: "",
      por_default: 0,
      id_sucursal: 0,
      tipo_uso: "", // vacio = Normal / "W" = Web
      id_caja: 0,
      direccion: "",
      localidad: "",
      
      disenio_factura: "",
      disenio_factura_color: "",
      numero_puerto: 1,
      velocidad: 9600,
      
      // Numeros y copias de comprobantes
      numero_comp_1: 0, copias_comp_1: 0,
      numero_comp_2: 0, copias_comp_2: 0,
      numero_comp_3: 0, copias_comp_3: 0,
      numero_comp_4: 0, copias_comp_4: 0,
      numero_comp_6: 0, copias_comp_6: 0,
      numero_comp_7: 0, copias_comp_7: 0,
      numero_comp_8: 0, copias_comp_8: 0,
      numero_comp_9: 0, copias_comp_9: 0,
      numero_comp_11: 0, copias_comp_11: 0,
      numero_comp_12: 0, copias_comp_12: 0,
      numero_comp_13: 0, copias_comp_13: 0,
      numero_comp_15: 0, copias_comp_15: 0,
      numero_comp_19: 0, copias_comp_19: 0,
      numero_comp_20: 0, copias_comp_20: 0,
      numero_comp_21: 0, copias_comp_21: 0,
      numero_comp_51: 0, copias_comp_51: 0,
      numero_comp_52: 0, copias_comp_52: 0,
      numero_comp_53: 0, copias_comp_53: 0,
      numero_comp_82: 0, copias_comp_82: 0,
      numero_comp_201: 0, copias_comp_201: 0,
      numero_comp_202: 0, copias_comp_202: 0,
      numero_comp_203: 0, copias_comp_203: 0,
      numero_comp_206: 0, copias_comp_206: 0,
      numero_comp_207: 0, copias_comp_207: 0,
      numero_comp_208: 0, copias_comp_208: 0,
      numero_comp_998: 0, copias_comp_998: 0,
      numero_comp_999: 0, copias_comp_999: 0,      
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.PuntosVenta = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "puntos_venta/"
		}
		
	});

})( app.collections, app.models.PuntoVenta, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PuntoVentaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#puntos_venta_item').html()),
    	events: {
  		"click": "editar",
  		"click .ver": "editar",
  		"click .delete": "borrar",
  		"click .duplicar": "duplicar"
  	},
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function()
    {
    	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
    	var obj = { permiso: this.permiso };
    	// Extendemos el objeto creado con el modelo de datos
    	$.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
    	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
    	location.href="app/#punto_venta/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();	// Eliminamos el modelo
      	$(this.el).remove();	// Lo eliminamos de la vista
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

  app.views.PuntosVentaTableView = app.mixins.View.extend({

  	template: _.template($("#puntos_venta_panel_template").html()),

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

			lista.on('add', this.addOne, this);
			lista.on('reset', this.addAll, this);
			lista.on('all', this.render, this);

			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			// Cargamos el template
			$(this.el).html(this.template(obj));
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
			var view = new app.views.PuntoVentaItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.PuntoVentaEditView = app.mixins.View.extend({

		template: _.template($("#puntos_venta_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
      "click .sincronizar": "sincronizar",
      // Si se cambia el tipo de impresion
      "change #puntos_venta_tipo_impresion": function(e) {
        var v = $("#puntos_venta_tipo_impresion").val();
        $("#puntos_venta_imp_fiscal_container").css("display",(v != "F")?"none":"block");
        $("#puntos_venta_disenio_factura_container").css("display",(v == "F")?"none":"block");
        $("#puntos_venta_disenio_factura_color_container").css("display",(v == "F")?"none":"block");
        $(".sincronizar").css("display",(v == "E")?"inline-block":"none");
      },
		},

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      this.options = options;
      _.bindAll(this);
      this.render();
    },
    
    sincronizar: function(e) {
      var punto_venta = $("#puntos_venta_numero").val();
      var id = $(e.currentTarget).attr("id").replace("sincronizar_","");
      $.ajax({
        "url":"facturas/function/sincronizar_numero/"+punto_venta+"/"+id+"/",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            show("Error al conectarse con AFIP");
          } else {
            $("#numero_comp_"+id).val(r.numero);
            $("#numero_comp_"+id).change();
            $("#numero_comp_"+id).focus();
          }
        }
      });
    },

    render: function()
    {
    	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
    	var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
    	// Extendemos el objeto creado con el modelo de datos
    	$.extend(obj,this.model.toJSON());

    	$(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("puntos_venta_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        if (this.$("#punto_venta_sucursales").length > 0) {
          this.model.set({
            "id_sucursal":self.$("#punto_venta_sucursales").val(),
          });
        }
        if (this.$("#puntos_venta_caja").length > 0) {
          this.model.set({
            "id_caja":self.$("#puntos_venta_caja").val(),
          });
        }
        this.model.set({
          "tipo_uso":self.$("#puntos_venta_tipo_uso").val(),
        })

        // No hay ningun error
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },
    

    guardar: function() 
    {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
            "id_empresa":ID_EMPRESA
          },{
          success: function(model,response) {
            window.location.reload();
          }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.PuntoVenta()
      this.render();
    },
		
	});

})(app.views, app.models);