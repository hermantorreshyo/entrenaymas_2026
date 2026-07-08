(function ( models ) {

  models.SindiPractica = Backbone.Model.extend({
    urlRoot: "sindi_practicas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      numero: 0,
      fecha: moment().format("DD/MM/YYYY"),
      importe: 0,
      id_tipo_bono: 0,
      id_afiliado: 0,
      id_paciente: 0,
      id_condicion_especial: 0,
      id_concepto: 0,
      hospital: 0,
      anulada: 0,
      codigoafiliado: "",
      identificadorafiliado: "",
      nombreafiliado: "",
      condicionespecial: "",
      items: [],
      id_tipo_practica: 0,
      impresa: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiPracticas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_practicas/function/buscar"
    }
  });
})( app.collections, app.models.SindiPractica, Backbone.Paginator);


(function ( app ) {
  app.views.SindiPracticaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_practicas_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar"
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.tabla = options.tabla;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = {
        permiso: this.permiso,
        small: ((typeof this.options.small != "undefined") ? this.options.small : 0),
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      return this;
    },
    editar: function() {
      var self = this;
      var sindi_practica = new app.models.SindiPractica({ "id": self.model.id });
      sindi_practica.fetch({
        "success":function() {
          var that = self
          var vista = new app.views.SindiPracticaEditView({
            model: sindi_practica,
            permiso: control.check("sindi_practicas")
          });
          crearLightboxHTML({
            "html":vista.el,
            "width":900,
            "height":140,
            "escapable":false,
            "callback":function() {
              that.tabla.collection.pager();
            },
          });
        }
      });
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
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

  app.views.SindiPracticasTableView = app.mixins.View.extend({

   template: _.template($("#sindi_practicas_panel_template").html()),

    myEvents: {
      "click .nuevo":function() {
        var self = this;
        this.sindi_bonos.buscar_por_codigo(self.nuevo);
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;
      var small = ((typeof this.options.small != "undefined") ? this.options.small : 0);
      this.sindi_bonos = ((typeof this.options.sindi_bonos != "undefined") ? this.options.sindi_bonos : 0);

      if (small == 0) {
        // Creamos la lista de paginacion
        var pagination = new app.mixins.PaginationView({
          collection: self.collection,
        });

        // Creamos el buscador
        var search = new app.mixins.SearchView({
          collection: self.collection,
        });
      }

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = {
        permiso: this.permiso,
        small: small,
      };

      // Cargamos el template
      $(this.el).html(this.template(obj));
      if (small == 0) {
        $(this.el).find(".pagination_container").html(pagination.el);
        // Cargamos el buscador
        $(this.el).find(".search_container").html(search.el);
      }
      // Vamos a buscar los elementos y lo paginamos
      self.collection.pager();
    },

    nuevo: function() {
      if (window.afiliado == null) {
        alert("Por favor selecione un afiliado");
        return;
      }
      if (window.afiliado.estado_obra_social == 0) {
        if (!isEmpty(window.afiliado.fecha_baja_obra_social)) {
          var limite = moment(window.afiliado.fecha_baja_obra_social).add(90,'days');
          if (moment().isAfter(limite)) {
            alert("El afiliado esta dado de baja en la obra social.");
            return;
          } else {
            alert("El afiliado esta dado de baja, pero tiene cobertura hasta "+limite.format("DD/MM/YYYY")+".");
          }
        } else {
          alert("El afiliado esta dado de baja en la obra social.");
          return;
        }
      }
      var self = this;
      var vista = new app.views.SindiPracticaEditView({
        model:new app.models.SindiPractica(),
        permiso: control.check("sindi_practicas"),
        view: self,
      });
      crearLightboxHTML({
        "html":vista.el,
        "width":900,
        "height":140,
        "escapable":false,
        "callback":function() {
          self.collection.pager();
        },
      });
    },
    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.SindiPracticaItem({
        model: item,
        permiso: this.permiso,
        tabla: self,
        small: ((typeof this.options.small != "undefined") ? this.options.small : 0),
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiPracticaEditView = app.mixins.View.extend({

    template: _.template($("#sindi_practicas_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",
      "click .nuevo": "limpiar",
      "click .anular": "anular",
      "change #sindi_practicas_tipos":"seleccionar_tipo_practica",
      "change #sindi_practicas_item_codigo":function() {
        // Cuando cambiamos el codigo, movemos el nombre
        var id_nomenclador = this.$("#sindi_practicas_item_codigo").val();
        var id_nomenclador_2 = this.$("#sindi_practicas_item_nombre").val();
        if (id_nomenclador != id_nomenclador_2) {
          // Enlazamos el otro select
          this.$("#sindi_practicas_item_nombre").val(id_nomenclador).trigger("change");

          // Tomamos el valor de la practica
          var importe = parseFloat(this.$("#sindi_practicas_item_codigo option:selected").data("importe"));
          if (importe == 0) {
            importe = parseFloat(this.$("#sindi_practicas_tipos option:selected").data("precio"));
          }
          this.$("#sindi_practicas_item_precio").val(importe);
        }
      },
      "change #sindi_practicas_item_nombre":function() {
        // Cuando cambiamos el nombre, movemos el codigo
        var id_nomenclador = this.$("#sindi_practicas_item_nombre").val();
        var id_nomenclador_2 = this.$("#sindi_practicas_item_codigo").val();
        if (id_nomenclador != id_nomenclador_2) {
          // Enlazamos el otro select
          this.$("#sindi_practicas_item_codigo").val(id_nomenclador).trigger("change");

          // Tomamos el valor de la practica
          var importe = parseFloat(this.$("#sindi_practicas_item_nombre option:selected").data("importe"));
          if (importe == 0) {
            importe = parseFloat(this.$("#sindi_practicas_tipos option:selected").data("precio"));
          }
          this.$("#sindi_practicas_item_precio").val(importe);
        }
      },

      // Operaciones sobre items
      "click .agregar_item":"agregar_item",
      "click .eliminar_item":"eliminar_item",
      "click .editar_item":"editar_item",

    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      var self = this;
      if (this.options.permiso > 1) edicion = true;
      var obj = {
        edicion: edicion,
        id:this.model.id,
        permiso: control.check("sindi_practicas")
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      // Creamos el select
      var id_afiliado = (self.model.id == undefined) ? window.afiliado.id : self.model.get("id_paciente");
      new app.mixins.Select({
        modelClass: app.models.SindiCondicionEspecial, // MODELO
        url: "sindi_condiciones_especiales/function/buscar_por_afiliado/"+id_afiliado, // URL DEL CONTROL
        render: "#sindi_practicas_id_condicion_especial", // ID DEL SELECT
        firstOptions: ["<option value='1'>Ninguna</option>"],
        selected: self.model.get("id_condicion_especial"),
        disabled: (self.model.id != undefined),
        onComplete: function(c) {
          if (c.length > 0 && self.model.id == undefined) $("#condicion_especial_alerta").show();
        }
      });

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiTipoPractica, // MODELO
        url: "sindi_tipos_practicas/", // URL DEL CONTROL
        render: "#sindi_practicas_tipos", // ID DEL SELECT
        firstOptions: ["<option value='0'>Seleccione</option>"],
        fields: ["precio"],
        selected: self.model.get("id_tipo_practica"),
        disabled: (self.model.id != undefined),
        onComplete: function() {
          self.seleccionar_tipo_practica();
        }
      });

      // Si es nuevo, mostramos la tabla
      if (this.model.id == undefined) {
        var coleccion = new app.collections.SindiPracticas();
        coleccion.server_api = {
          id_afiliado: window.afiliado.id,
        };
        var tabla_footer_practicas = new app.views.SindiPracticasTableView({
          collection: coleccion,
          small: 1,
        });
        this.$("#tabla_footer_practicas").html(tabla_footer_practicas.el);
      }

      return this;
    },


    agregar_item: function(e) {
      e.preventDefault();

      var id_nomenclador = this.$("#sindi_practicas_item_nombre").val();
      var codigo = this.$("#sindi_practicas_item_codigo option:selected").text();
      var practica = this.$("#sindi_practicas_item_nombre option:selected").text();
      var cantidad = parseFloat(this.$("#sindi_practicas_item_cantidad").val());

      var importe_unitario = parseFloat(this.$("#sindi_practicas_item_precio").val());
      if (codigo == 0 || practica == "" || cantidad < 1) {
        alert("Seleccione una práctica para añadir!");
        return;
      }
      var subtotal = Number(cantidad * importe_unitario).toFixed(2);

      var tr = "<tr data-id_nomenclador='"+id_nomenclador+"' data-cantidad='"+cantidad+"' data-importe_unitario='"+importe_unitario+"'>";
      tr+="<td class='editar_item'>"+codigo+"</td>";
      tr+="<td class='editar_item'>"+practica+"</td>";
      tr+="<td class='editar_item'>"+cantidad+"</td>";
      tr+="<td class='editar_item'>"+importe_unitario+"</td>";
      tr+="<td class='editar_item'>"+subtotal+"</td>";
      tr+='<td><i class="fa fa-times eliminar_item text-danger"></i></td>';
      tr+="</tr>";
      if (this.tr_item == null) {
        // Estamos agregando un nuevo item

        // Controlamos si ya esta cargado uno igual
        var encontro = false;
        this.$("#sindi_practicas_items tbody tr").each(function(i,e){
          if ($(e).data("id_nomenclador") == id_nomenclador) {
            // Ya hay un elemento
            encontro = true;
            return;
          }
        });
        if (encontro) {
          alert("Ya existe un elemento igual.");
          return;
        }

        if ($('#sindi_practicas_items tr').length == 18) {
          alert("Cantidad maxima de practicas por Bono alcanzada!");
          return;
        }
        this.$("#sindi_practicas_items tbody").append(tr);
      } else {
        // Estamos editando, entonces reemplazamos el TR
        $(this.tr_item).replaceWith(tr);
      }
      this.tr_item = null;
      this.deshabilitar_tipo_practica();
      return false;
    },

    editar_item:function(e) {
      var tr = $(e.currentTarget).parents("tr");
      this.$("#sindi_practicas_item_codigo").val($(tr).data("id_nomenclador")).trigger("change");
      this.$("#sindi_practicas_item_cantidad").val($(tr).data("cantidad"));
      this.$("#sindi_practicas_item_precio").val($(tr).data("importe_unitario"));
      this.tr_item = tr;
    },

    eliminar_item:function(e) {
      $(e.currentTarget).parents("tr").remove();
      this.deshabilitar_tipo_practica();
    },

    seleccionar_tipo_practica: function() {
      var self = this;
      var id_tipo_practica = this.$("#sindi_practicas_tipos").val();
      if (id_tipo_practica == 0) return;
      new app.mixins.Select({
        modelClass: app.models.SindiNomenclador,
        url: "sindi_nomencladores/function/buscar/?limit=0&offset=9999&id_tipo_practica="+id_tipo_practica,
        render: "#sindi_practicas_item_codigo",
        campoSelect: "codigo",
        fields: ["importe"],
        onComplete: function() {
          crear_select2("sindi_practicas_item_codigo");
          $('#sindi_practicas_item_codigo').on('select2:open', function (e) {
            $(".select2-container").css("z-index",9999);  
          });
          self.$("#sindi_practicas_item_codigo").trigger("change");
        }
      });
      new app.mixins.Select({
        modelClass: app.models.SindiNomenclador,
        url: "sindi_nomencladores/function/buscar/?limit=0&offset=9999&id_tipo_practica="+id_tipo_practica+"&order_by=nombre",
        render: "#sindi_practicas_item_nombre",
        fields: ["importe"],
        onComplete: function() {
          crear_select2("sindi_practicas_item_nombre");
          $(".select2-container").css("z-index",9999);
          $('#sindi_practicas_item_nombre').on('select2:open', function (e) {
            $(".select2-container").css("z-index",9999);  
          });
          self.$("#sindi_practicas_item_nombre").trigger("change");
        }
      });
    },

    validar: function() {
      try {

        var importe = 0;
        var items = new Array();
        this.$("#sindi_practicas_items tbody tr").each(function(i,e){
          var cantidad = parseFloat($(e).data("cantidad"));
          var importe_unitario = parseFloat($(e).data("importe_unitario"));
          importe = importe + (cantidad * importe_unitario);
          items.push({
            "id_nomenclador":$(e).data("id_nomenclador"),
            "cantidad":$(e).data("cantidad"),
            "importe_unitario":$(e).data("importe_unitario"),
          });
        });

        // Validamos los campos que sean necesarios
        this.model.set({
          "importe":importe,
          "items":items,
          "id_afiliado":window.afiliado.id_titular,
          "id_paciente":window.afiliado.id,
          "id_tipo_practica":$("#sindi_practicas_tipos").val(),
          "id_tipo_bono": $("#sindi_practicas_id_tipo_bono option:selected").val(),
          "id_sindi_practica_tipos": $("#sindi_practicas_id_condicion_especial option:selected").val(),
          "id_condicion_especial": $("#sindi_practicas_id_condicion_especial option:selected").val(),
          "hospital": $("#sindi_practicas_hospital").is(":checked")?"1":"0",
          "fecha": moment(),
        })
        return true;
      } catch(e) {
        return false;
      }
    },

    deshabilitar_tipo_practica: function() {
      if ($("#sindi_practicas_items tbody tr").length > 0){
        $("#sindi_practicas_tipos").attr("disabled","disabled");
        $("#sindi_practicas_id_condicion_especial").attr("disabled","disabled");
      } else {
        $("#sindi_practicas_tipos").removeAttr("disabled");
        $("#sindi_practicas_id_condicion_especial").removeAttr("disabled");
      }
    },

    anular: function() {
      var self = this;
      this.model.save({
        "anulada":1,
      },{
        "success":function(model,response) {
          self.cerrar();
        }
      });
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            self.imprimir();
          }
        });
      }
    },

    imprimir: function() {
      var self = this;
      $('.modal').modal('hide');
      window.open("sindi_practicas/function/imprimir/"+self.model.id,"_blank");
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    limpiar : function() {
      this.model = new app.models.SindiPractica();
      this.render();
    },

  });

})(app.views, app.models);