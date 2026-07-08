// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Clasificado = Backbone.Model.extend({
    urlRoot: "clasificados",
    defaults: {
      // Atributos que no se persisten directamente
      images: [],
      categoria: "",
      usuario: "",
      atributos: [],
      
      titulo: "",
      subtitulo: "",
      descripcion: "",
      id_categoria: 0,
      activo_desde: "",
      activo_hasta: "",
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
      fecha: "",

      activo: 1,
      activo_desde: "",
      activo_hasta: "",
      destacado: 0,
      texto: "",
      texto_privado: "",
      precio: "",
      path: "",
      
      seo_title: "",
      seo_keywords: "",
      seo_description: "",
      
      direccion: "",
      telefono: "",
      email: "",
      latitud: 0,
      longitud: 0,
      
      id_publicidad: 0,
	  facebook: "",
    },
  });
	  
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Clasificados = paginator.requestPager.extend({

    model: model,
    
    paginator_core: {
      url: function() {
        var texto = (isEmpty(this.meta("texto")) ? 0 : this.meta("texto"));
        var id_categoria = ((this.meta("id_categoria") == undefined) ? 0 : this.meta("id_categoria"));
        var fecha = ((this.meta("fecha") == undefined) ? 0 : this.meta("fecha"));
        var s = "clasificados/function/ver";
        s=s+"/"+texto;
        s=s+"/"+id_categoria;
        s=s+"/"+fecha;
        return s;
      }
    },
    
    paginator_ui: {
      perPage: 50,
    },
	  
  });

})( app.collections, app.models.Clasificado, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ClasificadosTableView = app.mixins.View.extend({

    template: _.template($("#clasificados_resultados_template").html()),
      
    myEvents: {
      "change #clasificados_buscar":"buscar",
      "click #clasificados_buscar_avanzada_btn":"buscar_avanzada",
      "click .enviar": "enviar",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "keydown #clasificados_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#clasificados_texto").focus(); }
      },
      "click .eliminar_lote":"eliminar_lote",
      "click .destacar_lote":"destacar_lote",
      "click .activar_lote":"activar_lote",      
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
      this.id_categoria = (typeof this.options.id_categoria != "undefined") ? this.options.id_categoria : 0;
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";      
      
      this.render();
			this.collection.on('sync', this.addAll, this);
      this.buscar();
		},
    
    render: function() {
      
			// Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
				collection: this.collection
			});
			
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
        "filter":this.filter,
        "id_categoria":this.id_categoria,
      }));
      
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
      
      // BUSQUEDA AVANZADA POR CATEGORIAS
      var r = "<option value='0'>Categoria</option>";
      r+= workspace.crear_select(categorias_clasificados,"",this.id_categoria);
      this.$("#clasificados_buscar_categorias").html(r).select2({});
      
    },
    
    buscar: function() {
      var self = this;
      var filter = this.$("#clasificados_buscar").val();
      if (typeof filter != "undefined") filter = filter.trim();
      else filter = "";
      self.filter = filter;
      
      this.collection.server_api = {
        "filter":self.filter,
        "id_categoria":self.id_categoria,
      };
      this.collection.pager();
    },
    
    buscar_avanzada: function() {
      this.id_categoria = this.$("#clasificados_buscar_categorias").val();
      this.buscar();
    },
    
    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);  
    },
    
    addOne : function ( item ) {
      var view = new app.views.ClasificadosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
    
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"clasificados"
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
      window.open("clasificados/function/exportar_csv/","_blank");
    },    
    
    enviar: function() {
      var checks = this.$("#clasificados_tabla .check-row:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var art = clasificados.get(id);
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
      var checks = this.$("#clasificados_tabla .check-row:checked");
      if (checks.length == 0) return;
      if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
        $(checks).each(function(i,e){
          var id = $(e).val();
          var art = clasificados.get(id);
          art.destroy();	// Eliminamos el modelo
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
  app.views.ClasificadosItemResultados = app.mixins.View.extend({
    
    template: _.template($("#clasificados_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
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
          "table":"clasificados",
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
          "table":"clasificados",
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
          "table":"clasificados",
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
            "url":"clasificados/function/duplicar/"+self.model.id,
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
          this.model.destroy();	// Eliminamos el modelo
          $(this.el).remove();	// Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_clasificado_seleccionado = this.model.get("codigo");
        window.clasificado_seleccionado = this.model;
        $('.modal:last').modal('hide');        
      } else {
        location.href="app/#clasificado/"+this.model.id;
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

  app.views.ClasificadoEditView = app.mixins.View.extend({

    template: _.template($("#clasificado_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      //"change #clasificado_categorias":"render_atributos",
      
      // Para que al mover de tab se vea bien
      "click #link_tab2":function() {
        var self = this;
        setTimeout(function(){
          if (self.map == undefined) self.render_map();
          google.maps.event.trigger(self.map, "resize");
          self.map.setCenter(self.coor);
        },100);
      },
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
    
      var r = workspace.crear_select(categorias_clasificados,"",self.model.get("id_categoria"));
      this.$("#clasificado_categorias").html(r).select2({});
    
      /*
      var activo_desde = this.model.get("activo_desde");
      if (isEmpty(activo_desde)) activo_desde = moment().startOf('month').toDate();
      createtimepicker($(this.el).find("#clasificado_activo_desde"),activo_desde);
      
      var activo_hasta = this.model.get("activo_hasta");
      if (isEmpty(activo_hasta)) activo_hasta = moment().endOf('month').toDate();
      createtimepicker($(this.el).find("#clasificado_activo_hasta"),activo_hasta);      
      */
      // Cuando cambian las imagens, renderizamos la tabla
      //this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      //this.render_tabla_fotos();
      //this.render_atributos();
      
      new app.mixins.Select({
        modelClass: app.models.Publicidad,
        url: "publicidades/",
        render: "#clasificado_publicidades",
        firstOptions: ["<option value='0'>Publicidad</option>"],
        selected: self.model.get("id_publicidad"),
        campoSelect: "nombre,categoria",
        success:function(c) {
          $("#clasificado_publicidades").select2({});
        }
      });      
      
      //$(this.el).find("#images_tabla").sortable();
      
      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {
        console.log(e);
      }
    },
    
    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud");
      var longitud = self.model.get("longitud");
      //var zoom = parseInt(self.model.get("zoom"));
      var zoom = 16;
      if (latitud == 0 && longitud == 0) {
        latitud = -34.641487; longitud = -60.470778;
      }
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(self.$("#mapa")[0], mapOptions);
      
      // Place a draggable marker on the map
      self.marker = new google.maps.Marker({
        position: self.coor,
        map: self.map,
        draggable:true,
        title:"Arrastralo a la direccion correcta"
      });
      google.maps.event.addListener(self.marker,"dragend",function(event) {
        var lat = event.latLng.lat(); 
        var lng = event.latLng.lng();
        self.model.set({
          "latitud":lat,
          "longitud":lng
        });
      });       
    },
    
    /*
    render_atributos: function() {
      var self = this;
      var id_categoria = self.$("#clasificado_categorias").val();
      var id = this.model.id;
      if (id === undefined) id = 0;
      $.ajax({
        "url":"clasificados_atributos/function/get_values/"+id_categoria+"/"+id+"/",
        "dataType":"json",
        "success":function(atributos) {          
          self.$("#caracteristica_panel").empty();
          for(var i=0;i<atributos.length;i++) {
            var a = atributos[i];
            var s = "";
            s+='<div class="form-group">'
            s+='<label class="col-md-2 control-label">'+a.nombre+'</label>';
            s+='<div class="col-md-10">';
            s+='<input type="text" data-id_atributo="'+a.id_atributo+'" value="'+a.valor+'" class="form-control atributo"/>';
            s+='</div>';
            s+='</div>';
            self.$("#caracteristica_panel").append(s);
          }
        }
      });
    },
    */
    /*
    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
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
    },
    */
    
    validar: function() {
      try {
        var self = this;
        
        this.model.set({
          "id_categoria":self.$("#clasificado_categorias").val(),
          "id_publicidad":self.$("#clasificado_publicidades").val(),
          "categoria":self.$("#clasificado_categorias option:selected").text(),
          "path":self.$("#hidden_path").val(),
        });
        
        // Listado de Imagenes
        /*
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
        
        var activo_desde = self.model.get("activo_desde")+":00";
        var activo_hasta = self.model.get("activo_hasta")+":00";
        self.model.set({
          "activo_desde":activo_desde,
          "activo_hasta":activo_hasta,
        });
        
        // Texto del clasificado
        var cktext = CKEDITOR.instances['clasificado_texto'].getData();
        self.model.set({"texto":cktext});
        
        var cktext = CKEDITOR.instances['clasificado_texto_privado'].getData();
        self.model.set({"texto_privado":cktext});
        
        // Creamos el array de atributos
        var atributos = new Array();
        self.$(".atributo").each(function(i,e){
          var id_atributo = $(e).data("id_atributo");
          var valor = $(e).val();
          atributos.push({
            "id_atributo":id_atributo,
            "valor":valor,
          });
        });
        self.model.set({
          "atributos":atributos,
        });
        */
        
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },	
	
    guardar:function() {
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              clasificados.add(model);
              location.href="app/#clasificados";
            }
          }
        });
      }	  
    },    
	
  });
})(app);
