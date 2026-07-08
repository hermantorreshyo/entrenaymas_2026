// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Chequera = Backbone.Model.extend({
        urlRoot: "/chequeras/",
        defaults: {
			id:0,
			id_banco: 0,
			id_empresa: 0,
			banco: "",
			numero: "",
			numero_desde: 0,
			numero_hasta: 0,
			terminada: 0,
        }
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Chequeras = paginator.requestPager.extend({

	model: model,

	paginator_core: {
	    url: "/chequeras/"
	}
	    
    });

})( app.collections, app.models.Chequera, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

    collections.ChequerasList = Backbone.Collection.extend({
        model: model,
        url: "/chequeras/",
        initialize: function() {
            this.fetch();
        },
        parse: function(response) {
            return response.results;
        }
    });

})( app.collections, app.models.Chequera);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ChequeraItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#chequeras_item').html()),
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
            
            this.editView = this.options.editView;
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
	    this.editView.trigger("ver",this.model);
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

app.views.ChequerasTableView = Backbone.View.extend({

    template: _.template($("#chequeras_panel_template").html()),

    attributes: {
	class: "panel"
    },
	
    events : {
	"click .buscar" : "buscar",
	"keypress .input": "searchOnEnter",
    },

    initialize : function () {

	_.bindAll(this); // Para que this pueda ser utilizado en las funciones

	var lista = this.collection;

	// Guardamos las referencias
	this.permiso = this.options.permiso;
	this.editView = this.options.editView;

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
	var obj = { permiso: this.permiso };
	
	// Cargamos el template
	$(this.el).html(this.template(obj));
	// Cargamos el paginador
	$(this.el).find(".pagination_container").html(pagination.el);
    
	new app.mixins.Select({
	    modelClass: app.models.Banco,
	    campoSelect: "nombre",
	    url: "/bancos/",
	    renderIn: "#chequeras_table_bancos_select_container",
	    name : "id_banco",
	    firstOptions: ["<option value='0'>Todos</option>"],
	});	    
	
	// Vamos a buscar los elementos y lo paginamos
	lista.pager();
    },
	
    searchOnEnter: function(e) {
	if (e.keyCode == 13) this.buscar();
    },
    
    buscar : function() {
	
	var id_banco = $(this.el).find("#chequeras_table_bancos_select_container select").val();
	this.collection.server_api.filter = "";
	this.collection.server_api.id_banco = id_banco;
	this.collection.pager();	    
	
    },

    addAll : function () {
	$(this.el).find("tbody").empty();
	this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
	var view = new app.views.ChequeraItem({
	    model: item,
	    permiso: this.permiso,
	    editView: this.editView
	});
	$(this.el).find("tbody").append(view.render().el);
    }

});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

    views.ChequeraEditView = Backbone.View.extend({

	template: _.template($("#chequeras_edit_panel_template").html()),

	attributes: {
		class: "panel"
	},

	events: {
	    "click .guardar": "guardar",
	    "click .limpiar": "limpiar",
	    "change .input": "onchange"
	},

	initialize: function() {
	    // Si el modelo cambia, debemos renderizar devuelta el elemento
	    //this.model.bind("change",this.render,this);
	    this.model.bind("destroy",this.render,this);
	    this.bind("ver",this.ver,this); // Mostramos el objeto
	    this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
	    _.bindAll(this);
	    this.render();
	},

	render: function() {
	    // Creamos un objeto para agregarle las otras propiedades que no son el modelo
	    var permiso = false;
	    if (control.check(this.options.id_modulo) > 1) permiso = true;
	    var obj = { edicion: permiso };
	    // Extendemos el objeto creado con el modelo de datos
	    $.extend(obj,this.model.toJSON());

	    $(this.el).html(this.template(obj));
	
	    new app.mixins.Select({
			modelClass: app.models.Banco,
			campoSelect: "nombre",
			url: "/bancos/",
			renderIn: "#chequeras_banco_select_container",
			name : "id_banco",
			firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
			selected: this.model.get("id_banco"),
	    });
	    return this;
	},

	// Rellena los campos con el modelo pasado por parametro
	// Luego la vista mostrara los datos para editar o solamente para ver
	ver: function(model) {
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

	validar: function() {
	    try {
			var self = this;
			// Validamos los campos que sean necesarios
			validate_input("chequeras_numero",IS_EMPTY,"Por favor, ingrese un numero.");
			if ($(self.el).find("#chequeras_banco_select_container select").val() == 0) {
				show("Por favor seleccione un banco.");
				$(self.el).find("#chequeras_banco_select_container select").focus();
				return false;
			}
			// No hay ningun error
			$(".error").removeClass("error");
			return true;
	    } catch(e) {
		return false;
	    }
	},
        
	guardar: function() {
	    var self = this;
	    if (this.validar()) {
		if (this.model.id == null || this.model.id == 0) {
		    this.model.set({id:0});
		}
		this.model.set({
		    id_banco : $(self.el).find("#chequeras_banco_select_container select").val(),
		    id_empresa : $(self.el).find("#chequeras_empresas_select").val(),
		    banco : $(self.el).find("#chequeras_banco_select_container select option:checked").text(),
		});
		this.model.save({},{
		    success: function(model,response) {
				if (model.id == -1) {
					alert("ERROR: La chequera ya existe o la cantidad de cheques a generar es muy alta.");
				} else {
					alert("Los datos han sido guardados con exito.");
					model.set({id:response.id});
					self.collection.add(this.model);
				}
		    }
		});
	    }
	},
		
	limpiar : function() {
	    this.model = new app.models.Chequera();
	    this.render();
	},		
    });
})(app.views, app.models);



// ------------------------
//   CREACION DEL MODULO
// ------------------------

(function(app){
	
    app.modules.ChequerasModule = function() {

	var perm = control.check("chequeras");

	// Cargamos la coleccion
	app.collections.chequeras = new app.collections.Chequeras();

	app.views.chequeraEditView = new app.views.ChequeraEditView({
	    model: new app.models.Chequera(),
	    permiso: perm,
	    collection: app.collections.chequeras,
	    id_modulo: "chequeras"
	});

	// Cargamos la tabla, y le pasamos la coleccion
	app.views.chequerasTableView = new app.views.ChequerasTableView({
	    collection: app.collections.chequeras,
	    editView: app.views.chequeraEditView,
	    permiso: perm
	});			

    }

})(app);