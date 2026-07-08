// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Rubro = Backbone.Model.extend({
    urlRoot: "rubros/",
    defaults: {
      nombre: "",
      subtitulo: "",
      destacado: 0,
      id_padre: 0,
      activo: 1,
      rubros_relacionados: [], // Categorias relacionadas
      path: "",
      images: [],
      id_usuario: ((ID_EMPRESA == 571 && PERFIL == 661) ? ID_USUARIO : 0),

      // Estos campos en realidad se guardan en la tabla rubros_web
      seo_title: "",
      seo_description: "",
      seo_keywords: "",
      texto: "",
      texto_en: "",
      texto_pt: "",
      h1: "",
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Rubros = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "rubros/"
    }
    
  });

})( app.collections, app.models.Rubro, Backbone.Paginator);




// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

  app.views.RubrosTreeView = app.mixins.View.extend({

    template: _.template($("#rubros_tree_panel_template").html()),

    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.preventDefault();
        var id = $(e.currentTarget).parents(".dd-item").data("id");
        var cat = new app.models.Rubro({ id: id });
        cat.fetch({
          "success":function(){
            self.ver(cat);
          }
        });
      },
      "click .reordenar_todos":function() {
        if (!confirm("Desea ordenar los elementos por orden alfabetico?")) return;
        var self = this;
        $.ajax({
          "url":"rubros/function/reordenar_todos/",
          "dataType":"json",
          "success":function(res) {
            location.reload();
          }
        });
      },
      "click .mover_lote": function() {
        if (typeof window.rubros_marcados == "undefined") return;
        if (window.rubros_marcados.length == 0) return;
        var view = new app.views.RubroMoverPadreView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
        });
      },
      "click .eliminar_lote":function() {
        if (typeof window.rubros_marcados == "undefined") return;
        if (window.rubros_marcados.length == 0) return;
        if (!confirm("Realmente desea eliminar los elementos seleccionados?")) return;
        var self = this;
        $.ajax({
          "url":"rubros/function/eliminar_por_lote/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ids":window.rubros_marcados,
          },
          "success":function(res) {
            location.reload();
          }
        });
      },
      "click .nuevo":function() {
        var modelo = new app.models.Rubro();
        this.ver(modelo);
      },
    },

    marcar: function(e) {
      e.stopPropagation();
      e.preventDefault();
      var el = e.currentTarget;
      var marcado = false;
      window.rubros_marcados = new Array();
      $(".check-row").each(function(i,e){
        if ($(e).is(":checked")) {
          marcado = true;
          window.rubros_marcados.push($(e).val());
        }
      });
      if (marcado) $(".bulk_action").slideDown();
      else $(".bulk_action").slideUp();
    },

    ver: function(modelo) {
      var categoria = new app.views.RubroEditView({
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
        "url":"rubros/function/reorder/",
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

  app.views.RubroItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#rubros_item').html()),
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
      location.href="app/#rubro/"+this.model.id;
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

  app.views.RubrosTableView = Backbone.View.extend({

   template: _.template($("#rubros_panel_template").html()),

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
      var view = new app.views.RubroItem({
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

  views.RubroEditView = app.mixins.View.extend({

    template: _.template($("#rubros_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .eliminar": "eliminar",
      "click .cerrar": function(){
        $(".modal:last").trigger('click');
      },
      "click #rubro_4_link":function() {
        var en = CKEDITOR.instances["rubro_texto"];
        if (!en) workspace.crear_editor('rubro_texto',{"toolbar":"Basic"});
      },
      "click #rubro_link_2":function() {
        var en = CKEDITOR.instances["rubro_texto_en"];
        if (!en) workspace.crear_editor('rubro_texto_en',{"toolbar":"Basic"});
      },
      "click #rubro_link_3":function() {
        var en = CKEDITOR.instances["rubro_texto_pt"];
        if (!en) workspace.crear_editor('rubro_texto_pt',{"toolbar":"Basic"});
      },
    },

    eliminar : function() {
      if (!confirmar("Realmente desea eliminar este elemento?")) return;
      var self = this;      
      var rubro = new app.models.Rubro({
        "id":self.model.id
      });
      rubro.destroy();
      rubro.fetch({
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

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      $(this.el).find("#rubros_tree").fancytree({
        source: {
          url: 'rubros/function/get_arbol/'
        },
        selectMode: 3,
        checkbox: true,
        renderNode: function(event,data) {
          var node = data.node;
          // Controlamos si el ID esta en los relacionados
          var selected = false;
          var rel = self.model.get("rubros_relacionados");
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
        modelClass: app.models.Rubro,
        url: "rubros/function/get_select/",
        render: "#rubros_padre",
        firstOptions: ["<option value='0'>Ninguno</option>"],
        name : "id_padre",
        selected: this.model.get("id_padre"),
      });        

      // Cuando cambian las imagens, renderizamos la tabla
      this.stopListening();
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();            
      $(this.el).find("#images_tabla").sortable();

      var es = CKEDITOR.instances["rubro_texto"];
      if (es) CKEDITOR.remove(es);
      var en = CKEDITOR.instances["rubro_texto_en"];
      if (en) CKEDITOR.remove(en);
      var pt = CKEDITOR.instances["rubro_texto_pt"];
      if (pt) CKEDITOR.remove(pt);

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("rubros_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        
        // Arbol de categorias de relacionados
        var rubros_relacionados = new Array();
        var rel = $("#rubros_tree").fancytree("getTree").getSelectedNodes();
        for(var i=0;i<rel.length;i++) {
          var o = rel[i];
          rubros_relacionados.push({
            "id":o.key,
          });
        }
        self.model.set({"rubros_relacionados":rubros_relacionados});

        if (!self.model.isNew()) {
          if (self.model.id == self.model.get("id_padre")) {
            alert("La categoria padre no puede ser la misma categoria.");
            return false;
          }
        }

        if (typeof CKEDITOR.instances['rubro_texto'] != "undefined") {
          self.model.set({
            "texto":CKEDITOR.instances['rubro_texto'].getData()
          });
        }
        if (typeof CKEDITOR.instances['rubro_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['rubro_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['rubro_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['rubro_texto_pt'].getData(),
          });
        }

        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
        
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
          "id_padre":$("#rubros_padre").val(),
          "path":self.$("#hidden_path").val(),
        },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Rubro()
      this.render();
    },

  });

})(app.views, app.models);




// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.RubroMiniEditView = app.mixins.View.extend({

    template: _.template($("#rubros_edit_mini_panel_template").html()),

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
        modelClass: app.models.Rubro,
        url: "rubros/function/get_select/",
        render: "#rubros_mini_padre",
        firstOptions: ["<option value='0'>Ninguno</option>"],
        name : "id_padre",
        selected: this.model.get("id_padre"),
      });

      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#rubros_mini_nombre").val($(this.input).val().trim());
      }
      return this;
    },

    focus: function() {
      $(this.el).find("#rubros_mini_nombre").focus();
    },

    validar: function() {
      var self = this;
      try {
        validate_input("rubros_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "nombre":$("#rubros_mini_nombre").val(),
          "id_padre":$("#rubros_mini_padre").val(),
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



(function ( app ) {
  app.views.RubroMoverPadreView = app.mixins.View.extend({
    template: _.template($("#rubro_mover_padre_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .agregar_rubro":function(e) {
        var self = this;
        if ($(".rubro_edit_mini").length > 0) return;
        var form = new app.views.RubroMiniEditView({
          "model": new app.models.Rubro(),
          "callback":function(m){
            self.id_rubro = m;
            self.cargar_rubros();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete rubro_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#rubros_mini_nombre").focus();
      },
      "click .guardar":function() {
        var self = this;
        var id_rubro = self.$("#rubro_mover_padre_rubros").val();
        $.ajax({
          "timeout":0,
          "url":"rubros/function/mover_lote/",
          "dataType":"json",
          "type":"post",
          "data":{
            "rubros":window.rubros_marcados,
            "id_rubro":id_rubro,
          },
          "success":function() {
            location.reload();
          },
          "error":function() {
            alert("Ocurrio un error al cambiar la categoria de los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());
      this.cargar_rubros();
    },
    cargar_rubros: function() {
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/function/get_select/",
        render: "#rubro_mover_padre_rubros",
        firstOptions: ["<option value='0'>Sin categoria padre</option>"],
        selected: self.id_rubro,
      });      
    }
  });
})(app);
