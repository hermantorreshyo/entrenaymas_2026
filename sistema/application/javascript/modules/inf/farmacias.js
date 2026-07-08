// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Farmacia = Backbone.Model.extend({
        urlRoot: "farmacias/",
        defaults: {
            nombre: "",
            direccion: "",
            telefono: "",
            id_empresa: ID_EMPRESA,
            latitud: LATITUD,
            longitud: LONGITUD,
            zoom: 15,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Farmacias = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "farmacias/"
		},
		
	});

})( app.collections, app.models.Farmacia, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.FarmaciaItem = Backbone.View.extend({
        tagName: "tr",
        attributes: function() {
            return {
                id: this.model.id // Es necesario hacer esto para reordenar
            }
        },
        template: _.template($('#farmacias_item').html()),
      	events: {
    		"click .edit": "editar",
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
        	location.href="app/#farmacia/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
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

    app.views.FarmaciasTableView = app.mixins.View.extend({

    	template: _.template($("#farmacias_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
            this.options = options;
			this.permiso = this.options.permiso;

			// Creamos la lista de farmaciacion
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
			// Cargamos el farmaciador
			$(this.el).find(".pagination_container").html(pagination.el);
			// Cargamos el buscador
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo farmaciamos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.FarmaciaItem({
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

    views.FarmaciaEditView = app.mixins.View.extend({

        template: _.template($("#farmacias_edit_panel_template").html()),

        myEvents: {
            "click .guardar": "guardar",
            "click .nuevo": "limpiar",
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
            this.model.bind("destroy",this.render,this);
            this.options = options;
            _.bindAll(this);
            this.render();
            try {
                loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
            } catch(e) {}            
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

        render: function()
        {
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
                validate_input("farmacias_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
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
                
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                return true;                
            } catch(e) {
                return false;
            }
        },
        
        guardar: function() {
            var self = this;
            if (this.validar()) {
                this.model.save({
                    },{
                    success: function(model,response) {
                        location.href="app/#farmacias";
                    }
                });                 
            }
        },
        
        limpiar : function() {
            this.model = new app.models.Farmacia()
            this.render();
        },
		
    });

})(app.views, app.models);