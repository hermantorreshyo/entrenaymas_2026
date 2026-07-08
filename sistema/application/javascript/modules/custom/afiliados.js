// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Afiliado = Backbone.Model.extend({
        urlRoot: "afiliados/",
        defaults: {
            nombre: "",
            apellido: "",
            activo: 1,
            id_empresa: ID_EMPRESA,
            id_usuario: ID_USUARIO,
            dni: "",
            fecha_nac: "",
            calle: "",
            numero: "",
            piso: "",
            depto: "",
            localidad: "",
            partido: "",
            profesion: "",
            sexo: "M",
            telefono: "",
            celular: "",
            email: "",
            password: "",
            afiliado: 0,
            referente: "",
            facebook: "",
            twitter: "",
            instagram: "",
            otras_redes: "",
            password: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Afiliados = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "afiliados/"
		},

        paginator_ui: {
            perPage: 10,
            order_by: 'apellido',
            order: 'asc',
        },
		
	});

})( app.collections, app.models.Afiliado, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.AfiliadoItem = app.mixins.View.extend({
        tagName: "tr",
        template: _.template($('#afiliados_item').html()),
      	myEvents: {
            "click .data":"seleccionar",
    		"click .delete": "borrar",
    		"click .duplicar": "duplicar",
            "keyup .radio":function(e) {
                if (e.which == 13) { this.seleccionar(); }
            },
			
            "focus .radio":function(e) {
                $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
				$(e.currentTarget).parents("tr").addClass("fila_roja");
				$(e.currentTarget).prop("checked",true);
            },
			"blur .radio":function(e) {
				$(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
				$(".radio").prop("checked",false);
			},
            "click .activo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"custom_afiliados",
                  "url":"afiliados/function/change_property/",
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
        seleccionar: function() {
            if (this.habilitar_seleccion) {
                window.codigo_afiliado_seleccionado = this.model.get("codigo");
                window.afiliado_seleccionado = this.model;
                $('.modal:last').modal('hide');                
            } else {
                if (!this.habilitar_seleccion) location.href="app/#afiliado/"+this.model.id;
            }
        },
        initialize: function(options) {
            // Si el modelo cambia, debemos renderizar devuelta el elemento
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.permiso = this.options.permiso;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            _.bindAll(this);
        },
        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var obj = { permiso: this.permiso, seleccionar: this.habilitar_seleccion };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

            $(this.el).html(this.template(obj));
            return this;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista                
                //this.model.set({"activo":0});
                //this.render();
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

    app.views.AfiliadosTableView = app.mixins.View.extend({

    	template: _.template($("#afiliados_panel_template").html()),
        
        myEvents: {
            "keydown #afiliados_table tbody tr .radio:first":function(e) {
                // Si estamos en el primer elemento y apretamos la flechita de arriba
                if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
            },
            "click .exportar_excel":"exportar",
            "click .importar_excel":"importar",
            "click .exportar_csv":"exportar_csv",
            "click .importar_csv":"importar_csv",
            "change #afiliados_buscar":"buscar",
            "click .buscar":"buscar",

            // METODOS ESPECIALES
            "click .importar_cuentas":"importar_cuentas",
            "click .importar_mora":"importar_mora",
        },

		initialize : function (options) {
            
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;

			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
				collection: this.collection
			});

            this.collection.on('sync', this.addAll, this);
            this.collection.goTo(1);            

            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
		},
        
        buscar: function() {
            var datos = {};
            datos.term = self.$("#afiliados_buscar").val().trim();
            this.collection.server_api = datos;
            this.collection.pager();
        },
        
        render: function() {
            
        },
        
        exportar: function() {
            this.exportar_excel({
                "filename":"afiliados",
                "table":"afiliados",
            });            
        },

        importar: function() {
        },
        
        exportar_csv: function(obj) {
            window.open("afiliados/function/exportar_csv/","_blank");
        },
        
        importar_csv: function() {
            app.views.importar = new app.views.Importar({
                "table":"afiliados",
            });
            crearLightboxHTML({
                "html":app.views.importar.el,
                "width":450,
                "height":140,
            });
        }, 

		addAll : function () {
            this.$("#afiliados_table tbody").empty();
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
			var view = new app.views.AfiliadoItem({
				model: item,
				permiso: this.permiso,
                habilitar_seleccion: this.habilitar_seleccion, 
			});
			$(this.el).find("tbody").append(view.render().el);
		},
	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.AfiliadoEditView = app.mixins.View.extend({

		template: _.template($("#afiliados_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
		},

        initialize: function(options) {
            this.options = options;
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.render();
        },

        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
            var self = this;
        	var edicion = false;
        	if (this.options.permiso > 1) edicion = true;
        	var obj = { "edicion": edicion, "id":this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());
        	$(this.el).html(this.template(obj));
            
            var fecha_nac = this.model.get("fecha_nac");
            if (isEmpty(fecha_nac)) fecha_nac = new Date();
            createdatepicker($(this.el).find("#afiliados_fecha_nac"),fecha_nac);

            return this;
        },

        validar: function() {
            try {
                validate_input("afiliados_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
                validate_input("afiliados_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
                var password_1 = $("#afiliados_password").val();
                var password_2 = $("#afiliados_password_2").val();
                if (password_1 != password_2) {
                    show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
                    $("#afiliados_password_2").focus();
                    return false;
                }
                if (!isEmpty(password_1)) {
                    password_1 = hex_md5(password_1);
                    this.model.set({
                        "password":password_1
                    });                    
                }

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
                this.model.save({},{
                    success: function(model,response) {
                        if (response.error == 1) {
                            show(response.mensaje);
                        } else {
                            if (control.check("afiliados")<3) {
                                location.reload();
                            } else {
                                location.href="app/#afiliados";    
                            }
                        }
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.Afiliado()
            this.render();
        },
		
	});

})(app.views, app.models);