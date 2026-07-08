// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ChequePropio = Backbone.Model.extend({
    urlRoot: "cheques_propios",
    defaults: {
      id_banco: 0,
      id_orden_pago: 0,
      banco: "",
      numero: "",
      fecha_emision: $.datepicker.formatDate("dd/mm/yy",new Date()),
      fecha_cobro: $.datepicker.formatDate("dd/mm/yy",new Date()),
      fecha_debitado: "",
      cliente: "",
      id_cliente: 0,
      monto: 0,
      motivo: "",
      devuelto: 0,
      anulado: 0,
      entregado : 0,
    }
  });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.ChequesPropios = paginator.requestPager.extend({
		model: model,
		paginator_core: {
		    url: "cheques_propios/"
		}
    });

})( app.collections, app.models.ChequePropio, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

    collections.ChequesPropiosList = Backbone.Collection.extend({
        model: model,
        url: "/cheques_propios/",
        initialize: function() {
            this.fetch();
        },
        parse: function(response) {
            return response.results;
        }
    });

})( app.collections, app.models.ChequePropio);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ChequePropioItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#cheques_propios_item').html()),
      	events: {
		    "click .edit": "editar",
		    "click .ver": "editar",
		    "click .delete": "borrar",
		    "click .duplicar": "duplicar"
    	},
        initialize: function() {
            // Si el modelo cambia, debemos renderizar devuelta el elemento
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
            this.editView = this.options.editView;
            this.permiso = this.options.permiso;
            _.bindAll(this);
        },
        render: function()
        {
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
		this.editView.trigger("ver",this.model);
	    } else {
		// Debemos seleccionar un elemento cuando se hace click
		window.codigo_proveedor = this.model.get("codigo");
		$("#simplemodal-overlay").trigger("click")
	    }
        },
        borrar: function() {
            if (confirm("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
		this.editView.trigger("limpiar",this.model);
            }
        },
        duplicar: function() {
	    var clonado = this.model.clone();
	    clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
	    clonado.save({},{
		success: function(model,response) {
		    model.set({id:response.id});
		}
	    });
	    this.model.collection.add(clonado);
        }
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

app.views.ChequesPropiosTableView = Backbone.View.extend({

    template: _.template($("#cheques_propios_panel_template").html()),

    attributes: {
	class: "panel"
    },
	
    events : {
	"click .buscar" : "buscar",
	"keypress .input": "searchOnEnter",
    },

    initialize : function (options) {

		_.bindAll(this); // Para que this pueda ser utilizado en las funciones

		var lista = this.collection;
		this.options = options;

		// Guardamos las referencias
		this.permiso = this.options.permiso;
		this.editView = this.options.editView;
	    this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;

	// Creamos la lista de paginacion
	var pagination = new app.mixins.PaginationView({
	    collection: lista,
	    ver_numeros_pagina : false
	});

	lista.on('sync', this.addAll, this);
	
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
	    modelClass: app.models.Banco,
	    campoSelect: "nombre",
	    url: "/bancos/",
	    renderIn: "#cheques_propios_table_bancos_select_container",
	    name : "id_banco",
	    firstOptions: ["<option value='0'>Todos</option>"],
	});
	
	new app.mixins.Select({
	    modelClass: app.models.Cliente,
	    url: "/clientes/",
	    renderIn: "#cheques_propios_table_clientes_select_container",
	    name : "id_cliente",
	    firstOptions: ["<option value='0'>Todos</option>"],
	});		
	
		if (this.lightbox) {
		    $(document).ready(function(){
				$(self.el).find(".search_container #cheques_propios_table_clientes_select_container").focus();
		    });
		}		
    },
	
    searchOnEnter: function(e) {
		if (e.keyCode == 13) this.buscar();
    },
	
    buscar : function() {
		var id_banco = $(this.el).find("#cheques_propios_table_bancos_select_container select").val();
		var id_cliente = $(this.el).find("#cheques_propios_table_clientes_select_container select").val();
		var entregado = $(this.el).find("input[name=entregado]:checked").val();
		var numero = $(this.el).find("#cheques_propios_buscar_numero").val();
		var datos = {};
		datos.filter = "";
		datos.id_cliente = id_cliente;
		datos.id_banco = id_banco;
		datos.entregado = entregado;
		datos.numero = numero;
		this.collection.server_api = datos;
		this.collection.pager();
    },

    addAll : function () {
	$(this.el).find("tbody").empty();
	this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
	var view = new app.views.ChequePropioItem({
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

views.ChequePropioEditView = Backbone.View.extend({

    template: _.template($("#cheques_propios_edit_panel_template").html()),

    attributes: {
	class: "panel"
    },

    events: {
	"click .guardar": "guardar",
	"click .limpiar": "limpiar",
	"click .orden_pago": "ver_orden_pago",
	"change .input": "onchange",
	"change input[name=devuelto]": "cambiar_devuelto",
	"change input[name=anulado]": "cambiar_anulado",
	"change input[name=entregado]": "cambiar_entregado",
    },

    initialize: function() 
    {
	// Si el modelo cambia, debemos renderizar devuelta el elemento
	//this.model.bind("change",this.render,this);
	this.model.bind("destroy",this.render,this);

	this.bind("ver",this.ver,this); // Mostramos el objeto
	this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
	_.bindAll(this);

	this.render();
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

    render: function()
    {
	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
	var self = this;
	var permiso = false;
	if (control.check(this.options.id_modulo) > 1) permiso = true;
	var obj = { edicion: permiso };
	// Extendemos el objeto creado con el modelo de datos
	$.extend(obj,this.model.toJSON());

	$(this.el).html(this.template(obj));
	    
	new app.mixins.Select({
	    modelClass: app.models.Cliente,
	    url: "/clientes/",
	    renderIn: "#cheques_propios_clientes_select_container",
	    name : "id_cliente",
	    firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
	    selected: this.model.get("id_cliente"),
	});
    
	new app.mixins.Select({
	    modelClass: app.models.Banco,
	    campoSelect: "nombre",
	    url: "/bancos/",
	    renderIn: "#cheques_propios_banco_select_container",
	    name : "id_banco",
	    firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
	    selected: this.model.get("id_banco"),
	});
	
	$(this.el).find("#cheques_propios_fecha_emision").datepicker({
	    "dateFormat":"dd/mm/yy",
	    "currentText":"Hoy",
	    "buttonImage": "/resources/images/datepicker.png",
	    "buttonImageOnly": true,
	    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
	    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
	    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
	    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
	    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
	    "nextText":"Proximo",
	    "prevText":"Anterior",
	    "defaultDate":self.model.get("fecha_emision")
	});
	
	$(this.el).find("#cheques_propios_fecha_cobro").datepicker({
	    "dateFormat":"dd/mm/yy",
	    "currentText":"Hoy",
	    "buttonImage": "/resources/images/datepicker.png",
	    "buttonImageOnly": true,
	    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
	    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
	    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
	    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
	    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
	    "nextText":"Proximo",
	    "prevText":"Anterior",
	    "defaultDate":self.model.get("fecha_cobro")
	});
	
	$(this.el).find("#cheques_propios_fecha_debitado").datepicker({
	    "dateFormat":"dd/mm/yy",
	    "currentText":"Hoy",
	    "buttonImage": "/resources/images/datepicker.png",
	    "buttonImageOnly": true,
	    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
	    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
	    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
	    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
	    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
	    "nextText":"Proximo",
	    "prevText":"Anterior",
	    "defaultDate":self.model.get("fecha_debitado")
	});
	
	return this;
    },

    // Rellena los campos con el modelo pasado por parametro
    // Luego la vista mostrara los datos para editar o solamente para ver
    ver: function(model) 
    {
	// Las modificaciones la realizamos sobre una copia
	this.model = model;
	this.render();
    },

    onchange: function (e) {
	var id = $(e.currentTarget).attr("name");
	var value = $(e.currentTarget).val();
	obj = "{\""+id+"\":\""+value+"\"}";
	var objInst = JSON.parse(obj);
	this.model.set(objInst);
    },
	
    cambiar_devuelto : function(e) {
	if ($(e.currentTarget).is(":checked")) {
	    this.model.set({ "devuelto":1 });
	} else {
	    this.model.set({ "devuelto":0 });
	}
    },
	
    cambiar_anulado : function(e) {
	if ($(e.currentTarget).is(":checked")) {
	    this.model.set({ "anulado":1 });
	} else {
	    this.model.set({ "anulado":0 });
	}
    },
	
    cambiar_entregado : function(e) {
	if ($(e.currentTarget).is(":checked")) {
	    this.model.set({ "entregado":1 });
	} else {
	    this.model.set({ "entregado":0 });
	}
    },	

    validar: function() {
        try {
	    var self = this;

	    if ($(self.el).find("#cheques_propios_clientes_select_container select").val() == 0) {
		show("Por favor seleccione un cliente.");
		$(self.el).find("#cheques_propios_clientes_select_container select").focus();
		return false;
	    }
    
	    validate_input("cheques_propios_numero",IS_EMPTY,"Por favor, ingrese un numero.");
	    
	    if ($(self.el).find("#cheques_propios_banco_select_container select").val() == 0) {
		show("Por favor seleccione un banco.");
		$(self.el).find("#cheques_propios_banco_select_container select").focus();
		return false;
	    }
	    
	    validate_input("cheques_propios_monto",NOT_EMPTY_INTEGER_OR_DECIMAL,"Por favor, ingrese un numero.");
	    validate_input("cheques_propios_fecha_emision",IS_EMPTY,"Por favor, ingrese una fecha.");
	    validate_input("cheques_propios_fecha_cobro",IS_EMPTY,"Por favor, ingrese una fecha.");
	    
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
		this.collection.add(this.model);
	    }
	    
	    this.model.set({
		id_cliente : $(self.el).find("#cheques_propios_clientes_select_container select").val(),
		cliente : $(self.el).find("#cheques_propios_clientes_select_container select option:checked").text(),				
		id_banco : $(self.el).find("#cheques_propios_banco_select_container select").val(),
		banco : $(self.el).find("#cheques_propios_banco_select_container select option:checked").text(),
		fecha_emision: $(self.el).find("#cheques_propios_fecha_emision").val(),
		fecha_cobro: $(self.el).find("#cheques_propios_fecha_cobro").val(),
	    });
	    
	    this.model.save({},{
		success: function(model,response) {
		    alert("Los datos han sido guardados con exito.");
		    self.limpiar();
		}
	    });
	}
    },
		
    limpiar : function() {
	this.model = new app.models.ChequePropio();
	this.render();
    },		
});
})(app.views, app.models);



// ------------------------
//   CREACION DEL MODULO
// ------------------------

(function(app){
	
    app.modules.ChequesPropiosModule = function() {

	var perm = control.check("cheques");

	// Cargamos la coleccion
	app.collections.cheques_propios = new app.collections.ChequesPropios();

	app.views.cheque_propioEditView = new app.views.ChequePropioEditView({
	    model: new app.models.ChequePropio(),
	    permiso: perm,
	    collection: app.collections.cheques_propios,
	    id_modulo: "cheques"
	});

	// Cargamos la tabla, y le pasamos la coleccion
	app.views.cheques_propiosTableView = new app.views.ChequesPropiosTableView({
	    collection: app.collections.cheques_propios,
	    editView: app.views.cheque_propioEditView,
	    permiso: perm
	});			

    }

})(app);





// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

app.views.ChequesPropiosBusquedaView = Backbone.View.extend({

    template: _.template($("#cheques_propios_busqueda_template").html()),

    events : {
	"click .entregar" : "entregar",
    },

    initialize : function () {

	var self = this;
	_.bindAll(this); // Para que this pueda ser utilizado en las funciones

	// Cargamos el template
	$(this.el).html(this.template());
	
	new app.mixins.Select({
	    modelClass: app.models.Banco,
	    campoSelect: "nombre",
	    url: "/bancos/",
	    renderIn: "#cheques_propios_busqueda_banco",
	    firstOptions: ["<option value='-1'>Seleccione una opcion</option>"],
	    name : "id_banco",
	    change : self.seleccionar_numeros
	});
	
	var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
	$(this.el).find("#cheques_propios_busqueda_fecha_cobro").datepicker({
	    "dateFormat":"dd/mm/yy",
	    "currentText":"Hoy",
	    "buttonImage": "/resources/images/datepicker.png",
	    "buttonImageOnly": true,
	    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
	    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
	    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
	    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
	    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
	    "nextText":"Proximo",
	    "prevText":"Anterior",
	    "defaultDate":fecha
	});
	$(this.el).find("#cheques_propios_busqueda_fecha_cobro").val(fecha);

	$(this.el).find("#cheques_propios_busqueda_fecha_emision").datepicker({
	    "dateFormat":"dd/mm/yy",
	    "currentText":"Hoy",
	    "buttonImage": "/resources/images/datepicker.png",
	    "buttonImageOnly": true,
	    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
	    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
	    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
	    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
	    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
	    "nextText":"Proximo",
	    "prevText":"Anterior",
	    "defaultDate":fecha
	});
	$(this.el).find("#cheques_propios_busqueda_fecha_emision").val(fecha);
	
    },
	
    seleccionar_numeros : function() {
	    
	var self = this;
	var id_banco = $(this.el).find("#cheques_propios_busqueda_banco select").val();
	var id_empresa = this.model.get("id_empresa");
	new app.mixins.Select({
	    modelClass: app.models.ChequePropio,
	    campoSelect: "numero",
	    url: "/cheques_propios/function/get_by_banco/"+id_banco+"/"+id_empresa,
	    renderIn: "#cheques_propios_busqueda_cheque",
	    firstOptions: ["<option value='-1'>Seleccione una opcion</option>"],
	    name : "id_cheque",
	    change: self.seleccionar_cheque
	});
    },
	
    seleccionar_cheque : function(collec) {
	    
	var id = $(this.el).find("#cheques_propios_busqueda_cheque select").val();
	for (var i = 0; i < collec.models.length; i++) {
	    var c = collec.models[i];
	    if (c.id == id) {
		this.cheque_propio = c;
	    }
	}
    },
	
    entregar : function() {
	    
	window.cheque_propio = this.cheque_propio;
	try {
	    // Controlamos que seleccione un banco
	    if ($(this.el).find("#cheques_propios_busqueda_banco select").val() == -1) {
		show("Por favor seleccione un banco.");
		return;
	    }
	    // Controlamos que seleccione un numero
	    if ($(this.el).find("#cheques_propios_busqueda_cheque select").val() == -1) {
		show("Por favor seleccione cheque.");
		return;
	    }
	    
	    // Tomamos los valores de los inputs
	    var monto = validate_input("cheques_propios_busqueda_monto",NOT_EMPTY_INTEGER_OR_DECIMAL,"Por favor ingrese un monto.");
	    var fecha_emision = validate_input("cheques_propios_busqueda_fecha_emision",IS_EMPTY,"Por favor seleccione una fecha.");
	    var fecha_cobro = validate_input("cheques_propios_busqueda_fecha_cobro",IS_EMPTY,"Por favor seleccione una fecha.");
		
	} catch(e) {
	    return;
	}
	
	this.cheque_propio.set({
	    "fecha_cobro":fecha_cobro,
	    "fecha_emision":fecha_emision,
	    "monto":monto
	});
	
	$(".simplemodal-overlay:last").trigger("click");
    },
	
});
})(app);