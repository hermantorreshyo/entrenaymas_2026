// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Vehiculo = Backbone.Model.extend({
    urlRoot: "vehiculos",
    defaults: {
      nombre: "",
      tipo: "",
      patente: "",
      id_empresa:ID_EMPRESA,
    },
  });
      
})( app.models );


(function ( models ) {

  models.Asiento = Backbone.Model.extend({
    urlRoot: "asientos",
    defaults: {
      id_reserva: 0,
      id_empresa: ID_EMPRESA,
      piso: 1,
      activo: 1,
      id_vehiculo: 0,
      numero_asiento: "",
      id_tipo_tarifa: 0,
      posicion_x: 0,
      posicion_y: 0,
    },
  });
        
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Vehiculos = paginator.requestPager.extend({

    model: model,
    
    paginator_core: {
      url: "vehiculos/"
    }
  
  });

})( app.collections, app.models.Vehiculo, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.VehiculosTableView = app.mixins.View.extend({

    template: _.template($("#vehiculos_resultados_template").html()),
        
    myEvents: {
      "change #vehiculos_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {   
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      // Filtros de la vehiculo
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
      this.render();
      this.collection.on('sync', this.addAll, this);

      this.collection.server_api = {
        "filter":this.filter,
      };            
      this.collection.goTo(this.pagina);
    },

    render: function() {
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      this.$(".pagination_container").html(pagination.el);
      return this;
    },
        
    buscar: function() {
      this.filter = this.$("#vehiculos_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
      };
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
      var view = new app.views.VehiculosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },
                
  });

})(app);


// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.VehiculosItemResultados = app.mixins.View.extend({
    template: _.template($("#vehiculos_item_resultados_template").html()),
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
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"vehiculos/function/duplicar/"+self.model.id,
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
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_vehiculo_seleccionado = this.model.get("codigo");
        window.vehiculo_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#vehiculo/"+this.model.id;
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

  app.views.VehiculoEditView = app.mixins.View.extend({

    template: _.template($("#vehiculo_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .tab_link":function(e) {
        // Vemos los asientos dependiendo del piso
        var self = this;
        var filter = $("#viaje_asientos_buscar_cliente").val();
        var piso = $(e.currentTarget).data("i");
        var asientos = new app.views.AsientosView({
          "piso":piso,
          "model":self.model,
          "edicion":self.edicion,
          "id_viaje":self.id_viaje,
          "filter":filter,
        });
        this.$("#tab"+piso).html(asientos.el);
      },
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.edicion = (typeof this.options.edicion != "undefined") ? this.options.edicion : true;
      this.id_viaje = (typeof this.options.id_viaje != "undefined") ? this.options.id_viaje : 0;
      var obj = { "edicion": this.edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // En NOROESTE no se muestra
      if (ID_EMPRESA == 501) this.$(".tab_link.active").trigger('click');
    },
    
    validar: function() {
      try {
        var self = this;
        validate_input("vehiculo_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  
  
    guardar:function() {
      var self = this;
      var es_nuevo = false;
      if (this.validar()) {
        if (this.model.id == null) {
          es_nuevo = true;
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              if (es_nuevo) location.href = "app/#vehiculo/"+self.model.id;    
              else location.href = "app/#vehiculos";
            }
          }
        });
      }      
    },
          
  });
})(app);


// GRILLA DE MESAS
(function ( app ) {

  app.views.AsientosView = app.mixins.View.extend({

    template: _.template($("#asientos_template").html()),
        
    myEvents: {
      "click .buscar":"buscar",
      "click .agregar_mesa":function(e) {
        var self = this;
        var td = $(e.currentTarget).parent();
        var x = $(td).data("x");
        var y = $(td).data("y");
        var piso = $(td).data("piso");
        var modelo = new app.models.Asiento({
          "numero_asiento": "",
          "posicion_x": x,
          "posicion_y": y,
          "piso": piso,
          "id_vehiculo":self.model.id,
        });
        var view = new app.views.AsientoEditView({
          "model":modelo
        });
        crearLightboxHTML({
          "html":view.el,
          "width":550,
          "height":140,
        });
        $("#asiento_nombre").focus();
      },
    },
    
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.piso = this.options.piso;
      this.edicion = (typeof options.edicion != "undefined") ? options.edicion : false;
      this.id_viaje = (typeof options.id_viaje != "undefined") ? options.id_viaje : 0;
      this.filter = (typeof options.filter != "undefined") ? options.filter : "";
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template({
        "piso":self.piso,
        "edicion":self.edicion,
      }));

      // Si estamos editando, buscamos las asientos de este salon
      $.ajax({
        "url":"asientos/function/ver/",
        "dataType":"json",
        "data":{
          "piso":self.piso,
          "id_vehiculo":self.model.id,
          "id_viaje":self.id_viaje, // INDICA QUE TENEMOS QUE MOSTRAR LOS ASIENTOS DE ESE VIAJE
        },
        "type":"post",
        "success":function(r) {
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            var modelo = new app.models.Asiento(o);
            var view = new app.views.AsientoView({
              model: modelo,
              edicion: self.edicion,
            });

            // Si estamos filtrando por algun nombre de cliente
            if (!isEmpty(self.filter)) {
              var reg = new RegExp(self.filter, 'gi');
              var occurs = modelo.get("nombre").match(reg);
              if (occurs != null) {
                $(view.el).addClass("active")
              }
            }

            $(view.el).css("position","absolute");
            $(view.el).css("top",50*o.posicion_y);
            $(view.el).css("left",50*o.posicion_x);
            self.$(".mesas_layer").append(view.el);
          }
        },
      });
      return this;
    },
  });

})(app);


// EDICION DE LOS DATOS DE UNA MESA
(function ( app ) {

  app.views.AsientoEditView = app.mixins.View.extend({

    template: _.template($("#asiento_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "keypress #asiento_numero_asiento":function(e) {
        if (e.which == 13) { this.guardar(); }
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar la asiento?")) {
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
        validate_input("asiento_numero_asiento",IS_EMPTY,"Por favor, ingrese un numero.");
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
          "numero_asiento":self.$("#asiento_numero_asiento").val(),
          "id_tipo_tarifa": self.$("#asiento_tarifas").val(),
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




// VISTA DE UNA MESA DENTRO DE LA GRILLA
(function ( app ) {

  app.views.AsientoView = app.mixins.View.extend({

    template: _.template($("#asiento_view_template").html()),
    
    className: "mesa",

    myEvents: {
      "click":function() {
        var self = this;
        if (this.edicion && !this.start_drag) {
          // Mostramos la vista para editar una asiento
          var view = new app.views.AsientoEditView({
            "model":self.model
          });
          crearLightboxHTML({
            "html":view.el,
            "width":550,
            "height":140,
          });

        } else if (!this.edicion) {

          // Si ya tiene una reserva, la mostramos para editar
          if ($(this.el).hasClass('mesa_ocupada')) {

            // Editamos la reserva
            var reserva = new app.models.ReservaAsiento({
              "id":self.model.get("id_reserva"),
            });
            reserva.fetch({
              "success":function(){
                console.log(reserva);

                // Si somos administrador, ID_SUCURSAL == 0
                // Sino, solo podemos editar las reservas de nuestra propia sucursal
                if (ID_SUCURSAL == 0 || ID_SUCURSAL == reserva.get("id_sucursal")) {
                  var view = new app.views.ReservaAsientoEditView({
                    "model":reserva,
                  })
                  crearLightboxHTML({
                    "html":view.el,
                    "width":1000,
                    "height":500,
                  });
                }
              }
            });
            
          } else {

            // No esta ocupada
            // La marcamos para agregar una reserva despues
            if ($(this.el).hasClass('mesa_reservada')) {
              // Sacamos del array
              $(this.el).removeClass('mesa_reservada');
              var asientos = new Array();
              _.each(window.asientos_seleccionados,function(e){
                if (e.id != self.model.id) asientos.push(e);
              });
              window.asientos_seleccionados = asientos;
            } else {
              // Ponemos en el array
              if (typeof window.asientos_seleccionados == "undefined") window.asientos_seleccionados = new Array();
              window.asientos_seleccionados.push(this.model);
              $(this.el).addClass('mesa_reservada');
            }

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

      // Si la asiento esta ocupada
      if (!this.edicion && this.model.get("id_reserva")>0) {
        var id_name = "asiento_"+this.model.id;
        $(this.el).attr("id",id_name);
        $(this.el).addClass("mesa_ocupada");

        // Ponemos el tooltip
        var title = "<div class='tal'><span class='fs16'>"+this.model.get("nombre")+"</span>";
        if (!isEmpty(this.model.get("vendedor"))) title+="<br/>Vendedor: "+this.model.get("vendedor");
        if (!isEmpty(this.model.get("salida_desde"))) title+="<br/>Salida: "+this.model.get("salida_desde");
        title+="</div>";
        $(this.el).attr("title",title);
        $(this.el).tooltip({
          "html": true,
          "placement":"right",
        });

        // Ponemos el color del vendedor en caso que lo tuviera
        if (!isEmpty(this.model.get("color"))) {
          this.$("div:first").css("backgroundColor",this.model.get("color"));  
        }

        var items_menu = {
          "mover_asiento": {name: "Mover asiento" },
        };
        $.contextMenu({
          selector: "#"+id_name, 
          callback: function(key, options) {
            if (key == "mover_asiento") {
              self.mover_asiento(id_name);
            }
          },
          items: items_menu,
        });

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

    mover_asiento: function(id) {
      /*
      id = id.replace('asiento_','');
      var nuevo = prompt("Ingrese el numero de asiento nuevo");
      if (nuevo != false) {
        $.ajax({
          "url":"/sistema/"
        })
      }*/
    },

  });
})(app);
