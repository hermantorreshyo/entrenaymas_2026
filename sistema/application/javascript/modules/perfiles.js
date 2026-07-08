// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Perfil = Backbone.Model.extend({
    urlRoot: "perfiles/",
    defaults: {
      nombre: "",
      principal: 0,
      solo_usuario: 0,
    }
  });
    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Perfiles = paginator.requestPager.extend({

  model: model,

  paginator_core: {
    url: "perfiles/"
  }
  
  });

})( app.collections, app.models.Perfil, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

  collections.PerfilesList = Backbone.Collection.extend({
    model: model,
    url: "perfiles/",
    initialize: function() {
      this.fetch();
    },
    parse: function(response) {
      return response.results;
    }
  });

})( app.collections, app.models.Perfil);


(function (app) {

  app.views.PerfilesSelect = Backbone.View.extend({
    tagName: "select",
    attributes: {
      class: "select",
      id: "id_perfiles"
    },
    initialize: function(options) {
      this.collection.bind("all",this.render,this);
      this.options = options;
      this.selected = this.options.selected;
      _.bindAll(this);
    },
    render: function() {
      $(this.el).empty();
      for(var i=0; i<this.collection.length; i++) {
        var e = this.collection.models[i];
        var selected = (this.selected == e.id ) ? 'selected' : '';
        $(this.el).append("<option value='"+e.id+"' "+selected+">"+e.get("nombre")+"</option>");
      }
      return this;
    }
  });

})(app);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PerfilItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#perfiles_item').html()),
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
    location.href = "app/#perfil/"+this.model.id;
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
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

  app.views.PerfilesTableView = Backbone.View.extend({

    template: _.template($("#perfiles_panel_template").html()),

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
    var view = new app.views.PerfilItem({
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

  views.PerfilEditView = app.mixins.View.extend({

  template: _.template($("#perfiles_edit_panel_template").html()),

  myEvents: {
    "click .guardar": "guardar",
    "click .limpiar": "limpiar",
    "click .todos_administrables":function() {
      this.$(".permiso").val("3");
    },
    "click .todos_ocultos":function() {
      this.$(".permiso").val("0");
    },
  },

  initialize: function(options) {
    this.bind("ver",this.ver,this); // Mostramos el objeto
    this.options = options;
    _.bindAll(this);
    this.render();
  },

  render: function() {
    // Creamos un objeto para agregarle las otras propiedades que no son el modelo
    var edicion = false;
    if (this.options.permiso > 1) edicion = true;
    var obj = { edicion: edicion, id:this.model.id };
    // Extendemos el objeto creado con el modelo de datos
    $.extend(obj,this.model.toJSON());

    $(this.el).html(this.template(obj));

    var id_perfil = 0
    if (!this.model.isNew()) id_perfil = this.model.id;

    $.ajax({
    "url":'permisos/function/get_permisos_by_perfil/'+id_perfil+"/"+ID_PROYECTO,
    "dataType":"json",
    "success":function(r) {
      $("#perfiles_tabla tbody").empty();
      for(var i=0;i<r.length;i++) {
      var modulo = r[i];
      var tr = "";

      // Si estamos creando un perfil nuevo, ponemos todo como administrable
      if (id_perfil == 0) modulo.permiso = 3;

      if (modulo.id_modulo != 0) {
        tr+="<tr class='modulo' data-id='"+modulo.id_modulo+"'>";
        tr+="<td>";
        tr+=((modulo.orden_2 != 0 && modulo.orden_1 != 0)?"<span class='dib w30'></span>":"");
        tr+="<span class=''>"+modulo.title+"</td>";
        tr+="<td>";
        tr+="<select class='permiso form-control no-model'>";
        tr+="<option value='0' "+((modulo.permiso==0)?"selected":"")+">"+(IDIOMA == "en" ? "Hidden" : "Oculto")+"</option>";
        tr+="<option value='1' "+((modulo.permiso==1)?"selected":"")+">"+(IDIOMA == "en" ? "Visible" : "Visible")+"</option>";
        tr+="<option value='2' "+((modulo.permiso==2)?"selected":"")+">"+(IDIOMA == "en" ? "Editable" : "Editable")+"</option>";
        tr+="<option value='3' "+((modulo.permiso==3)?"selected":"")+">"+(IDIOMA == "en" ? "Full" : "Administrable")+"</option>";
        tr+="</select>";
        tr+="</td>";
        tr+="</tr>";
      } else {
        tr+="<tr>";
        tr+="<td colspan='2'>";
        tr+="<span class='bold'>"+modulo.title+"</td>";
        tr+="</td>";
        tr+="</tr>";
      }
      $("#perfiles_tabla tbody").append(tr);
      }
    }
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

    guardar: function() 
    {
      // Obtenemos los IDS de los permisos seleccionados por el usuario
      var ids = new Array();
      $("#perfiles_tree select").each(function(i,e){
        var id = $(e).data("id");
        var permiso = $(e).val();
        if (permiso == null) permiso = 1;
        var o = { "id":id, "permiso":permiso };
        ids.push(o);
      });

      // Obtenemos los IDS de los permisos seleccionados por el usuario
      var ids = new Array();
      $("#perfiles_tabla tbody .modulo").each(function(i,e){
        var id = $(e).data("id");
        var permiso = $(e).find(".permiso").val();
        if (permiso == null) permiso = 0;
        var o = { "id":id, "permiso":permiso };
        ids.push(o);
      });
      // Lo agregamos el modelo
      this.model.set({"permisos":ids});

      if (this.model.id == null) {
      this.model.set({id:0});
      }
      this.model.save({},{
      success: function(model,response) {
          location.reload();
      }
      });
  },
  
    limpiar : function() {
      this.model = new app.models.Perfil()
      this.render();
    },  
  });

})(app.views, app.models);
