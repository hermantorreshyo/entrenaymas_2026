// -----------
//   MODELO
// -----------

(function ( models ) {

    models.WebCategoria = Backbone.Model.extend({
        urlRoot: "web_categorias/",
        defaults: {
            nombre_es: "",
            id_padre: 0,
            id_proyecto: 2,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.WebCategorias = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "web_categorias/"
		}
		
	});

})( app.collections, app.models.WebCategoria, Backbone.Paginator);




// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

    app.views.WebCategoriasTreeView = app.mixins.View.extend({

        template: _.template($("#web_categorias_tree_panel_template").html()),
    
        initialize : function () {
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.render();
        },
        
        render : function() {
            
            var self = this;
            $(this.el).html(this.template());
            
            // Cargamos el arbol con permisos
            $(this.el).find("#web_categorias_tree").fancytree({
                source: {
                    url: 'web_categorias/function/get_arbol/'
                },
                renderNode: function(event,data) {
                    var node = data.node;
                    node.setExpanded(true);
                },
                dblclick: function(event,data) {
                    if (data.node.data.id_padre == 0) location.href = "app/#web_categoria/"+data.node.key;
                    else location.href = "app/#web_categoria/"+data.node.key;
                }
            });
            return this;	    
        },        

    });
})(app);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.WebCategoriaEditView = app.mixins.View.extend({

		template: _.template($("#web_categorias_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
            "click .eliminar": "eliminar",
		},
        
        eliminar : function() {
            if (!confirmar("Realmente desea eliminar este elemento?")) return;
            var self = this;	    
            var web_categoria = new app.models.WebCategoria({
                "id":self.model.id
            });
            web_categoria.destroy();
            web_categoria.fetch({
                "success":function() {
                    location.href="app/#web_categorias";
                }
            });
        },        

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.options = options;
            this.render();
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
            
            new app.mixins.Select({
                modelClass: app.models.WebCategoria,
                url: "web_categorias/function/get_select/",
                render: "#web_categorias_padre",
                firstOptions: ["<option value='0'>Ninguna</option>"],
                name : "id_padre",
                campoSelect: "nombre_es",
                selected: this.model.get("id_padre"),
            });            

            return this;
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("web_categorias_nombre_es",IS_EMPTY,"Por favor, ingrese un nombre.");

                this.model.set({
                    "id_padre":$("#web_categorias_padre").val(),
                });
                
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
                        "id_empresa":ID_EMPRESA,
                    },{
                    success: function(model,response) {
                        location.href="app/#web_categorias";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.WebCategoria()
            this.render();
        },
		
	});

})(app.views, app.models);