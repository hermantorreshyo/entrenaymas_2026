// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Viaje = Backbone.Model.extend({
    urlRoot: "viajes",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_cliente: 0,
      id_promocion: 0,
      nombre: "",
      nombre_en: "",
      nombre_pt: "",
      subtitulo: "",
      subtitulo_en: "",
      subtitulo_pt: "",
      path: "",
      fecha: "",
      fecha_llegada: "",
      id_categoria: 0,
      categoria: "",
      observaciones: "",
      observaciones_en: "",
      observaciones_pt: "",
      vehiculos_tripulantes: [],
      precios: [],
      opcionales: [],
      relacionados: [],
      images:[],
      texto: "",
      texto_en: "",
      texto_pt: "",
      oferta: 0,
      activo: 1,
      destacado: 0,
      latitud: 0,
      longitud: 0,
      zoom: 12,
      video: "",
      orden: 0,
      total_asientos: 0,
      total_ocupados: 0,
      custom_1: "",
      custom_1_en: "",
      custom_1_pt: "",
      custom_2: "",
      custom_2_en: "",
      custom_2_pt: "",
      custom_3: "",
      custom_3_en: "",
      custom_3_pt: "",
      custom_4: "",
      custom_4_en: "",
      custom_4_pt: "",
      custom_5: "",
      custom_5_en: "",
      custom_5_pt: "",
      custom_6: "",
      custom_6_en: "",
      custom_6_pt: "",
      custom_7: "",
      custom_7_en: "",
      custom_7_pt: "",
      custom_8: "",
      custom_8_en: "",
      custom_8_pt: "",
      custom_9: "",
      custom_9_en: "",
      custom_9_pt: "",
      custom_10: "",
      custom_10_en: "",
      custom_10_pt: "",
      lunes: 1,
      martes: 1,
      miercoles: 1,
      jueves: 1,
      viernes: 1,
      sabado: 1,
      domingo: 1,
      caracteristicas: "",
      link_terminos: "",
      solo_consultar: 0,
      precio: 0,
      estado: 0,
    },
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Viajes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "viajes/function/buscar/"
    },
  });

})( app.collections, app.models.Viaje, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ViajesTableView = app.mixins.View.extend({

    template: _.template($("#viajes_resultados_template").html()),

    myEvents: {
      "change #viajes_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      window.viajes_filter = (typeof window.viajes_filter != "undefined") ? window.viajes_filter : "";
      window.viajes_buscar_activo = (typeof window.viajes_buscar_activo != "undefined") ? window.viajes_buscar_activo : (ID_EMPRESA == 135 ? 1 : -1);
      window.viajes_fecha = (typeof window.viajes_fecha != "undefined") ? window.viajes_fecha : "";
      window.viajes_page = (typeof window.viajes_page != "undefined") ? window.viajes_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(this.pagination.el);

      return this;
    },

    buscar: function() {
      var cambio_parametros = false;

      if (window.viajes_filter != this.$("#viajes_buscar").val().trim()) {
        window.viajes_filter = this.$("#viajes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (window.viajes_buscar_activo != this.$("#viajes_buscar_activos").val()) {
        window.viajes_buscar_activo = this.$("#viajes_buscar_activos").val();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.viajes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.viajes_filter),
        "activo":window.viajes_buscar_activo,
      }
      if (PERFIL == 589) {
        // Filtramos solamente por el chofer
        datos.id_tripulante = ID_USUARIO;
      }
      this.collection.server_api = datos;
      this.collection.goTo(window.viajes_page);
    },

    addAll : function () {
      window.viajes_page = this.pagination.getPage();
      $(this.el).find(".tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (ID_EMPRESA == 135 || this.collection.length > 0 || PERFIL == 589) {
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
      var view = new app.views.ViajesItemResultados({
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
  app.views.ViajesItemResultados = app.mixins.View.extend({

    template: _.template($("#viajes_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .imprimir_contrato":function() {
        window.open("viajes/function/imprimir_contrato/"+this.model.id,"_blank");
      },
      "click .realizar":function() {
        var self = this;
        self.model.set({"estado":1});
        this.change_property({
          "table":"via_viajes",
          "url":"viajes/function/change_property/",
          "attribute":"estado",
          "value":1,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
      },
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
            "url":"viajes/function/duplicar/"+self.model.id,
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
    "click .destacado":function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      var destacado = this.model.get("destacado");
      destacado = (destacado == 1)?0:1;
      self.model.set({"destacado":destacado});
      this.change_property({
        "table":"via_viajes",
        "url":"viajes/function/change_property/",
        "attribute":"destacado",
        "value":destacado,
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
        "table":"via_viajes",
        "url":"viajes/function/change_property/",
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
    if (ID_EMPRESA == 501 && PERFIL == 589) return;
    if (this.habilitar_seleccion) {
      window.codigo_viaje_seleccionado = this.model.get("codigo");
      window.viaje_seleccionado = this.model;
      $('.modal:last').modal('hide');
    } else {
      location.href="app/#viaje/"+this.model.id;
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

  app.views.ViajeEditView = app.mixins.View.extend({

    template: _.template($("#viaje_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .atras": function() {
        window.history.back();
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

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "viajes/function/upload_images/",
          "view": self,
        });
      },

      "click #vehiculo_agregar":"agregar_vehiculo",
      "click .eliminar_vehiculo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #viaje_vehiculos_comision":function(e) {
        if (e.which == 13) this.agregar_vehiculo();
      },

      "click #relacionado_agregar":"agregar_relacionado",
      "click .eliminar_relacionado":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      "click #opcional_agregar":"agregar_opcional",
      "click .eliminar_opcional":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      "click #precio_agregar":"agregar_precio",
      "click .editar_precio":"editar_precio",
      "click .eliminar_precio":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #viaje_precio_monto":function(e) {
        if (e.which == 13) this.agregar_precio();
      },

      "click #cargar_mapa":"get_coords_by_address",
      "click .add_marker": function() {
        if (this.map == undefined) return;
        var position = this.map.getCenter();
        this.add_marker(position.lat(),position.lng());
      },
      "click #viaje_link_2":function() {
        if (typeof CKEDITOR.instances["viaje_texto_en"] == "undefined") { 
          workspace.crear_editor('viaje_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #viaje_link_3":function() {
        if (typeof CKEDITOR.instances["viaje_texto_pt"] == "undefined") {
          workspace.crear_editor('viaje_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
      "click .nueva_categoria":function(e) {
        var self = this;
        if ($(".categoria_viaje_edit_mini").length > 0) return;
        var form = new app.views.CategoriaViajeEditViewMini({
          "model": new app.models.CategoriaViaje(),
          "callback":self.cargar_categorias,
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete categoria_viaje_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#categorias_viajes_mini_nombre").focus();
      },
      "click #viaje_mapa_expand_link":function() {
        var self = this;
        setTimeout(function(){
          if (self.map == undefined) self.render_map();
          google.maps.event.trigger(self.map, "resize");
          self.map.setCenter(self.coor);
        },100);
      },
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
        firstOptions: ["<option value='0'>-</option>"],
        render: "#viaje_clientes",
        selected: self.model.get("id_cliente"),
        onComplete:function(c) {
          crear_select2("viaje_clientes");
        }                    
      });
    },

    cargar_categorias: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.CategoriaViaje,
        url: "categorias_viajes/function/get_select/",
        render: "#viaje_categorias",
        selected: self.model.get("id_categoria"),
      });
    },

    agregar_precio: function() {
      // Controlamos los valores
      var fecha_desde = $("#viaje_precio_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor ingrese una fecha");
        $("#viaje_precio_fecha_desde").focus();
        return;
      }
      var fecha_hasta = $("#viaje_precio_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor ingrese una fecha");
        $("#viaje_precio_fecha_hasta").focus();
        return;
      }
      var edad_desde = $("#viaje_precio_edad_desde").val();
      if (isEmpty(edad_desde)) {
        alert("Por favor ingrese una edad");
        $("#viaje_precio_edad_desde").focus();
        return;
      }
      var edad_hasta = $("#viaje_precio_edad_hasta").val();
      if (isEmpty(edad_hasta)) {
        alert("Por favor ingrese una edad");
        $("#viaje_precio_edad_hasta").focus();
        return;
      }
      var id_tipo_tarifa = $("#viaje_precio_tarifas").val();
      var tarifa = $("#viaje_precio_tarifas option:selected").text();
      var moneda = $("#viaje_precio_moneda").val();
      var monto = parseFloat($("#viaje_precio_monto").val());
      if (isNaN(monto) || monto <= 0) monto = 0;
      var cantidad = $("#precio_cantidad").val();

      var lunes = this.$("#viaje_precio_lunes").is(":checked")?1:0;
      var martes = this.$("#viaje_precio_martes").is(":checked")?1:0;
      var miercoles = this.$("#viaje_precio_miercoles").is(":checked")?1:0;
      var jueves = this.$("#viaje_precio_jueves").is(":checked")?1:0;
      var viernes = this.$("#viaje_precio_viernes").is(":checked")?1:0;
      var sabado = this.$("#viaje_precio_sabado").is(":checked")?1:0;
      var domingo = this.$("#viaje_precio_domingo").is(":checked")?1:0;

      var tr = "<tr>";
      tr+="<td class='id_tipo_tarifa editar_precio dn'>"+id_tipo_tarifa+"</td>";
      tr+="<td class='tarifa editar_precio'><span class='text-info'>"+tarifa+"</td>";
      tr+="<td class='fecha_desde editar_precio'>"+fecha_desde+"</td>";
      tr+="<td class='fecha_hasta editar_precio'>"+fecha_hasta+"</td>";
      tr+="<td class='edad_desde editar_precio'>"+edad_desde+"</td>";
      tr+="<td class='edad_hasta editar_precio'>"+edad_hasta+"</td>";
      tr+="<td class='moneda tar pr0 editar_precio'>"+moneda+"</td>";
      tr+="<td class='precio'>"+Number(monto).toFixed(2)+"</td>";
      tr+="<td class='tar'>";
      tr+="<input type='hidden' class='lunes' value='"+lunes+"'/>";
      tr+="<input type='hidden' class='martes' value='"+martes+"'/>";
      tr+="<input type='hidden' class='miercoles' value='"+miercoles+"'/>";
      tr+="<input type='hidden' class='jueves' value='"+jueves+"'/>";
      tr+="<input type='hidden' class='viernes' value='"+viernes+"'/>";
      tr+="<input type='hidden' class='sabado' value='"+sabado+"'/>";
      tr+="<input type='hidden' class='domingo' value='"+domingo+"'/>";
      tr+="<button class='btn btn-sm btn-white eliminar_precio'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_precio == null) {
        $("#viaje_precios_tabla tbody").append(tr);
      } else {
        $(this.item_precio).replaceWith(tr);
        this.item_precio = null;
      }
      $("#viaje_precio_edad_desde").val("");
      $("#viaje_precio_edad_hasta").val("");
      $("#viaje_precio_fecha_desde").val("");
      $("#viaje_precio_fecha_hasta").val("");
      $("#viaje_precio_lunes").prop("checked",true);
      $("#viaje_precio_martes").prop("checked",true);
      $("#viaje_precio_miercoles").prop("checked",true);
      $("#viaje_precio_jueves").prop("checked",true);
      $("#viaje_precio_viernes").prop("checked",true);
      $("#viaje_precio_sabado").prop("checked",true);
      $("#viaje_precio_domingo").prop("checked",true);
      $("#viaje_precio_monto").val("");
    },
    
    editar_precio: function(e) {
      this.item_precio = $(e.currentTarget).parents("tr");
      $("#viaje_precio_tarifas").val($(this.item_precio).find(".id_tipo_tarifa").text());
      $("#viaje_precio_fecha_desde").val($(this.item_precio).find(".fecha_desde").text());
      $("#viaje_precio_fecha_hasta").val($(this.item_precio).find(".fecha_hasta").text());
      $("#viaje_precio_edad_desde").val($(this.item_precio).find(".edad_desde").text());
      $("#viaje_precio_edad_hasta").val($(this.item_precio).find(".edad_hasta").text());
      $("#viaje_precio_moneda").val($(this.item_precio).find(".moneda").text());
      $("#viaje_precio_monto").val($(this.item_precio).find(".precio").text());
      $("#viaje_precio_lunes").prop("checked",($(this.item_precio).find(".lunes").val()=="1"));
      $("#viaje_precio_martes").prop("checked",($(this.item_precio).find(".martes").val()=="1"));
      $("#viaje_precio_miercoles").prop("checked",($(this.item_precio).find(".miercoles").val()=="1"));
      $("#viaje_precio_jueves").prop("checked",($(this.item_precio).find(".jueves").val()=="1"));
      $("#viaje_precio_viernes").prop("checked",($(this.item_precio).find(".viernes").val()=="1"));
      $("#viaje_precio_sabado").prop("checked",($(this.item_precio).find(".sabado").val()=="1"));
      $("#viaje_precio_domingo").prop("checked",($(this.item_precio).find(".domingo").val()=="1"));
    },

    agregar_vehiculo: function() {
      // Controlamos los valores
      var id_vehiculo = this.$("#viaje_vehiculos").val();
      if (id_vehiculo == 0) {
        alert("Por favor seleccione un vehiculo");
        $("#viaje_vehiculos").focus();
        return;
      }
      var id_tripulante = this.$("#viaje_tripulantes").val();
      if (id_tripulante == 0) {
        alert("Por favor seleccione un tripulante");
        $("#viaje_tripulantes").focus();
        return;
      }

      var comision = (this.$("#viaje_vehiculos_comision").length > 0) ? parseFloat(this.$("#viaje_vehiculos_comision").val()) : 0;
      if (isNaN(comision)) comision = 0;

      if (this.$("#fila_"+id_vehiculo+"_"+id_tripulante).length > 0) return;
      var vehiculo = this.$("#viaje_vehiculos option:selected").text();
      var tripulante = this.$("#viaje_tripulantes option:selected").text();
      var tr = "<tr id='fila_"+id_vehiculo+"_"+id_tripulante+"' data-id_vehiculo='"+id_vehiculo+"' data-comision='"+comision+"' data-id_tripulante='"+id_tripulante+"'>";
      tr+="<td>"+vehiculo+"</td>";
      tr+="<td>"+tripulante+"</td>";
      if (this.$("#viaje_vehiculos_comision").length > 0) {
        tr+="<td>"+Number(comision).toFixed(2)+"</td>";
      }
      tr+="<td><i class='fa fa-times eliminar_vehiculo text-danger cp'></i></td>";
      tr+="</tr>";
      $("#vehiculos_tabla tbody").append(tr);

      // Limpiamos los campos
      this.$("#viaje_vehiculos").val("0");
      this.$("#viaje_tripulantes").val("0");
      this.$("#viaje_vehiculos_comision").val("");
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

      if (control.check("vehiculos")>0) {
        new app.mixins.Select({
          modelClass: app.models.Vehiculo,
          url: "vehiculos/",
          render: "#viaje_vehiculos",
          firstOptions: ["<option value='0'>Seleccione</option>"],
        });
        new app.mixins.Select({
          modelClass: app.models.Tripulante,
          url: "tripulantes/",
          render: "#viaje_tripulantes",
          firstOptions: ["<option value='0'>Seleccione</option>"],
        });
      }

      if (ID_EMPRESA == 501) this.cargar_clientes();

      if (control.check("opcionales")>0) {
        this.cargar_opcionales();
      }
      this.cargar_relacionados();

      $(this.el).find("#viaje_caracteristicas").select2({
        tags: true,
      });

      if (isEmpty(this.model.get("fecha"))) {
        this.model.set("fecha",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#viaje_fecha"),this.model.get("fecha"));

      if (isEmpty(this.model.get("fecha_llegada"))) {
        this.model.set("fecha_llegada",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#viaje_fecha_llegada"),this.model.get("fecha_llegada"));

      createdatepicker(this.$("#viaje_precio_fecha_desde"),new Date());
      createdatepicker(this.$("#viaje_precio_fecha_hasta"),new Date());

      if (control.check("web_configuracion")>0) { 

        this.marcadores = new Array();
        this.total_marcadores = 0;
        this.latitud_original = -34.6156625;
        this.longitud_original = -58.5033598;

        self.cargar_categorias();

        if (self.$("#viaje_mapa").length > 0) {
          try {
            loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
          } catch(e) {}
        }

        // Cuando cambian las imagens, renderizamos la tabla
        this.stopListening();
        this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
        this.render_tabla_fotos();
        $(this.el).find("#images_tabla").sortable();

        if (control.check("promociones")) {
          new app.mixins.Select({
            modelClass: app.models.Promocion,
            url: "promociones/",
            render: "#viaje_promociones",
            firstOptions: ["<option value='0'>-</option>"],
            selected: self.model.get("id_promocion"),
            onComplete:function(c) {
              crear_select2("viaje_promociones");
            }                    
          });
        }
      }
    },

    cargar_opcionales: function(id_opcional) {
      var self = this;
      id_opcional = (id_opcional || 0);
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Opcional,
        url: "opcionales/function/buscar/?offset=99999&limit=0",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        render: "#viaje_opcionales",
        selected: id_opcional,
        onComplete:function(c) {
          crear_select2("viaje_opcionales");
        }                    
      });
    },

    agregar_opcional: function() {
      var id_opcional = $("#viaje_opcionales").val();
      if (id_opcional == 0) {
        alert("Por favor seleccione un opcional");
        $("#viaje_opcionales").focus();
        return;
      }
      if (this.$("#opcional_"+id_opcional).length > 0) return;
      var opcional = $("#viaje_opcionales option:selected").text();
      var tr = "<tr id='opcional_"+id_opcional+"' data-id='"+id_opcional+"'>";
      tr+="<td>"+opcional+"</td>";
      tr+="<td><i class='fa fa-times eliminar_opcional text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#opcionales_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#viaje_opcionales").focus();
    },


    cargar_relacionados: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Opcional,
        url: "viajes/function/buscar/?offset=99999&limit=0",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        render: "#viaje_relacionados",
        onComplete:function(c) {
          crear_select2("viaje_relacionados");
        }                    
      });
    },

    agregar_relacionado: function() {
      var id_relacionado = $("#viaje_relacionados").val();
      if (id_relacionado == 0) {
        alert("Por favor seleccione un viaje");
        $("#viaje_relacionados").focus();
        return;
      }
      if (this.$("#relacionado_"+id_relacionado).length > 0) return;
      var relacionado = $("#viaje_relacionados option:selected").text();
      var tr = "<tr id='relacionado_"+id_relacionado+"' data-id='"+id_relacionado+"'>";
      tr+="<td>"+relacionado+"</td>";
      tr+="<td><i class='fa fa-times eliminar_relacionado text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#relacionados_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#viaje_relacionados").focus();
    },


    get_coords_by_address: function() {
      var self = this;
      if (self.map == undefined) return;
      var calle = $("#viaje_direccion").val();
      self.geocoder.geocode( { 'address': calle}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          var location = results[0].geometry.location;
          var latitud = location.lat();
          var longitud = location.lng();
          var coor = new google.maps.LatLng(latitud,longitud);
          self.map.setCenter(coor);
          self.map.setZoom(18);
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },

    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length == 0) {
        this.$("#images_container").removeClass('tiene');
      } else {
        this.$("#images_container").addClass('tiene');
        for(var i=0;i<images.length;i++) {
          var path = images[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='dn filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
        }
      }
    },

    render_map: function() {
      var self = this;
      var zoom = parseInt(self.model.get("zoom"));
      var latitud = (self.model.get("latitud"));
      var longitud = (self.model.get("longitud"));
      if (latitud == 0) {
        var latitud = this.latitud_original;
        var longitud = this.longitud_original;
        zoom = 12;
      }
      self.geocoder = new google.maps.Geocoder();
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        "zoom": zoom,
        "center": self.coor
      }
      self.map = new google.maps.Map(document.getElementById("viaje_mapa"), mapOptions);
      // Agregamos los marcadores en el mapa
      if (!isEmpty(self.model.get("posiciones"))) {
        var posiciones = self.model.get("posiciones").split("/");
        for(var i=0;i<posiciones.length;i++) {
          var pos = posiciones[i];
          var p = pos.split(";");
          self.add_marker(p[0],p[1]);
        }            
      }
    },

    add_marker: function(latitud,longitud) {
      var self = this;
      var coord = new google.maps.LatLng(latitud,longitud);    
      var marker = new google.maps.Marker({
        position: coord,
        map: self.map,
        draggable:true,
        title:"Arrastralo a la direccion correcta"
      });
      marker.id = this.total_marcadores;
      google.maps.event.addListener(marker, "dblclick", function (e) { 
        if (confirm("Realmente desea eliminar el marcador?")) {
          self.marcadores = self.marcadores.filter(function(ee){
            return (ee.id != marker.id);
          });
          marker.setMap(null);
        }
      });
      this.marcadores.push(marker);
      this.total_marcadores++;
    },

  validar: function() {
    try {
      var self = this;

      if (ID_EMPRESA != 501) validate_input("viaje_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");

      if (control.check("vehiculos")>0) {

        var vehiculos_tripulantes = new Array();
        $("#vehiculos_tabla tbody tr").each(function(i,e){
          var id_vehiculo = $(e).data("id_vehiculo");
          var id_tripulante = $(e).data("id_tripulante");
          var comision = $(e).data("comision");
          vehiculos_tripulantes.push({
            "id_vehiculo":id_vehiculo,
            "id_tripulante":id_tripulante,
            "comision":comision,
          });
        });
        this.model.set({"vehiculos_tripulantes":vehiculos_tripulantes});
      }

      // Las caracteristicas van todas juntas separadas por ;;;
      if (self.$("#viaje_caracteristicas").length > 0) {
        var c = self.$("#viaje_caracteristicas").select2("val");
        if (c != null) this.model.set({ "caracteristicas":c.join(";;;") });
      }

      this.model.set({
        "nombre":((self.$("#viaje_nombre").length>0) ? self.$("#viaje_nombre").val() : ""),
        "nombre_en":((self.$("#viaje_nombre_en").length>0) ? self.$("#viaje_nombre_en").val() : ""),
        "nombre_pt":((self.$("#viaje_nombre_pt").length>0) ? self.$("#viaje_nombre_pt").val() : ""),
        "observaciones":((self.$("#viaje_observaciones").length>0) ? self.$("#viaje_observaciones").val() : ""),
        "observaciones_en":((self.$("#viaje_observaciones_en").length>0) ? self.$("#viaje_observaciones_en").val() : ""),
        "observaciones_pt":((self.$("#viaje_observaciones_pt").length>0) ? self.$("#viaje_observaciones_pt").val() : ""),
        "subtitulo":((self.$("#viaje_subtitulo").length>0) ? self.$("#viaje_subtitulo").val() : ""),
        "subtitulo_en":((self.$("#viaje_subtitulo_en").length>0) ? self.$("#viaje_subtitulo_en").val() : ""),
        "subtitulo_pt":((self.$("#viaje_subtitulo_pt").length>0) ? self.$("#viaje_subtitulo_pt").val() : ""),
        "fecha":self.$("#viaje_fecha").val(),
        "fecha_llegada":self.$("#viaje_fecha_llegada").val(),
        "id_promocion":((self.$("#viaje_promociones").length>0) ? self.$("#viaje_promociones").val() : 0),
        "id_categoria":((self.$("#viaje_categorias").length>0) ? self.$("#viaje_categorias").val() : 0),
        "categoria":((self.$("#viaje_categorias").length>0) ? self.$("#viaje_categorias option:selected").text() : ""),
      });

      if (ID_EMPRESA == 501) {
        validate_input("viaje_custom_1",IS_EMPTY,"Por favor, ingrese un valor.");
        validate_input("viaje_custom_2",IS_EMPTY,"Por favor, ingrese un valor.");
        this.model.set({
          "nombre":self.$("#viaje_custom_1").val()+" - "+self.$("#viaje_custom_2").val(),
          "id_cliente":self.$("#viaje_clientes").val(),
        });
      }

      if (control.check("opcionales")>0) {
        var opcionales = new Array();
        $("#opcionales_tabla tbody tr").each(function(i,e){
          opcionales.push({
            "id": $(e).data("id"),
          });
        });
        this.model.set({"opcionales":opcionales});
      } 

      var relacionados = new Array();
      $("#relacionados_tabla tbody tr").each(function(i,e){
        relacionados.push({
          "id": $(e).data("id"),
        });
      });
      this.model.set({"relacionados":relacionados});

      // Si los custom llegan a ser fileuploaders, hay que setearlos en el modelo
      for(var i=1;i<=10;i++) {
        if ((self.$("#hidden_custom_"+i).length > 0)) {
          var cus = $(self.el).find("#hidden_custom_"+i).val();
          var key = "custom_"+i;
          var obj = {};
          obj[key] = cus;
          this.model.set(obj);
        }          
      }

      // Guardamos los precios
      var precios = new Array();
      if (this.$("#viaje_tarifas_tabla").length > 0) {
        var k = 0;
        $("#viaje_tarifas_tabla tbody tr").each(function(i,e){
          var precio = $(e).find(".precio").val();
          precios.push({
            "id_tipo_tarifa": $(e).find(".tarifa").val(),
            "precio": precio,
            "moneda": $(e).find(".moneda").val(),
            "recargo": $(e).find(".recargo").val(),
            "recargo_2": $(e).find(".recargo_2").val(),
            "recargo_3": $(e).find(".recargo_3").val(),
            "recargo_4": $(e).find(".recargo_4").val(),
          });
          if (k==0) self.model.set({"precio":precio});
          k++;
        });
        this.model.set({"precios":precios});        
      }

      if (this.$("#viaje_precios_tabla").length > 0) {
        var k = 0;
        $("#viaje_precios_tabla tbody tr").each(function(i,e){
          var precio = $(e).find(".precio").text();
          precios.push({
            "id_tipo_tarifa": $(e).find(".id_tipo_tarifa").text(),
            "fecha_desde": $(e).find(".fecha_desde").text(),
            "fecha_hasta": $(e).find(".fecha_hasta").text(),
            "edad_desde": $(e).find(".edad_desde").text(),
            "edad_hasta": $(e).find(".edad_hasta").text(),
            "moneda": $(e).find(".moneda").text(),
            "lunes": $(e).find(".lunes").val(),
            "martes": $(e).find(".martes").val(),
            "miercoles": $(e).find(".miercoles").val(),
            "jueves": $(e).find(".jueves").val(),
            "viernes": $(e).find(".viernes").val(),
            "sabado": $(e).find(".sabado").val(),
            "domingo": $(e).find(".domingo").val(),
            "precio": precio,
          });
          if (k==0) self.model.set({"precio":precio});
          k++;
        });
        this.model.set({"precios":precios});
      }

      if (control.check("web_configuracion")>0) { 

        self.model.set({
          "path":self.$("#hidden_path").val(),
        });

          // Coordenadas
          var a = new Array();
          for(var i=0;i<self.marcadores.length;i++) {
            var marker = self.marcadores[i];
            var pos = marker.getPosition();
            var latitud = (isNaN(pos.lat())) ? 0 : pos.lat();
            var longitud = (isNaN(pos.lng())) ? 0 : pos.lng();
            var c = latitud+";"+longitud;
            a.push(c);
          }
          var posiciones = a.join("/");
          
          if (typeof self.map != "undefined") {
            var zoom = parseInt(self.map.getZoom());
            var pos = self.map.getCenter();
            var latitud = (isNaN(pos.lat())) ? 0 : pos.lat();
            var longitud = (isNaN(pos.lng())) ? 0 : pos.lng();
            this.model.set({
              "zoom":zoom,
              "latitud":latitud,
              "longitud":longitud,
              "posiciones":posiciones,
            });                        
          }

          // Listado de Imagenes
          var images = new Array();
          $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
            images.push($(e).text());
          });
          self.model.set({"images":images});

          // Texto del entrada
          var cktext = CKEDITOR.instances['viaje_texto'].getData();
          self.model.set({"texto":cktext});

          if (typeof CKEDITOR.instances["viaje_texto_en"] != "undefined") { 
            var texto_en = CKEDITOR.instances['viaje_texto_en'].getData();
            self.model.set({"texto_en":texto_en});
          }
          if (typeof CKEDITOR.instances["viaje_texto_pt"] != "undefined") { 
            var texto_pt = CKEDITOR.instances['viaje_texto_pt'].getData();
            self.model.set({"texto_pt":texto_pt});
          }

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
              history.back();
            }
          }
        });
      }      
    },

  });
})(app);





// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.ViajeAsientosView = app.mixins.View.extend({

    template: _.template($("#viaje_asientos_template").html()),

    myEvents: {
      "click .asignar":"asignar",
      "click .guardar":"guardar",
      "click .imprimir_manifiesto":"imprimir_manifiesto",
      "click .imprimir_pasajeros":"imprimir_pasajeros",
      "click .imprimir_taquilla":"imprimir_taquilla",
      "click .ver_habitaciones":"ver_habitaciones",
      "change #viaje_asientos_buscar_cliente":"buscar",
      "click .buscar_cliente":"buscar",
      "change #viaje_asientos_vehiculos":function(e) {
        var self = this;
        var id_vehiculo = $(e.currentTarget).val();
        // Obtenemos el vehiculo
        var vehiculo = new app.models.Vehiculo({"id":id_vehiculo});
        vehiculo.fetch({
          "success":function() {
            // Creamos la grilla de asientos de ese vehiculo
            var view = new app.views.VehiculoEditView({
              "model":vehiculo,
              "edicion":false,
              "id_viaje":self.model.id, // INDICA QUE TENEMOS QUE MOSTRAR LOS ASIENTOS DE SE VIAJE EN PARTICULAR
            });
            self.$("#viaje_asientos_dibujo").html(view.el);                        
          }
        });
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
      window.asientos_seleccionados = new Array();
      this.$("#viaje_asientos_vehiculos").trigger("change");
    },  

    ver_habitaciones: function() {
      var self = this;
      console.log(self.model);
      var view = new app.views.AsientoHabitacionesView({
        "id_viaje":self.model.id,
        "model":new app.models.AbstractModel(),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":800,
        "height":500,
      });
    },

    guardar: function() {
      var self = this;
      this.model.save({
        "texto":self.$("#viaje_asientos_observaciones").val(),
      },{
        "success":function(){
          alert("Los datos se han guardado correctamente.")
        }
      });
    },

    imprimir_manifiesto: function() {
      var id_vehiculo = this.$("#viaje_asientos_vehiculos").val();
      var url = "viajes/function/imprimir_manifiesto/?id_viaje="+this.model.id+"&id_vehiculo="+id_vehiculo;
      window.open(url,"_blank");
    },

    imprimir_pasajeros: function() {
      var id_vehiculo = this.$("#viaje_asientos_vehiculos").val();
      var url = "viajes/function/imprimir_pasajeros/?id_viaje="+this.model.id+"&id_vehiculo="+id_vehiculo;
      window.open(url,"_blank");
    },

    imprimir_taquilla: function() {
      var id_vehiculo = this.$("#viaje_asientos_vehiculos").val();
      var url = "viajes/function/imprimir_taquilla/?id_viaje="+this.model.id+"&id_vehiculo="+id_vehiculo;
      window.open(url,"_blank");
    },

    buscar: function() {
      $("#viaje_asientos_dibujo .tab_link.active").trigger("click");
    },

    // ABRIMOS EL MODAL PARA ASIGNAR CLIENTES EN LOS ASIENTOS
    asignar: function() {

      if (typeof window.asientos_seleccionados == "undefined") window.asientos_seleccionados = new Array();
      if (window.asientos_seleccionados.length == 0) {
        alert("Por favor seleccione al menos un asiento para asignar");
        return;
      }

      // Acomodamos el array de asientos
      var self = this;
      var asientos = new Array();
      for(var i=0;i<window.asientos_seleccionados.length;i++) {
        var a = window.asientos_seleccionados[i];
        var asiento = {
          "id_asiento": a.id,
          "id_vehiculo": a.get("id_vehiculo"),
          "id_reserva": 0,
          "numero_asiento": a.get("numero_asiento"),
          "id_tipo_tarifa": a.get("id_tipo_tarifa"),
          "nacionalidad":"Argentino",
        };
        // Buscamos el precio de ese asiento dependiendo la tarifa
        var precios = self.model.get("precios");
        for(var j=0;j<precios.length;j++) {
          var precio = precios[j];
          if (precio.id_tipo_tarifa == asiento.id_tipo_tarifa) {
            asiento.precio = precio.precio;
            asiento.recargo = precio.recargo;
            asiento.recargo_2 = precio.recargo_2;
            asiento.recargo_3 = precio.recargo_3;
            asiento.recargo_4 = precio.recargo_4;
          }
        }
        asientos.push(asiento);
      }

      // Agregamos una reserva NUEVA
      var id_vehiculo = this.$("#viaje_asientos_vehiculos").val();
      var reserva = new app.models.ReservaAsiento({
        "id_vehiculo":id_vehiculo,
        "id_viaje":self.model.id,
        "asientos":asientos,
        "pagos":[],
      });
      var view = new app.views.ReservaAsientoEditView({
        "model":reserva,
      })
      crearLightboxHTML({
        "html":view.el,
        "width":1000,
        "height":500,
      });
    },
    
  });
})(app);







(function ( models ) {

  models.ReservaAsiento = Backbone.Model.extend({
    urlRoot: "reservas_asientos",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_usuario: ID_USUARIO,
      id_sucursal: ID_SUCURSAL,
      id_cliente: 0,
      cliente: "",
      cliente_telefono: "",
      cliente_email: "",
      id_viaje: 0,
      id_vehiculo: 0,
      fecha_reserva: "",
      salida_desde: "",
      total: 0, // Estos datos se calculan
      pagado: 0,
      asientos: [],
      pagos: [],
      opcionales: [],
      id_tipo_estado: 0,
      observaciones: "",
      prestador_servicio: "",
      hotel: "",
      fecha_llegada_hotel: "",
      hotel_observaciones: "",
      id_vendedor: 0,
      fecha_realizacion: "",
    },
  });
      
})( app.models );


(function ( models ) {

  models.AsientoItem = Backbone.Model.extend({
    urlRoot: "reservas_asientos",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_reserva: 0,
      id_asiento: 0,
      id_vehiculo: 0,
      nombre: "",
      apellido: "",
      menor: 0,
      nacionalidad: "",
      dni: "",
      fecha_nac: "",
      precio: 0,
      recargo: 0,
      recargo_2: 0,
      recargo_3: 0,
      recargo_4: 0,
      moneda: "$",
      hotel: "",
      tipo_habitacion: 0,
      numero_habitacion: "",
    }
  });
        
})( app.models );


(function ( models ) {

  models.ReservaPagoItem = Backbone.Model.extend({
    urlRoot: "reservas_asientos",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha: "",
      metodo: "",
      total: 0,
    }
  });
        
})( app.models );


(function ( models ) {

  models.ReservaOpcionalItem = Backbone.Model.extend({
    urlRoot: "reservas_asientos",
    defaults: {
      id_empresa: ID_EMPRESA,
      total: 0,
      opcional: "",
      id_opcional: 0,
      id_reserva: 0,
    }
  });
        
})( app.models );


(function ( app ) {

  app.views.ReservaAsientoEditView = app.mixins.View.extend({

    template: _.template($("#reserva_asiento_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "click .imprimir": "imprimir",
      "click .imprimir_recibo": "imprimir_recibo",
      "click .voucher": "voucher",
      "click .cerrar": function() { $(".modal:last").trigger('click'); },
      "click #reserva_asiento_agregar_pago": "agregar_pago",
      "keypress #reserva_asiento_total_pago":function(e) {
        if (e.which == 13) this.agregar_pago();
      },
      "click #reserva_asiento_agregar_opcional": "agregar_opcional",
      "keypress #reserva_asiento_opcional_total":function(e) {
        if (e.which == 13) this.agregar_opcional();
      },
      "click .agregar_pasajero": "agregar_pasajero",
    },
            
    initialize: function(options) {
      var self = this;
      _.bindAll(this);

      // Renderizamos y limpiamos
      this.render();

      // Creamos una nueva coleccion de asientos
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.AsientoItem,
      });
      this.asientos = new ItemsCollection();
      this.asientos.on('all', this.render_tabla_asientos, this);
      this.asientos.on('add', this.addAsiento, this);

      // Creamos una nueva coleccion de pagos
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.ReservaPagoItem,
      });
      this.pagos = new ItemsCollection();
      this.pagos.on('all', this.render_tabla_pagos, this);
      this.pagos.on('add', this.addPago, this);           

      if (control.check("opcionales")>0) {
        // Creamos una nueva coleccion de pagos
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ReservaOpcionalItem,
        });
        this.opcionales = new ItemsCollection();
        this.opcionales.on('all', this.render_tabla_opcionales, this);
        this.opcionales.on('add', this.addOpcional, this);

        var opcionales = this.model.get("opcionales");
        for(var i=0;i<opcionales.length;i++) {
          var p = opcionales[i];
          var fi = new app.models.ReservaOpcionalItem(p);
          this.opcionales.add(fi);
        }
      }

      // Agregamos los datos a cada coleccion            
      var asientos = this.model.get("asientos");
      for(var i=0;i<asientos.length;i++) {
        var p = asientos[i];
        var fi = new app.models.AsientoItem(p);
        this.asientos.add(fi);
      }
      var pagos = this.model.get("pagos");
      for(var i=0;i<pagos.length;i++) {
        var p = pagos[i];
        var fi = new app.models.ReservaPagoItem(p);
        this.pagos.add(fi);
      }

      var id_cliente = self.model.get("id_cliente");
      if (id_cliente != 0) {
        var cliente = new app.models.Cliente({"id":id_cliente});
        cliente.fetch({
          "success":function() {
            self.seleccionar_cliente(cliente);        
          },
        });        
      }
    },

    render: function() {

      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#reserva_asiento_cliente");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });            
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });  

      if (control.check("opcionales")>0) {
        this.cargar_opcionales();
      }
      var fecha_reserva = this.model.get("fecha_reserva");
      if (isEmpty(fecha_reserva)) fecha_reserva = new Date();
      createdatepicker(this.$("#reserva_asiento_fecha"),fecha_reserva);
      createdatepicker(this.$("#reserva_asiento_fecha_pago"),moment().format("DD/MM/YYYY"));

      var fecha_llegada_hotel = this.model.get("fecha_llegada_hotel");
      if (isEmpty(fecha_llegada_hotel)) fecha_llegada_hotel = new Date();
      createdatepicker(this.$("#reserva_fecha_llegada_hotel"),fecha_llegada_hotel);
    },

    cargar_opcionales: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Opcional,
        url: "opcionales/",
        render: "#reserva_asiento_opcionales",
      });
    },

    seleccionar_cliente: function(r) {
      var self = this;
      // Seteamos el cliente
      self.model.set({
        "cliente":r.get("nombre"),
        "id_cliente":r.id,
      });
      self.$("#reserva_asiento_cliente").val(r.get("nombre"));
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#reserva_asiento_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);                
    },

    ver_buscar_cliente: function() {
      var self = this;
      var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
        collection: clientes,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarClientes.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#reserva_asiento_cliente").select();                    
        }
      });
      $(".search_input").select();
    },

    // Agrega el item a la lista
    agregar_opcional : function() {
      var self = this;
      var total = this.$("#reserva_asiento_opcional_total").val();
      total = parseFloat(total);
      if (isNaN(total)) {
        alert("Por favor ingrese un numero.");
        this.$("#reserva_asiento_opcional_total").select();
        return false;
      }
      var id_opcional = this.$("#reserva_asiento_opcionales").val();
      var opcional = this.$("#reserva_asiento_opcionales option:selected").text();
      var values = {
        "id_opcional":id_opcional,
        "opcional":opcional,
        "total":total,
      };
      if (this.opcional != undefined) {
        this.opcional.set(values);
      } else {
        var opcional = new app.models.ReservaOpcionalItem(values);
        this.opcionales.add(opcional);
      }
      this.opcional = undefined;
      this.limpiar_opcional();
    },

    editar_opcional: function(r) {
      var self = this;
      self.opcional = r;
      $("#reserva_asiento_opcional_total").val(this.opcional.get("total"));
      $("#reserva_asiento_opcionales").val(this.opcional.get("id_opcional"));
      this.$("#reserva_asiento_opcional_total").select();
    },

    render_tabla_opcionales : function () {
      this.$("#tabla_opcionales tbody").empty();
      this.opcionales.each(this.addOpcional);
      this.calcular_totales();
    },

    addOpcional : function ( item ) {
      var view = new app.views.ReservaOpcionalItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_opcionales tbody").append(view.render().el);
      this.calcular_totales();
    }, 

    // Agrega el item a la lista
    agregar_pago : function() {
      var self = this;
      var fecha = this.$("#reserva_asiento_fecha_pago").val();
      var total = this.$("#reserva_asiento_total_pago").val();
      total = parseFloat(total);
      if (isNaN(total)) {
        alert("Por favor ingrese un numero.");
        this.$("#reserva_asiento_total_pago").select();
        return false;
      }
      if (total == 0) {
        alert("Por favor ingrese un monto.");
        this.$("#reserva_asiento_total_pago").select();
        return false;            
      }
      var metodo = this.$("#reserva_asiento_metodo_pago").val();
      var values = {
        "metodo":metodo,
        "total":total,
        "fecha":fecha,
      };
      if (this.pago != undefined) {
        this.pago.set(values);
      } else {
        var pago = new app.models.ReservaPagoItem(values);
        this.pagos.add(pago);
      }
      this.pago = undefined;
      this.limpiar_pago();
    },

    editar_pago: function(r) {
      var self = this;
      self.pago = r;
      $("#reserva_asiento_fecha_pago").val(this.pago.get("fecha"));
      $("#reserva_asiento_total_pago").val(this.pago.get("total"));
      $("#reserva_asiento_metodo_pago").val(this.pago.get("metodo"));
      this.$("#reserva_asiento_total_pago").select();
    },

    render_tabla_pagos : function () {
      this.$("#tabla_pagos tbody").empty();
      this.pagos.each(this.addPago);
      this.calcular_totales();
    },

    addPago : function ( item ) {
      var view = new app.views.ReservaPagoItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_pagos tbody").append(view.render().el);
      this.calcular_totales();
    }, 
    
    render_tabla_asientos : function () {
      this.$("#tabla_habitaciones tbody").empty();
      this.$("#tabla_asientos tbody").empty();
      this.asientos.each(this.addAsiento);
      this.calcular_totales();
    },

    agregar_pasajero: function() {
      this.asientos.add(new app.models.AsientoItem());
    },

    addAsiento : function ( item ) {
      var view = new app.views.AsientoItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_asientos tbody").append(view.render().el);

      if (this.$("#tabla_habitaciones").length > 0) {
        var view2 = new app.views.AsientoHabitacionItem({
          "model": item,
          "view":this,
        });
        this.$("#tabla_habitaciones tbody").append(view2.render().el);
      }

      this.calcular_totales();
    },

    calcular_totales : function() {
      
      // Totales de pasajes
      var total_asientos = 0;
      this.asientos.each(function(p){
        total_asientos += parseFloat(p.get("precio")) + parseFloat(p.get("recargo")) + parseFloat(p.get("recargo_2")) + parseFloat(p.get("recargo_3")) + parseFloat(p.get("recargo_4"));
      });

      // Totales de pagos
      var total_pagos = 0;
      this.pagos.each(function(p){
        total_pagos += parseFloat(p.get("total"));
      });

      var total_opcionales = 0;
      if (control.check("opcionales")>0) {
        // Totales de opcionales
        this.opcionales.each(function(p){
          total_opcionales += parseFloat(p.get("total"));
        });
      }

      // Diferencia
      var total = total_asientos + total_opcionales;
      var diferencia = total - total_pagos;

      this.model.set({
        "pagado":total_pagos,
        "total":total,
      });
      this.$("#reserva_asientos_subtotal_pagos").html("Subtotal: $ "+Number(total_pagos).toFixed(2));
      this.$("#reserva_asientos_subtotal_asientos").html("Subtotal: $ "+Number(total_asientos).toFixed(2));
      if (this.$("#reserva_asientos_subtotal_opcionales").length > 0) this.$("#reserva_asientos_subtotal_opcionales").html("Subtotal: $ "+Number(total_opcionales).toFixed(2));
      this.$("#reserva_viaje_diferencia").html("$ "+Number(diferencia).toFixed(2));
    },

    limpiar_pago: function() {
      this.$("#reserva_asiento_total_pago").val("");
    },

    limpiar_opcional: function() {
      this.$("#reserva_asiento_opcional_total").val("");
    },

    imprimir: function() {
      var url = "viajes/function/imprimir_boleto/?id_reserva="+this.model.id;
      window.open(url,"_blank");
    },

    imprimir_recibo: function() {
      var url = "viajes/function/imprimir_recibo/?id_reserva="+this.model.id;
      window.open(url,"_blank");
    },

    voucher: function() {
      var url = "reservas_asientos/function/voucher/"+this.model.id+"/";
      workspace.imprimir_reporte(url);
    },

    eliminar: function() {
      if (!confirm("Realmente desea eliminar la reserva?")) return;
      this.model.destroy();   // Eliminamos el modelo
      $(this.el).remove();    // Lo eliminamos de la vista
      location.reload();
    },

    validar: function() {
      try {
        var self = this;
        if (this.asientos.length == 0) {
          alert("Por favor agregue algun pasajero antes de guardar.");
          return false;
        }

        // Recorremos los asientos y controlamos que se hayan cargado todos los datos
        var error = false;
        var mensaje = "";
        self.asientos.each(function(a){
          if (isEmpty(a.get("nombre"))) {
            error = true;
            mensaje = "Por favor ingrese el nombre del pasajero";
            return false;
          }
          /*
          if (isEmpty(a.get("dni"))) {
            error = true;
            mensaje = "Por favor ingrese el DNI";
            return false;
          }*/
        });
        if (error) {
          alert(mensaje);
          return false;
        }

        if (control.check("opcionales")>0) {
          this.model.set({
            "opcionales":self.opcionales.toJSON(),          
          });
        }

        if (control.check("vendedores")>0) {
          this.model.set({
            "id_vendedor":self.$("#reserva_viaje_vendedores").val()
          })
        }

        this.model.set({
          "asientos":self.asientos.toJSON(),
          "pagos":self.pagos.toJSON(),
          "fecha_reserva":self.$("#reserva_asiento_fecha").val(),
          "salida_desde":self.$("#reserva_asiento_salida_desde").val(),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    guardar:function() {
      var self = this;
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


(function ( app ) {
  app.views.AsientoItem = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_asiento_item_template").html()),
    tagName: "tr",
    myEvents: {
      /*
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      */
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
        return false;
      },
      "keypress .form-control":function(e) {
        if (e.which == 13) { this.post_change_form_control(e); }
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = {  };

      // Buscamos el precio de ese asiento dependiendo la tarifa
      if (typeof window.viaje != "undefined") {
        var precios = window.viaje.get("precios");
        for(var j=0;j< precios.length;j++) {
          var precio = precios[j];
          if (precio.id_tipo_tarifa == this.model.get("id_tipo_tarifa")) {
            obj.precio_default = precio.precio;
            obj.recargo_default = precio.recargo;
            obj.recargo_2_default = precio.recargo_2;
            obj.recargo_3_default = precio.recargo_3;
            obj.recargo_4_default = precio.recargo_4;
          }
        }        
      }

      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      this.$(".fecha").mask("99/99/9999");
      return this;
    },
    post_change_form_control: function(e) {
      if (typeof $(e.currentTarget).data("next-select") != "undefined") {
        var next = $(e.currentTarget).data("next-select");
        this.$(next).select();
      }          
    },
  });
})(app);


(function ( app ) {
  app.views.AsientoHabitacionItem = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_asiento_habitacion_item_template").html()),
    tagName: "tr",
    myEvents: {
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = {};
      if (typeof this.model.id != undefined) { obj.id = 0; }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);

(function ( app ) {
  app.views.AsientoHabitacionesView = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_asiento_habitaciones_template").html()),
    myEvents: {
      "click .asignar_habitaciones":"asignar_habitaciones",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      self.id_vehiculo = (typeof this.options.id_vehiculo != "undefined") ? this.options.id_vehiculo : 0;
      self.id_viaje = (typeof this.options.id_viaje != "undefined") ? this.options.id_viaje : 0;
      self.render();
      self.buscar();
    },
    buscar: function() {
      var self = this;
      $.ajax({
        "url":"reservas_asientos/function/get_asientos/",
        "data":{
          "id_vehiculo":self.id_vehiculo,
          "id_viaje":self.id_viaje,
        },
        "dataType":"json",
        "type":"post",
        "success":function(res){
          $("#tabla_habitaciones tbody").empty();
          for(var i=0;i<res.results.length;i++) {
            var o = res.results[i];
            var reserva = new app.models.ReservaAsiento(o);
            var view2 = new app.views.AsientoHabitacionItem({
              "model": reserva,
              "view":this,
            });
            $("#tabla_habitaciones tbody").append(view2.el);
          }
        }
      });
    },
    render: function() {
      $(this.el).html(this.template());
      return this;
    },
    asignar_habitaciones: function() {
      var self = this;
      var view = new app.views.AsignarHabitacionesView({
        "model":new app.models.AbstractModel(),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":500,
        "callback":function() {
          self.buscar();
        }
      });
    },
  });
})(app);


(function ( app ) {
  app.views.AsignarHabitacionesView = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_asiento_asignar_habitacion").html()),
    myEvents: {
      "click .guardar":"guardar",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      self.id_vehiculo = (typeof this.options.id_vehiculo != "undefined") ? this.options.id_vehiculo : 0;
      self.id_viaje = (typeof this.options.id_viaje != "undefined") ? this.options.id_viaje : 0;
      self.render();
    },
    render: function() {
      $(this.el).html(this.template());
      return this;
    },
    guardar: function() {
      var self = this;
      var ids = new Array();
      $("#tabla_habitaciones .check-row:checked").each(function(i,e){
        var id = $(e).parents("tr").find(".id").val();
        ids.push(id);
      });
      if (ids.length == 0) return;
      $.ajax({
        "url":"reservas_asientos/function/asignar_habitaciones/",
        "type":"post",
        "dataType":"json",
        "data": {
          "ids":ids.join("-"),
          "tipo_habitacion":self.$("#reserva_viaje_asiento_asignar_tipo_habitacion").val(),
          "numero_habitacion":self.$("#reserva_viaje_asiento_asignar_numero_habitacion").val(),
          "hotel":self.$("#reserva_viaje_asiento_asignar_hotel option:selected").text(),
        },
        "success":function() {
          $('.modal:last').modal('hide');
        },
      });
    },
  });
})(app);


(function ( app ) {
  app.views.ReservaPagoItem = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_pago_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_pago(this.model);
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = {  };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);


(function ( app ) {
  app.views.ReservaOpcionalItem = app.mixins.View.extend({
      
    template: _.template($("#reserva_viaje_opcional_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_opcional(this.model);
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = {  };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);

(function ( app ) {
  app.views.ReservaAsientoDashboard = app.mixins.View.extend({
    template: _.template($("#reserva_asiento_dashboard_template").html()),
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