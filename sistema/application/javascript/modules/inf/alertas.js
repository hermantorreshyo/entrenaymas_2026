// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Alerta = Backbone.Model.extend({
        urlRoot: "alertas",
        defaults: {
            usuario: "",
            direccion: "",
            latitud: 0,
            longitud: 0,
            id_empresa: ID_EMPRESA,
            id_usuario: 0,
            estado: "I",
            tipo: "B",
            observacion: "",
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Alertas = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'fecha',
            order: 'desc',
        },
    
        paginator_core: {
            url: "alertas/function/ver/",
        }
	    
    });

})( app.collections, app.models.Alerta, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.AlertasMapaView = app.mixins.View.extend({

        template: _.template($("#alertas_mapa_template").html()),
            
        myEvents: {
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.collection = new app.collections.Alertas();
            this.render();
            this.collection.on('sync', this.addAll, this);

            try {
                loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
            } catch(e) {
                setTimeout(function(){
                    self.render_map();
                },1000);
            }            
		},

        render: function() {
            $(this.el).html(this.template({
                "permiso":this.permiso,
            }));
            return this;
        },
        
        buscar: function() {
            this.collection.pager();
        },
        
        render_map: function() {
            var self = this;
            self.geocoder = new google.maps.Geocoder();
            var mapOptions = {
              zoom: 15,
              center: new google.maps.LatLng(-34.6414442,-60.4706614)
            }
            self.map = new google.maps.Map(document.getElementById("mapa"), mapOptions);
            //self.connect();
            window.timer = setInterval(function(){
                self.buscar();    
            },5000);
        },

        addAll : function () {
            this.$("#alertas_lista").empty();
            if (this.collection.length > 0) this.collection.each(this.addOne);
        },
        
        addOne : function ( item ) {
            var self = this;
            var view = new app.views.AlertasItemResultados({
                model: item,
            });
            $(this.el).find("#alertas_lista").append(view.render().el);
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(item.get("latitud"),item.get("longitud")),
                map: self.map,
            });            
        },        

        connect: function() {
            var self = this;
            window.source = new EventSource("application/cronjobs/alertas.php");

            window.source.addEventListener("chat", function(e) {
                var item = new app.models.Alerta();
                item.set(JSON.parse(e.data));
                var view = new app.views.AlertasItemResultados({
                    model: item,
                });
                self.$("#alertas_lista").append(view.render().el);

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(item.get("latitud"),item.get("longitud")),
                    map: self.map,
                });

            }, false);

            window.source.addEventListener("open", function(e) {
                console.log("Connection was opened.");
            }, false);

            window.source.addEventListener("error", function(e) {
                window.source.close();
            }, false);

        },

    });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.AlertasItemResultados = app.mixins.View.extend({
        
        template: _.template($("#alertas_item_template").html()),
        myEvents: {
            "click .data":"seleccionar",
        },
        seleccionar: function() {
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

/*

// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

    app.views.AlertaEditView = app.mixins.View.extend({

        template: _.template($("#propiedad_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click #cargar_mapa":"get_coords_by_address",
            
            "change #propiedad_tipos_operacion":function(e) {
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
                $("#propiedad_superficie_total").val(total);
            },
            
            "click .nuevo_propietario":function(e){
                var self = this;
                if ($(".propietario_edit_mini").length > 0) return;
                var form = new app.views.PropietarioEditViewMini({
                    "model": new app.models.Propietario(),
                    "callback":self.cargar_propietarios,
                });
                var width = 350;
                var position = $(e.currentTarget).offset();
                var top = position.top + $(e.currentTarget).outerHeight();
                var container = $("<div class='customcomplete propietario_edit_mini'/>");
                $(container).css({
                    "top":top+"px",
                    "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
                    "display":"block",
                    "width":width+"px",
                });
                $(container).append("<div class='new-container'></div>");
                $(container).find(".new-container").append(form.el);
                $("body").append(container);
                $("#propietarios_mini_nombre").focus();
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
            
            $(this.el).find("#propiedad_tipos_estado").select2({});
            $(this.el).find("#propiedad_tipos_operacion").select2({});
            $(this.el).find("#propiedad_tipos_inmueble").select2({});
            
            this.cargar_propietarios();
            
            if (isEmpty(self.model.get("caracteristicas"))) {
                var c = new Array();
            } else {
                var c = self.model.get("caracteristicas").split(";;;");
                $(this.el).find("#propiedad_caracteristicas").val(c.join(","));                
            }
            $(this.el).find("#propiedad_caracteristicas").select2({
                tags: c
            });
            
            // AUTOCOMPLETE PARA RELACIONAR CON OTRAS PROPIEDADES
            var input = $(this.el).find("#propiedades_buscar_propiedades");
            $(input).customcomplete({
                "collection":propiedades,
                "form":null, // No quiero que se creen nuevos productos
                "info":"descripcion",
                "image_field":"path",
                "image_path":"/sistema",
                "onSelect":function(item){
                    var tr = "";
                    tr+="<li class='list-group-item'>";
                    tr+="<span class='id dn'>"+item.value+"</span>";
                    tr+="<span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
                    tr+="<img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/"+item.path+"'/>";
                    tr+="<span class='filename'>"+item.info+"</span>";
                    tr+="<span class='pull-right m-t'><i class='fa fa-fw fa-times'></i> </span>";
                    tr+="</li>";
                    $("#propiedades_tabla_relacionados").append(tr);
                    $(self.el).find("#propiedades_buscar_propiedades").val("");
                }
            });             
            
            // AUTOCOMPLETE DE LOCALIDADES
            $(this.el).find("#propiedad_localidad").autocomplete({
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
                        $("#propiedad_calle").focus();
                    }
                },
			});            
            
            if (CONFIGURACION_AUTOGENERAR_CODIGOS == 1) {
                // Estamos creando un cliente nuevo
                if (this.model.id == undefined) {
                    $.ajax({
                        "url":"propiedades/function/next/",
                        "dataType":"json",
                        "success":function(r) {
                            $(self.el).find("#propiedad_codigo").val(r.codigo);
                        }
                    });
                }                
            }
            
            
            // Cuando cambian las imagens, renderizamos la tabla
            this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
            this.listenTo(this.model, 'change_table', self.render_tabla_planos);
            this.render_tabla_fotos();
            this.render_tabla_planos();
            
            $(this.el).find("#images_tabla").sortable();
            $(this.el).find("#planos_tabla").sortable();
            $(this.el).find("#propiedades_tabla_relacionados").sortable();
        },
        
        render_tabla_fotos: function() {
            var images = this.model.get("images");
            this.$("#images_tabla").empty();
            if (images.length > 0) {
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
                this.$("#images_container").show();
            } else {
                this.$("#images_container").hide();
            }
        },
        
        render_tabla_planos: function() {
            var planos = this.model.get("planos");
            this.$("#planos_tabla").empty();
            if (planos.length > 0) {
                for(var i=0;i<planos.length;i++) {
                    var path = planos[i];
                    var pth = path+"?t="+parseInt(Math.random()*100000);
                    var li = "";
                    li+="<li class='list-group-item'>";
                    li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
                    li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
                    li+=" <span class='filename'>"+path+"</span>";
                    li+=" <span class='cp pull-right m-t eliminar_foto' data-property='planos'><i class='fa fa-fw fa-times'></i> </span>";
                    li+=" <span data-id='planos' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
                    li+="</li>";
                    this.$("#planos_tabla").append(li);
                }
                this.$("#planos_container").show();
            } else {
                this.$("#planos_container").hide();
            }
        },

        cargar_propietarios: function(id_propietario) {
            var self = this;
            // Si se manda por parametro un ID, hay que poner ese nuevo en el modelo
            if (id_propietario != undefined) {
                this.model.set({ "id_propietario": id_propietario });
            }
            // Eliminamos el select2 para volverlo a crear (sino hace uno abajo del otro)
            $("#s2id_propiedad_propietarios").remove();
            
            // Creamos el select
            new app.mixins.Select({
                modelClass: app.models.Propietario,
                url: "propietarios/",
                firstOptions: ["<option value='0'>Sin Definir</option>"],
                render: "#propiedad_propietarios",
                selected: self.model.get("id_propietario"),
                success:function(c) {
                    $("#propiedad_propietarios").removeClass("form-control");
                    $("#propiedad_propietarios").select2({});
                }                    
            });
        },
        
        
        get_coords_by_address: function() {
            var self = this;
            if (self.map == undefined) return;
            var calle = $("#propiedad_calle").val();
            if (isEmpty(calle)) {
                alert("Por favor ingrese una calle");
                $("#propiedad_calle").focus();
                return;
            }
            var altura = $("#propiedad_altura").val();
            if (isEmpty(altura)) {
                alert("Por favor ingrese una calle");
                $("#propiedad_altura").focus();
                return;
            }
            var localidad = $("#propiedad_localidad").val();
            if (isEmpty(localidad)) {
                alert("Por favor ingrese una localidad");
                $("#propiedad_localidad").focus();
                return;
            }

            localidad = localidad.replace("(Bs. As.)",", Buenos Aires");
            localidad = localidad.replace("(CABA)",", Ciudad Autonoma de Buenos Aires");
            localidad = localidad.replace("(Cat.)",", Catamarca");
            localidad = localidad.replace("(Chaco)",", Chaco");
            localidad = localidad.replace("(Chu.)",", Chubut");
            localidad = localidad.replace("(Cba.)",", Cordoba");
            localidad = localidad.replace("(Corr.)",", Corrientes");
            localidad = localidad.replace("(E. Rios)",", Entre Rios");
            localidad = localidad.replace("(For.)",", Formosa");
            localidad = localidad.replace("(Jujuy)",", Jujuy");
            localidad = localidad.replace("(La Pampa)",", La Pampa");
            localidad = localidad.replace("(La Rioja)",", La Rioja");
            localidad = localidad.replace("(Mend.)",", Mendoza");
            localidad = localidad.replace("(Mis.)",", Misiones");
            localidad = localidad.replace("(Neuq.)",", Neuquen");
            localidad = localidad.replace("(Rio N.)",", Rio Negro");
            localidad = localidad.replace("(Salta)",", Salta");
            localidad = localidad.replace("(S. Juan)",", San Juan");
            localidad = localidad.replace("(S. Luis)",", San Luis");
            localidad = localidad.replace("(S. Cruz)",", Santa Cruz");
            localidad = localidad.replace("(Sta. Fe)",", Santa Fe");
            localidad = localidad.replace("(Sgo. Est.)",", Santiago del Estero");
            localidad = localidad.replace("(T. Fgo.)",", Tierra del Fuego");
            localidad = localidad.replace("(Tucuman)",", Tucuman");

            var address = calle+" "+altura+", "+localidad+", Argentina";
            self.geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var location = results[0].geometry.location;
                    var latitud = location.lat();
                    var longitud = location.lng();
                    self.coor = new google.maps.LatLng(latitud,longitud);
                    console.log(self.coor.lat());
                    console.log(self.coor.lng());
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
                var self = this;
                
                validate_input("propiedad_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                var c = $(self.el).find("#propiedad_caracteristicas").select2("val");
                
                this.model.set({
                    "caracteristicas":c.join(";;;"),
                    "codigo":$(self.el).find("#propiedad_codigo").val(),
                    "moneda":$(self.el).find("#propiedad_monedas").val(),
                    "id_tipo_inmueble":$(self.el).find("#propiedad_tipos_inmueble").val(),
                    "id_tipo_operacion":$(self.el).find("#propiedad_tipos_operacion").val(),
                    "id_tipo_estado":$(self.el).find("#propiedad_tipos_estado").val(),
                    "precio_final":$(self.el).find("#propiedad_precio_final").val(),
                    "path":$(self.el).find("#hidden_path").val(),
                });
                
                // Listado de Imagenes
                var images = new Array();
                $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
                    images.push($(e).text());
                });
                self.model.set({"images":images});

                // Listado de Imagenes
                var planos = new Array();
                $(this.el).find("#planos_tabla .list-group-item .filename").each(function(i,e){
                    planos.push($(e).text());
                });
                self.model.set({"planos":planos});                
                
                // Listado de Propiedades Relacionados
                var relacionados = new Array();
                $(this.el).find("#propiedades_tabla_relacionados .list-group-item").each(function(i,e){
                    relacionados.push({
                        "id":$(e).find(".id").text(),
                        "destacado":0,
                    });
                });
                self.model.set({"relacionados":relacionados});
                
                // Texto del propiedad
                var cktext = CKEDITOR.instances['propiedad_texto'].getData();
                self.model.set({"texto":cktext});
                
                // Coordenadas
                if (self.marker != undefined) {
                    var pos = self.marker.getPosition();
                    var zoom = self.map.getZoom();

                    // Controlamos si el Street View esta abierto
                    var panorama = self.map.getStreetView();
                    if (panorama != undefined && panorama.getVisible()) {
                        // pov = Point Of View
                        // Es un objeto con dos parametros:
                        // heading = angulo con respecto al norte
                        // pitch = angulo con respecto a la camara de street view
                        var pov = panorama.getPov();
                        this.model.set({
                            "heading": pov.heading,
                            "pitch": pov.pitch,
                        });
                    }

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
                            propiedades.add(model);
                            location.href="app/#propiedades";
                        }
                    }
                });
            }	    
        },
        	
    });
})(app);
*/