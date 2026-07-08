(function ( models ) {

  models.CarpPostulante = Backbone.Model.extend({
    urlRoot: "carp_postulantes/",
    defaults: {
      password: "",
      id_empresa: ID_EMPRESA,
      id_usuario: 0,
      id_propietario: 0,
      propietario: "",
      estado: 0,
      nombre: "",
      dni: "",
      direccion: "",
      agencia: "",
      numero_calle: "",
      ciudad: "La Plata",
      apellido: "",
      fecha_alta: "",
      fecha_baja: "",
      telefono: "",
      email: "",
      activo: 1,
      observaciones: "",
      vehiculo: "",
      path: "",
      bolsa_trabajo: 1,
      latitud: 0,
      longitud: 0,
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.CarpPostulantes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "carp_postulantes/"
    }
    
  });

})( app.collections, app.models.CarpPostulante, Backbone.Paginator);



(function ( app ) {

  app.views.CarpPostulanteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#carp_postulantes_item').html()),
    myEvents: {
      "click .contratar":"contratar",
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .enviar_whatsapp":function(){
        var mensaje = ""
        var tel = this.model.get("telefono");
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel+"?text="+encodeURIComponent(mensaje);
        window.open(link_ws,"_blank");
      },
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"com_usuarios",
          "url":"carp_postulantes/function/change_property/",
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
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var self = this;
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));            
      return this;
    },
    editar: function() {
      location.href="app/#carp_postulante/"+this.model.id;
    },
    contratar: function() {
      if (!confirm("Realmente desea contratar a este postulante?")) return;
      var self = this;
      $.ajax({
        "url":"carp_postulantes/function/contratar/",
        "dataType":"json",
        "data":{
          "id": self.model.id
        },
        "type":"post",
        "success":function(r) {
          if (r.error == 0) {
            alert("Los datos han sido guardados correctamente.");
            location.reload();
          } else {
            alert(r.mensaje);
          }
        }
      });
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
    },
  });

})( app );


(function ( app ) {

  app.views.CarpPostulantesTableView = app.mixins.View.extend({

    template: _.template($("#carp_postulantes_panel_template").html()),

    myEvents: {
      "click .cambiar_tab":function(e){
        var self = this;
        window.carp_postulantes_tipo = $(e.currentTarget).data("tipo");
        this.buscar();
      },
      "click .buscar":"buscar",
      "keypress #carp_postulantes_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
    },

    render_map: function() {
      var self = this;
      var centro_latitud = 0; 
      var centro_longitud = 0; 
      var cantidad = 0;
      var zoom = 12;
      var marcadores = new Array();
      if (this.collection.length > 0) {
        this.collection.each(function(p){
          if (p.get("latitud") != 0 && p.get("longitud") != 0) {
            var lat = parseFloat(p.get("latitud"));
            var lon = parseFloat(p.get("longitud"));
            marcadores.push({
              "lat":lat,
              "lon":lon,
              "id":p.id,
              "id_empresa":p.get("id_empresa"),
            });
            centro_latitud += lat;
            centro_longitud += lon;
            cantidad++;
          }
        });
      }
      if (cantidad > 0) {
        centro_latitud = centro_latitud / cantidad;
        centro_longitud = centro_longitud / cantidad;
      } else {
        centro_latitud = -34.6156625; 
        centro_longitud = -58.5033598;
      }

      self.coor = new google.maps.LatLng(centro_latitud,centro_longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(document.getElementById("carp_postulantes_mapa"), mapOptions);
      for(var i=0;i<marcadores.length;i++) {
        var m = marcadores[i];
        var marker = new google.maps.Marker({
          position: new google.maps.LatLng(m.lat,m.lon),
          map: self.map,
        })
        self.attachEvent(marker,m.id,m.id_empresa);
      }
    },    

    attachEvent: function(marker,id,id_empresa) {
      var self = this;
      google.maps.event.addListener(marker,'click', function(){
        location.href = "app/#carp_postulante/"+id;
      });
    },  

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = (typeof options.permiso != undefined) ? options.permiso : 0;

      window.carp_postulantes_filter = (typeof window.carp_postulantes_filter != "undefined") ? window.carp_postulantes_filter : "";
      window.carp_postulantes_estado = (typeof window.carp_postulantes_estado != "undefined") ? window.carp_postulantes_estado : "";
      window.carp_postulantes_page = (typeof window.carp_postulantes_page != "undefined") ? window.carp_postulantes_page : 1;
      window.carp_postulantes_tipo = (typeof window.carp_postulantes_tipo != "undefined") ? window.carp_postulantes_tipo : 1;

      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {

      var self = this;
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: self.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "edicion":(this.permiso > 3),
      }));

      this.$(".pagination_container").html(this.pagination.el);

      return this;
    },

    buscar: function() {
      var cambio_parametros = false;
      if (window.carp_postulantes_filter != this.$("#carp_postulantes_buscar").val().trim()) {
        window.carp_postulantes_filter = this.$("#carp_postulantes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros || window.carp_postulantes_tipo == 2) window.carp_postulantes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.carp_postulantes_filter),
        "estado":window.carp_postulantes_estado,
        "offset":((window.carp_postulantes_tipo == 1)?30:99999),
      }
      this.collection.server_api = datos;
      this.collection.goTo(window.carp_postulantes_page);
    },

    addAll : function () {
      var self = this;
      if (window.carp_postulantes_tipo == 1) {
        // LISTADO
        this.$("#carp_postulantes_mapa").hide();
        this.$("#carp_postulantes_listado").show();
        $(this.el).find("tbody").empty();
        this.collection.each(this.addOne);        
      } else {
        // MAPA
        this.$("#carp_postulantes_listado").hide();
        this.$("#carp_postulantes_mapa").show();
        try {
          loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
        } catch(e) {
          setTimeout(function(){
            self.render_map();
          },1000);
        }
        setTimeout(function(){
          if (self.map == undefined) self.render_map();
          google.maps.event.trigger(self.map, "resize");
          self.map.setCenter(self.coor);
        },1000);          
      }
    },

    addOne : function ( item ) {
      var view = new app.views.CarpPostulanteItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.CarpPostulanteEditView = app.mixins.View.extend({

    template: _.template($("#carp_postulantes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #cargar_mapa":"get_coords_by_address",
      "change #carp_postulante_direccion":"get_coords_by_address",
      "change #carp_postulante_numero_calle":"get_coords_by_address",
      "change #carp_postulante_ciudad":"get_coords_by_address",      
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1 || this.model.isNew()) edicion = true;
      var obj = { 
        edicion: edicion, 
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {
        setTimeout(function(){
          self.render_map();
        },1000);
      }
      setTimeout(function(){
        if (self.map == undefined) self.render_map();
        google.maps.event.trigger(self.map, "resize");
        self.map.setCenter(self.coor);
      },1000);
    },

    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud");
      var longitud = self.model.get("longitud");
      var zoom = 17;
      if (latitud == 0 && longitud == 0) {
        latitud = -34.6156625; 
        longitud = -58.5033598;
      }
      self.geocoder = new google.maps.Geocoder();
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(document.getElementById("carp_postulante_mapa"), mapOptions);

      // Place a draggable marker on the map
      self.marker = new google.maps.Marker({
        position: self.coor,
        map: self.map,
        draggable:true,
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
      var calle = $("#carp_postulante_direccion").val();
      if (isEmpty(calle)) {
        alert("Por favor ingrese una calle");
        $("#carp_postulante_direccion").focus();
        return;
      }
      var altura = $("#carp_postulante_numero_calle").val();
      var localidad = $("#carp_postulante_ciudad").val();
      if (isEmpty(localidad)) {
        alert("Por favor ingrese una localidad");
        $("#carp_postulante_ciudad").focus();
        return;
      }
      var address = calle+" "+altura+", "+localidad+", Argentina";
      self.geocoder.geocode( { 'address': address}, function(results, status) {
        console.log(results);
        if (status == google.maps.GeocoderStatus.OK) {
          var location = results[0].geometry.location;
          var latitud = location.lat();
          var longitud = location.lng();
          self.coor = new google.maps.LatLng(latitud,longitud);
          self.model.set({
            "latitud":latitud,
            "longitud":longitud,
          })
          self.map.setCenter(self.coor);
          self.map.setZoom(18);
          self.marker.setPosition(self.coor);
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },    

    validar: function() {
      try {
        validate_input("carp_postulante_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("carp_postulante_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
        validate_input("carp_postulante_dni",IS_EMPTY,"Por favor, ingrese un DNI.");

        this.model.set({
          "path":(this.$("#hidden_path").length > 0) ? $("#hidden_path").val() : "",
          "estado":this.$("#carp_postulante_estados").val(),
        });
        return true;
      } catch(e) {
        return false;
      }
    },
        
    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.set({});
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            location.href = "app/#carp_postulantes";
          }
        });
      }
    },
    
  });

})(app.views, app.models);