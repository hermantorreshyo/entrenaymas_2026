// -----------
//   MODELO
// -----------

(function ( models ) {

    models.TipoComprobante = Backbone.Model.extend({
        urlRoot: "tipos_comprobante/",
        defaults: {
	    nombre: "",
	    operacion: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.TiposComprobante = paginator.requestPager.extend({

	model: model,

	paginator_core: {
	    url: "tipos_comprobante/"
	}
	    
    });

})( app.collections, app.models.TipoComprobante, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

    collections.TiposComprobanteList = Backbone.Collection.extend({
        model: model,
        url: "tipos_comprobante/",
        initialize: function() {
            this.fetch();
        },
        parse: function(response) {
            return response.results;
        }
    });

})( app.collections, app.models.TipoComprobante);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.TipoComprobanteItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#tipos_comprobante_item').html()),
      	events: {
	    "click .edit": "editar",
	    "click .ver": "editar",
	    "click .delete": "borrar",
	    "click .duplicar": "duplicar"
    	},
        initialize: function(options) {
            // Si el modelo cambia, debemos renderizar devuelta el elemento
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.editView = this.options.editView;
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
	    this.editView.trigger("ver",this.model);
        },
        borrar: function() {
            if (confirmar("Realmente desea eliminar este elemento?")) {
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

    app.views.TiposComprobanteTableView = Backbone.View.extend({

    	template: _.template($("#tipos_comprobante_panel_template").html()),

    	attributes: {
	    class: "panel"
    	},

	initialize : function (options) {

	    _.bindAll(this); // Para que this pueda ser utilizado en las funciones

	    var lista = this.collection;

	    // Guardamos las referencias
        this.options = options;
	    this.permiso = this.options.permiso;
	    this.editView = this.options.editView;

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
	    var view = new app.views.TipoComprobanteItem({
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

    views.TipoComprobanteEditView = Backbone.View.extend({

	template: _.template($("#tipos_comprobante_edit_panel_template").html()),

    	attributes: {
	    class: "panel"
    	},

	events: {
	    "click .guardar": "guardar",
	    "click .limpiar": "limpiar",
	    "change .input": "onchange"
	},

        initialize: function(options) 
        {
            // Si el modelo cambia, debemos renderizar devuelta el elemento
            //this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.bind("ver",this.ver,this); // Mostramos el objeto
            this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
            _.bindAll(this);

            this.render();
        },

        render: function()
        {
	    // Creamos un objeto para agregarle las otras propiedades que no son el modelo
	    var permiso = false;
	    if (control.check(this.options.id_modulo) > 1) permiso = true;
	    var obj = { edicion: permiso };
	    // Extendemos el objeto creado con el modelo de datos
	    $.extend(obj,this.model.toJSON());

	    $(this.el).html(this.template(obj));

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

        validar: function() {
            try {
		var self = this;
                
                // Validamos los campos que sean necesarios
                validate_input("tipos_comprobante_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            
            } catch(e) {
                return false;
            }
        },
        

        guardar: function() 
        {
            if (this.validar()) {
        	if (this.model.id == null || this.model.id == 0) {
        	    this.model.set({id:0});
		    this.collection.add(this.model);
        	}
        	this.model.save({},{
		    success: function(model,response) {
			show("Los datos han sido guardados con exito.");
		        model.set({id:response.id});
		    }
        	});
            }
	},
		
        limpiar : function() {

            this.model = new app.models.TipoComprobante()
            this.render();
            
        },		
    });

})(app.views, app.models);



// ------------------------
//   CREACION DEL MODULO
// ------------------------

(function(app){
	
    app.modules.TiposComprobanteModule = function() {

	var perm = control.check("tipos_comprobante");

	// Cargamos la coleccion
	app.collections.tipos_comprobante = new app.collections.TiposComprobante();

	app.views.tipo_comprobanteEditView = new app.views.TipoComprobanteEditView({
	    model: new app.models.TipoComprobante(),
	    permiso: perm,
	    collection: app.collections.tipos_comprobante,
	    id_modulo: "tipos_comprobante"
	});

	// Cargamos la tabla, y le pasamos la coleccion
	app.views.tipos_comprobanteTableView = new app.views.TiposComprobanteTableView({
	    collection: app.collections.tipos_comprobante,
	    editView: app.views.tipo_comprobanteEditView,
	    permiso: perm
	});			

    }

})(app);
