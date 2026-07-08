(function ( models ) {

  models.Tarjeta = Backbone.Model.extend({
    urlRoot: "tarjetas",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      numero_comercio: "",
      cuotas: [],
    },
  });

})( app.models );


(function (collections, model, paginator) {

  collections.Tarjetas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "tarjetas/"
    }
  });

})( app.collections, app.models.Tarjeta, Backbone.Paginator);


(function ( app ) {

  app.views.TarjetasTableView = app.mixins.View.extend({

    template: _.template($("#tarjetas_resultados_template").html()),

    myEvents: {
      "change #tarjetas_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      // Filtros de la tarjeta
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
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);

      return this;
    },

    buscar: function() {
      this.filter = this.$("#tarjetas_buscar").val().trim();
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
      var view = new app.views.TarjetasItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },

  });

})(app);


(function ( app ) {
  app.views.TarjetasItemResultados = app.mixins.View.extend({

    template: _.template($("#tarjetas_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"tarjetas/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
            },
          });
        }
        return false;
      },
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
      window.codigo_tarjeta_seleccionado = this.model.get("codigo");
      window.tarjeta_seleccionado = this.model;
      $('.modal:last').modal('hide');
    } else {
      location.href="app/#tarjeta/"+this.model.id;
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

  app.views.TarjetaEditView = app.mixins.View.extend({

    template: _.template($("#tarjeta_template").html()),

    myEvents: {
      "click .guardar": "guardar",

      "click #cuota_agregar":"agregar_cuota",
      "click .editar_cuota":"editar_cuota",
      "click .eliminar_cuota":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #tarjeta_cuota_interes":function(e) {
        if (e.which == 13) this.agregar_cuota();
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

      createdatepicker(this.$("#tarjeta_cuota_fecha_desde"));
      createdatepicker(this.$("#tarjeta_cuota_fecha_hasta"));
    },

    agregar_cuota: function() {
      // Controlamos los valores
      var fecha_desde = $("#tarjeta_cuota_fecha_desde").val();
      var fecha_hasta = $("#tarjeta_cuota_fecha_hasta").val();
      var interes = $("#tarjeta_cuota_interes").val();
      if (isEmpty(interes)) {
        alert("Por favor ingrese un valor");
        $("#tarjeta_cuota_interes").focus();
        return;
      }
      var cuota = $("#tarjeta_cuotas").val();
      var interes_especial = $("#tarjeta_cuota_interes_especial").val();

      var tr = "<tr>";
      tr+="<td class='cuota editar_cuota'><span class='text-info'>"+cuota+"</td>";
      tr+="<td class='fecha_desde dn editar_cuota'>"+fecha_desde+"</td>";
      tr+="<td class='fecha_hasta dn editar_cuota'>"+fecha_hasta+"</td>";
      tr+="<td class='interes_especial dn editar_cuota'>"+interes_especial+"</td>";
      tr+="<td class='interes editar_cuota'>"+Number(interes).toFixed(6)+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_cuota'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_precio == null) {
        $("#tarjeta_cuotas_tabla tbody").append(tr);
      } else {
        $(this.item_precio).replaceWith(tr);
        this.item_precio = null;
      }
      $("#tarjeta_cuota_fecha_desde").val("");
      $("#tarjeta_cuota_fecha_hasta").val("");
      $("#tarjeta_cuota_interes").val("");
      $("#tarjeta_cuota_interes_especial").val("");
      $("#tarjeta_cuotas").focus();
    },
    
    editar_cuota: function(e) {
      this.item_precio = $(e.currentTarget).parents("tr");
      $("#tarjeta_cuotas").val($(this.item_precio).find(".cuota").text());
      $("#tarjeta_cuota_fecha_desde").val($(this.item_precio).find(".fecha_desde").text());
      $("#tarjeta_cuota_fecha_hasta").val($(this.item_precio).find(".fecha_hasta").text());
      $("#tarjeta_cuota_interes_especial").val($(this.item_precio).find(".interes_especial").text());
      $("#tarjeta_cuota_interes").val($(this.item_precio).find(".interes").text());
    },

    validar: function() {
      try {
        var self = this;

        validate_input("tarjeta_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var k = 0;
        var cuotas = new Array();
        $("#tarjeta_cuotas_tabla tbody tr").each(function(i,e){
          cuotas.push({
            "cuota_desde": $(e).find(".cuota").text(),
            "cuota_hasta": $(e).find(".cuota").text(),
            "fecha_desde": $(e).find(".fecha_desde").text(),
            "fecha_hasta": $(e).find(".fecha_hasta").text(),
            "interes": $(e).find(".interes").text(),
            "interes_especial": $(e).find(".interes_especial").text(),
          });
          k++;
        });
        this.model.set({"cuotas":cuotas});

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
              location.reload();
            }
          }
        });
      }      
    },

  });
})(app);