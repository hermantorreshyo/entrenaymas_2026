// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ClasificadoObjeto = Backbone.Model.extend({
        urlRoot: "objetos",
        defaults: {
            // Atributos que no se persisten directamente
            images: [],
            nombre: "",
            cliente: "",
            id_cliente: 0,
            moneda: "",
            precio_final: 0,
            activo: 1,
            nuevo: 0,
            destacado: 0,
            texto: "",
			valido_desde: "",
			valido_hasta: "",
			texto_privado: "",
            cantidad_consultas: 0,
            id_usuario: ID_USUARIO,
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.ClasificadosObjetos = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'fecha',
            order: 'desc',
        },
    
        paginator_core: {
            url: "objetos/function/ver",
        }
	    
    });

})( app.collections, app.models.ClasificadoObjeto, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ClasificadosObjetosTableView = app.mixins.View.extend({

        template: _.template($("#clasificados_objetos_resultados_template").html()),
            
        myEvents: {
            "change #objetos_buscar":"buscar",
        },
        
		initialize : function (options) {
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.permiso = this.options.permiso;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
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
                "seleccionar":this.habilitar_seleccion
            }));
            
            // Cargamos el paginador
            $(this.el).find(".pagination_container").html(pagination.el);
        },
        
        buscar: function() {
            var self = this;
            var filter = this.$("#objetos_buscar").val();
            self.filter = (typeof filter != "undefined") ? filter.trim() : "";
            self.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : 0;
            this.collection.server_api = {
                "filter":self.filter,
                "id_usuario":self.id_usuario,
            };
            this.collection.pager();            
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);
        },
        
        addOne : function ( item ) {
            var view = new app.views.ObjetosItemResultados({
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
    app.views.ObjetosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#clasificados_objetos_item_resultados_template").html()),
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
                  "table":"clasif_objetos",
                  "url":"objetos/function/change_property/",
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
                  "table":"clasif_objetos",
                  "url":"objetos/function/change_property/",
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
                  "table":"clasif_objetos",
                  "url":"objetos/function/change_property/",
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
                        "url":"objetos/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            objetos.add(d);
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
                window.open("objetos/function/compartir/"+this.model.id+"/","_blank","height=250,width=450,location=no,resizable=no,scrollbars=no,titlebar=no,menubar=no,top=200,left=200");
                return false;
            },            
        },
        seleccionar: function() {
            if (this.habilitar_seleccion) {
                window.codigo_objeto_seleccionado = this.model.get("codigo");
                window.objeto_seleccionado = this.model;
                $('.modal:last').modal('hide');
            } else {
                location.href="app/#clasificado_objeto/"+this.model.id;
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

    app.views.ClasificadoObjetoEditView = app.mixins.View.extend({

        template: _.template($("#clasificado_objeto_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",            
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

            //this.cargar_clientes();
            
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
                render: "#clasificado_objeto_clientes",
                selected: self.model.get("id_cliente"),
                success:function(c) {
                  crear_select2("clasificado_objeto_clientes");
                }                    
            });
        },
            
        validar: function() {
            try {
                var self = this;
                
                this.model.set({
                    "moneda":$(self.el).find("#clasificado_objeto_monedas").val(),
                    "precio_final":$(self.el).find("#clasificado_objeto_precio_final").val(),
					"id_cliente":$(self.el).find("#clasificado_objeto_clientes").val(),
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
                
                // Texto del objeto
                self.model.set({
    		    "texto":$("#clasificado_objeto_texto").val(),
    		    "texto_privado":$("#clasificado_objeto_texto_privado").val()
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
                            location.href="app/#clasificados_objetos";
                        }
                    }
                });
            }	    
        },
        	
    });
})(app);
