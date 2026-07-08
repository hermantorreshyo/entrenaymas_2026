// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ClasificadoCategoria = Backbone.Model.extend({
        urlRoot: "clasificados_categorias/",
        defaults: {
            nombre: "",
            path: "",
            id_padre: 0,
            activo: 1,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.ClasificadosCategorias = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "clasificados_categorias/"
		}
		
	});

})( app.collections, app.models.ClasificadoCategoria, Backbone.Paginator);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ClasificadoCategoriaItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#clasificados_categorias_item').html()),
      	events: {
    		"click .edit": "editar",
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
        	location.href="app/#clasificado_categoria/"+this.model.id;
        },
        borrar: function() {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
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

    app.views.ClasificadosCategoriasTableView = Backbone.View.extend({

    	template: _.template($("#clasificados_categorias_panel_template").html()),

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
			var view = new app.views.ClasificadoCategoriaItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);




(function ( app ) {

    app.views.ClasificadosCategoriasTreeView = app.mixins.View.extend({

        template: _.template($("#clasificados_categorias_tree_panel_template").html()),
    
        initialize : function () {
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.render();
        },
        
        render : function() {
            
            var self = this;
            $(this.el).html(this.template());
            
            // Cargamos el arbol con permisos
            $(this.el).find("#clasificados_categorias_tree").fancytree({
                extensions: ["dnd"],
                source: {
                    url: 'clasificados_categorias/function/get_arbol/'
                },
                renderNode: function(event,data) {
                    var node = data.node;
                    node.setExpanded(true);
                },
                dblclick: function(event,data) {
                    location.href = "app/#clasificado_categoria/"+data.node.key;
                },
                dnd: {
                  autoExpandMS: 400,
                  draggable: { // modify default jQuery draggable options
                    zIndex: 1000,
                    scroll: false,
                    containment: "parent",
                    revert: "invalid"
                  },
                  preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                  preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
              
                  dragStart: function(node, data) {
                    // This function MUST be defined to enable dragging for the tree.
                    // Return false to cancel dragging of node.
                    //    if( data.originalEvent.shiftKey ) ...          
                    //    if( node.isFolder() ) { return false; }
                    return true;
                  },
                  dragEnter: function(node, data) {
                    /* data.otherNode may be null for non-fancytree droppables.
                     * Return false to disallow dropping on node. In this case
                     * dragOver and dragLeave are not called.
                     * Return 'over', 'before, or 'after' to force a hitMode.
                     * Return ['before', 'after'] to restrict available hitModes.
                     * Any other return value will calc the hitMode from the cursor position.
                     */
                    // Prevent dropping a parent below another parent (only sort
                    // nodes under the same parent):
                    if(node.parent !== data.otherNode.parent){
                      return false; // Se mueve dentro del mismo padre
                    }
                    // Don't allow dropping *over* a node (would create a child). Just
                    // allow changing the order:
                    return ["before", "after"]; // No permite ponerlo adentro de otro
                    // Accept everything:
                    // return true;
                  },
                  dragOver: function(node, data) {
                  },
                  dragLeave: function(node, data) {
                  },
                  dragStop: function(node, data) {
                  },
                  dragDrop: function(node, data) {
                    // This function MUST be defined to enable dropping of items on the tree.
                    // data.hitMode is 'before', 'after', or 'over'.
                    // We could for example move the source to the new target:
                    data.otherNode.moveTo(node, data.hitMode);
                    
                    var array = new Array();
                    for(var i=0;i<node.parent.children.length;i++) {
                        var c = node.parent.children[i];
                        array.push(c.key);
                    }
                    var elements = array.join("-");
                    $.ajax({
                        "url":"clasificados_categorias/function/reorder/",
                        "type":"post",
                        "dataType":"json",
                        "data":{
                            "elements":elements,
                            "filter_value":node.data.id_padre,
                        },
                        "success":function(r){
                            console.log(r);
                        }
                    });
                  }
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

	views.ClasificadoCategoriaEditView = app.mixins.View.extend({

		template: _.template($("#clasificados_categorias_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
            "click .eliminar": "eliminar",
		},
        
        eliminar : function() {
            if (!confirmar("Realmente desea eliminar este elemento?")) return;
            var self = this;	    
            var clasificado_categoria = new app.models.ClasificadoCategoria({
                "id":self.model.id
            });
            clasificado_categoria.destroy();
            clasificado_categoria.fetch({
                "success":function() {
                    location.href="app/#clasificados_categorias";
                }
            });
        },        

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            this.options = options;
            _.bindAll(this);
            this.render();
        },

        render: function()
        {
            var self = this;
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));
            
            //r = "<option value='0'>-</option>";
            //r += workspace.crear_select(categorias_clasificados,"",self.model.get("id_padre"));
            //this.$("#clasificados_categorias_padre").html(r);            
            
            /*
            if (self.model.get("atributos").length>0) {
                $(this.el).find("#clasificados_categorias_atributos").val(self.model.get("atributos").join(","));                
            }
            // Cargamos las etiquetas con AJAX
            $.ajax({
                "url":"clasificados_atributos/",
                "dataType":"json",
                "success":function(r) {
                    var atributos = new Array();
                    for(var i=0;i<r.results.length;i++) {
                        var a = r.results[i];
                        atributos.push(a.nombre);
                    }
                    $(self.el).find("#clasificados_categorias_atributos").select2({
                        tags: atributos,
                    });
                }
            });
            */
            /*
            new app.mixins.Select({
                modelClass: app.models.ClasificadoCategoria,
                url: "clasificados_categorias/function/get_select/",
                render: "#clasificados_categorias_padre",
                firstOptions: ["<option value='0'>Ninguno</option>"],
                name : "id_padre",
                selected: this.model.get("id_padre"),
            });
            */

            return this;
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("clasificados_categorias_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
                // Las etiquetas se tratan como array porque son entidades separadas
                //var atributos = self.$("#clasificados_categorias_atributos").select2("val");
                
                // Arbol de categorias de relacionados
                /*
                var categorias_relacionadas = new Array();
                var rel = $("#clasificados_categorias_tree").fancytree("getTree").getSelectedNodes();
                for(var i=0;i<rel.length;i++) {
                    var o = rel[i];
                    categorias_relacionadas.push({
                        "id":o.key,
                    });
                }
                self.model.set({
                    "categorias_relacionadas":categorias_relacionadas,
                    "atributos":atributos,
                });
                */
                
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
                        "id_padre":$("#clasificados_categorias_padre").val(),
                    },{
                    success: function(model,response) {
                        location.href="app/#clasificados_categorias";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.ClasificadoCategoria()
            this.render();
        },
		
	});

})(app.views, app.models);