(function ( models ) {

  models.Curso = Backbone.Model.extend({
    urlRoot: "cursos/",
    defaults: {
      nombre: "",
      subtitulo: "",
      autor: "",
      path: "",
      texto: "",
      activo: 1,
      destacado: 0,
      id_categoria: 0,
      categoria: "",
      clases: [],
      etiquetas: [],
      images: [],
      tipo: 0,
      moneda: "",
      precio_final: 0,
      porc_bonif: 0,
      precio_final_dto: 0,
      seo_title: "",
      seo_keywords: "",
      seo_description: "",
      seo_sitemap_priority: "",
      seo_sitemap_change_freq: "",
      seo_sitemap_priority: "",
      seo_sitemap_change_freq: "",
      seo_ocultar_sitemap: 0,
      custom_1: "",
      custom_2: "",
      custom_3: "",
      custom_4: "",
      custom_5: "",
      custom_6: "",
      custom_7: "",
      custom_8: "",
      custom_9: "",
      custom_10: "",
      video: "",
      fecha: "",
      mostrar_fecha: 1,
      archivo: "",
      usuarios: [],
      texto_finalizacion: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.Cursos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cursos/"
    }
  });
})( app.collections, app.models.Curso, Backbone.Paginator);


(function ( app ) {
  app.views.CursoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#cursos_item').html()),
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
          "table":"cursos",
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
          "table":"cursos",
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
      location.href="app/#curso/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    duplicar: function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      if (confirm("Desea duplicar el elemento?")) {
        $.ajax({
          "url":"cursos/function/duplicar/"+self.model.id,
          "dataType":"json",
          "success":function(r){
            //window.location.href = "app/#articulo/"+r.id;
            location.reload();
          },
        });
      }
      return false;      
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.CursosTableView = app.mixins.View.extend({

   template: _.template($("#cursos_panel_template").html()),

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
      var view = new app.views.CursoItem({
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

  views.CursoEditView = app.mixins.View.extend({

    template: _.template($("#cursos_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",

      // USUARIOS
      "click .agregar_cliente":"agregar_cliente",
      "click .eliminar_cliente":"eliminar_cliente",

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "entradas/function/upload_images/",
          "view": self,
        });
      },

      "change #curso_precio_final":function(e) {
        this.editar_precio_final();
      },
      "change #curso_porc_bonif": function(e){
        this.editar_precio_final();
      },

      "click .nuevo_clase":function(){
        var self = this;
        var v = new app.views.CursoClaseEditView({
          model: new app.models.CursoClase(),
          collection: self.clases,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
        });
      },      
    },

    editar_precio_final: function() {
      var precio_final = parseFloat($("#curso_precio_final").val());
      var porc_bonif = $("#curso_porc_bonif").val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      $("#curso_precio_final_dto").val(Number(precio_final_dto).toFixed(2));            
      this.model.set({
        "precio_final":precio_final,
        "porc_bonif":porc_bonif,
        "precio_final_dto":precio_final_dto,
      });
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

      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();

      new app.mixins.Select({
        modelClass: app.models.CursoCategoria,
        url: "cursos_categorias/?offset=9999",
        render: "#curso_categorias",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_categoria"),
        onComplete:function(c) {
          crear_select2("curso_categorias");
        }                    
      });      

      new app.mixins.Select({
        modelClass: app.models.CursoAutor,
        url: "cursos_autores/?offset=9999",
        render: "#curso_autores",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_autor"),
        onComplete:function(c) {
          crear_select2("curso_autores");
        }                    
      });     


      self.cliente = null;
      var input = this.$("#cursos_clientes");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "label":"[email]",
        "form":null,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });

      self.clases = new app.collections.CursosClases();
      var dep = this.model.get("clases");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.CursoClase(dd);
        self.clases.add(ddo);
      }
      this.clasesTable = new app.views.CursosClasesTableView({
        collection: self.clases
      });
      this.$("#cursos_clases").html(this.clasesTable.el);      

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker($(this.el).find("#curso_fecha"),fecha);      

      $(this.el).find("#images_tabla").sortable();

      self.$("#curso_etiquetas").select2({
        tags: true,
        minimumInputLength: 3,
        ajax: {
          url: "cursos_etiquetas/function/get_by_nombre/",
          dataType: 'json',
          delay: 1000,
          data: function (params) {
            return {
              term: params.term,
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;
            return {
              results: data,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
          cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
      });      

      this.render_tabla_clientes();

      return this;
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      self.$("#cursos_clientes").val(self.cliente.get("nombre"));
      setTimeout(function(){
        self.$('#cursos_clientes').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
      self.agregar_cliente();
    },

    agregar_cliente: function() {
      var self = this;
      if (self.cliente == null) {
        alert("Por favor seleccione un cliente.");
        return;
      }
      var usuarios = this.model.get("usuarios");

      // Controlamos que el usuario no exista ya
      var encontro = false;
      var id = self.cliente.id;
      for(var i=0;i<usuarios.length;i++) {
        var u = usuarios[i];
        if (u.id_usuario == id) {
          encontro = true;
          break;
        }
      }
      if (!encontro) {
        // Agregamos al array
        usuarios.push({
          "id_usuario":self.cliente.id,
          "nombre":self.cliente.get("nombre"),
        });
      }
      self.$("#cursos_clientes").val("");
      self.cliente = null;
      this.model.get({"usuarios":usuarios});
      this.render_tabla_clientes();
    },

    eliminar_cliente: function(e) {
      var c = $(e.currentTarget).parents(".cliente");
      var id = $(c).data("id")
      var usuarios = this.model.get("usuarios");
      var usuarios2 = new Array();
      for(var i=0;i<usuarios.length;i++) {
        var u = usuarios[i];
        if (u.id_usuario != id) usuarios2.push(u);
      }
      this.model.set({"usuarios":usuarios2});
      $(c).remove();
    },

    render_tabla_clientes: function() {
      var usuarios = this.model.get("usuarios");
      this.$("#usuarios_tabla").empty();
      for(var i=0;i<usuarios.length;i++) {
        var u = usuarios[i];
        var li = "";
        li+="<li class='cliente oh list-group-item' data-id='"+u.id_usuario+"'>";
        li+=" <span class='mt5 dib'>"+u.nombre+"</span>";
        li+=" <button class='btn cp pull-right btn-white eliminar_cliente'><i class='fa fa-trash'></i></button>";
        li+="</li>";
        this.$("#usuarios_tabla").append(li);
      }
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
        validate_input("cursos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        this.model.set({
          "mostrar_fecha": ((self.$("#curso_mostrar_fecha").length > 0) ? (self.$("#curso_mostrar_fecha").is(":checked")?1:0) : 0),
          "nombre":self.$("#cursos_nombre").val(),
          "subtitulo":self.$("#cursos_subtitulo").val(),
          "id_categoria":self.$("#curso_categorias").val(),
          "texto":CKEDITOR.instances['curso_texto'].getData(),
          "id_autor":self.$("#curso_autores").val(),
          "tipo":self.$("#curso_tipo").val(),
          "moneda":self.$("#curso_monedas").val(),
          "precio_final":self.$("#curso_precio_final").val(),
          "porc_bonif":self.$("#curso_porc_bonif").val(),
          "precio_final_dto":self.$("#curso_precio_final_dto").val(),
          "archivo":self.$("#hidden_archivo").val(),
        });

        if (self.$("#curso_etiquetas").length > 0) {
          var c = self.$("#curso_etiquetas").select2("val");
          this.model.set({ "etiquetas":((c==null)?[]:c) });
        }

        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});

        // Si los custom llegan a ser fileuploaders, hay que setearlos en el modelo
        for(var i=1;i<=10;i++) {
          if ((self.$("#hidden_custom_"+i).length > 0)) {
            var cus = self.$("#hidden_custom_"+i).val();
            var key = "custom_"+i;
            var obj = {};
            obj[key] = cus;
            this.model.set(obj);
          }          
        }     

        var fecha = self.model.get("fecha")+":00";
        self.model.set({"fecha":fecha});

        // Listado de departamentos
        var clases = new Array();
        self.clases.each(function(dpto){
          clases.push(dpto.toJSON());
        });
        self.model.set({"clases":clases});

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
            location.href="app/#cursos";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Curso();
      this.render();
    },

  });

})(app.views, app.models);





(function ( models ) {

  models.CursoClase = Backbone.Model.extend({
    urlRoot: "clases",
    defaults: {
      nombre: "",
      path_clase: "",
      video: "",
      id_empresa: ID_EMPRESA,
      id_curso: 0,
      orden: 0,
      texto: "",
      audio: "",
      custom_1: "",
      custom_2: "",
      custom_3: "",
      custom_4: "",
      custom_5: "",
      custom_6: "",
      custom_7: "",
      custom_8: "",
      custom_9: "",
      custom_10: "",
      preguntas: [],
      respuestas_correctas: 0,

      eliminado: 0,
      insertado: 0,
    },
  });
      
})( app.models );


(function (collections, model) {
  collections.CursosClases = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.CursoClase);


(function ( app ) {

  app.views.CursosClasesTableView = app.mixins.View.extend({

    template: _.template($("#cursos_clases_resultados_template").html()),
        
    myEvents: {
      "change #clases_buscar":"buscar",
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
      var view = new app.views.CursosClasesItemResultados({
        model: item,
        collection: self.collection,
        view: self,
      });
      this.$(".tbody").append(view.render().el);
    },
            
  });

})(app);


(function ( app ) {
  app.views.CursosClasesItemResultados = app.mixins.View.extend({
        
    template: _.template($("#cursos_clases_item_resultados_template").html()),
    tagName: "tr",
    className: function(){
      return (this.model.get("eliminado") == 1)?"dn":"";
    },
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar la clase?")) {
          self.model.set({"eliminado":1});  // Eliminamos el modelo
          self.options.view.addAll();
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.CursoClaseEditView({
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

  app.views.CursoClaseEditView = app.mixins.View.extend({

    template: _.template($("#curso_clase_template").html()),
            
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

      self.preguntas = new app.collections.CursosPreguntas();
      var dep = this.model.get("preguntas");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.CursoPregunta(dd);
        self.preguntas.add(ddo);
      }
      this.preguntasTable = new app.views.CursosPreguntasTableView({
        collection: self.preguntas
      });
      this.$("#cursos_preguntas").html(this.preguntasTable.el);
    },

    validar: function() {
      try {
        var self = this;
        validate_input("curso_clase_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");

        // Si los custom llegan a ser fileuploaders, hay que setearlos en el modelo
        for(var i=1;i<=10;i++) {
          if ((self.$("#hidden_custom_"+i).length > 0)) {
            var cus = self.$("#hidden_custom_"+i).val();
            var key = "custom_"+i;
            var obj = {};
            obj[key] = cus;
            this.model.set(obj);
          }          
        }

        // Listado de preguntas
        var preguntas = new Array();
        self.preguntas.each(function(dpto){
          preguntas.push(dpto.toJSON());
        });
        self.model.set({"preguntas":preguntas});

        this.model.set({
          "nombre":self.$("#curso_clase_nombre").val(),
          "texto":self.$("#curso_clase_texto").val(),
          "video":self.$("#curso_clase_video").val(),
          "orden":self.$("#curso_clase_orden").val(),
          "path_clase":$("#hidden_path_clase").val(),
          "audio":$("#hidden_audio").val(),
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
          this.model.set({
            id:maxId,
            insertado:1,
          });
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);






// ======================================================
// PREGUNTAS
// ======================================================

(function ( models ) {

  models.CursoPregunta = Backbone.Model.extend({
    urlRoot: "preguntas",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_clase: 0,
      pregunta: "",
      respuestas: [],
      orden: 0,
      eliminado: 0,
      insertado: 0,
    },
  });
      
})( app.models );


(function (collections, model) {
  collections.CursosPreguntas = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.CursoPregunta);

(function ( app ) {

  app.views.CursosPreguntasTableView = app.mixins.View.extend({

    template: _.template($("#cursos_preguntas_template").html()),
        
    myEvents: {
      "click .agregar_pregunta":"agregar_pregunta",
      "keypress #curso_preguntas_texto":function(e){
        if (e.which == 13) this.agregar_pregunta();
      }
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

    agregar_pregunta: function() {
      var texto = this.$("#curso_preguntas_texto").val();
      if (isEmpty(texto)) return;
      var model = new app.models.CursoPregunta({
        "pregunta":texto,
        "insertado":1,
        "respuestas":[],
      });
      this.$("#curso_preguntas_texto").val("");
      this.collection.add(model);
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      this.$(".curso_preguntas_table").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.CursosPreguntasItemResultados({
        model: item,
        collection: self.collection,
        view: self,
      });
      this.$(".curso_preguntas_table").append(view.render().el);
    },
            
  });

})(app);


(function ( app ) {
  app.views.CursosPreguntasItemResultados = app.mixins.View.extend({
        
    template: _.template($("#cursos_preguntas_item_resultados_template").html()),
    className: function(){
      return (this.model.get("eliminado") == 1)?"dn":"";
    },
    myEvents: {
      "click .eliminar_pregunta":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar la pregunta?")) {
          self.model.set({"eliminado":1});  // Eliminamos el modelo
          self.options.view.addAll();
        }
        return false;
      },
      "keypress #curso_pregunta_respuesta":function(e){
        if (e.which == 13) this.agregar_respuesta();
      },      
      "click .eliminar_respuesta":"eliminar_respuesta",
      "click .checkbox_respuesta":"seleccionar_respuesta",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      this.render_tabla_respuestas();
      return this;
    },

    agregar_respuesta: function() {
      var self = this;
      var texto = this.$("#curso_pregunta_respuesta").val();
      if (isEmpty(texto)) return;
      var respuestas = this.model.get("respuestas");

      // Agregamos el maximo
      var maxId = 0;
      for(var i=0;i<respuestas.length;i++) {
        var u = respuestas[i];
        if (u.id > maxId) maxId = u.id;
      }
      maxId++;

      respuestas.push({
        "id":maxId,
        "respuesta":texto,
        "correcta":0,
        "insertado":1,
        "eliminado":0,
      });
      self.$("#curso_pregunta_respuesta").val("");
      this.model.set({"respuestas":respuestas});
      this.render_tabla_respuestas();
    },

    eliminar_respuesta: function(e) {
      if (!confirm("Desea borrar la respuesta?")) return;
      var c = $(e.currentTarget).parents(".respuesta");
      var id = $(c).data("id");
      _.each(this.model.get("respuestas"),function(r){
        if (r.id == id) r.eliminado = 1;
      })
      $(c).remove();
    },

    seleccionar_respuesta: function() {
      var self = this;
      this.$(".respuesta").each(function(i,e){
        var id = $(e).data("id");
        var correcta = ($(e).find(".checkbox_respuesta").is(":checked")) ? 1 : 0;
        _.each(self.model.get("respuestas"),function(r){
          if (r.id == id) r.correcta = correcta;
        })
      });
    },

    render_tabla_respuestas: function() {
      var respuestas = this.model.get("respuestas");
      this.$(".curso_pregunta_respuestas").empty();
      for(var i=0;i<respuestas.length;i++) {
        var u = respuestas[i];
        if (u.eliminado == 1) continue;
        var li = "";
        li+="<li class='respuesta oh list-group-item' data-id='"+u.id+"'>";
        li+=" <span class='mt5 dib'>";
        li+= "<input type='radio' "+((u.correcta == 1)?"checked":"")+" class='checkbox_respuesta mr10' id='respuesta_"+u.id+"' name='pregunta_"+this.model.id+"'/>";
        li+= "<label for='respuesta_"+u.id+"'>"+u.respuesta+"</label></span>";
        li+=" <a href='javascript:void(0)' class='btn cp pull-right eliminar_respuesta'><i class='fa fa-times text-danger'></i></a>";
        li+="</li>";
        this.$(".curso_pregunta_respuestas").append(li);
      }
    }, 

  });
})(app);







// =======================================================

(function ( models ) {

  models.CursoCategoria = Backbone.Model.extend({
    urlRoot: "cursos_categorias/",
    defaults: {
      nombre: "",
      activo: 1,
      destacado: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.CursosCategorias = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cursos_categorias/"
    }
  });
})( app.collections, app.models.CursoCategoria, Backbone.Paginator);


(function ( app ) {
  app.views.CursoCategoriaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#cursos_categorias_item').html()),
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
          "table":"cursos_categorias",
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
          "table":"cursos_categorias",
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
      location.href="app/#curso_categoria/"+this.model.id;
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

  app.views.CursosCategoriasTableView = app.mixins.View.extend({

   template: _.template($("#cursos_categorias_panel_template").html()),

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
      var view = new app.views.CursoCategoriaItem({
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

  views.CursoCategoriaEditView = app.mixins.View.extend({

    template: _.template($("#cursos_categorias_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
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

      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("cursos_categorias_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
        },{
          success: function(model,response) {
            location.href="app/#cursos_categorias";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CursoCategoria();
      this.render();
    },

  });

})(app.views, app.models);



// ===========================================================================

(function ( models ) {

  models.CursoAutor = Backbone.Model.extend({
    urlRoot: "cursos_autores/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
      nombre: "",
      path: "",
      texto: "",
      telefono: "",
      email: "",
      web: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.CursosAutores = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cursos_autores/"
    }
  });
})( app.collections, app.models.CursoAutor, Backbone.Paginator);


(function ( app ) {
  app.views.CursoAutorItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cursos_autores_item').html()),
    events: {
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
      location.href="app/#curso_autor/"+this.model.id;
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

  app.views.CursosAutoresTableView = app.mixins.View.extend({

   template: _.template($("#cursos_autores_panel_template").html()),

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
      var view = new app.views.CursoAutorItem({
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

  views.CursoAutorEditView = app.mixins.View.extend({

    template: _.template($("#cursos_autores_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .nuevo": "limpiar",
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
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

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        this.model.set({
          "path":self.$("#hidden_path").val(),
          "tipo":self.$("#cursos_autores_tipo").val(),
        });
        
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
        },{
          success: function(model,response) {
            location.href="app/#cursos_autores";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CursoAutor();
      this.render();
    },

  });

})(app.views, app.models);