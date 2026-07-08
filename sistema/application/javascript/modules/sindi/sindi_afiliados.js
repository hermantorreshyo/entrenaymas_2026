(function ( models ) {

  models.SindiAfiliado = Backbone.Model.extend({
    urlRoot: "sindi_afiliados/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      apellido: "",
      codigo: 0,
      identificador: 0,
      domicilio: "",
      telefono: "",
      fecha_nacimiento: "",
      estado_civil: 0,
      dni: "",
      cuil: "",
      id_afiliado: 0,
      id_paciente: 0,
      fecha_ingreso_empresa: "",
      fecha_alta: "",
      tipo_afiliado: "",
      id_tipo_afiliado: 0,
      empresa: "",
      id_empresa_transporte: 0,
      id_localidad: 0,
      localidad: "",
      id_condicion_especial: 0,
      estado_obra_social: 0,
      estado_sindicato: 0,
      id_titular: 0,
      condiciones_especiales: [],
      limites: [],
      localidad: "",
    }
  });
})( app.models );


(function ( models ) {

  models.SindiLocalidad = Backbone.Model.extend({
    urlRoot: "sindi_localidades/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
    }
  });

})( app.models );

(function ( models ) {
  models.SindiAfiliadoConsumo = Backbone.Model.extend({
    urlRoot: "sindi_afiliados/function/buscar_consumos/",
    defaults: {
      nombre: "",
      fecha: "",
      numero: 0,
      id_afiliado: 0,
      id_paciente: 0,
      observaciones: "",
      tipo: "",
      importe: 0,
      desde: "",
      hasta: "",
      id_empresa: ID_EMPRESA,
    }
  });
})( app.models );

(function ( models ) {
  models.SindiAfiliadoEmpresa = Backbone.Model.extend({
    urlRoot: "sindi_afiliados/function/buscar_empresas/",
    defaults: {
      id_sindi_empresa: 0,
      id_afiliado: 0,
      fecha: "",
      fecha_baja: "",
      fecha_alta: "",
      id_empresa: ID_EMPRESA,
    }
  });
})( app.models );

(function ( models ) {
  models.SindiAfiliadoSindicato = Backbone.Model.extend({
    urlRoot: "sindi_afiliados/function/buscar_sindicatos/",
    defaults: {
      id_afiliado: 0,
      id_empresa_transporte: 0,
      id_tipo_afiliado: 0,
      fecha_alta: "",
      fecha_baja: "",
      motivo: "",
      id_empresa: ID_EMPRESA,
    }
  });
})( app.models );

// COLECCIONES


(function (collections, model, paginator) {
  collections.SindiAfiliados = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_afiliados/function/buscar/",
    }
  });
})( app.collections, app.models.SindiAfiliado, Backbone.Paginator);


(function (collections, model, paginator) {
  collections.SindiAfiliadosConsumos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_afiliados/function/buscar_consumos/",
    }
  });
})( app.collections, app.models.SindiAfiliadoConsumo, Backbone.Paginator);

(function (collections, model, paginator) {
  collections.SindiAfiliadosEmpresas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_afiliados/function/buscar_empresas/",
    }
  });
})( app.collections, app.models.SindiAfiliadoEmpresa, Backbone.Paginator);

(function (collections, model, paginator) {
  collections.SindiAfiliadosSindicatos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_afiliados/function/buscar_sindicatos/",
    }
  });
})( app.collections, app.models.SindiAfiliadoSindicato, Backbone.Paginator);

(function ( app ) {
  app.views.SindiAfiliadoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_afiliados_item').html()),
    events: {
      "click .ver":function() {
        location.href="app/#sindi_afiliado/"+this.model.get("id_titular");
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
    /*
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    */
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.SindiAfiliadosTableView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_panel_template").html()),

    myEvents: {
      "click .nuevo":"nuevo",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var self = this;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };

      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(pagination.el);
      $(this.el).find(".search_container").html(search.el);
      self.collection.pager();
    },

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiAfiliadoEditView({
        model: new app.models.SindiAfiliado(),
        permiso: control.check("sindi_afiliados"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "escapable":false,
        "callback":function() {
          if (typeof window.id_afiliado == "undefined") return;
          location.href = "app/#sindi_afiliado/"+window.id_afiliado;
        }
      });
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.SindiAfiliadoItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// =====================================
// VISTA GENERAL DEL AFILIADO
// =====================================

(function ( views, models ) {

  views.SindiAfiliadoDetalleView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliado_detalle_template").html()),

    myEvents: {
      "click .editar":"editar",
      "click .alta_sindicato": "alta_sindicato",
      "click .baja_sindicato": "baja_sindicato",
      "click .alta_os": "alta_os",
      "click .baja_os": "baja_os",
      "click .alta_empresa":"alta_empresa",
      "click .baja_empresa":"baja_empresa",
      "click #consumos_link":"cargar_consumos",
      "click #historial_link":"cargar_historial",
      "click .nuevo_afiliado":function(){
        var self = this;
        var v = new app.views.SindiAfiliadoEditView({
          model: new app.models.SindiAfiliado(),
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":900,
          "height":140,
          "escapable":false,
        });
      },
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.tab_activo = (typeof options.tab_activo != "undefined" ? options.tab_activo : "grupo_familiar");
      this.guardando = 0;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = {
        "edicion": edicion,
        "id":this.model.id,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      this.cargar_grupo_familiar();
      return this;
    },

    alta_empresa: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoEmpresaAltaBajaView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.$("#sindi_afiliados_afiliados_empresas_motivo").val(),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_empresa: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoEmpresaAltaBajaView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.$("#sindi_afiliados_afiliados_empresas_motivo").val(),
          "empresa_transporte":self.model.get("nombreempresa"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    cargar_grupo_familiar: function() {
      var self = this;
      var coleccion = new app.collections.SindiAfiliados();
      var grupo_familiar = new app.views.SindiAfiliadosGrupoFamiliarTableView({
        model: self.model,
        collection: coleccion,
      });
      this.$("#tab1_afiliados").html(grupo_familiar.el);
    },

    cargar_consumos: function() {
      var self = this;
      var consumos = new app.views.SindiAfiliadosConsumosTableView({
        model: self.model,
        collection: new app.collections.SindiAfiliadosConsumos(),
      });
      this.$("#tab2_afiliados").html(consumos.el);
    },

    alta_os: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoOSEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_os: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoOSEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    alta_sindicato: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoSindicatoEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_sindicato: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoSindicatoEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    editar: function() {
      var self = this;
      var modelo = new app.models.SindiAfiliado({ 'id':self.model.get("id_titular") });
      modelo.fetch({
        "success":function() {
          var v = new app.views.SindiAfiliadoEditView({
            model: modelo,
            permiso: control.check("sindi_afiliados"),
            view: self,
          });
          crearLightboxHTML({
            "html":v.el,
            "width":900,
            "height":140,
            "escapable":false,
            "callback":function() {
              self.parent.buscar();
            }
          });
        },
      });
    },

  });

})(app.views, app.models);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiAfiliadoEditView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar":"cerrar",
      "click .nuevo": "limpiar",
      "change #condiciones_especiales_select": "fechaselect",
      "change #sindi_afiliados_codigo": function() {
        $("#label-codigo").hide();
      },

      // Operaciones sobre condiciones especiales
      "click .solicitar":"solicitar",
      "click .asignar":"asignar",
      "click .solicitaryasignar":"solicitaryasignar",
      "click .eliminar_condicion":"eliminar_condicion",

      // Operaciones sobre limites
      "click .agregar_limite":"agregar_limite",
      "click .eliminar_limite":"eliminar_limite",
      "click .editar_limite":"editar_limite",

      "change #sindi_afiliados_limite_consulta_cantidad":function() {
        var cantidad = this.$("#sindi_afiliados_limite_consulta_cantidad").val();
        if (cantidad <= 2) {
          this.$("#sindi_afiliados_limite_consulta_cantidad").val(2);
          this.$("#sindi_afiliados_limite_consulta_fecha").val("");
          this.$("#sindi_afiliados_limite_consulta_fecha").attr("disabled","disabled")
          this.$("#sindi_afiliados_limite_consulta_motivo").val("");
          this.$("#sindi_afiliados_limite_consulta_motivo").attr("disabled","disabled")
        } else {
          this.$("#sindi_afiliados_limite_consulta_fecha").removeAttr("disabled");
          this.$("#sindi_afiliados_limite_consulta_motivo").removeAttr("disabled");
        }
      },

      "change #sindi_afiliados_limite_recetarios_cantidad":function() {
        var cantidad = this.$("#sindi_afiliados_limite_recetarios_cantidad").val();
        if (cantidad <= 2) {
          this.$("#sindi_afiliados_limite_recetarios_cantidad").val(2);
          this.$("#sindi_afiliados_limite_recetarios_fecha").val("");
          this.$("#sindi_afiliados_limite_recetarios_fecha").attr("disabled","disabled")
          this.$("#sindi_afiliados_limite_recetarios_motivo").val("");
          this.$("#sindi_afiliados_limite_recetarios_motivo").attr("disabled","disabled")
        } else {
          this.$("#sindi_afiliados_limite_recetarios_fecha").removeAttr("disabled");
          this.$("#sindi_afiliados_limite_recetarios_motivo").removeAttr("disabled");
        }
      },

      "change #sindi_afiliados_limite_recetarios_cantidad":function() {
        var cantidad = this.$("#sindi_afiliados_limite_recetarios_cantidad").val();
        if (cantidad <= 2) {
          this.$("#sindi_afiliados_limite_recetarios_cantidad").val(2);
          this.$("#sindi_afiliados_limite_recetarios_fecha").val("");
          this.$("#sindi_afiliados_limite_recetarios_fecha").attr("disabled","disabled")
          this.$("#sindi_afiliados_limite_recetarios_motivo").val("");
          this.$("#sindi_afiliados_limite_recetarios_motivo").attr("disabled","disabled")
        } else {
          this.$("#sindi_afiliados_limite_recetarios_fecha").removeAttr("disabled");
          this.$("#sindi_afiliados_limite_recetarios_motivo").removeAttr("disabled");
        }
      },

      "change #sindi_afiliados_limite_recetarios_70_cantidad":function() {
        var cantidad = this.$("#sindi_afiliados_limite_recetarios_70_cantidad").val();
        if (cantidad <= 0) {
          this.$("#sindi_afiliados_limite_recetarios_70_cantidad").val(0);
          this.$("#sindi_afiliados_limite_recetarios_70_fecha").val("");
          this.$("#sindi_afiliados_limite_recetarios_70_fecha").attr("disabled","disabled")
          this.$("#sindi_afiliados_limite_recetarios_70_motivo").val("");
          this.$("#sindi_afiliados_limite_recetarios_70_motivo").attr("disabled","disabled")
        } else {
          this.$("#sindi_afiliados_limite_recetarios_70_fecha").removeAttr("disabled");
          this.$("#sindi_afiliados_limite_recetarios_70_motivo").removeAttr("disabled");
        }
      },

      "change #sindi_afiliados_limite_recetarios_100_cantidad":function() {
        var cantidad = this.$("#sindi_afiliados_limite_recetarios_100_cantidad").val();
        if (cantidad <= 0) {
          this.$("#sindi_afiliados_limite_recetarios_100_cantidad").val(0);
          this.$("#sindi_afiliados_limite_recetarios_100_fecha").val("");
          this.$("#sindi_afiliados_limite_recetarios_100_fecha").attr("disabled","disabled")
          this.$("#sindi_afiliados_limite_recetarios_100_motivo").val("");
          this.$("#sindi_afiliados_limite_recetarios_100_motivo").attr("disabled","disabled")
        } else {
          this.$("#sindi_afiliados_limite_recetarios_100_fecha").removeAttr("disabled");
          this.$("#sindi_afiliados_limite_recetarios_100_motivo").removeAttr("disabled");
        }
      },
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.tr_limite = null;
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
        tab_activo: ((typeof self.options.tab_activo != "undefined") ? self.options.tab_activo : "ficha"),
        subtab_activo: ((typeof self.options.subtab_activo != "undefined") ? self.options.subtab_activo : ""),
        permiso:control.check("sindi_afiliados"),
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiLocalidad, // MODELO
        url: "sindi_localidades/function/buscar/?offset=99999", // URL DEL CONTROL
        firstOptions: ["<option value='0'>Sin Definir</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_afiliados_id_localidad", // ID DEL SELECT
        selected: self.model.get("id_localidad"), // VALOR SELECCIONADO
        name: "id_localidad", // NOMBRE DEL CAMPO EN LA TABLA
        onComplete: function() {
          crear_select2("sindi_afiliados_id_localidad");
        }
      });

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiCondicionEspecial, // MODELO
        url: "sindi_condiciones_especiales/?offset=99999", // URL DEL CONTROL
        render: "#condiciones_especiales_select", // ID DEL SELECT
        fields: ["vencimiento"],
        onComplete: function() {
          $("#condiciones_especiales_select").trigger("change");
        }
      });

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiTipoPractica, // MODELO
        url: "sindi_tipos_practicas/?offset=99999", // URL DEL CONTROL
        render: "#sindi_afiliados_tipo_practica_select", // ID DEL SELECT
        onComplete: function() {
          crear_select2("sindi_afiliados_tipo_practica_select");
        }
      });

      return this;
    },

    fechaselect: function(){
      meses = $("#condiciones_especiales_select").find(':selected').data("vencimiento");
      venci = moment().add(meses, 'months').format("YYYY-MM-DD");
      $("#condicion_especial_fecha_vencimiento").val(venci);
    },

    cerrar: function(e) {
      if (confirm('Si continua cerrando los cambios no se guardaran!')) {
        $('.modal:last').modal('hide');
      }
    },

    agregar_limite: function(e) {
      e.preventDefault();
      var id_tipo_practica = this.$("#sindi_afiliados_tipo_practica_select").val();
      var tipo_practica = this.$("#sindi_afiliados_tipo_practica_select option:selected").text();
      var cantidad = this.$("#sindi_afiliados_cantidad").val();
      var meses = this.$("#sindi_afiliados_meses").val();
      var motivo = this.$("#sindi_afiliados_motivo").val();
      var vencimiento = this.$("#sindi_afiliados_vencimiento").val();
      var id = this.$("#sindi_afiliados_limite_id").val();

      if (isEmpty(cantidad) || cantidad <= 0) {
        alert("Por favor ingrese una cantidad");
        this.$("#sindi_afiliados_cantidad").select();
        return;
      }

      if (isEmpty(meses) || meses <= 0) {
        alert("Por favor ingrese los meses");
        this.$("#sindi_afiliados_meses").select();
        return;
      }

      var tr = "<tr data-id='"+id+"' data-vencimiento='"+vencimiento+"' data-tipo_practica='"+tipo_practica+"' data-id_tipo_practica='"+id_tipo_practica+"' data-cantidad='"+cantidad+"' data-meses='"+meses+"' data-motivo='"+motivo+"'>";
      tr+='<td><label class="i-checks m-b-none"><input class="esc check-row" type="checkbox"><i></i></label></td>'
      tr+="<td>"+tipo_practica+"</td>";
      tr+="<td>"+cantidad+"</td>";
      tr+="<td>"+meses+"</td>";
      tr+="<td>"+vencimiento+"</td>";
      tr+="<td>"+motivo+"</td>";
      tr+='<td><i class="fa fa-pencil editar_limite"></i></td>';
      tr+='<td><i class="fa fa-times eliminar_limite text-danger"></i></td>';
      tr+="</tr>";
      if (this.tr_limite == null) {
        // Estamos agregando un nuevo limite

        // Controlamos si ya esta cargado uno igual
        var encontro = false;
        this.$("#sindi_limites_afiliados_table tbody tr").each(function(i,e){
          if ($(e).data("id_tipo_practica") == id_tipo_practica && $(e).data("cantidad") == cantidad && $(e).data("meses") == meses) {
            // Ya hay un elemento
            encontro = true;
            return;
          }
        });
        if (encontro) {
          alert("Ya existe un elemento igual.");
          return;
        }

        this.$("#sindi_limites_afiliados_table tbody").append(tr);
      } else {
        // Estamos editando, entonces reemplazamos el TR
        $(this.tr_limite).replaceWith(tr);
      }
      this.limpiar_limite();
      return false;
    },

    editar_limite:function(e) {
      var tr = $(e.currentTarget).parents("tr");
      this.$("#sindi_afiliados_tipo_practica_select").val($(tr).data("id_tipo_practica"));
      this.$("#sindi_afiliados_cantidad").val($(tr).data("cantidad"));
      this.$("#sindi_afiliados_meses").val($(tr).data("meses"));
      this.$("#sindi_afiliados_motivo").val($(tr).data("motivo"));
      this.$("#sindi_afiliados_vencimiento").val($(tr).data("vencimiento"));
      this.$("#sindi_afiliados_limite_id").val($(tr).data("id"));
      this.tr_limite = tr;
    },

    eliminar_limite:function(e) {
      $(e.currentTarget).parents("tr").remove();
    },

    limpiar_limite: function() {
      this.$("#sindi_afiliados_tipo_practica_select").val(0);
      this.$("#sindi_afiliados_cantidad").val("");
      this.$("#sindi_afiliados_meses").val("");
      this.$("#sindi_afiliados_motivo").val("");
      this.$("#sindi_afiliados_vencimiento").val("");
      this.$("#sindi_afiliados_limite_id").val(0);
    },

    solicitar: function(e) {
      e.preventDefault();
      var id_condicion = this.$("#condiciones_especiales_select").val();
      if (id_condicion == 1) return false;
      if (this.$("#condicion_"+id_condicion).length > 0) return false;
      // TODO: Ver el tema del vencimiento
      var tr = "<tr data-nombre='"+this.$("#condiciones_especiales_select option:selected").text()+"' data-vencimiento='"+$("#condicion_especial_fecha_vencimiento").val()+"' data-estado='0' id='condicion_"+id_condicion+"'>";
      tr+="<td>"+this.$("#condiciones_especiales_select option:selected").text()+"</td>";
      tr+='<td style="width:20%; text-align:center"><span class="label label-warning">Solicitado</span></td>';
      tr+='<td style="width:20%; text-align:center">'+$("#condicion_especial_fecha_vencimiento").val()+'</td>';
      tr+='<td style="width:20%; text-align:center"><button class="btn btn-danger btn-xs eliminar_condicion">Remover</button></td>';
      tr+="</tr>";
      this.$("#condiciones_especiales tbody").append(tr);
      return false;
    },

    solicitaryasignar: function(e) {
      e.preventDefault();
      var id_condicion = this.$("#condiciones_especiales_select").val();
      if (id_condicion == 1) return false;
      if (this.$("#condicion_"+id_condicion).length > 0) return false;
      // TODO: Ver el tema del vencimiento
      var tr = "<tr data-nombre='"+this.$("#condiciones_especiales_select option:selected").text()+"' data-vencimiento='"+$("#condicion_especial_fecha_vencimiento").val()+"' data-estado='1' id='condicion_"+id_condicion+"'>";
      tr+="<td>"+this.$("#condiciones_especiales_select option:selected").text()+"</td>";
      tr+='<td style="width:20%; text-align:center"><span class="label label-success">Asignado</span></td>';
      tr+='<td style="width:20%; text-align:center">'+$("#condicion_especial_fecha_vencimiento").val()+'</td>';
      tr+='<td style="width:20%; text-align:center"><button class="btn btn-danger btn-xs eliminar_condicion">Remover</button></td>';
      tr+="</tr>";
      this.$("#condiciones_especiales tbody").append(tr);
      return false;
    },

    eliminar_condicion: function(e) {
      e.preventDefault();
      $(e.currentTarget).parents("tr").remove();
      return false;
    },

    asignar: function(e) {
      e.preventDefault();
      $(e.currentTarget).parents("tr").data("estado",1);
      $(e.currentTarget).parents("tr").find(".label-warning").removeClass("label-warning").addClass("label-success").text("Asignado");
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        //validate_input("sindi_afiliados_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        if (this.model.id != null) {
          var condiciones_especiales = new Array();
          this.$("#condiciones_especiales tbody tr").each(function(i,e){
            var id = $(e).attr("id");
            id = id.replace("condicion_","");
            if (id != null) {
              condiciones_especiales.push({
                "id":id,
                "estado":$(e).data("estado"),
                "vence":$(e).data("vencimiento"),
                "nombre":$(e).data("nombre"),
              });
            }
          });
          this.model.set({
            "condiciones_especiales":condiciones_especiales
          });
        }

        var limites = new Array();

        // Limite de consulta
        limites.push({
          "id":$("#consulta_id").val(),
          "tipo":1,
          "vencimiento":$("#sindi_afiliados_limite_consulta_fecha").val(),
          "cantidad":$("#sindi_afiliados_limite_consulta_cantidad").val(),
          "motivo":$("#sindi_afiliados_limite_consulta_motivo").val(),
          "id_tipo_practica":0,
          "tipo_practica":"",
          "meses":1,
        });

        // Limite de farmacia
        limites.push({
          "id":$("#recetarios_id").val(),
          "tipo":2,
          "vencimiento":$("#sindi_afiliados_limite_recetarios_fecha").val(),
          "cantidad":$("#sindi_afiliados_limite_recetarios_cantidad").val(),
          "motivo":$("#sindi_afiliados_limite_recetarios_motivo").val(),
          "id_tipo_practica":0,
          "tipo_practica":"",
          "meses":1,
        });

        // Limite de farmacia al 70
        limites.push({
          "id":$("#recetarios_70_id").val(),
          "tipo":3,
          "vencimiento":$("#sindi_afiliados_limite_recetarios_70_fecha").val(),
          "cantidad":$("#sindi_afiliados_limite_recetarios_70_cantidad").val(),
          "motivo":$("#sindi_afiliados_limite_recetarios_70_motivo").val(),
          "id_tipo_practica":0,
          "tipo_practica":"",
          "meses":1,
        });

        // Limite de farmacia al 100
        limites.push({
          "id":$("#recetarios_100_id").val(),
          "tipo":4,
          "vencimiento":$("#sindi_afiliados_limite_recetarios_100_fecha").val(),
          "cantidad":$("#sindi_afiliados_limite_recetarios_100_cantidad").val(),
          "motivo":$("#sindi_afiliados_limite_recetarios_100_motivo").val(),
          "id_tipo_practica":0,
          "tipo_practica":"",
          "meses":1,
        });

        this.$("#sindi_limites_afiliados_table tbody tr").each(function(i,e){
          limites.push({
            "tipo":5,
            "vencimiento":$(e).data("vencimiento"),
            "id_tipo_practica":$(e).data("id_tipo_practica"),
            "tipo_practica":$(e).data("tipo_practica"),
            "cantidad":$(e).data("cantidad"),
            "meses":$(e).data("meses"),
            "motivo":$(e).data("motivo"),
            "id":$(e).data("id"),
          });
        });
        this.model.set({
          "limites":limites
        });

        // No hay ningun error

        return true;
      } catch(e) {
        return false;
      }
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
            //window.id_afiliado = model.id;
            $('.modal:last').modal('hide');
            workspace.ver_sindi_afiliado(model.id);
          },
          "error": function(model,response){
            $("#label-codigo").show();
            $("#sindi_afiliados_codigo").select();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiAfiliado();
      this.render();
    },

  });

})(app.views, app.models);



// =======================================
// GRUPO FAMILIAR

(function ( app ) {

  app.views.SindiAfiliadosGrupoFamiliarTableView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_grupo_familiar_template").html()),

    myEvents: {
      "click .nuevogrp":"nuevo",

    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var self = this;
      this.options = options;
      this.permiso = this.options.permiso;
      this.collection.on('sync', this.addAll, this);
      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      this.buscar();
    },

    buscar: function() {
      var self = this;
      // Vamos a buscar los elementos y lo paginamos
      this.collection.server_api = {
        "codigo": self.model.get("codigo"),
        "order_by": "identificador",
        "order": "asc",
      };
      this.collection.pager();
    },

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiAfiliadoEditView({
        model: new app.models.SindiAfiliado({
          codigo: self.model.get("codigo"),
        }),
        permiso: control.check("sindi_afiliados"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "escapable":false,
        "callback":function() {
          if (window.id_afiliado != undefined) {
            $('.modal:last').modal('hide');
            workspace.ver_sindi_afiliado(model.id);
          }
        }
      });
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.SindiAfiliadoGrupoFamiliarItem({
        model: item,
        parent: self,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( app ) {
  app.views.SindiAfiliadoGrupoFamiliarItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_afiliados_grupo_familiar_item').html()),
    events: {
      "click .ver": "editar",
      "click .alta_sindicato_item": "alta_sindicato_item",
      "click .baja_sindicato_item": "baja_sindicato_item",
      "click .alta_os_item": "alta_os_item",
      "click .baja_os_item": "baja_os_item",
      "click .delete": "borrar",
      "click .cerrar":"cerrar",
      "click .sacar_bono":"sacar_bono",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.parent = options.parent;
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
    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    sacar_bono: function() {
      window.afiliado = this.model.toJSON();
      location.href = "app/#sindi_bonos";
    },

    alta_os_item: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoOSEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_os_item: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoOSEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    alta_sindicato_item: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoSindicatoEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_sindicato_item: function() {
      var self = this;
      var view = new app.views.SindiAfiliadoSindicatoEditView({
        model: new app.models.AbstractModel({
          "id_afiliado":self.model.id,
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "motivo":self.model.get("motivo"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    editar: function() {
      var self = this;
      var modelo = new app.models.SindiAfiliado({ 'id':self.model.id });
      modelo.fetch({
        "success":function() {
          var v = new app.views.SindiAfiliadoEditView({
            model: modelo,
            permiso: control.check("sindi_afiliados"),
            view: self,
          });
          crearLightboxHTML({
            "html":v.el,
            "width":900,
            "height":140,
            "escapable":false,
            "callback":function() {
             // location.reload();
            }
          });
        },
      });
    },

    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );



(function ( app ) {

  app.views.SindiAfiliadosConsumosTableView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_consumos_template").html()),

    myEvents: {
      "change #sindi_afiliados_consumos_tipos":"buscar",
      "change #sindi_afiliados_consumos_grupo_familiar":"buscar",
      "change #sindi_afiliados_consumos_desde":"buscar",
      "change #sindi_afiliados_consumos_hasta":"buscar",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var self = this;
      this.options = options;
      this.permiso = this.options.permiso;
      this.collection.on('sync', this.addAll, this);
      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
      });
      $(this.el).find(".pagination_container").html(pagination.el);

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiAfiliado, // MODELO
        url: "sindi_afiliados/function/buscar/?codigo="+self.model.get("codigo")+"?offset=99999",
        firstOptions: ["<option value=''>Todos</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_afiliados_consumos_grupo_familiar", // ID DEL SELECT
      });

      this.buscar();
    },

    buscar: function() {
      var self = this;
      //var desde =
      //var hasta =
      // Vamos a buscar los elementos y lo paginamos
      this.collection.server_api = {
        "id_afiliado":self.model.id,
        "tipo":self.$("#sindi_afiliados_consumos_tipos").val(),
        "id_paciente":self.$("#sindi_afiliados_consumos_grupo_familiar").val(),
        "desde":self.$("#sindi_afiliados_consumos_desde").val(),
        "hasta":self.$("#sindi_afiliados_consumos_hasta").val(),
      };
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.SindiAfiliadoConsumoItem({
        model: item,
        parent: self,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);

(function ( app ) {
  app.views.SindiAfiliadoConsumoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_afiliados_consumo_item').html()),
    events: {
      "click .ver": "ver",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.parent = options.parent;
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
    ver: function() {
      var self = this;
      var tipo = self.model.get("tipo");
      if (tipo == "C") {
        var modelo = new app.models.SindiConsulta({"id":self.model.id});
        modelo.fetch({
          "success":function() {
            var vista = new app.views.SindiConsultaEditView({
              model: modelo,
              permiso: control.check("sindi_consultas")
            });
            crearLightboxHTML({
              "html":vista.el,
              "width":900,
              "height":140,
              "escapable":false,
            });
          }
        });
      } else if (tipo == "P") {
        var modelo = new app.models.SindiPractica({"id":self.model.id});
        modelo.fetch({
          "success":function() {
            var vista = new app.views.SindiPracticaEditView({
              model: modelo,
              permiso: control.check("sindi_practicas")
            });
            crearLightboxHTML({
              "html":vista.el,
              "width":900,
              "height":140,
              "escapable":false,
            });
          }
        });
      } else if (tipo == "R") {
        var modelo = new app.models.SindiReintegro({"id":self.model.id});
        modelo.fetch({
          "success":function() {
            var vista = new app.views.SindiReintegroEditView({
              model: modelo,
              permiso: control.check("sindi_reintegros")
            });
            crearLightboxHTML({
              "html":vista.el,
              "width":900,
              "height":140,
              "escapable":false,
            });
          }
        });
      } else if (tipo == "T") {
        var modelo = new app.models.SindiRecetario({"id":self.model.id});
        modelo.fetch({
          "success":function() {
            var vista = new app.views.SindiRecetarioEditView({
              model: modelo,
              permiso: control.check("sindi_recetarios")
            });
            crearLightboxHTML({
              "html":vista.el,
              "width":900,
              "height":140,
              "escapable":false,
            });
          }
        });
      }
    },
  });

})( app );

(function ( app ) {
  app.views.SindiAfiliadoEmpresaAltaBajaView = app.mixins.View.extend({
    template: _.template($('#sindi_afiliados_empresa_alta_baja_template').html()),
    myEvents: {
      "click .guardar":"guardar",
      "click .cerrar":"cerrar",
    },
    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var self = this;

      if (self.model.get("tipo") == "alta") {
        this.$("#boton_guardar_alta_baja_empresa").hide();
        new app.mixins.Select({
          modelClass: app.models.SindiEmpresa,
          url: "sindi_empresas/function/buscar/?offset=99999",
          render: "#sindi_afiliados_empresas",
          selected: self.model.get("id_empresa_transporte"),
          disabled: (self.model.tipo == "baja"),
          onComplete: function() {
            crear_select2("sindi_afiliados_empresas");
            $("#boton_guardar_alta_baja_empresa").show();
          }
        });
      }

      return this;
    },
    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },
    guardar: function() {
      var self = this;
      $.ajax({
        "url":"sindi_afiliados/function/alta_baja_empresa/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_afiliado":self.model.get("id_afiliado"),
          "fecha":self.$("#sindi_afiliados_empresa_fecha").val(),
          "id_sindi_empresa":self.$("#sindi_afiliados_empresas").val(),
          "tipo":self.model.get("tipo"),
        },
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            location.reload();
            //workspace.ver_sindi_afiliado(self.model.id);
          }
        }
      });
    }
  });

})( app );



// -------------------------------
//   VISTA DE EDICION DE SINDICATO
// -------------------------------
(function ( views, models ) {

  views.SindiAfiliadoSindicatoEditView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_sindicato_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar":"cerrar",

    },

    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    guardar: function() {
      var self = this;
      $.ajax({
        "url":"sindi_afiliados/function/alta_baja_sindicato/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_afiliado":self.model.get("id_afiliado"),
          "motivo":self.$("#sindi_afiliados_sindicato_motivo").val(),
          "fecha":self.$("#sindi_afiliados_sindicato_fecha").val(),
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "tipo":self.model.get("tipo"),
        },
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            location.reload();
          }
        }
      });
    },

  });

})(app.views, app.models);


// -------------------------------
// VISTA DE EDICION DE OBRA SOCIAL
// -------------------------------
(function ( views, models ) {

  views.SindiAfiliadoOSEditView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_os_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar":"cerrar",

    },

    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    guardar: function() {
      var self = this;
      $.ajax({
        "url":"sindi_afiliados/function/alta_baja_os/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_afiliado":self.model.get("id_afiliado"),
          "motivo":self.$("#sindi_afiliados_obra_social_motivo").val(),
          "fecha":self.$("#sindi_afiliados_obra_social_fecha").val(),
          "id_empresa_transporte":self.model.get("id_empresa_transporte"),
          "tipo":self.model.get("tipo"),
        },
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            location.reload();
          }
        }
      });
    },
  });

})(app.views, app.models);

// VISTA DEL BUSCADOR

(function ( app ) {

  app.views.SindiAfiliadosBuscarView = app.mixins.View.extend({

    template: _.template($("#sindi_afiliados_buscar_template").html()),

    myEvents: {
      "click .nuevo":"nuevo",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var self = this;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
        ver_filas_paginas: false,
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };

      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(pagination.el);
      $(this.el).find(".search_container").html(search.el);
      self.collection.pager();
    },

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiAfiliadoEditView({
        model: new app.models.SindiAfiliado(),
        permiso: control.check("sindi_afiliados"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "callback":function() {

        }
      });
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.SindiAfiliadoBuscarItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);

(function ( app ) {
  app.views.SindiAfiliadoBuscarItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_afiliados_buscar_item').html()),
    events: {
      "click .ver":function() {
        code = this.model.get("codigo");
        iden = this.model.get("identificador");
        codeiden = code+"-"+iden;
        $('.modal:last').modal('hide');
        $("#buscador_codigo").val(codeiden);
        var enter = jQuery.Event("keypress");
          enter.which = 13; //choose the one you want
          enter.keyCode = 13;
        $("#buscador_codigo").trigger(enter);
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
    /*
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    */
  });

})( app );