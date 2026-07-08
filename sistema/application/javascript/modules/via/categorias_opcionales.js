// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CategoriaOpcional = Backbone.Model.extend({
    urlRoot: "categorias_opcionales/",
    defaults: {
      nombre: "",
      nombre_en: "",
      nombre_pt: "",
      path: "",
      id_padre: 0,
      activo: 1,
      id_empresa: ID_EMPRESA,
      link: "",
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.CategoriasOpcionales = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "categorias_opcionales/"
    }
    
  });

})( app.collections, app.models.CategoriaOpcional, Backbone.Paginator);


// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

  app.views.CategoriasOpcionalesTreeView = app.mixins.View.extend({

    template: _.template($("#categorias_opcionales_tree_panel_template").html()),

    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.preventDefault();
        var id = $(e.currentTarget).parents(".dd-item").data("id");
        var cat = new app.models.CategoriaOpcional({ id: id });
        cat.fetch({
          "success":function(){
            self.ver(cat);
          }
        });
      },
      "click .nuevo":function() {
        var modelo = new app.models.CategoriaOpcional();
        this.ver(modelo);
      },
    },

    ver: function(modelo) {
      var categoria = new app.views.CategoriaOpcionalEditView({
        model: modelo,
        permiso: 3,
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
      this.$('.dd').on('change',this.reorder);            
      
      return this;      
    },        

    reorder: function() {
      var serialize = this.$('.dd').nestable('serialize');
      $.ajax({
        "url":"categorias_opcionales/function/reorder/",
        "type":"post",
        "dataType":"json",
        "data":{
          "datos":serialize,
        }
      });
    },

  });
})(app);

// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CategoriaOpcionalEditView = app.mixins.View.extend({

    template: _.template($("#categorias_opcionales_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .eliminar": "eliminar",
    },

    eliminar : function() {
      if (!confirmar("Realmente desea eliminar este elemento?")) return;
      var self = this;      
      var categoria_opcional = new app.models.CategoriaOpcional({
        "id":self.model.id
      });
      categoria_opcional.destroy();
      categoria_opcional.fetch({
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

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      new app.mixins.Select({
        modelClass: app.models.CategoriaOpcional,
        url: "categorias_opcionales/function/get_select/",
        render: "#categorias_opcionales_padre",
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
        validate_input("categorias_opcionales_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        
        self.model.set({
          "path":self.$("#hidden_path").val(),
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
          "id_padre":$("#categorias_opcionales_padre").val(),
        },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CategoriaOpcional()
      this.render();
    },

  });

})(app.views, app.models);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CategoriaOpcionalEditViewMini = app.mixins.View.extend({

    template: _.template($("#categorias_opcionales_edit_mini_panel_template").html()),

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

      new app.mixins.Select({
        modelClass: app.models.CategoriaOpcional,
        url: "categorias_opcionales/function/get_select/",
        render: "#categorias_opcionales_mini_padre",
        firstOptions: ["<option value='0'>Ninguno</option>"],
        name : "id_padre",
        selected: this.model.get("id_padre"),
      });

      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#categorias_opcionales_mini_nombre").val($(this.input).val().trim());
      }

      return this;
    },

    focus: function() {
      $(this.el).find("#categorias_opcionales_mini_nombre").focus();
    },

    validar: function() {
      var self = this;
      try {
        validate_input("categorias_opcionales_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "nombre":$("#categorias_opcionales_mini_nombre").val(),
          "id_padre":$("#categorias_opcionales_mini_padre").val(),
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