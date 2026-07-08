// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Vendedor = Backbone.Model.extend({
    urlRoot: "vendedores/",
    defaults: {
      nombre : "",
      comision : 0,
      email: "",
      telefono: "",
      direccion: "",
      codigo: "",
      color: "",
      id_sucursal: 0,
      id_punto_venta: 0,
      password: "",
      limite_descuento: 0,
      perfil_app: 0,
      lista_defecto: 0,
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Vendedores = paginator.requestPager.extend({

    model: model,
  
    paginator_core: {
      url: "vendedores/"
    }
	  
  });

})( app.collections, app.models.Vendedor, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.VendedorItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#vendedores_item').html()),
    	events: {
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
      location.href="app/#vendedor/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();	// Eliminamos el modelo
      	$(this.el).remove();	// Lo eliminamos de la vista
        this.collection.fetch();
      }
      e.stopPropagation();
    },
    duplicar: function() {
      var self = this;
      var clonado = this.model.clone();
      clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
      clonado.save({},{
        success: function(model,response) {
          //model.set({id:response.id});
          self.collection.fetch();
        }
      });
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

app.views.VendedoresTableView = app.mixins.View.extend({

  template: _.template($("#vendedores_panel_template").html()),

  initialize : function (options) {

    _.bindAll(this); // Para que this pueda ser utilizado en las funciones
  
    var lista = this.collection;
  
    // Guardamos las referencias
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

    // Actualizamos el array cacheado
    //window.vendedores = this.collection.toJSON();
  },

  addOne : function ( item ) {
    var view = new app.views.VendedorItem({
      model: item,
      permiso: this.permiso,
      collection: this.collection,
    });
    $(this.el).find("tbody").append(view.render().el);
  }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.VendedorEditView = app.mixins.View.extend({

    template: _.template($("#vendedores_edit_panel_template").html()),
	
    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
    },

    initialize: function(options) 
    {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      //this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.bind("ver",this.ver,this); // Mostramos el objeto
			this.bind("limpiar",this.limpiar,this);
      _.bindAll(this);

      this.render();
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

      this.$(".color").colorpicker({
        format: "rgba"
      });
      return this;
    },

    validar: function() {
      try {
        var self = this;
        // Validamos los campos que sean necesarios
        validate_input("vendedores_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var color = this.$(".color").colorpicker('getValue');
        this.model.set({
          "color":color,
        });

        if (this.$("#vendedores_sucursales").length > 0) {
          this.model.set({
            "id_sucursal":this.$("#vendedores_sucursales").val(),
            "id_punto_venta":this.$("#vendedores_puntos_venta").val(),
          });
        }

        if (this.$("#vendedores_listas").length > 0) {
          this.model.set({
            "lista_defecto":this.$("#vendedores_listas").val(),
          });
        }

        if (this.$("#vendedores_perfil_app").length > 0) {
          this.model.set({
            "perfil_app":self.$("#vendedores_perfil_app").val(),
          });
        }

        // No hay ningun error
        $(".error").removeClass("error");
        return true;
      
      } catch(e) {
        return false;
      }
    },
    

    guardar: function() 
    {
      var self = this;
      if (this.validar()) {

        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
            "id_empresa":ID_EMPRESA,
          },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },
		
    limpiar : function() {
      
      this.model = new app.models.Vendedor();
      this.render();
      
    },
	
  });

})(app.views, app.models);




(function ( app ) {

  app.views.VendedoresSeguimientoView = app.mixins.View.extend({

    template: _.template($("#vendedores_seguimiento_template").html()),

    myEvents: {
      "click .buscar":"buscar",
    },

    initialize : function () {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      createdatepicker(this.$("#vendedores_seguimiento_fecha_desde"),new Date());
      createdatepicker(this.$("#vendedores_seguimiento_fecha_hasta"),new Date());
      this.$("#vendedores_seguimiento_hora_desde").mask("99:99");
      this.$("#vendedores_seguimiento_hora_hasta").mask("99:99");

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
      },1000);              
    },

    render_map: function() {
      var self = this;
      var latitud = -34.6156625; 
      var longitud = -58.5033598;
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: 15,
        center: self.coor
      }
      self.map = new google.maps.Map(document.getElementById("vendedores_seguimiento_mapa"), mapOptions);
    },    

    buscar: function() {
      var self = this;
      var id_empresa = (ID_EMPRESA == 980) ? 953 : ID_EMPRESA;
      var vista = this.$("#vendedores_seguimiento_vista").val();
      var id_vendedor = this.$("#vendedores_seguimiento_vendedores").val();
      var fecha_desde = this.$("#vendedores_seguimiento_fecha_desde").val();
      var fecha_hasta = this.$("#vendedores_seguimiento_fecha_hasta").val();
      var hora_desde = this.$("#vendedores_seguimiento_hora_desde").val();
      var hora_hasta = this.$("#vendedores_seguimiento_hora_hasta").val();
      $.ajax({
        "url":"distrivar/function/get_posiciones/",
        "type":"post",
        "dataType":"json",
        "data":{
          "vista":vista,
          "id_empresa":id_empresa,
          "id_vendedor":id_vendedor,
          "fecha_desde":fecha_desde,
          "fecha_hasta":fecha_hasta,
          "hora_desde":hora_desde,
          "hora_hasta":hora_hasta,
        },
        "success":function(r) {
          var posiciones = new Array();
          if (typeof self.polyline != "undefined") self.polyline.setMap(null);
          if (typeof self.marker != "undefined") self.marker.setMap(null);
          for (var i = 0; i < r.results.length; i++) {
            var o = r.results[i];
            // Agregamos la posicion al array para despues crear el camino
            posiciones.push(new google.maps.LatLng(o.latitud, o.longitud));
            // Agregamos el marcador en ese punto
            if (vista == 2) {
              self.marker = new google.maps.Marker({
                position: new google.maps.LatLng(o.latitud, o.longitud),
                map: self.map,
              });
            }
            if (i==0) self.map.setCenter(new google.maps.LatLng(o.latitud, o.longitud));
          }
          // Creamos el camino
          self.polyline = new google.maps.Polyline({
            path : posiciones,
            geodesic : true,
            strokeColor : '#FF0000',
            strokeOpacity : 1,
            strokeWeight : 4,
            map : self.map
          });
        }
      })
    }

  });
})(app);