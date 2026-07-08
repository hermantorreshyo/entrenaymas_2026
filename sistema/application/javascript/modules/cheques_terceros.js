// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ChequeTercero = Backbone.Model.extend({
        urlRoot: "cheques_terceros/",
        defaults: {
            id:0,
            id_banco: 0,
            id_orden_pago: 0,
            banco: "",
            numero: "",
            fecha_emision: "",
            fecha_cobro: "",
            fecha_debitado: "",
            cliente: "",
            id_cliente: 0,
            monto: 0,
            sucursal: "",
            titular: "",
            motivo: "",
            devuelto: 0,
            entregado: 0,
            cuit_titular: "",
            id_empresa: ID_EMPRESA,
        }
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.ChequesTerceros = paginator.requestPager.extend({

        model: model,
    
        paginator_core: {
            url: "cheques_terceros/"
        }
	    
    });

})( app.collections, app.models.ChequeTercero, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

    collections.ChequesTercerosList = Backbone.Collection.extend({
        model: model,
        url: "cheques_terceros/",
        initialize: function() {
            this.fetch();
        },
        parse: function(response) {
            return response.results;
        }
    });

})( app.collections, app.models.ChequeTercero);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ChequeTerceroItem = app.mixins.View.extend({
        tagName: "tr",
        template: _.template($('#cheques_terceros_item').html()),
      	myEvents: {
			"click .edit": "editar",
			"click .ver": "editar",
			"click .delete": "borrar",
    	},
        initialize: function(options) {
            // Si el modelo cambia, debemos renderizar devuelta el elemento
            this.options = options;
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
            this.permiso = this.options.permiso;
            _.bindAll(this);
        },
        render: function() {
			// Creamos un objeto para agregarle las otras propiedades que no son el modelo
			var obj = {
				permiso: this.permiso,
				lightbox: this.lightbox				
			};
			// Extendemos el objeto creado con el modelo de datos
			$.extend(obj,this.model.toJSON());

            $(this.el).html(this.template(obj));
            return this;
        },
        editar: function() {
			if (this.lightbox == false) {
				// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
				location.href="app/#cheque_tercero/"+this.model.id;
			} else {
				// Debemos seleccionar un elemento cuando se hace click
				window.cheque_tercero = this.model;
				$(".modal").last().trigger("click");
			}
        },
        borrar: function() {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
        },
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

app.views.ChequesTercerosTableView = app.mixins.View.extend({

    template: _.template($("#cheques_terceros_panel_template").html()),

	myEvents : {
	    "click .buscar" : "buscar",
	    "keypress #cheques_terceros_texto": "searchOnEnter",
	},

	initialize : function (options) {

	    _.bindAll(this); // Para que this pueda ser utilizado en las funciones

	    var lista = this.collection;

	    // Guardamos las referencias
        this.options = options;
	    this.permiso = this.options.permiso;
	    this.editView = this.options.editView;
		this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;

	    // Creamos la lista de paginacion
	    var pagination = new app.mixins.PaginationView({
		collection: lista,
		ver_numeros_pagina : false
	    });

	    lista.on('add', this.addOne, this);
	    lista.on('reset', this.addAll, this);
	    lista.on('all', this.render, this);

	    
	    // Renderizamos por primera vez la tabla:
	    // ----------------------------------------
	    var obj = {
			permiso: this.permiso,
			lightbox: this.lightbox
	    };
	    
	    // Cargamos el template
	    $(this.el).html(this.template(obj));
	    // Cargamos el paginador
	    $(this.el).find(".pagination_container").html(pagination.el);
	    
	    new app.mixins.Select({
			modelClass: app.models.Cliente,
			campoSelect: "nombre",
			url: "clientes/",
			render: "#cheques_terceros_clientes",
			name : "id_cliente",
			firstOptions: ["<option value='0'>Cliente</option>"],
	    });		
	    
	    // Buscamos los elementos para paginar los resultados
	    var entregado = $(this.el).find("#cheques_terceros_entregados").val();
        var server_api = {
            "filter":"",
            "id_banco":0,
            "id_cliente":0,
            "entregado":entregado,
        };
	    this.collection.server_api = server_api;
	    this.collection.pager();
		
	    if (this.lightbox) {
		$(document).ready(function(){
		    $(self.el).find(".search_container #cheques_terceros_clientes").focus();
		});
	    }		
	},
	
	searchOnEnter: function(e) {
	    if (e.keyCode == 13) this.buscar();
	},
	
	buscar : function() {
	    var id_banco = $(this.el).find("#cheques_terceros_bancos").val();
	    var id_cliente = $(this.el).find("#cheques_terceros_clientes").val();
	    var entregado = $(this.el).find("#cheques_terceros_entregados").val();
		var filtro = $(this.el).find("#cheques_terceros_texto").val();
	    if (entregado == undefined) entregado = -1;
        
        var server_api = {
            "filter":filtro,
            "id_banco":id_banco,
            "id_cliente":id_cliente,
            "entregado":entregado,
        };        
	    this.collection.server_api = server_api;
	    this.collection.pager();	    
	},

	addAll : function () {
	    $(this.el).find("tbody").empty();
	    this.collection.each(this.addOne);
        if (this.collection.meta("suma") !== undefined) $(this.el).find("#cheques_terceros_total").val(Number(this.collection.meta("suma")).format());
	},

	addOne : function ( item ) {
	    var view = new app.views.ChequeTerceroItem({
			model: item,
			permiso: this.permiso,
			editView: this.editView,
			lightbox: this.lightbox,
	    });
	    $(this.el).find("tbody").append(view.render().el);
	}

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

    views.ChequeTerceroEditView = app.mixins.View.extend({

	template: _.template($("#cheques_terceros_edit_panel_template").html()),

	myEvents: {
	    "click .guardar": "guardar",
	    "click .orden_pago": "ver_orden_pago",
	    "change input[name=devuelto]": "cambiar_devuelto",
	},
	
	ver_orden_pago : function() {
	    var self = this;
	    var ordenPago = new app.models.OrdenPago({
			"id": self.model.get("id_orden_pago")
	    });
	    ordenPago.fetch({
			"success":function() {
				app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
					model: ordenPago
				});
				// Abrimos el lightbox de pagos
				crearLightboxHTML({
					"html":app.views.ordenPagoProveedores.el,
					"width":620,
					"height":565,
				});
			}
	    });
	},	

	initialize: function(options) {
	    _.bindAll(this);
        this.options = options;
		this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
	    this.render();
	},

	render: function()
	{
	    // Creamos un objeto para agregarle las otras propiedades que no son el modelo
		var self = this;
		var edicion = false;
		if (this.options.permiso > 1) edicion = true;
		var obj = { edicion: edicion, id:this.model.id, lightbox:this.lightbox };
		$.extend(obj,this.model.toJSON());
		$(this.el).html(this.template(obj));
        
        if (!this.lightbox) {
            new app.mixins.Select({
                modelClass: app.models.Cliente,
                url: "clientes/",
                render: "#cheques_terceros_clientes",
                name : "id_cliente",
                firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
                selected: this.model.get("id_cliente"),
            });
        }
		
		createdatepicker($(this.el).find("#cheques_terceros_fecha_emision"),self.model.get("fecha_emision"));
		createdatepicker($(this.el).find("#cheques_terceros_fecha_cobro"),self.model.get("fecha_cobro"));
		createdatepicker($(this.el).find("#cheques_terceros_fecha_debitado"),self.model.get("fecha_debitado"));
		
	    return this;
	},

	cambiar_devuelto : function(e) {
	    if ($(e.currentTarget).is(":checked")) {
			this.model.set({ "devuelto":1 });
	    } else {
			this.model.set({ "devuelto":0 });
	    }
	},

	validar: function() {
	    try {
			var self = this;
	
			if ($(self.el).find("#cheques_terceros_clientes").val() == 0) {
				show("Por favor seleccione un cliente.");
				$(self.el).find("#cheques_terceros_clientes").focus();
				return false;
			}
		
			validate_input("cheques_terceros_numero",IS_EMPTY,"Por favor, ingrese un numero.");
			
			if ($(self.el).find("#cheques_terceros_bancos").val() == 0) {
				show("Por favor seleccione un banco.");
				$(self.el).find("#cheques_terceros_bancos").focus();
				return false;
			}
			
			validate_input("cheques_terceros_monto",NOT_EMPTY_INTEGER_OR_DECIMAL,"Por favor, ingrese un numero.");
			validate_input("cheques_terceros_fecha_emision",IS_EMPTY,"Por favor, ingrese una fecha.");
			validate_input("cheques_terceros_fecha_cobro",IS_EMPTY,"Por favor, ingrese una fecha.");
            
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
			if (this.model.id == null || this.model.id == 0) {
				this.model.set({id:0});
			}
			
			this.model.set({
				id_cliente : $(self.el).find("#cheques_terceros_clientes").val(),
				cliente : $(self.el).find("#cheques_terceros_clientes option:checked").text(),				
				id_banco : $(self.el).find("#cheques_terceros_bancos").val(),
				banco : $(self.el).find("#cheques_terceros_bancos option:checked").text(),
				fecha_emision: $(self.el).find("#cheques_terceros_fecha_emision").val(),
				fecha_cobro: $(self.el).find("#cheques_terceros_fecha_cobro").val(),
				fecha_debitado: $(self.el).find("#cheques_terceros_fecha_debitado").val(),
			});
			
			this.model.save({},{
				success: function(model,response) {
					if (self.lightbox) {
						// Debemos seleccionar un elemento cuando se hace click
						window.cheque_tercero = self.model;
						$(".modal").last().trigger("click");
					} else {
						show("Los datos han sido guardados con exito.");
						location.href="app/#cheques_terceros";						
					}
				}
			});
	    }
	},
		
    });
})(app.views, app.models);
