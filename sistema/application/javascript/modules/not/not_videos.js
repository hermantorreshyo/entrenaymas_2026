// -----------
//   MODELO
// -----------

(function ( models ) {

  models.NotVideo = Backbone.Model.extend({
    urlRoot: "not_videos",
    defaults: {
      // Atributos que no se persisten directamente
      categoria: "",
      
      titulo: "",
      titulo_pt: "",
      titulo_en: "",
      subtitulo: "",
      subtitulo_pt: "",
      subtitulo_en: "",
      id_evento: 0,
      id_cliente: 0,
      id_categoria: 0,
      fecha: "",
      mostrar_fecha: 1,
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
      link: "",
      activo: 1,
      destacado: 0,
      texto: "",
      texto_en: "",
      texto_pt: "",
      link_youtube: "",
      seo_title: "",
      seo_keywords: "",
      seo_description: "",      
    },
  });
      
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.NotVideos = paginator.requestPager.extend({
      
    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'A.fecha',
      order: 'desc',
    },
    
    paginator_core: {
      url: "not_videos/function/ver",
    }
      
  });

})( app.collections, app.models.NotVideo, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.NotVideosTableView = app.mixins.View.extend({

    template: _.template($("#not_videos_resultados_template").html()),
            
    myEvents: {
      "change #not_videos_buscar":"buscar",
      "click #not_videos_buscar_avanzada_btn":"buscar_avanzada",
      "keydown #not_videos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#not_videos_texto").focus(); }
      },
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.not_videos_filter = (typeof window.not_videos_filter != "undefined") ? window.not_videos_filter : "";
      window.not_videos_id_categoria = (typeof window.not_videos_id_categoria != "undefined") ? window.not_videos_id_categoria : 0;
      window.not_videos_fecha = (typeof window.not_videos_fecha != "undefined") ? window.not_videos_fecha : "";
      window.not_videos_page = (typeof window.not_videos_page != "undefined") ? window.not_videos_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },
        
    render: function() {
            
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
            
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
            
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(this.pagination.el);

      // Se clona el array con slice(0), porque sino quedaba el TODAS en el detalle
      var cat = categorias_noticias.slice(0);
      cat.unshift({
        children: [],
        fija: 0,
        id: 0,
        id_padre: 0,
        key: "",
        nombre_es: "Todas",
        title: "Todas",
      });    
      var r = workspace.crear_select(cat,"",window.not_videos_id_categoria);
      this.$("#not_videos_buscar_categorias").html(r).select2({}).change(function(){
        window.not_videos_id_categoria = $(this).val();
      });
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.not_videos_filter != this.$("#not_videos_buscar").val().trim()) {
        window.not_videos_filter = this.$("#not_videos_buscar").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.not_videos_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.not_videos_filter),
        "id_categoria":window.not_videos_id_categoria,
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.not_videos_page);
    },
        
    buscar_avanzada: function() {
      var self = this;
      // Buscamos por categoria
      var c = self.$("#not_videos_buscar_categorias").val();
      self.id_categoria = c;
      this.buscar();
    },
        
    addAll : function () {
      window.not_videos_page = this.pagination.getPage();
      $(this.el).find(".tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (this.collection.length > 0) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        } else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var view = new app.views.NotVideosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.NotVideosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#not_videos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .notificar_email":function() {
        var self = this;
        $.ajax({
          "url":"not_videos/function/notificar_email/"+self.model.id,
          "dataType":"json",
          "success":function(r){
            alert(r.mensaje);
            if (r.error == 0) location.reload();
          }
        });
      },
      "keyup .radio":function(e) {
        if (e.which == 13) { this.seleccionar(); }
        e.stopPropagation();
      },
      "focus .radio":function(e) {
        $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
        e.stopPropagation();
        e.preventDefault();
        return false;
      },
      "blur .radio":function(e) {
        $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
        e.stopPropagation();
        e.preventDefault();
        return false;
      },
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"not_videos",
          "url":"not_videos/function/change_property/",
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
          "table":"not_videos",
          "url":"not_videos/function/change_property/",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"not_videos/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
            },
          });                    
        }
        return false;
      },
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
      if (this.habilitar_seleccion) {
        window.not_video_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#not_video/"+this.model.id;
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.render();
    },
    render: function() {
      var obj = { seleccionar: this.habilitar_seleccion };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);


// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.NotVideoEditView = app.mixins.View.extend({

    template: _.template($("#not_video_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .guardar_borrador": "guardar_borrador",
      "click .previsualizar": function(){
        this.previsualizar(false);
      },

      "click .agregar_categoria":function(e) {
        var self = this;
        if ($(".categoria_edit_mini").length > 0) return;
        var form = new app.views.CategoriaEntradaMiniEditView({
          "model": new app.models.CategoriaEntrada(),
          "callback":function(m){
            var that = self;
            self.model.set({ "id_categoria":m });
            $.ajax({
              "url":"categorias_entradas/function/get_arbol/",
              "dataType":"json",
              "success":function(r){
                categorias_noticias = r;
                that.cargar_categorias_entradas();
              },
            });
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete categoria_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#categorias_entradas_mini_nombre").focus();
      },

      "click #not_video_link_2":function() {
        if (typeof CKEDITOR.instances["not_video_texto_en"] == "undefined") { 
          workspace.crear_editor('not_video_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #not_video_link_3":function() {
        if (typeof CKEDITOR.instances["not_video_texto_pt"] == "undefined") {
          workspace.crear_editor('not_video_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
    },
        
    cargar_categorias_entradas: function() {
      var self = this;
      var r = workspace.crear_select(categorias_noticias,"",self.model.get("id_categoria"));
      this.$("#not_video_categorias").html(r);
    }, 

    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
        
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
        
      this.cargar_categorias_entradas();         

      new app.mixins.Select({
        modelClass: app.models.NotEvento,
        url: "not_eventos/",
        render: "#not_video_eventos",
        firstOptions: ["<option value='0'>-</option>"],
        campoSelect: "titulo",
        selected: self.model.get("id_evento"),
        onComplete:function(c) {
          crear_select2("not_video_eventos");
        }                    
      });

      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/?tipo=0",
        render: "#not_video_clientes",
        name : "id_cliente",
        firstOptions: ["<option value='0'>-</option>"],
        selected: this.model.get("id_cliente"),
      });

      var fecha = this.model.get("fecha");
      // Siempre que se edita se toma la nueva fecha
      if (ID_EMPRESA == 225) fecha = new Date();
      else if (isEmpty(fecha)) fecha = new Date();
      createtimepicker($(this.el).find("#not_video_fecha"),fecha);
    },

    validar: function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      try {
        var self = this;
        
        var titulo = this.$("#not_video_titulo").val();
        if (isEmpty(titulo)) {
          if (silence) return false;
          else {
            alert("Por favor, ingrese un titulo.");
            this.$("#not_video_titulo").focus();
            return false;
          }
        }
        titulo = titulo.replace(/\'/g,"&#039;");
        titulo = titulo.replace(/\"/g,"&quot;");
        this.model.set({
          "titulo":titulo,
        });
          
        var id_categoria = self.$("#not_video_categorias").val();
        if (id_categoria == null) id_categoria = 0;
        this.model.set({
          "id_cliente": ((self.$("#not_video_clientes").length > 0) ? self.$("#not_video_clientes").val() : 0),
          "seo_title": (self.$("#not_video_seo_title").length > 0) ? self.$("#not_video_seo_title").val() : "",
          "seo_description": (self.$("#not_video_seo_description").length > 0) ? self.$("#not_video_seo_description").val() : "",
          "seo_keywords": (self.$("#not_video_seo_keywords").length > 0) ? self.$("#not_video_seo_keywords").val() : "",
          "id_categoria":id_categoria,
          "id_evento":$("#not_video_eventos").val(),
          "categoria":self.$("#not_video_categorias option:selected").text(),
          "mostrar_fecha": ((self.$("#not_video_mostrar_fecha").length > 0) ? (self.$("#not_video_mostrar_fecha").is(":checked")?1:0) : 0),
        });
        var fecha = self.model.get("fecha")+":00";
        self.model.set({"fecha":fecha});
        
        // Texto del not_video
        self.model.set({
          "texto":CKEDITOR.instances['not_video_texto'].getData(),
        });
        if (typeof CKEDITOR.instances['not_video_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['not_video_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['not_video_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['not_video_texto_pt'].getData(),
          });
        }
        
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  
  
    guardar:function() {
      if (this.validar()) {
        this.model.set({
          "activo":1,
        });
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              history.back();
            }
          }
        });
      }      
    },    

    guardar_borrador:function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      if (this.validar()) {
        var activo = 0;
        if (this.model.id == null) {
          this.model.set({ id:0 });
        } else {
          activo = this.model.get("activo");
        }
        this.model.save({
          "activo":activo,
        },{
          success: function(model,response) {
            if (!silence) {
              if (response.error == 1) {
                show(response.mensaje);
                return;
              } else {
                history.back();
              }
            }
          }
        });
      }      
    },      

    previsualizar:function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      var self = this;
      if (this.validar(silence)) {
        var activo = 0;
        if (this.model.id == null) {
          this.model.set({
            "id":0,
            "activo":0,
          });
        } else {
          activo = this.model.get("activo");
        }
        this.model.save({
          "activo":activo,
        },{
          success: function(model,response) {
            if (!silence) {
              if (response.error == 1) {
                show(response.mensaje);
                return;
              } else {
                var link = "http://"+String(DOMINIO+''+self.model.get("link")+'?preview=1').replace('//','/');
                window.open(link,"_blank");
              }
            }
          }
        });
      }      
    },
  
  });
})(app);
