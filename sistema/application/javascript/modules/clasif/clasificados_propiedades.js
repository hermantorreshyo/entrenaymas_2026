// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ClasificadosPropiedadesTableView = app.mixins.View.extend({

    template: _.template($("#clasificados_propiedades_resultados_template").html()),
      
    myEvents: {
      "change #clasificados_propiedades_buscar":"buscar",
      "click .buscar":"buscar",
      "click .exportar_precios": "exportar_precios",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "click .enviar": "enviar",
      "keydown #clasificados_propiedades_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#clasificados_propiedades_texto").focus(); }
      },
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
      this.id_tipo_inmueble = (typeof this.options.id_tipo_inmueble != "undefined") ? this.options.id_tipo_inmueble : 0;
      this.id_tipo_estado = (typeof this.options.id_tipo_estado != "undefined") ? this.options.id_tipo_estado : 0;
      this.id_tipo_operacion = (typeof this.options.id_tipo_operacion != "undefined") ? this.options.id_tipo_operacion : 0;
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;      

			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
				collection: this.collection
			});

      this.collection.on('sync', this.addAll, this);
			
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
      
      $(this.el).find("#clasificados_propiedades_buscar_tipos_estado").select2();
      $(this.el).find("#clasificados_propiedades_buscar_tipos_inmueble").select2();
      $(this.el).find("#clasificados_propiedades_buscar_tipos_operacion").select2();
      
      this.collection.server_api = {
        "filter":this.filter,
        "id_tipo_inmueble":this.id_tipo_inmueble,
        "id_tipo_estado":this.id_tipo_estado,
        "id_tipo_operacion":this.id_tipo_operacion,
      };      
      if (SOLO_USUARIO == 1) {
        this.collection.server_api.id_usuario = ID_USUARIO;
      }
      this.collection.goTo(this.pagina);
		},

    buscar: function() {
      this.id_tipo_estado = this.$("#clasificados_propiedades_buscar_tipos_estado").select2("val");
      this.id_tipo_inmueble = this.$("#clasificados_propiedades_buscar_tipos_inmueble").select2("val");
      this.id_tipo_operacion = this.$("#clasificados_propiedades_buscar_tipos_operacion").select2("val");
      this.filter = this.$("#propiedades_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
        "id_tipo_estado":this.id_tipo_estado,
        "id_tipo_inmueble":this.id_tipo_inmueble,
        "id_tipo_operacion":this.id_tipo_operacion,
      };
      if (SOLO_USUARIO == 1) {
        this.collection.server_api.id_usuario = ID_USUARIO;
      }
      this.collection.pager();      
    },

    addAll : function () {
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
      var view = new app.views.PropiedadesItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
        
    exportar_precios: function() {
      var fecha = $("#clasificados_propiedades_fecha").val();
      if (isEmpty(fecha)) fecha = 0;
      else fecha = fecha.replace(/\//g,"-");
      window.open("propiedades/function/exportar_mercader/"+fecha,"_blank");
    },
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"propiedades"
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
      window.open("propiedades/function/exportar_csv/","_blank");
    },
    
    enviar: function() {
      var self = this;
      var checks = this.$("#clasificados_propiedades_tabla .check-row:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var art = self.collection.get(id);
        links_adjuntos.push({
          tipo: TIPO_ADJUNTO_PROPIEDAD,
          id_objeto: id,
          nombre: art.get("nombre"),
        });
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Fichas de Propiedades",
      });
      workspace.nuevo_email(email);
    },    
    
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ClasificadosPropiedadesItemResultados = app.mixins.View.extend({
    
    template: _.template($("#clasificados_propiedades_item_resultados_template").html()),
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
          "table":"inm_propiedades",
          "url":"propiedades/function/change_property/",
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
          "table":"inm_propiedades",
          "url":"propiedades/function/change_property/",
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
          "table":"inm_propiedades",
          "url":"propiedades/function/change_property/",
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
            "url":"propiedades/function/duplicar/"+self.model.id,
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
      "click .facebook":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        window.open("propiedades/function/compartir/"+this.model.id+"/","_blank","height=250,width=450,location=no,resizable=no,scrollbars=no,titlebar=no,menubar=no,top=200,left=200");
        return false;
      },      
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_propiedad_seleccionado = this.model.get("codigo");
        window.propiedad_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#clasificado_propiedad/"+this.model.id;
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



(function ( app ) {

  app.views.ClasificadoPropiedadEditView = app.mixins.View.extend({

    template: _.template($("#clasificado_propiedad_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "click #cargar_mapa":"get_coords_by_address",
      
      "change #clasificado_propiedad_tipos_operacion":function(e) {
        var id = $(e.currentTarget).val();
        var width = (typeof window.PROPIEDAD_IMAGE_WIDTH === "undefined") ? 400 : window.PROPIEDAD_IMAGE_WIDTH;
        var height = (typeof window.PROPIEDAD_IMAGE_HEIGHT === "undefined") ? 400 : window.PROPIEDAD_IMAGE_HEIGHT;
        if (typeof window["PROPIEDAD_IMAGE_WIDTH_TIPO_OPERACION_"+id] === "undefined") {
          $(this.el).find("#path_width").val(width);
          $(this.el).find("#path_height").val(height);
        } else {
          $(this.el).find("#path_width").val(window["PROPIEDAD_IMAGE_WIDTH_TIPO_OPERACION_"+id]);
          $(this.el).find("#path_height").val(window["PROPIEDAD_IMAGE_HEIGHT_TIPO_OPERACION_"+id]);
        }
      },
      
      "change .superficie":function() {
        var total = 0;
        $(".superficie").each(function(i,e){
          var v = parseInt($(e).val());
          if (isNaN(v)) v = 0;
          total += v;
        });
        $("#clasificado_propiedad_superficie_total").val(total);
      },
      
      "click .nuevo_cliente":function(e){
        var self = this;
        if ($(".cliente_edit_mini").length > 0) return;
        var form = new app.views.ClienteEditViewMini({
          "model": new app.models.Cliente(),
          "callback":self.cargar_clientes,
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete cliente_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#clientes_mini_nombre").focus();
      },
      
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
      _.bindAll(this);
      
      var edicion = false;
      this.options = options;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      $(this.el).find("#clasificado_propiedad_tipos_estado").select2({});
      $(this.el).find("#clasificado_propiedad_tipos_operacion").select2({});
      $(this.el).find("#clasificado_propiedad_tipos_inmueble").select2({});
      
      //this.cargar_clientes();
      
      $(this.el).find("#clasificado_propiedad_caracteristicas").select2({
        tags: true
      });
      
      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#clasificado_propiedad_localidad").autocomplete({
        "minLength":3,
        "source":function(request,response) {
          $.ajax({
            "url":"localidades/function/get_by_nombre/",
            "data":{
              "term":request.term
            },
            "dataType":"json",
            "type":"get",
            "success":function(res){
              response(res);
            }
          });
        },
        "select":function(event,ui){
          if (ui.item.latitud != null && ui.item.longitud != null) {
            self.model.set({
              "latitud":ui.item.latitud,
              "longitud":ui.item.longitud,
              "id_localidad":ui.item.id,
              "localidad":ui.item.label,
            });
            self.render_map();
            $("#clasificado_propiedad_calle").focus();
          }
        },
			});      
      
      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {
        setTimeout(function(){
          self.render_map();
        },1000);
      }
      
      // Cuando cambian las imagens, renderizamos la tabla
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      
      $(this.el).find("#images_tabla").sortable();
    },
    
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
        li+=" <span class='filename dn'>"+path+"</span>";
        li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
        li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
        li+="</li>";
        this.$("#images_tabla").append(li);
      }
    },
    
    cargar_clientes: function(id_cliente) {
      var self = this;
      // Si se manda por parametro un ID, hay que poner ese nuevo en el modelo
      if (id_cliente != undefined) {
        this.model.set({ "id_cliente": id_cliente });
      }
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#clasificado_propiedad_clientes",
        selected: self.model.get("id_cliente"),
        success:function(c) {
          crear_select2("clasificado_propiedad_clientes");
        }          
      });
    },
    
    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud");
      var longitud = self.model.get("longitud");
      var zoom = parseInt(self.model.get("zoom"));
      if (latitud == 0 && longitud == 0) {
        latitud = -34.6156625; longitud = -58.5033598; zoom: 9;
      }
      self.geocoder = new google.maps.Geocoder();
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(document.getElementById("mapa"), mapOptions);
      
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
    
    get_coords_by_address: function() {
      var self = this;
      if (self.map == undefined) return;
      var calle = $("#clasificado_propiedad_calle").val();
      if (isEmpty(calle)) {
        alert("Por favor ingrese una calle");
        $("#clasificado_propiedad_calle").focus();
        return;
      }
      var altura = $("#clasificado_propiedad_altura").val();
      if (isEmpty(altura)) {
        alert("Por favor ingrese una calle");
        $("#clasificado_propiedad_altura").focus();
        return;
      }
      var localidad = $("#clasificado_propiedad_localidad").val();
      if (isEmpty(localidad)) {
        alert("Por favor ingrese una localidad");
        $("#clasificado_propiedad_localidad").focus();
        return;
      }
      var address = calle+" "+altura+", "+localidad+", Argentina";
      self.geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
        var location = results[0].geometry.location;
        var latitud = location.lat();
        var longitud = location.lng();
        self.coor = new google.maps.LatLng(latitud,longitud);
        self.map.setCenter(self.coor);
        self.marker.setPosition(self.coor);
        self.model.set({
          "latitud":latitud,
          "longitud":longitud
        });
        } else {
        alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },
    
    validar: function() {
      try {
        var self = this;
        
        if (self.$("#clasificado_propiedad_caracteristicas").length > 0) {
          var c = self.$("#clasificado_propiedad_caracteristicas").select2("val");
          if (c != null) this.model.set({ "caracteristicas":c.join(";;;") });
        }

        this.model.set({
          "moneda":$(self.el).find("#clasificado_propiedad_monedas").val(),
          "id_tipo_inmueble":$(self.el).find("#clasificado_propiedad_tipos_inmueble").val(),
					"tipo_inmueble":$(self.el).find("#clasificado_propiedad_tipos_inmueble option:selected").text(),
          "id_tipo_operacion":$(self.el).find("#clasificado_propiedad_tipos_operacion").val(),
					"tipo_operacion":$(self.el).find("#clasificado_propiedad_tipos_operacion option:selected").text(),
          "id_tipo_estado":$(self.el).find("#clasificado_propiedad_tipos_estado").val(),
					"tipo_estado":$(self.el).find("#clasificado_propiedad_tipos_estado option:selected").text(),
          "precio_final":$(self.el).find("#clasificado_propiedad_precio_final").val(),
          "id_usuario":(($(self.el).find("#clasificado_propiedad_usuarios").length > 0) ? $(self.el).find("#clasificado_propiedad_usuarios").val() : ID_USUARIO),
        });
        
        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
				
				if (images.length > 0) {
					var path = images[0];
					self.model.set({
						"path":path
					});
				}
        
        // Texto del propiedad
        self.model.set({
          "texto":$("#clasificado_propiedad_texto").val(),
          "texto_privado":$("#clasificado_propiedad_texto_privado").val(),
        });
        
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

        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
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
              location.href="app/#clasificados_propiedades";
            }
          }
        });
      }	  
    },
    	
  });
})(app);
