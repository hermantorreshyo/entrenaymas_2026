// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Entrada = Backbone.Model.extend({
    urlRoot: "entradas",
    defaults: {
      // Atributos que no se persisten directamente
      images: [],
      relacionados: [], // Productos relacionados
      categorias_relacionados: [], // Categorias relacionadas
      categoria: "",
      usuario: "",
      etiquetas: [],
      preguntas: [],
      horarios: [],
      
      titulo: "",
      titulo_pt: "",
      titulo_en: "",
      subtitulo: "",
      subtitulo_pt: "",
      subtitulo_en: "",
      antetitulo: "",
      descripcion: "",
      descripcion_en: "",
      descripcion_pt: "",
      id_categoria: 0,
      id_cliente: 0,
      id_evento: 0,
      fecha: "",
      mostrar_fecha: 1,
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,

      activo: 1,
      destacado: 0,
      texto: "",
      texto_en: "",
      texto_pt: "",
      video: "",
      path: "",
      path_2: "",
      eliminada: 0,
      eliminada_fecha: "",
      privada: 0,
      id_comision: 0,
      id_editor: 0,
      
      seo_title: "",
      seo_keywords: "",
      seo_description: "",
      
      latitud: 0,
      longitud: 0,
      zoom: 15,
      fuente: "",
      
      relacionados_tipo: "U", // U = Ultimos - A = Aleatorio
      relacionados_cantidad: 3, // Cantidad de elementos por categoria que se muestran
      
      comentarios: [],
      comentarios_activo: 1,
      archivo: "",
      texto_destacado: "",
      link_externo: "",

      direccion: "",
      localidad: "",
      id_pais: 0,
      logo: "",
      habilitar_contacto: 0,
      nivel_importancia: 0,

      habilitar_emociones: (ID_EMPRESA == 225) ? 1 : 0,
      emocion_1_label: (ID_EMPRESA == 225) ? "Me encanta" : "",
      emocion_1_cant: 0,
      emocion_2_label: (ID_EMPRESA == 225) ? "Me alegra" : "",
      emocion_2_cant: 0,
      emocion_3_label: (ID_EMPRESA == 225) ? "Me enoja" : "",
      emocion_3_cant: 0,
      emocion_4_label: (ID_EMPRESA == 225) ? "Me entristece" : "",
      emocion_4_cant: 0,

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
      custom_11: "",
      custom_12: "",
      custom_13: "",
      custom_14: "",
      custom_15: "",
      custom_16: "",
      custom_17: "",
      custom_18: "",
      custom_19: "",
      custom_20: "",

      seo_sitemap_priority: 0,
      seo_sitemap_change_freq: "",
      seo_ocultar_sitemap: 0,
    },
  });
      
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Entradas = paginator.requestPager.extend({
      
    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'A.fecha',
      order: 'desc',
    },
    
    paginator_core: {
      url: "entradas/function/ver",
    }
      
  });

})( app.collections, app.models.Entrada, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.EntradasTableView = app.mixins.View.extend({

    template: _.template($("#entradas_resultados_template").html()),
            
    myEvents: {
      "change #entradas_buscar":"buscar",
      "click #entradas_buscar_avanzada_btn":"buscar_avanzada",
      "click .enviar": "enviar",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "click .eliminar_lote":"eliminar_lote",
      "click .destacar_lote":"destacar_lote",
      "click .activar_lote":"activar_lote",
      "keydown #entradas_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#entradas_texto").focus(); }
      },
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.entradas_filter = (typeof window.entradas_filter != "undefined") ? window.entradas_filter : "";
      window.entradas_id_categoria = (typeof window.entradas_id_categoria != "undefined") ? window.entradas_id_categoria : 0;
      window.entradas_fecha = (typeof window.entradas_fecha != "undefined") ? window.entradas_fecha : "";
      window.entradas_page = (typeof window.entradas_page != "undefined") ? window.entradas_page : 1;
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
      if (!((MILLING == 1) && (PERFIL == 344 || PERFIL == 349))) {
        cat.unshift({
          children: [],
          fija: 0,
          id: 0,
          id_padre: 0,
          key: "",
          nombre_es: "-",
          title: "-",
        });
      }
      var r = workspace.crear_select(cat,"",window.entradas_id_categoria,function(item){
        if ((MILLING == 1) && (PERFIL == 344 || PERFIL == 349)) {
          return (item.title == "Blog");
        } else return true;
      });
      this.$("#entradas_buscar_categorias").html(r).select2({}).change(function(){
        window.entradas_id_categoria = $(this).val();
      });
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.entradas_filter != this.$("#entradas_buscar").val().trim()) {
        window.entradas_filter = this.$("#entradas_buscar").val().trim();
        cambio_parametros = true;
      }

      if (window.entradas_id_categoria != this.$("#entradas_buscar_categorias").val()) {
        window.entradas_id_categoria = this.$("#entradas_buscar_categorias").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.entradas_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.entradas_filter),
        "id_categoria":window.entradas_id_categoria,
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.entradas_page);
    },
        
    buscar_avanzada: function() {
      var self = this;
      // Buscamos por categoria
      var c = self.$("#entradas_buscar_categorias").val();
      self.id_categoria = c;
      this.buscar();
    },
        
    addAll : function () {
      window.entradas_page = this.pagination.getPage();
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
      var view = new app.views.EntradasItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"entradas"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        
        
    exportar: function(obj) {
      // Reemplazamos el ver por el exportar
      var url = this.collection.url;
      url = url.replace("/ver/","/exportar/");
      // Los parametros de orden se envian por GET
      url += "?order="+this.collection.paginator_ui.order+"&order_by="+this.collection.paginator_ui.order_by;
      window.open(url,"_blank");
    },
        
    exportar_csv: function(obj) {
      window.open("entradas/function/exportar_csv/","_blank");
    },        
        
    enviar: function() {
      var self = this;
      var checks = this.$("#entradas_tabla .check-row:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var art = self.collection.get(id);
        links_adjuntos.push({
          tipo: TIPO_ADJUNTO_ARTICULO,
          id_objeto: id,
          nombre: art.get("nombre"),
        });
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Fichas de Productos",
      });
      workspace.nuevo_email(email);
    },
        
    eliminar_lote: function() {
      var self = this;
      var checks = this.$("#entradas_tabla .check-row:checked");
      if (checks.length == 0) return;
      if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
        $(checks).each(function(i,e){
          var id = $(e).val();
          var art = self.collection.get(id);
          art.destroy();  // Eliminamos el modelo
          $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
        });
      }            
    },
    activar_lote: function() {
        
    },
    destacar_lote: function() {
        
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.EntradasItemResultados = app.mixins.View.extend({
        
    template: _.template($("#entradas_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .notificar_email":function() {
        var self = this;
        $.ajax({
          "url":"entradas/function/notificar_email/"+self.model.id,
          "dataType":"json",
          "success":function(r){
            alert(r.mensaje);
            if (r.error == 0) location.reload();
          }
        });
      },
      "click .notificar_laboral_gym":function() {
        var self = this;
        $.ajax({
          "timeout":0,
          "url":"laboral_gym/function/notificar/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id_entrada":self.model.id
          },
          "success":function(r){
            alert(r.mensaje);
          }
        });
      },
      "click .notificar":function() {
        var self = this;
        $.ajax({
          "url":"entradas/function/notificar/"+self.model.id,
          "dataType":"json",
          "success":function(r){
            alert(r.mensaje);
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
          "table":"not_entradas",
          "url":"entradas/function/change_property/",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .nuevo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nuevo = this.model.get("nuevo");
        nuevo = (nuevo == 1)?0:1;
        self.model.set({"nuevo":nuevo});
        this.change_property({
          "table":"not_entradas",
          "url":"entradas/function/change_property/",
          "attribute":"nuevo",
          "value":nuevo,
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
          "table":"not_entradas",
          "url":"entradas/function/change_property/",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .nivel_importancia_0":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nivel_importancia = 0;
        self.model.set({"nivel_importancia":nivel_importancia});
        $.ajax({
          "url":"entradas/function/set_nivel_importancia/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":self.model.id,
            "value":nivel_importancia,
            "destacado":0,
          },
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .nivel_importancia_1":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nivel_importancia = 1;
        self.model.set({"nivel_importancia":nivel_importancia});
        $.ajax({
          "url":"entradas/function/set_nivel_importancia/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":self.model.id,
            "value":nivel_importancia,
            "destacado":1,
          },
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .nivel_importancia_2":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nivel_importancia = 2;
        self.model.set({"nivel_importancia":nivel_importancia});
        $.ajax({
          "url":"entradas/function/set_nivel_importancia/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":self.model.id,
            "value":nivel_importancia,
            "destacado":1,
          },
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
            "url":"entradas/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
            },
          });                    
        }
        return false;
      },
      "click .compilar_html":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea compilar la pagina?")) {
          window.open("entradas/function/compilar/"+this.model.id,"_blank");
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
        //window.codigo_entrada_seleccionado = this.model.get("codigo");
        window.entrada_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#entrada/"+this.model.id;
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

  app.views.EntradaEditView = app.mixins.View.extend({

    template: _.template($("#entrada_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .restaurar": "restaurar",
      "click .guardar_borrador": "guardar_borrador",
      "click .previsualizar": function(){
        this.previsualizar(false);
      },

      "click #expand_capacitaciones":function() {
        this.$("#link_custom_8").trigger("click");
      },
      "click #link_custom_7":function() {
        if (typeof CKEDITOR.instances["entrada_custom_7"] == "undefined") { 
          workspace.crear_editor('entrada_custom_7',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_8":function() {
        if (typeof CKEDITOR.instances["entrada_custom_8"] == "undefined") { 
          workspace.crear_editor('entrada_custom_8',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_9":function() {
        if (typeof CKEDITOR.instances["entrada_custom_9"] == "undefined") { 
          workspace.crear_editor('entrada_custom_9',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_10":function() {
        if (typeof CKEDITOR.instances["entrada_custom_10"] == "undefined") { 
          workspace.crear_editor('entrada_custom_10',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_11":function() {
        if (typeof CKEDITOR.instances["entrada_custom_11"] == "undefined") { 
          workspace.crear_editor('entrada_custom_11',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_12":function() {
        if (typeof CKEDITOR.instances["entrada_custom_12"] == "undefined") { 
          workspace.crear_editor('entrada_custom_12',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_13":function() {
        if (typeof CKEDITOR.instances["entrada_custom_13"] == "undefined") { 
          workspace.crear_editor('entrada_custom_13',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_14":function() {
        if (typeof CKEDITOR.instances["entrada_custom_14"] == "undefined") { 
          workspace.crear_editor('entrada_custom_14',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_custom_16":function() {
        if (typeof CKEDITOR.instances["entrada_custom_16"] == "undefined") { 
          workspace.crear_editor('entrada_custom_16',{
            "toolbar":"Basic"
          });
        }
      },


      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "entradas/function/upload_images/",
          "view": self,
        });
      },

      // TABLA DE PREGUNTAS
      "click #preguntas_agregar":"agregar_pregunta",
      "keypress #entrada_entrevista_segundos":function(e) {
        if (e.which==13) this.agregar_pregunta();
      },
      "click .editar_pregunta":function(e){
        this.editar_pregunta($(e.currentTarget).parents("tr"));
      },
      "click .eliminar_pregunta":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      // TABLA DE HORARIOS
      "click #horarios_agregar":"agregar_horario",
      "keypress #entrada_horarios_hora":function(e) {
        if (e.which==13) this.agregar_horario();
      },
      "click .editar_horario":function(e){
        this.editar_horario($(e.currentTarget).parents("tr"));
      },
      "click .eliminar_horario":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      "click #expand_principal":"expand_principal",
      "click #expand_relacionados":"expand_relacionados",
      "click #expand_mapa":"expand_mapa",

      "keydown #entrada_localidad":function(e) {
        if (e.which == 13) this.get_coords_by_address();
      },

      "click .eliminar_relacionado":function(e) {
        if (confirm("Realmente desea eliminar la relacion?")) {
          $(e.currentTarget).parents("li").remove();
        }
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

      "change #entrada_categorias":function(e) {
        var id = $(e.currentTarget).val();
        var width = (typeof ENTRADA_IMAGE_WIDTH === "undefined") ? 400 : ENTRADA_IMAGE_WIDTH;
        var height = (typeof ENTRADA_IMAGE_HEIGHT === "undefined") ? 400 : ENTRADA_IMAGE_HEIGHT;
        try {
          width = eval("ENTRADA_IMAGE_WIDTH_CATEGORIA_"+id);
          height = eval("ENTRADA_IMAGE_HEIGHT_CATEGORIA_"+id);
        } catch(e) {}
        this.$("#path_width").val(width);
        this.$("#path_height").val(height);
      },

      "click #entrada_link_2":function() {
        if (typeof CKEDITOR.instances["entrada_texto_en"] == "undefined") { 
          workspace.crear_editor('entrada_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #entrada_link_3":function() {
        if (typeof CKEDITOR.instances["entrada_texto_pt"] == "undefined") {
          workspace.crear_editor('entrada_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },

      "click .add_marker": function() {
        this.add_marker(LATITUD,LONGITUD);
      },
      
      "click .activar_comentario":function(e){
        var id = $(e.currentTarget).data("id");
        var estado = ($(e.currentTarget).hasClass("active") ? 0 : 1);
        $.ajax({
          "url":"/sistema/entradas/function/activar_comentario/",
          "type":"post",
          "data":{
            "id":id,
            "estado":estado
          },
          "dataType":"json",
          "success":function(res){
            if (res.error == 0) {
              if (estado==0) $(e.currentTarget).removeClass("active");
              else $(e.currentTarget).addClass("active");
            }
          },
        });
      },
      "click .eliminar_comentario":function(e){
        var id = $(e.currentTarget).data("id");
        if (!confirm("Realmente desea eliminar el comentario?")) return;
        $.ajax({
          "url":"/sistema/entradas/function/eliminar_comentario/",
          "type":"post",
          "data":{
            "id":id,
          },
          "dataType":"json",
          "success":function(res){
            if (res.error == 0) {
              $(e.currentTarget).parents("tr").remove();
            }
          },
        });
      },

      "change #entrada_pais":"get_coords_by_address",
    },
        
    add_marker: function(latitud,longitud) {
      var self = this;
      var coord = new google.maps.LatLng(latitud,longitud);    
      this.marker = new google.maps.Marker({
        position: coord,
        map: self.map,
        draggable:true,
        title:"Arrastralo a la direccion correcta"
      });
      google.maps.event.addListener(this.marker, "dblclick", function (e) { 
        if (confirm("Realmente desea eliminar el marcador?")) {
          self.marker.setMap(null);
        }
      });
    },

    cargar_comisiones: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Comision,
        url: "comisiones/?offset=9999",
        render: "#entradas_comisiones",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_comision"),
        onComplete:function(c) {
          crear_select2("entradas_comisiones");
        }                    
      });
    },  

    cargar_editores: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.NotEditor,
        url: "not_editores/?offset=9999",
        render: "#entrada_editores",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_editor"),
        onComplete:function(c) {
          crear_select2("entrada_editores");
        }                    
      });
    },  

    cargar_categorias_entradas: function() {
      var self = this;
      var r = workspace.crear_select(categorias_noticias,"",self.model.get("id_categoria"),function(item){
        if ((MILLING == 1) && (PERFIL == 344 || PERFIL == 349)) {
          return (item.title == "Blog");
        } else return true;
      });
      this.$("#entrada_categorias").html(r);
    }, 

    // Cuando expandimos por primera vez el panel principal
    expand_principal: function() {
      var self = this;
      if (this.expand_principal_key == 1) return;
      this.expand_principal_key = 1;

      /*
      
        $(this.el).find("#entrada_etiquetas").select2({
          tags: true,
        });
      

      // Cargamos las etiquetas con AJAX
      $.ajax({
        "url":"entradas_etiquetas/",
        "dataType":"json",
        "success":function(r) {
          var etiquetas = self.model.get("etiquetas");
          for(var i=0;i<r.results.length;i++) {
            var a = r.results[i];
            var encontro = false;
            for(var j=0;j<etiquetas.length;j++) {
              var et = etiquetas[j];
              if (et == a.nombre) encontro = true;
            }
            if (!encontro) $("#entrada_etiquetas").append("<option>"+a.nombre+"</option>");
          }
          $("#entrada_etiquetas").trigger("change");
        }
      });
      */
    },

    expand_capacitaciones: function() {
      var self = this;
      if (this.expand_capacitaciones_key == 1) return;
      this.expand_capacitaciones_key = 1;
      self.$("#link_custom_7").trigger("click");
    },

    // Cuando expandimos por primera vez el panel de relacionados
    expand_relacionados: function() {

      var self = this;
      if (this.expand_relacionados_key == 1) return;
      this.expand_relacionados_key = 1;

      $(this.el).find("#entradas_categorias_tree").fancytree({
        source: {
          url: 'categorias_entradas/function/get_arbol/'
        },
        selectMode: 3,
        checkbox: true,
        renderNode: function(event,data) {
          var node = data.node;
          
          // Controlamos si el ID esta en los relacionados
          var selected = false;
          var rel = self.model.get("categorias_relacionados");
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
        
      // AUTOCOMPLETE DE ARTICULOS
      // -------------------------
      var input = $(this.el).find("#entradas_buscar_productos");
      $(input).customcomplete({
        "url":"/sistema/entradas/function/ver/",
        "form":null, // No quiero que se creen nuevos productos
        "label":"titulo",
        "info":"subtitulo",
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          var tr = "";
          tr+="<li class='list-group-item'>";
          tr+="<span class='id dn'>"+item.value+"</span>";
          tr+="<span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          tr+="<img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/"+item.path+"'/>";
          tr+="<span class='filename'>"+item.label+"</span>";
          tr+="<span class='pull-right m-t eliminar_foto'><i class='fa fa-fw fa-times'></i> </span>";
          tr+="</li>";
          $("#entradas_tabla_relacionados").append(tr);
          self.$("#entradas_buscar_productos").val("");
        }
      });  
    },

    // Cuando expandimos por primera vez el panel de ubicacion
    expand_mapa: function() {

      var self = this;
      if (this.expand_mapa_key == 1) return;
      this.expand_mapa_key = 1;

      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {
        console.log(e);
      }
      
      setTimeout(function(){
        if (self.map == undefined) self.render_map();
        google.maps.event.trigger(self.map, "resize");
        self.map.setCenter(self.coor);
      },100);

      // SELECT DE PAISES
      if (self.$("#entrada_pais option").length == 0) {
        new app.mixins.Select({
          modelClass: app.models.Pais,
          url: "paises/",
          render: "#entrada_pais",
          firstOptions: ["<option value='0'>Pais</option>"],
          selected: self.model.get("id_pais"),
          onComplete:function(c) {
            crear_select2("entrada_pais");
          },
        });
      }
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
        
      // Llaves de los paneles
      this.expand_principal_key = 0;
      this.expand_relacionados_key = 0;
      this.expand_mapa_key = 0;
      this.expand_capacitaciones_key = 0;

      this.cargar_categorias_entradas();
      if (this.$("#entrada_editores").length > 0) this.cargar_editores();
        
      var fecha = this.model.get("fecha");
      // Siempre que se edita se toma la nueva fecha
      if (ID_EMPRESA == 225) fecha = new Date();
      else if (isEmpty(fecha)) fecha = new Date();
      createtimepicker($(this.el).find("#entrada_fecha"),fecha);

      if (this.$("#entrada_horarios_fecha").length > 0) {
        createdatepicker($(this.el).find("#entrada_horarios_fecha"),new Date());  
        this.$("#entrada_horarios_hora").mask("99:99");
      }
      
      // Cuando cambian las imagens, renderizamos la tabla
      this.stopListening();
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();            
        
      $(this.el).find("#images_tabla").sortable();
      $(this.el).find("#entradas_tabla_relacionados").sortable();

      if (ID_EMPRESA == 70) {
        try {
          loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
        } catch(e) {
          console.log(e);
        }
        setTimeout(function(){
          if (self.map == undefined) return;
          google.maps.event.trigger(self.map, "resize");
          self.map.setCenter(self.coor);
        },100);
      }

      if (ID_EMPRESA == 225) this.expand_principal();

      if (this.$("#entradas_clientes").length > 0) {
        new app.mixins.Select({
          modelClass: app.models.Cliente,
          url: "clientes/?tipo=0",
          render: "#entradas_clientes",
          name : "id_cliente",
          firstOptions: ["<option value='0'>-</option>"],
          selected: this.model.get("id_cliente"),
        });
      }

      // Milling and Grain
      if (MILLING == 1) {
        var proximos = (typeof this.model.id == "undefined") ? "1" : "-1";
        new app.mixins.Select({
          modelClass: app.models.NotEvento,
          url: "not_eventos/function/ver/?categoria=0&offset=9999&proximos="+proximos,
          render: "#entradas_eventos",
          name : "custom_1",
          campoSelect: "titulo",
          firstOptions: ["<option value='0'>-</option>"],
          selected: this.model.get("custom_1"),
        });
        new app.mixins.Select({
          modelClass: app.models.NotEvento,
          url: "not_eventos/function/ver/?categoria=1&offset=9999&proximos="+proximos,
          render: "#entradas_training",
          name : "custom_2",
          campoSelect: "titulo",
          firstOptions: ["<option value='0'>-</option>"],
          selected: this.model.get("custom_2"),
        });
      }

      if (ID_PROYECTO == 5) {
        this.cargar_comisiones();
      }

      if (self.$("#entrada_etiquetas").length > 0) { 
        self.$("#entrada_etiquetas").select2({
          tags: true,
          minimumInputLength: 3,
          ajax: {
            url: "entradas_etiquetas/function/get_by_nombre/",
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
      }      

      // Autoguardado cada 30 segundos
      // Primero se hace un setTimeout para que el window.timer no se elimine por workspace route
      /*
      setTimeout(function(){
        if (typeof window.timer !== "undefined") {
          window.clearInterval(window.timer);
        }
        window.timer = setInterval(function(){
          self.previsualizar(true);
        },30000);
      },30000);
      */

      this.$("#entrada_categorias").trigger("change");
    },

    get_coords_by_address: function() {
      var self = this;
      if (self.map == undefined) return;
      var calle = $("#entrada_localidad").val();
      var pais = $("#entrada_pais option:selected").text();
      if (pais == "Pais") pais = "";
      if (isEmpty(calle)) {
        alert("Por favor ingrese una calle o localidad");
        $("#entrada_localidad").focus();
        return;
      }
      var address = calle + ((!isEmpty(pais)) ? ", "+pais : "");
      self.geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          var location = results[0].geometry.location;
          var latitud = location.lat();
          var longitud = location.lng();
          self.add_marker(latitud,longitud);
          self.map.setCenter(location);
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },


    agregar_horario: function() {

      var fecha = $("#entrada_horarios_fecha").val();
      if (isEmpty(fecha)) {
        alert("Por favor ingrese una fecha");
        $("#entrada_horarios_fecha").focus();
        return;
      }
      var hora = $("#entrada_horarios_hora").val();
      if (isEmpty(hora)) {
        alert("Por favor ingrese una hora");
        $("#entrada_horarios_hora").focus();
        return;
      }
      var tr = "<tr>";
      tr+="<td class='editar_horario fecha'>"+fecha+"</td>";
      tr+="<td class='editar_horario hora'>"+hora+"</td>";
      tr+="<td><i class='fa fa-times eliminar_horario text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.horario_item == null) {
        $("#horarios_tabla tbody").append(tr);
      } else {
        $(this.horario_item).replaceWith(tr);
        this.horario_item = null;
      }
      $("#entrada_horarios_fecha").val("");
      $("#entrada_horarios_hora").val("");
      $("#entrada_horarios_fecha").focus();
    },

    editar_horario: function(r) {
      this.horario_item = r;
      $("#entrada_horarios_fecha").val($(r).find(".fecha").text());
      $("#entrada_horarios_hora").val($(r).find(".hora").text());
      this.$("#entrada_horarios_fecha").select();
    },


    agregar_pregunta: function() {

      var pregunta = $("#entrada_entrevista_pregunta").val();
      if (isEmpty(pregunta)) {
        alert("Por favor ingrese una pregunta");
        $("#entrada_entrevista_pregunta").focus();
        return;
      }
      var respuesta = $("#entrada_entrevista_respuesta").val();
      if (isEmpty(respuesta)) {
        alert("Por favor ingrese una respuesta");
        $("#entrada_entrevista_respuesta").focus();
        return;
      }
      var segundos = $("#entrada_entrevista_segundos").val();

      var tr = "<tr>";
      tr+="<td class='editar_pregunta pregunta'>"+pregunta+"</td>";
      tr+="<td class='editar_pregunta respuesta'>"+respuesta+"</td>";
      tr+="<td class='editar_pregunta segundos'>"+segundos+"</td>";
      tr+="<td><i class='fa fa-times eliminar_pregunta text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.pregunta_item == null) {
        $("#preguntas_tabla tbody").append(tr);
      } else {
        $(this.pregunta_item).replaceWith(tr);
        this.pregunta_item = null;
      }
      $("#entrada_entrevista_pregunta").val("");
      $("#entrada_entrevista_respuesta").val("");
      $("#entrada_entrevista_segundos").val("");
      $("#entrada_entrevista_pregunta").focus();
    },

    editar_pregunta: function(r) {
      this.pregunta_item = r;
      $("#entrada_entrevista_pregunta").val($(r).find(".pregunta").text());
      $("#entrada_entrevista_respuesta").val($(r).find(".respuesta").text());
      $("#entrada_entrevista_segundos").val($(r).find(".segundos").text());
      this.$("#entrada_entrevista_pregunta").select();
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
        
    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud");
      var longitud = self.model.get("longitud");
      var zoom = parseInt(self.model.get("zoom"));
      if (latitud == 0 && longitud == 0) {
        latitud = LATITUD;
        longitud = LONGITUD;
        zoom: 12;
      }
      self.geocoder = new google.maps.Geocoder();
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(self.$("#mapa")[0], mapOptions);
      
      // Si tiene seteado coordenadas, ponemos el marcador
      if (self.model.get("latitud") != 0 && self.model.get("longitud") != 0) {
        self.add_marker(self.model.get("latitud"),self.model.get("longitud"));
      }
    },        
        
    validar: function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      try {
        var self = this;
        
        var titulo = this.$("#entrada_titulo").val();
        if (isEmpty(titulo)) {
          if (silence) return false;
          else {
            alert("Por favor, ingrese un titulo.");
            this.$("#entrada_titulo").focus();
            return false;
          }
        }
        titulo = titulo.replace(/\'/g,"&#039;");
        titulo = titulo.replace(/\"/g,"&quot;");
        this.model.set({
          "titulo":titulo,
        });
        if (this.$("#entrada_nivel_importancia").length > 0) {
          var nivel_importancia = this.$("#entrada_nivel_importancia").val();
          this.model.set({
            "nivel_importancia":nivel_importancia,
            "destacado":((nivel_importancia > 0)?1:0),
          });
        }

        var preguntas = new Array();
        $("#preguntas_tabla tbody tr").each(function(i,e){
          preguntas.push({
            "pregunta": $(e).find(".pregunta").text(),
            "respuesta": $(e).find(".respuesta").text(),
            "segundos": $(e).find(".segundos").text(),
          });
        });
        this.model.set({"preguntas":preguntas});

        var horarios = new Array();
        $("#horarios_tabla tbody tr").each(function(i,e){
          horarios.push({
            "fecha": $(e).find(".fecha").text(),
            "hora": $(e).find(".hora").text(),
          });
        });
        this.model.set({"horarios":horarios});
          
        // Las etiquetas se tratan como array porque son entidades separadas
        if (this.expand_principal_key == 1) {
          if (self.$("#entrada_etiquetas").length > 0) {
            var c = self.$("#entrada_etiquetas").select2("val");
            this.model.set({ "etiquetas":((c==null)?[]:c) });
          }
        }
          
        var id_categoria = self.$("#entrada_categorias").val();
        if (id_categoria == null) id_categoria = 0;

        if (this.$("#entrada_editores").length > 0) {
          var id_editor = this.$("#entrada_editores").val();
          if (id_editor == null) id_editor = 0;
          this.model.set({
            "id_editor":id_editor,
          });
        }

        this.model.set({
          "seo_title": (self.$("#entrada_seo_title").length > 0) ? self.$("#entrada_seo_title").val() : "",
          "seo_description": (self.$("#entrada_seo_description").length > 0) ? self.$("#entrada_seo_description").val() : "",
          "seo_keywords": (self.$("#entrada_seo_keywords").length > 0) ? self.$("#entrada_seo_keywords").val() : "",
          "id_categoria":id_categoria,
          "categoria":self.$("#entrada_categorias option:selected").text(),
          "path":self.$("#hidden_path").val(),
          "logo": ((self.$("#hidden_logo").length > 0) ? self.$("#hidden_logo").val() : ""),
          "path_2": ((self.$("#hidden_path_2").length > 0) ? self.$("#hidden_path_2").val() : ""),
          "archivo": (self.$("#hidden_archivo").length > 0) ? self.$("#hidden_archivo").val() : "",
          "mostrar_fecha": ((self.$("#entrada_mostrar_fecha").length > 0) ? (self.$("#entrada_mostrar_fecha").is(":checked")?1:0) : 0),
          "privada": ((self.$("#entrada_privada").length > 0) ? (self.$("#entrada_privada").is(":checked")?1:0) : 0),
          "id_comision": (self.$("#entradas_comisiones").length > 0) ? self.$("#entradas_comisiones").val() : 0,
        });

        if (this.$("#entrada_pais").length > 0) {
          var id_pais = this.$("#entrada_pais").val();
          if (id_pais !== null) this.model.set({"id_pais":id_pais});
        }

        // Si los custom llegan a ser fileuploaders, hay que setearlos en el modelo
        for(var i=1;i<=20;i++) {
          if ((self.$("#hidden_custom_"+i).length > 0)) {
            var cus = self.$("#hidden_custom_"+i).val();
            var key = "custom_"+i;
            var obj = {};
            obj[key] = cus;
            this.model.set(obj);
          }          
        }
        
        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
          
        // Listado de Entradas Relacionados
        if (this.expand_relacionados_key == 1) {

          var relacionados = new Array();
          $(this.el).find("#entradas_tabla_relacionados .list-group-item").each(function(i,e){
            relacionados.push({
              "id":$(e).find(".id").text(),
              "destacado":0,
            });
          });
          self.model.set({"relacionados":relacionados});
      
          // Arbol de categorias de relacionados
          var categorias_relacionados = new Array();
          var rel = $("#entradas_categorias_tree").fancytree("getTree").getSelectedNodes();
          for(var i=0;i<rel.length;i++) {
            var o = rel[i];
            categorias_relacionados.push({
              "id":o.key,
            });
          }
          self.model.set({"categorias_relacionados":categorias_relacionados});
        }
        
        // Coordenadas
        if (self.marker != undefined) {
          var pos = self.marker.getPosition();
          var zoom = self.map.getZoom();
          this.model.set({
            "latitud":(isNaN(pos.lat())) ? 0 : pos.lat(),
            "longitud":(isNaN(pos.lng())) ? 0 : pos.lng(),
            "zoom":zoom,
          });
        } 

        if (this.$("#entradas_clientes").length > 0) {
          this.model.set({
            "id_cliente":this.$("#entradas_clientes").val(),
          });
        }

        if (this.$("#entrada_usuarios").length > 0) {
          this.model.set({
            "id_usuario":this.$("#entrada_usuarios").val()
          });
        }

        if (MILLING == 1) {
          this.model.set({
            "custom_1":this.$("#entradas_eventos").val(),
          });
          this.model.set({
            "custom_2":this.$("#entradas_training").val(),
          });
        }
        
        var fecha = self.model.get("fecha")+":00";
        self.model.set({"fecha":fecha});
        
        // Texto del entrada
        self.model.set({
          "texto":CKEDITOR.instances['entrada_texto'].getData(),
        });
        if (typeof CKEDITOR.instances['entrada_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['entrada_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['entrada_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['entrada_texto_pt'].getData(),
          });
        }

        // SAMF
        if (ID_EMPRESA == 285) {

          if (typeof CKEDITOR.instances['entrada_custom_7'] != "undefined") {
            self.model.set({
              "custom_7":CKEDITOR.instances['entrada_custom_7'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_8'] != "undefined") {
            self.model.set({
              "custom_8":CKEDITOR.instances['entrada_custom_8'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_9'] != "undefined") {
            self.model.set({
              "custom_9":CKEDITOR.instances['entrada_custom_9'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_10'] != "undefined") {
            self.model.set({
              "custom_10":CKEDITOR.instances['entrada_custom_10'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_11'] != "undefined") {
            self.model.set({
              "custom_11":CKEDITOR.instances['entrada_custom_11'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_12'] != "undefined") {
            self.model.set({
              "custom_12":CKEDITOR.instances['entrada_custom_12'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_13'] != "undefined") {
            self.model.set({
              "custom_13":CKEDITOR.instances['entrada_custom_13'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_14'] != "undefined") {
            self.model.set({
              "custom_14":CKEDITOR.instances['entrada_custom_14'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_16'] != "undefined") {
            self.model.set({
              "custom_16":CKEDITOR.instances['entrada_custom_16'].getData(),
            });
          }

        // COLEGIO DE ODONTOLOGOS
        } else if (ID_EMPRESA == 606) {

          if (typeof CKEDITOR.instances['entrada_custom_8'] != "undefined") {
            self.model.set({
              "custom_8":CKEDITOR.instances['entrada_custom_8'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_9'] != "undefined") {
            self.model.set({
              "custom_9":CKEDITOR.instances['entrada_custom_9'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_10'] != "undefined") {
            self.model.set({
              "custom_10":CKEDITOR.instances['entrada_custom_10'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_11'] != "undefined") {
            self.model.set({
              "custom_11":CKEDITOR.instances['entrada_custom_11'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_12'] != "undefined") {
            self.model.set({
              "custom_12":CKEDITOR.instances['entrada_custom_12'].getData(),
            });
          }
          if (typeof CKEDITOR.instances['entrada_custom_13'] != "undefined") {
            self.model.set({
              "custom_13":CKEDITOR.instances['entrada_custom_13'].getData(),
            });
          }
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

    restaurar:function() {
      if (this.validar()) {
        this.model.set({
          "eliminada":0,
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
        console.log("ACTIVO");
        console.log(activo);
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
        console.log("ACTIVO");
        console.log(activo);
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
