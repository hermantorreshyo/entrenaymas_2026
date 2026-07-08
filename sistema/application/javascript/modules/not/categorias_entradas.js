// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CategoriaEntrada = Backbone.Model.extend({
    urlRoot: "categorias_entradas/",
    defaults: {
      nombre: "",
      nombre_en: "",
      nombre_pt: "",
      path: "",
      external_link: "",
      id_padre: 0,
      activo: 1,
      fija: 0, // Si esta en 1, no se puede borrar
      color: "",
      texto: "",
      mostrar_home: 0,
      categorias_relacionadas: [], // Categorias relacionadas
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.CategoriasEntradas = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "categorias_entradas/"
		}
		
	});

})( app.collections, app.models.CategoriaEntrada, Backbone.Paginator);




// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

  app.views.CategoriasEntradasTreeView = app.mixins.View.extend({

    template: _.template($("#categorias_entradas_tree_panel_template").html()),
    
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.preventDefault();
        var id = $(e.currentTarget).parents(".dd-item").data("id");
        var cat = new app.models.CategoriaEntrada({ id: id });
        cat.fetch({
          "success":function(){
            self.ver(cat);
          }
        });
      },
      "click .nuevo":function() {
        var modelo = new app.models.CategoriaEntrada();
        this.ver(modelo);
      },
    },
    
    ver: function(modelo) {
      var permiso = control.check("categorias_entradas");
      if (permiso <= 1) return;
      var categoria = new app.views.CategoriaEntradaEditView({
        model: modelo,
        permiso: permiso,
      });
      var d = $("<div/>").append(categoria.el);
      crearLightboxHTML({
        "html":d,
        "width":600,
        "height":500,
      });
    },
  
    initialize : function () {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.render();
    },
    
    render : function() {
      
      var self = this;
      $(this.el).html(this.template());
      
      this.$('.dd').nestable();
      var permiso = control.check("categorias_entradas");
      if (permiso > 1) this.$('.dd').on('change',this.reorder);      
      
      return this;	  
    },    

    reorder: function() {
      var serialize = this.$('.dd').nestable('serialize');
      $.ajax({
        "url":"categorias_entradas/function/reorder/",
        "type":"post",
        "dataType":"json",
        "data":{
          "datos":serialize,
        }
      });
    },

  });
})(app);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.CategoriaEntradaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#categorias_entradas_item').html()),
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
    	location.href="app/#categoria_entrada/"+this.model.id;
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

  app.views.CategoriasEntradasTableView = Backbone.View.extend({

  	template: _.template($("#categorias_entradas_panel_template").html()),

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
			var view = new app.views.CategoriaEntradaItem({
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

	views.CategoriaEntradaEditView = app.mixins.View.extend({

		template: _.template($("#categorias_entradas_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
      "click .eliminar": "eliminar",
		},
    
    eliminar : function() {
      if (!confirmar("Realmente desea eliminar este elemento?")) return;
      var self = this;	  
      var categoria_entrada = new app.models.CategoriaEntrada({
        "id":self.model.id
      });
      categoria_entrada.destroy();
      categoria_entrada.fetch({
        "success":function() {
          location.reload();
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
      
      this.$(".color").colorpicker({
        format: "rgba"
      });
      
      $(this.el).find("#categorias_entradas_tree").fancytree({
        source: {
          url: 'categorias_entradas/function/get_arbol/'
        },
        selectMode: 3,
        checkbox: true,
        renderNode: function(event,data) {
          var node = data.node;
          
          // Controlamos si el ID esta en los relacionados
          var selected = false;
          var rel = self.model.get("categorias_relacionadas");
          for(var i=0;i<rel.length;i++) {
            var o = rel[i];
            if (o.id == node.key) {
              selected = true;
              break;
            }
          }
          node.setSelected(selected);
          node.setExpanded(true);
        },
      });      
      
      new app.mixins.Select({
        modelClass: app.models.CategoriaEntrada,
        url: "categorias_entradas/function/get_select/",
        render: "#categorias_entradas_padre",
        firstOptions: ["<option value='0'>Ninguno</option>"],
        name : "id_padre",
        selected: this.model.get("id_padre"),
      });      

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("categorias_entradas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        
        var color = this.$(".color").colorpicker('getValue');
        
        // Arbol de categorias de relacionados
        var categorias_relacionadas = new Array();
        var rel = $("#categorias_entradas_tree").fancytree("getTree").getSelectedNodes();
        for(var i=0;i<rel.length;i++) {
          var o = rel[i];
          categorias_relacionadas.push({
            "id":o.key,
          });
        }
        self.model.set({
          "categorias_relacionadas":categorias_relacionadas,
          "color":color,
          "path":self.$("#hidden_path").val(),
          "texto":self.$("#categoria_entrada_texto").val(),
        });

        if (!self.model.isNew()) {
          if (self.model.id == self.model.get("id_padre")) {
            alert("La categoria padre no puede ser la misma categoria.");
            return false;
          }
        }
        
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
            "id_padre":$("#categorias_entradas_padre").val(),
          },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.CategoriaEntrada()
      this.render();
    },
		
	});

})(app.views, app.models);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CategoriaEntradaMiniEditView = app.mixins.View.extend({

    template: _.template($("#categorias_entradas_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          $(e.currentTarget).parent().next().find(".tab").focus();
        }
      },
      "keyup .tab":function(e) {
        if (e.which == 27) this.cerrar();
      },
      "keypress .guardar":function(e) {
        if (e.keyCode == 13) this.guardar();
      },
    },

    initialize: function(options) {
      this.options = options;
      this.input = this.options.input;
      this.onSave = this.options.onSave;
      this.callback = this.options.callback;

      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var obj = { id:this.model.id };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var a = new Array({
        "id":0,
        "title":"Ninguno",
        "children":[],
      });
      var b = a.concat(categorias_noticias);
      var r = workspace.crear_select(b,"",self.model.get("id_padre"));
      this.$("#categorias_entradas_mini_padre").html(r);
      
      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#categorias_entradas_mini_nombre").val($(this.input).val().trim());
      }

      return this;
    },

    focus: function() {
      $(this.el).find("#categorias_entradas_mini_nombre").focus();
    },

    validar: function() {
      var self = this;
      try {
        validate_input("categorias_entradas_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        return true;
      } catch(e) {
        return false;
      }
    },
    

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
            "id_empresa":ID_EMPRESA,
            "nombre":$("#categorias_entradas_mini_nombre").val(),
            "id_padre":$("#categorias_entradas_mini_padre").val(),
            "activo":1,
          },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (typeof self.onSave != "undefined") self.onSave(model);
              if (typeof self.callback != "undefined") self.callback(model.id);
              self.cerrar();
            }
          }
        });
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },
    
  });

})(app.views, app.models);