// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Publicidad = Backbone.Model.extend({
        urlRoot: "publicidades",
        defaults: {
            // Atributos que no se persisten directamente
            images: [],
            categoria: "",
            path: "",
            path_2: "",
            activo: 1,
            nombre: "",
            id_tipo_publicidad: 0,
            valida_desde: "",
            valida_hasta: "",
            id_categoria: 0,
            cliente: "",
            id_cliente: 0,
            codigo_html: "",
            link: "",
            relevancia: 0,
            costo: 0,
            cerrar: 0,
            limite_visualizaciones:0,
            id_vendedor: 0,
            dias_vencimiento: 0,
        },
    });
	    
})( app.models );


// -----------
//   MODELO
// -----------

(function ( models ) {

    models.PublicidadImpresion = Backbone.Model.extend({
        urlRoot: "publicidades",
        defaults: {
            activo: 0,
            nombre: "",
            categoria: "",
            relevancia: 0,
            costo: 0,
            impresiones: 0,
            promedio_impresiones_dia: 0,
            costo_impresion: 0,
            clicks: 0,
            costo_click: 0,
        },
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Publicidades = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 50,
            order_by: 'desde',
            order: 'desc',
        },
    
        paginator_core: {
            url: "publicidades/function/ver/",
        }
	    
    });

})( app.collections, app.models.Publicidad, Backbone.Paginator);


(function (collections, model, paginator) {

    collections.PublicidadesImpresiones = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'desde',
            order: 'desc',
        },
    
        paginator_core: {
            url: "publicidades/function/impresiones/",
        }
	    
    });

})( app.collections, app.models.PublicidadImpresion, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.PublicidadesTableView = app.mixins.View.extend({

        template: _.template($("#publicidades_resultados_template").html()),
            
        myEvents: {
            "change #publicidades_buscar":function() {
                this.filter = $("#publicidades_buscar").val().trim();
                this.buscar();
            },
            "keydown #publicidades_tabla tbody tr .radio:first":function(e) {
                // Si estamos en el primer elemento y apretamos la flechita de arriba
                if (e.which == 38) { e.preventDefault(); $("#publicidades_texto").focus(); }
            },
            "change #publicidades_vendedores":function(e) {
                this.id_vendedor = $(e.currentTarget).val();
                this.buscar();
            },
            "change #publicidades_clientes":function(e) {
                this.id_cliente = $(e.currentTarget).val();
                this.buscar();
            },
            "change #publicidades_activo":function(e) {
                this.activo = $(e.currentTarget).val();
                this.buscar();                
            },
            "click .nuevo":function() {
                var view = new app.views.PublicidadEditView({
                    "model":new app.models.Publicidad(),
                    "habilitar_seleccion":false,
                    "permiso":3,
                });
                crearLightboxHTML({
                    "html":view.el,
                    "width":700,
                    "height":140,
                });
            },
        },
        
        initialize : function (options) {
            
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.permiso = this.options.permiso;
            this.id_categoria = (typeof this.options.id_categoria != "undefined") ? this.options.id_categoria : 0;
            this.id_cliente = (typeof this.options.id_cliente != "undefined") ? this.options.id_cliente : 0;
            this.id_vendedor = (typeof this.options.id_vendedor != "undefined") ? this.options.id_vendedor : 0;
            this.activo = (typeof this.options.activo != "undefined") ? this.options.activo : -1;
            this.render();
            this.collection.on('sync', this.addAll, this);
            this.collection.goTo(1);
        },

        render: function() {
            var self = this;
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
            
            $(this.el).find("#publicidades_buscar_categorias").select2({
            }).on("change",function(e){
                self.id_categoria = $(self.el).find("#publicidades_buscar_categorias").select2("val");
                self.buscar();
            });
            
            // Creamos el select
            new app.mixins.Select({
                modelClass: app.models.Cliente,
                url: "clientes/",
                firstOptions: ["<option value='0'>Cliente</option>"],
                render: "#publicidades_clientes",
                success:function(c) {
                    $("#publicidades_clientes").removeClass("form-control");
                    $("#publicidades_clientes").select2({});
                }                    
            });
        },
        
        buscar: function() {
            var self = this;
            this.collection.server_api = {
                "filter":self.filter,
                "id_vendedor":self.id_vendedor,
                "id_cliente":self.id_cliente,
                "id_categoria":self.id_categoria,
                "activo":self.activo,
            };
            this.collection.pager();
        },
        
        addAll : function () {
            this.$("#publicidades_tabla tbody").empty();
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
            var view = new app.views.PublicidadesItemResultados({
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
    app.views.PublicidadesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#publicidades_item_resultados_template").html()),
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
            "click .activo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"not_publicidades",
                  "url":"publicidades/function/change_property/",
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
                        "url":"publicidades/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            publicidades.add(d);
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
            var self = this;
            if (this.habilitar_seleccion) {
                window.codigo_publicidad_seleccionado = this.model.get("codigo");
                window.publicidad_seleccionado = this.model;
                $('.modal:last').modal('hide');
            } else {
                var view = new app.views.PublicidadEditView({
                    "model":self.model,
                    "permiso":3,
                });
                crearLightboxHTML({
                    "html":view.el,
                    "width":700,
                    "height":140,
                });
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

    app.views.PublicidadEditView = app.mixins.View.extend({

        template: _.template($("#publicidad_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            
            "change #publicidad_categorias":function(e) {
                var ancho = this.$("#publicidad_categorias option:selected").data("ancho");
                var alto = this.$("#publicidad_categorias option:selected").data("alto");
                
				var id_tipo = this.$("#publicidad_categorias option:selected").data("id_tipo");
                this.$(".publicidad_tipo_container").hide();
                this.$("#publicidad_tipo_"+id_tipo).show();
				
                this.$("#path_height").val(alto);
                this.$("#path_width").val(ancho);
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
            
            this.render();
			
            var valida_desde = this.model.get("valida_desde");
            if (isEmpty(valida_desde)) valida_desde = moment().startOf('month').toDate();
            createtimepicker($(this.el).find("#publicidad_valida_desde"),valida_desde);
            
            var valida_hasta = this.model.get("valida_hasta");
            if (isEmpty(valida_hasta)) valida_hasta = moment().endOf('month').toDate();
            createtimepicker($(this.el).find("#publicidad_valida_hasta"),valida_hasta);            
        },
        
        render: function() {
            this.render_tabla_fotos();
            $(this.el).find("#publicidades_tabla").sortable();
            this.$("#publicidad_categorias").change();
            this.cargar_clientes();
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
                render: "#publicidad_clientes",
                selected: self.model.get("id_cliente"),
                onComplete:function(c) {
                  crear_select2("publicidad_clientes");
                }                    
            });
        },
		
        
        render_tabla_fotos: function() {
          var images = this.model.get("images");
          for(var i=0;i<images.length;i++) {
            var path = images[i];
            var pth = path+"?t="+parseInt(Math.random()*100000);
            var li = "";
            li+="<li class='list-group-item'>";
            li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
            li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
            li+=" <span class='filename'>"+path+"</span>";
            li+=" <span class='cp pull-right m-t eliminar_foto'><i class='fa fa-fw fa-times'></i> </span>";
            li+=" <span data-id='propiedades' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
            li+="</li>";
            this.$("#publicidades_tabla").append(li);
          }
        },        
        
        validar: function() {
            try {
                var self = this;
                
                validate_input("publicidad_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                this.model.set({
                    "path":$(self.el).find("#hidden_path").val(),
                    "path_2":$(self.el).find("#hidden_path_2").val(),
                    "valida_desde":$(this.el).find("#publicidad_valida_desde").val(),
                    "valida_hasta":$(this.el).find("#publicidad_valida_hasta").val(),
                    "cliente":$(self.el).find("#publicidad_clientes option:selected").text(),
                    "id_cliente":$(self.el).find("#publicidad_clientes").val(),
                    "id_vendedor":$(self.el).find("#publicidad_vendedores").val(),
                    "vendedor":$(self.el).find("#publicidad_vendedores option:selected").text(),
                });
                
                // Listado de Imagenes
                var images = new Array();
                $(this.el).find("#publicidades_tabla .list-group-item .filename").each(function(i,e){
                    images.push($(e).text());
                });
                self.model.set({"images":images});
                
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
                            app.views.publicidadesTableView.buscar();
                            $('.modal:last').modal('hide');
                            //publicidades.add(model);
                            //location.href="app/#publicidades";
                        }
                    }
                });
            }	    
        },        
	
    });
})(app);





// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.PublicidadesImpresionesView = app.mixins.View.extend({

        template: _.template($("#publicidades_impresiones_resultados_template").html()),
            
        myEvents: {
            "click .fa-search":function() {
                this.collection.pager();
            },
        },
        
        initialize : function (options) {
            
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.permiso = this.options.permiso;
      
            // Creamos la lista de paginacion
            var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
                collection: this.collection
            });
            
            this.collection.on('sync', this.addAll, this);
			
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "fecha_desde":"",
                "fecha_hasta":"",
            }));
            
            // Toma un mes anterior, el mes actual, y el mes siguiente
            var fecha_desde = new Date();
            var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
            fecha_desde = new Date(y, m, 1);
            fecha_hasta = new Date(y, m + 1, 0);						            
            
            createdatepicker($(this.el).find("#publicidades_impresiones_fecha_desde"),fecha_desde);
            createdatepicker($(this.el).find("#publicidades_impresiones_fecha_hasta"),fecha_hasta);            
            
            // Cargamos el paginador
            $(this.el).find(".pagination_container").html(pagination.el);
        },
        
        addAll : function () {
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);
        },
        
        addOne : function (item) {
            var view = new app.views.PublicidadesImpresionesItemResultados({
                model: item,
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
        
    });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.PublicidadesImpresionesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#publicidades_impresiones_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            
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

