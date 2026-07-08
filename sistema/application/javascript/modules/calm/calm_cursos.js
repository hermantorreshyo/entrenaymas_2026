(function ( models ) {

  models.CalmCurso = Backbone.Model.extend({
    urlRoot: "calm_cursos/",
    defaults: {
      nombre: "",
      subtitulo: "",
      autor: "",
      path: "",
      texto: "",
      path_audio: "",
      activo: 1,
      destacado: 0,
      id_categoria: 0,
      categoria: "",
      audios: [],
      premium: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.CalmCursos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "calm_cursos/"
    }
  });
})( app.collections, app.models.CalmCurso, Backbone.Paginator);


(function ( app ) {
  app.views.CalmCursoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#calm_cursos_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"calm_cursos",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },      
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"calm_cursos",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#calm_curso/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
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

  app.views.CalmCursosTableView = app.mixins.View.extend({

   template: _.template($("#calm_cursos_panel_template").html()),

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

      this.collection.on('sync', this.addAll, this);

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
      var view = new app.views.CalmCursoItem({
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

  views.CalmCursoEditView = app.mixins.View.extend({

    template: _.template($("#calm_cursos_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .nuevo_audio":function(){
        var self = this;
        var v = new app.views.CalmCursoAudioEditView({
          model: new app.models.CalmCursoAudio(),
          collection: self.audios,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
        });
      },      
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      new app.mixins.Select({
        modelClass: app.models.CalmCategoria,
        url: "calm_categorias/?offset=9999",
        render: "#calm_curso_categorias",
        firstOptions: ["<option value='0'>Todas</option>"],
        selected: self.model.get("id_categoria"),
        onComplete:function(c) {
          crear_select2("calm_curso_categorias");
        }                    
      });      

      self.audios = new app.collections.CalmCursosAudios();
      var dep = this.model.get("audios");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.CalmCursoAudio(dd);
        self.audios.add(ddo);
      }
      this.audiosTable = new app.views.CalmCursosAudiosTableView({
        collection: self.audios
      });
      this.$("#calm_cursos_audios").html(this.audiosTable.el);      

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("calm_cursos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        this.model.set({
          "id_categoria":self.$("#calm_curso_categorias").val(),
          "texto":CKEDITOR.instances['calm_curso_texto'].getData(),
          "premium":self.$("#calm_curso_premium").val(),
        });

        // Listado de departamentos
        var audios = new Array();
        self.audios.each(function(dpto){
          audios.push(dpto.toJSON());
        });
        self.model.set({"audios":audios});

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
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
          "path":$("#hidden_path").val(),
        },{
          success: function(model,response) {
            location.href="app/#calm_cursos";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CalmCurso();
      this.render();
    },

  });

})(app.views, app.models);





(function ( models ) {

  models.CalmCursoAudio = Backbone.Model.extend({
    urlRoot: "audios",
    defaults: {
      nombre: "",
      path_audio: "",
      duracion: "",
      id_empresa: ID_EMPRESA,
      id_curso: 0,
      orden: 0,
    },
  });
      
})( app.models );


(function (collections, model) {
  collections.CalmCursosAudios = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.CalmCursoAudio);


(function ( app ) {

  app.views.CalmCursosAudiosTableView = app.mixins.View.extend({

    template: _.template($("#calm_cursos_audios_resultados_template").html()),
        
    myEvents: {
      "change #audios_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_curso = (typeof this.options.id_curso != "undefined") ? this.options.id_curso : 0;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.CalmCursoesAudiosItemResultados({
        model: item,
        collection: self.collection,
      });
      this.$(".tbody").append(view.render().el);
    },
            
  });

})(app);


(function ( app ) {
  app.views.CalmCursoesAudiosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#calm_cursos_audios_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.CalmCursoAudioEditView({
        model:self.model,
        collection:self.collection,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":600,
        "height":140,
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {

  app.views.CalmCursoAudioEditView = app.mixins.View.extend({

    template: _.template($("#calm_curso_audio_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      var obj = { "id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },

    validar: function() {
      try {
        var self = this;
        validate_input("calm_curso_audio_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
        this.model.set({
          "nombre":self.$("#calm_curso_audio_nombre").val(),
          "path_audio":$("#hidden_path_audio").val(),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    guardar:function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          // NO PONEMOS ID = 0, PORQUE SINO NO AGREGA DOS ELEMENTOS CON EL MISMO ID
          var maxId = 0;
          this.collection.each(function(item){
            if (item.id > maxId) maxId = item.id;
          });
          maxId++;
          this.model.set({id:maxId});
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);
