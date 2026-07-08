// -----------
//   MODELO
// -----------

(function ( models ) {

  models.FacturasPaycomet = Backbone.Model.extend({
    urlRoot: "facturas_paycomet",
    defaults: {
      id_empresa: ID_EMPRESA,
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {
  collections.FacturasPaycomet = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "facturas_paycomet/"
    }
  });
})( app.collections, app.models.FacturasPaycomet, Backbone.Paginator);




// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.FacturasPaycometTableView = app.mixins.View.extend({

    template: _.template($("#facturas_paycomet_table_view").html()),

    myEvents: {
      "change .buscar":"buscar",
      "click .ver_mis_tarjetas": function(e) {
        var self = this;

        $.ajax({
          "url": "usuarios/function/get_tarjetas",
          "type": "post",
          "dataType": "json",
          "data": {
            "id_usuario": ID_USUARIO,
            "id_empresa": ID_EMPRESA,
          },success:function(r) {

            var v = new app.views.TarjetasUsuariosTableView({
              model: new app.models.AbstractModel({
                "tarjetas": r,
                "usuario": "",
                "id_usuario": ID_USUARIO,
              }),
              callback: function(mensaje) {
                console.log(mensaje);
              },
            });

            crearLightboxHTML({
              "html":v.el,
              "width":800,
              "height":400,
            });       
          },
        });

      },
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: this.collection
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: this.collection
      });
            
      this.collection.on('add', this.addOne, this);
      this.collection.on('all', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      new app.mixins.Select({
        modelClass: app.models.Usuario,
        url: "usuarios/?limit=0&offset=99999999",
        render: "#facturas_paycomet_usuario",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        onComplete: function() {
          self.$("#facturas_paycomet_usuario").select2();
        },        
      }); 


      this.buscar();
    },

    buscar: function() {
      var filtros = {};
      if (PERFIL == 1355) {
        filtros.id_usuario = this.$("#facturas_paycomet_usuario").val();
      } else {
        filtros.id_usuario = ID_USUARIO;
      }
      this.collection.server_api = filtros;
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.FacturasPaycometItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);

// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {
  app.views.FacturasPaycometItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#facturas_paycomet_item').html()),
    events: {
      "click .imprimir_factura": function() {
        /*
        $('.modal:last').modal('hide'); 
        var iframe = "<iframe style='width:100%; border:none; height:600px;' src='facturas_paycomet/function/ver_factura/"+this.model.id+"></iframe>";
        iframe+='<div class="text-right wrapper">';
        iframe+='<button class="btn btn-info btn-addon m-r">';
        iframe+='</button>';
        iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
        iframe+='</div>';
        crearLightboxHTML({
          "html":iframe,
          "width":920,
          "height":600,
          "callback": function() {

          },
        });*/        
      },
      "click .ver_tarjetas_usuarios": function(e) {
        var self = this;

        $.ajax({
          "url": "usuarios/function/get_tarjetas",
          "type": "post",
          "dataType": "json",
          "data": {
            "id_usuario": self.model.get("id_usuario"),
            "id_empresa": ID_EMPRESA,
          },success:function(r) {

            var v = new app.views.TarjetasUsuariosTableView({
              model: new app.models.AbstractModel({
                "tarjetas": r,
                "usuario": self.model.get("usuario"),
                "id_usuario": self.model.get("id_usuario"),
              }),
              callback: function(mensaje) {
                self.$(".ver_tarjetas_usuarios").html(mensaje);
              },
            });

            crearLightboxHTML({
              "html":v.el,
              "width":800,
              "height":400,
            });       
          },
        });

      },
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );

(function ( app ) {
  app.views.TarjetasUsuariosTableView = app.mixins.View.extend({
    template: _.template($('#tarjetas_usuarios_table_view').html()),
    events: {
      "click .cerrar": function(e) {
        $('.modal:last').modal('hide');
      },
      "click .eliminar_tarjeta": function(e) {
        $(e.currentTarget).parents("tr").addClass("dn eliminado");
      },
      "click .guardar": "guardar",
      "click #tarjetas_usuarios_agregar":"agregar_tarjeta",
    },
    
    agregar_tarjeta: function() {
      var numero = $("#tarjetas_usuarios_numero").val();
      var caducidad = $("#tarjetas_usuarios_vencimiento").val();

      if (numero.length != 19) {
        alert ("Ingrese una tarjeta valida");
        return false;
      }

      if (caducidad.length != 5) {
        alert ("Ingrese una fecha de vencimiento valida");
        return false;
      }

      var tr = "<tr data-id='0'>";
      tr+="<td class='numero'>"+numero.replaceAll('-', ' ')+"</td>";
      tr+="<td class='caducidad'>"+caducidad+"</td>";
      tr+="<td class=''>";
      tr+="<button title='Eliminar Tarjeta' data-toggle='tooltip' title='Tooltip on top' class='ml15 btn btn-sm btn-white eliminar_tarjeta'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";

      $("#usuarios_tarjetas_table tbody").append(tr);
      /*
      $("#tarjetas_usuarios_numero").val('');
      $("#tarjetas_usuarios_vencimiento").val('');*/
    },

    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;

      this.permiso = this.options.permiso;
      this.callback = this.options.callback;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso};
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      $(this.el).find("#tarjetas_usuarios_numero").mask("9999-9999-9999-9999");
      $(this.el).find("#tarjetas_usuarios_vencimiento").mask("99/99");

      return this;
    },
    guardar: function() {
      var self = this;

      var tarjetas = new Array();
      var tarjetas_disponibles = 0;
      $("#usuarios_tarjetas_table tbody tr").each(function(i, e) {

        var eliminado = $(e).hasClass("eliminado") ? 1 : 0;

        if (eliminado == 0) tarjetas_disponibles++;

        tarjetas.push({
          "numero": $(e).find(".numero").text().replaceAll(`\n`, ''),
          "caducidad": $(e).find(".caducidad").text().replaceAll(`\n`, ''),
          "id": $(e).attr("data-id"),
          "eliminado": eliminado,
        });
      });


      if (tarjetas.length == 0) {
        alert ("Por favor ingrese al menos un metodo de pago");
        return false;
      }

      var mensaje = tarjetas_disponibles+" tarjetas disponibles - Modificar";

      $.ajax({
        "url": "usuarios/function/save_tarjetas",
        "type": "post",
        "dataType": "json",
        "data": {
          "tarjetas": JSON.stringify(tarjetas),
          //Obtenemos id_usuario de options y no del modelo
          "id_usuario": self.model.get("id_usuario"),
          "id_empresa": ID_EMPRESA,
        }, success: function(r) {
          if (r.error == 1) {
            alert (r.mensaje);
          } else {
            alert (r.mensaje);
            self.callback(mensaje);
            $('.modal:last').modal('hide');
          }
        },

      });
    },
  });

})( app );

(function ( app ) {
  app.views.SeleccionarTarjetasTableView = app.mixins.View.extend({
    template: _.template($('#seleccionar_tarjetas_table_view').html()),
    events: {
      "click .cerrar": function(e) {
        $('.modal:last').modal('hide');
      },
      "click .guardar": "guardar",
      "click .seleccionar_tarjeta": function(e){
        this.$(".seleccionar_tarjeta").prop("checked", false);
        $(e.currentTarget).prop("checked", true);

        this.$(".guardar").prop("disabled", false);
      },
    },

    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;

      this.permiso = this.options.permiso;
      this.callback = this.options.callback;
      _.bindAll(this);
      this.render();

      this.$(".numero input").mask("9999-9999-9999-9999");
      this.$(".caducidad input").mask("99/99");
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso};
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      $(this.el).find(".numero input").mask("9999-9999-9999-9999");
      $(this.el).find(".caducidad input").mask("99/99");

      return this;
    },
    guardar: function() {
      var self = this;
      var id = "";
      var numero_tarjeta = "";
      var caducidad_tarjeta = "";
      var cvv_tarjeta = "";

      self.$("#seleccionar_tarjeta_table tbody tr").each(function(i, e) {
        if ($(e).find(".seleccionar_tarjeta").is(":checked")) {
          id = $(e).find(".seleccionar_tarjeta").parent().parent().parent().attr("data-id");
          numero_tarjeta = (id == 0) ? $(e).find(".numero input").val() : $(e).find(".numero").text();
          caducidad_tarjeta = (id == 0) ? $(e).find(".caducidad input").val() : $(e).find(".caducidad").text();
          cvv_tarjeta = (id == 0) ? $(e).find(".cvv input").val() : $(e).find(".cvv input").val();
        }
      });

      if (id == "") {
        alert ("Por favor seleccione una tarjeta");
        return false;
      }

      numero_tarjeta = numero_tarjeta.replaceAll(`\n`, '');
      caducidad_tarjeta = caducidad_tarjeta.replaceAll(`\n`, '');

      if (numero_tarjeta.length != 19) {
        alert ("Ingrese una tarjeta valida");
        return false;
      }

      if (caducidad_tarjeta.length != 5) {
        alert ("Ingrese una fecha de vencimiento valida");
        return false;
      }

      if (cvv_tarjeta == "") {
        alert ("Ingrese un CVV valido");
        return false;
      }

      console.log(id);
      console.log(numero_tarjeta);
      console.log(caducidad_tarjeta);
      console.log(cvv_tarjeta);
    },
  });

})( app );

