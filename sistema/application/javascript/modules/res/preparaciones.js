// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Mesa = Backbone.Model.extend({
        urlRoot: "mesas",
        defaults: {
            id_pedido: 0,
            id_estado_pedido: 0,
            id_empresa: ID_EMPRESA,
            id_salon: 0,
            nombre: "",
            forma: "R", // R = Redonda ; C = Cuadrada
            posicion_x: 0,
            posicion_y: 0,
        },
    });
	    
})( app.models );


(function ( models ) {

    models.Salon = Backbone.Model.extend({
        urlRoot: "salones",
        defaults: {
            id_empresa: ID_EMPRESA,
            nombre: "",
            zoom: 1,
        },
    });
        
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Mesas = paginator.requestPager.extend({

        model: model,
        
        paginator_core: {
            url: "mesas/"
        }
    
    });

})( app.collections, app.models.Mesa, Backbone.Paginator);


// GRILLA DE MESAS
(function ( app ) {

    app.views.MesasView = app.mixins.View.extend({

        template: _.template($("#mesas_template").html()),
            
        myEvents: {
            "click .buscar":"buscar",
            "click .agregar_mesa":function(e) {
                var td = $(e.currentTarget).parent();
                var x = $(td).data("x");
                var y = $(td).data("y");
                var id_salon = $(td).data("id_salon");
                var modelo = new app.models.Mesa({
                    "nombre": "",
                    "posicion_x": x,
                    "posicion_y": y,
                    "id_salon": id_salon,
                });
                var view = new app.views.MesaEditView({
                    "model":modelo
                });
                crearLightboxHTML({
                    "html":view.el,
                    "width":550,
                    "height":140,
                });
                $("#mesa_nombre").focus();
            },
        },
        
		initialize : function (options) {
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.id_salon = this.options.id_salon;
            this.edicion = (typeof options.edicion != "undefined") ? options.edicion : false;
            this.render();
		},

        render: function() {

            var self = this;
            $(this.el).html(this.template({
                "id_salon":self.id_salon,
                "edicion":self.edicion,
            }));

            // Si estamos editando, buscamos las mesas de este salon
            $.ajax({
                "url":"mesas/function/ver/",
                "dataType":"json",
                "data":{
                    "id_salon":self.id_salon,
                    "ver_pedidos":1, // Si la mesa tiene pedido activo, que lo muestre
                },
                "type":"post",
                "success":function(r) {
                    for(var i=0;i<r.results.length;i++) {
                        var o = r.results[i];
                        var modelo = new app.models.Mesa(o);
                        var view = new app.views.MesaView({
                            model: modelo,
                            edicion: self.edicion,
                        });
                        $(view.el).css("position","absolute");
                        $(view.el).css("top",50*o.posicion_y);
                        $(view.el).css("left",50*o.posicion_x);
                        self.$(".mesas_layer").append(view.el);
                    }
                },
            });

            // Sino, debemos buscar los pedidos que hay activos en las mesas

            return this;
        },
        
    });

})(app);


// VISTA DE UNA MESA DENTRO DE LA GRILLA
(function ( app ) {

    app.views.MesaView = app.mixins.View.extend({

        template: _.template($("#mesa_view_template").html()),
        
        className: "mesa",

        myEvents: {
            "click":function() {
                var self = this;
                if (this.edicion && !this.start_drag) {
                    // Mostramos la vista para editar una mesa
                    var view = new app.views.MesaEditView({
                        "model":self.model
                    });
                    crearLightboxHTML({
                        "html":view.el,
                        "width":550,
                        "height":140,
                    });

                } else if (!this.edicion) {

                    // Si no tiene pedido cargado
                    var id_pedido = this.model.get("id_pedido");
                    if (id_pedido == 0) {

                        // Mostramos para tomar el pedido
                        var modelo = new app.models.PedidoMesa({
                            titulo: "Mesa "+self.model.get("nombre"),
                            id_referencia: self.model.id, // ID_MESA
                        });
                        var view = new app.views.PedidoMesaEditView({
                            "model":modelo
                        });
                        crearLightboxHTML({
                            "html":view.el,
                            "width":550,
                            "height":140,
                        });

                    } else {

                        // Buscamos el pedido y lo mostramos
                        var modelo = new app.models.PedidoMesa({
                            "id":id_pedido,
                            "titulo": "Mesa "+self.model.get("nombre"),
                        });
                        modelo.fetch({
                            "success":function() {
                                var view = new app.views.PedidoMesaEditView({
                                    "model":modelo
                                });
                                crearLightboxHTML({
                                    "html":view.el,
                                    "width":550,
                                    "height":140,
                                });
                            }
                        })
                    }
                }
            }
        },
                
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.edicion = (typeof options.edicion != "undefined") ? options.edicion : false;
            this.render();
            self.start_drag = false;
        },

        render: function() {

            var self = this;
            var obj = { 
                "id":this.model.id,
                "edicion":this.edicion,
            }
            _.extend(obj,this.model.toJSON());
            $(this.el).html(this.template(obj));

            // Si la mesa esta ocupada
            if (!this.edicion && this.model.get("id_pedido")>0) {
                if (this.model.get("id_estado_pedido") == 1) {
                    $(this.el).addClass("mesa_reservada");    
                } else {
                    $(this.el).addClass("mesa_ocupada");
                }
                
            }

            // Hacemos que el elemento se pueda mover
            if (this.edicion) {
                $(this.el).draggable({ 
                    grid: [ 50, 50 ],
                    start: function() {
                        self.start_drag = true;
                    },
                    stop: function(e,ui) {
                        var x = ui.position.left / 50;
                        var y = ui.position.top / 50;
                        self.model.save({
                            "posicion_x":x,
                            "posicion_y":y,
                        });
                    }
                });                
            }
        },

    });
})(app);


// EDICION DE LOS DATOS DE UNA MESA
(function ( app ) {

    app.views.MesaEditView = app.mixins.View.extend({

        template: _.template($("#mesa_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "keypress #mesa_nombre":function(e) {
                if (e.which == 13) { this.guardar(); }
            },
            "click .eliminar":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                if (confirm("Realmente desea eliminar la mesa?")) {
                    this.model.destroy();   // Eliminamos el modelo
                    location.reload();
                }
                return false;
            },
        },
                
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.render();
        },

        render: function() {
            var obj = { "id":this.model.id }
            _.extend(obj,this.model.toJSON());
            $(this.el).html(this.template(obj));
        },

        validar: function() {
            try {
                var self = this;
                validate_input("mesa_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                return true;
            } catch(e) {
                return false;
            }
        },	
	
        guardar:function() {
            var self = this;
            if (this.validar()) {
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({
                    "nombre":self.$("#mesa_nombre").val(),
                },{
                    success: function(model,response) {
                        if (response.error == 1) {
                            show(response.mensaje);
                            return;
                        } else {
                            self.cerrar();
                        }
                    }
                });
            }	    
        },

        cerrar: function() {
            // Cerramos el modal
            $('.modal:last').modal('hide');
            // Volvemos a buscar
            $(".tab_link.active").trigger("click");
        },
        	
    });
})(app);

// TABS DE SALONES
(function ( app ) {

    app.views.SalonesView = app.mixins.View.extend({

        template: _.template($("#salones_template").html()),
            
        myEvents: {
            "click .nuevo":function(e) {
                var view = new app.views.SalonEditView({
                    "model":new app.models.Salon({id:0})
                });
                crearLightboxHTML({
                    "html":view.el,
                    "width":550,
                    "height":140,
                });
            },
            "click .tab_link":function(e) {
                var self = this;
                var id_salon = $(e.currentTarget).data("id");
                var i = $(e.currentTarget).data("i");
                var mesas = new app.views.MesasView({
                    "id_salon":id_salon,
                    "edicion":self.edicion,
                });
                this.$("#tab"+i).html(mesas.el);
            },
            "click .editar_salon":function(e) {
                var id = $(e.currentTarget).data("id");
                var modelo = new app.models.Salon({"id":id});
                modelo.fetch({
                    "success":function() {
                        var view = new app.views.SalonEditView({
                            "model":modelo
                        });
                        crearLightboxHTML({
                            "html":view.el,
                            "width":550,
                            "height":140,
                        });
                    }
                });
                e.stopPropagation();
                return false;
            },
        },
                
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.edicion = (typeof options.edicion != "undefined") ? options.edicion : false;
            this.render();
        },

        render: function() {
            var self = this;
            $(this.el).html(this.template({
                edicion: self.edicion
            }));
            this.$(".tab_link.active").trigger('click');
        },

    });
})(app);

// EDICION DE LOS DATOS DE UN SALON
(function ( app ) {

    app.views.SalonEditView = app.mixins.View.extend({

        template: _.template($("#salon_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .eliminar":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                if (confirm("Realmente desea eliminar el salon con todas las mesas?")) {
                    this.model.destroy();   // Eliminamos el modelo
                    location.reload();
                }
                return false;
            },
        },
                
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.render();
        },

        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
        },

        validar: function() {
            try {
                var self = this;
                validate_input("salon_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
                            location.reload();
                        }
                    }
                });
            }       
        },
            
    });
})(app);