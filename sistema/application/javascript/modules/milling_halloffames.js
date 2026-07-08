(function ( models ) {

  models.MillingHallOfFame = Backbone.Model.extend({
    urlRoot: "milling_halloffames/",
    defaults: {
      nombre: "",
      subtitulo: "",
      path: "",
      activo: 1,
      id_empresa: ID_EMPRESA,
      texto: "",
      comite: "",
      tipo: 0,
      images: [],
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.MillingHallOfFames = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "milling_halloffames/"
    }
  });
})( app.collections, app.models.MillingHallOfFame, Backbone.Paginator);


(function ( app ) {
  app.views.MillingHallOfFameItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#milling_halloffames_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"milling_halloffames",
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
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#milling_halloffame/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Are you sure to remove this element?")) {
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

  app.views.MillingHallOfFamesTableView = app.mixins.View.extend({

   template: _.template($("#milling_halloffames_panel_template").html()),

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
      var view = new app.views.MillingHallOfFameItem({
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

  views.MillingHallOfFameEditView = app.mixins.View.extend({

    template: _.template($("#milling_halloffames_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "milling_halloffames/function/upload_images/",
          "view": self,
        });
      },
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();

      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      $(this.el).find("#images_tabla").sortable();

      return this;
    },

    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length == 0) {
        this.$("#images_container").removeClass('tiene');
      } else {
        this.$("#images_container").addClass('tiene');
        for(var i=0;i<images.length;i++) {
          var path = images[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
        }                
      }
    },    

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("milling_halloffames_nombre",IS_EMPTY,"Please enter your name.");

        // Listado de Imagenes
        if ($(this.el).find("#images_tabla").length > 0) { 
          var images = new Array();
          $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
            images.push($(e).text());
          });
          self.model.set({"images":images});
        }

        if (self.$("#milling_halloffames_tipo").length > 0) {
          this.model.set({
            "tipo":self.$("#milling_halloffames_tipo").val(),
          });
        }

        if (self.$("#milling_halloffames_texto").length > 0) {
          var cktext = CKEDITOR.instances['milling_halloffames_texto'].getData();
          self.model.set({"texto":cktext});
        }
        if (self.$("#milling_halloffames_comite").length > 0) {
          var cktext = CKEDITOR.instances['milling_halloffames_comite'].getData();
          self.model.set({"comite":cktext});
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
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
          "path":$("#hidden_path").val(),
        },{
          success: function(model,response) {
            location.href="app/#milling_halloffames";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.MillingHallOfFame();
      this.render();
    },

  });

})(app.views, app.models);