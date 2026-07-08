(function ( models ) {

  models.Campania = Backbone.Model.extend({
  urlRoot: "campanias",
  defaults: {
    piezas: [],
    nombre: "",
    vendedor: "",
    id_vendedor: 0,
    valida_desde: "",
    valida_hasta: "",
    cliente: "",
    id_cliente: 0,
    costo: 0,
    dias_vencimiento: 0,
    cobranza: 1,
    estado: "A", // A = Activo; I = Inactivo; P = Pendiente
    pago_unico: 0, // Indica si hay que generar remitos por mes o no

    // Valores solo para Quepensas
    fullscreen: 0,
    mediana_destacada: 0,
    fija_medio: 0,
    fija_abajo: 0,

  },
  });
	  
})( app.models );


(function (collections, model, paginator) {

  collections.Campanias = paginator.requestPager.extend({

  model: model,
  
  paginator_ui: {
    perPage: 99999,
    order_by: 'valida_desde',
    order: 'desc',
  },

  paginator_core: {
    url: "campanias/",
  }
  
  });

})( app.collections, app.models.Campania, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CampaniasTableView = app.mixins.View.extend({

    template: _.template($("#campanias_resultados_template").html()),
      
    myEvents: {
      "click .buscar":"buscar",
      "change #campanias_buscar":function() {
        this.filter = $("#campanias_buscar").val().trim();
        this.buscar();
      },
      "keydown #campanias_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#campanias_texto").focus(); }
      },
      "change #campanias_vendedores":function(e) {
        this.id_vendedor = $(e.currentTarget).val();
        this.buscar();
      },
      "change #campanias_clientes":function(e) {
        this.id_cliente = $(e.currentTarget).val();
        this.buscar();
      },
      "change #campanias_estado":function(e) {
        this.estado = $(e.currentTarget).val();
        this.buscar();        
      },
      "click .nuevo":function() {
        var m = new app.models.Campania({
          "piezas":[],
        });
        var view = new app.views.CampaniaEditView({
          "model":m,
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
      //this.id_cliente = (typeof this.options.id_cliente != "undefined") ? this.options.id_cliente : 0;
      this.id_vendedor = (typeof this.options.id_vendedor != "undefined") ? this.options.id_vendedor : 0;
      if (ID_VENDEDOR != 0) this.id_vendedor = ID_VENDEDOR;
      this.estado = (typeof this.options.estado != "undefined") ? this.options.estado : "A";
      this.collection.on('sync', this.addAll, this);
      this.render();
      this.buscar();
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

      this.$("#campanias_hora_desde").mask("99:99");
      this.$("#campanias_hora_hasta").mask("99:99");
      
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
    },
    
    buscar: function() {
      var self = this;
      var filtros = {
        "filter":self.filter,
        "id_vendedor":self.id_vendedor,
        "estado":self.estado,
        "hora_desde":self.$("#campanias_hora_desde").val(),
        "hora_hasta":self.$("#campanias_hora_hasta").val(),
        "id_categoria":self.$("#campanias_categorias").val(),
      };
      if (this.$(".campanias_dias_check").length > 0) {
        this.$(".campanias_dias_check:checked").each(function(i,e){
          filtros[$(e).val()] = 1;
        });
      }
      this.collection.server_api = filtros;
      this.collection.pager();
    },
    
    addAll : function () {
      this.$("#campanias_tabla tbody").empty();
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
      var view = new app.views.CampaniasItemResultados({
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
  app.views.CampaniasItemResultados = app.mixins.View.extend({
    
    template: _.template($("#campanias_item_resultados_template").html()),
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
        window.codigo_campania_seleccionado = this.model.get("codigo");
        window.campania_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        self.model.fetch({
          "success":function(){
            var view = new app.views.CampaniaEditView({
              "model":self.model,
              "permiso":3,
            });
            crearLightboxHTML({
              "html":view.el,
              "width":700,
              "height":140,
            });
          }
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

  app.views.CampaniaEditView = app.mixins.View.extend({

    template: _.template($("#campania_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",

      // PIEZAS
      "click .ver_pieza":function(e) {
        var self = this;
        var piezas = this.model.get("piezas");
        var i = $(e.currentTarget).data("i");
        var p = new app.models.Pieza();
        p.set(piezas[i]);
        var piezaView = new app.views.PiezaEditView({
          piezas: self.model.get("piezas"),
          posicion: i,
          model: p,
        });
        var d = $("<div/>").append(piezaView.el);
        crearLightboxHTML({
          "html":d,
          "width":750,
          "height":500,
          "callback":function() {
            self.render_piezas();  
          }
        });
      },
      "click .nueva_pieza":function(e) {
        var self = this;
        var piezaView = new app.views.PiezaEditView({
          piezas: self.model.get("piezas"),
          posicion: -1, // La agrega al final
          model: new app.models.Pieza(),
        });
        var d = $("<div/>").append(piezaView.el);
        crearLightboxHTML({
          "html":d,
          "width":750,
          "height":500,
          "callback":function() {
            self.render_piezas();  
          }
        });
      },
      "click .eliminar_pieza":"eliminar_pieza",

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
      createdatepicker($(this.el).find("#campania_valida_desde"),valida_desde);
      
      var valida_hasta = this.model.get("valida_hasta");
      if (isEmpty(valida_hasta)) valida_hasta = moment().endOf('month').toDate();
      createdatepicker($(this.el).find("#campania_valida_hasta"),valida_hasta);      
    },
    
    render: function() {
      $(this.el).find("#campanias_tabla").sortable();
      this.$("#campania_categorias").change();
      this.cargar_clientes();
      this.render_piezas();
    },

    render_piezas: function() {
      this.$("#campania_piezas tbody").empty();
      var piezas = this.model.get("piezas");
      var tr = "";
      for(var i=0;i<piezas.length;i++) {
        var p = piezas[i];
        tr+= "<tr class='ver_pieza' data-i='"+i+"'>";
        tr+="<td><span class='text-info'>"+p.nombre+"</span></td>";
        tr+="<td>"+p.categoria+"</td>";
        tr+="<td>"+p.fecha_desde+"</td>";
        tr+="<td>"+p.fecha_hasta+"</td>";
        if (ID_EMPRESA == 70) {
          tr+="<td class='data'>";
          tr+= (p.repetir==0)?'24 Hs.':'';
          tr+= (p.repetir==2)?'12/24 Hs.':'';
          tr+= (p.repetir==3)?'8/24 Hs.':'';
          tr+= (p.repetir==4)?'6/24 Hs.':'';
          tr+= (p.repetir==6)?'4/24 Hs.':'';
          tr+= (p.repetir==8)?'3/24 Hs.':'';
          tr+= (p.repetir==12)?'2/24 Hs.':'';
          tr+="</td>";
          tr+="<td class='data'>";
          var dias = 0;
          dias = dias + ((p.lunes==1)?1:0);
          dias = dias + ((p.martes==1)?1:0);
          dias = dias + ((p.miercoles==1)?1:0);
          dias = dias + ((p.jueves==1)?1:0);
          dias = dias + ((p.viernes==1)?1:0);
          dias = dias + ((p.sabado==1)?1:0);
          dias = dias + ((p.domingo==1)?1:0);
          tr+= dias;
          tr+="</td>";
        }
        tr+="<td><span class='btn eliminar_pieza btn-white'><i class='fa fa-trash'></i></span></td>";
        tr+="</tr>";
      }
      this.$("#campania_piezas tbody").append(tr);
    },

    eliminar_pieza: function(e) {
      var self = this;
      e.stopPropagation();
      var pos = $(e.currentTarget).parents(".ver_pieza").first().data("i");
      var piezas = this.model.get("piezas");
      var piezas2 = new Array();
      var pieza = null;
      for(var i=0;i<piezas.length;i++) {
        if (i!=pos) piezas2.push(piezas[i]);
        else pieza = piezas[i];
      }
      if (pieza != null) {
        $.ajax({
          "url":"campanias/function/eliminar_pieza/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":pieza.id,
          },
          "success":function(){
            self.model.set({"piezas":piezas2});
            self.render_piezas();
          }
        })        
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
      url: "clientes/?tipo=0",
      firstOptions: ["<option value='0'>Sin Definir</option>"],
      render: "#campania_clientes",
      selected: self.model.get("id_cliente"),
      });
    },
		
    validar: function() {
      try {
        var self = this;
        
        validate_input("campania_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");

        if (self.$("#campania_pago_unico").length>0) {
          this.model.set({
            "pago_unico":(self.$("#campania_pago_unico").is(":checked")?1:0),
          });
        }
        if (self.$("#campania_primer_pago").length>0) {
          this.model.set({
            "primer_pago":(self.$("#campania_primer_pago").is(":checked")?1:0),
          });
        }
        
        this.model.set({
          "valida_desde":$(this.el).find("#campania_valida_desde").val(),
          "valida_hasta":$(this.el).find("#campania_valida_hasta").val(),
          "cliente":((self.$("#campania_clientes").length > 0) ? $(self.el).find("#campania_clientes option:selected").text() : ""),
          "id_cliente":((self.$("#campania_clientes").length > 0) ? $(self.el).find("#campania_clientes").val() : 0),
          "id_vendedor":$(self.el).find("#campania_vendedores").val(),
          "vendedor":$(self.el).find("#campania_vendedores option:selected").text(),
          "estado":$(self.el).find("#campania_estado").val(),
        });
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
              app.views.campaniasTableView.buscar();
              $('.modal:last').modal('hide');
            }
          }
        });
      }	  
    },    
	
  });
})(app);




// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Pieza = Backbone.Model.extend({
  urlRoot: "piezas",
  defaults: {
    id_campania: 0,
    campania: "",
    nombre: "",
    link: "",
    link_target: "_blank",
    fecha_desde: "",
    fecha_hasta: "",
    id_empresa: ID_EMPRESA,
    lunes: 1,
    martes: 1,
    miercoles: 1,
    jueves: 1,
    viernes: 1,
    sabado: 1,
    domingo: 1,
    hora_desde_1: "",
    hora_desde_2: "",
    hora_desde_3: "",
    hora_desde_4: "",
    hora_desde_5: "",
    hora_desde_6: "",
    hora_desde_7: "",
    hora_desde_8: "",
    hora_desde_9: "",
    hora_desde_10: "",
    hora_desde_11: "",
    hora_desde_12: "",
    hora_hasta_1: "",
    hora_hasta_2: "",
    hora_hasta_3: "",
    hora_hasta_4: "",
    hora_hasta_5: "",
    hora_hasta_6: "",
    hora_hasta_7: "",
    hora_hasta_8: "",
    hora_hasta_9: "",
    hora_hasta_10: "",
    hora_hasta_11: "",
    hora_hasta_12: "",
    path: "",
    path_2: "",
    path_3: "",
    path_video: "",
    cerrar: 0,
    cerrar_despues: 5,
    id_tipo_publicidad: 0,
    id_categoria: 0,
    id_categoria_entrada: 0,
    categoria: "",
    activo: 1,
    video: "",
    categorias_relacionados: [],
    prioridad: 1,
    repetir: 0,
    codigo: "",
  },
  });
    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Piezas = paginator.requestPager.extend({
    
    model: model,
    
    paginator_ui: {
      perPage: 30,
      order_by: 'fecha_desde',
      order: 'desc',
    },    
    
    paginator_core: {
      url: "piezas/function/ver",
    }
    
  });

})( app.collections, app.models.Pieza, Backbone.Paginator);



(function ( app ) {

  app.views.PiezaEditView = app.mixins.View.extend({

    template: _.template($("#pieza_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "change #pieza_fecha_hora_inicio":"calcular_horas",
      "change #pieza_repetir":"calcular_horas",
      "click .expand-link-horarios":function() {
        this.$("#horarios_container").slideToggle();
      },
      /*
      "change #pieza_categorias":function(e) {
        var ancho = this.$("#campania_categorias option:selected").data("ancho");
        var alto = this.$("#campania_categorias option:selected").data("alto");
        
        var id_tipo = this.$("#campania_categorias option:selected").data("id_tipo");
        this.$(".campania_tipo_container").hide();
        this.$("#campania_tipo_"+id_tipo).show();
        
        this.$("#path_height").val(alto);
        this.$("#path_width").val(ancho);
      },
      */
    },
    
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);

      console.log(options.piezas);
      this.piezas = options.piezas;
      this.posicion = options.posicion;
      
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#pieza_fecha_desde"),fecha_desde);

      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#pieza_fecha_hasta"),fecha_hasta);

      this.$("#pieza_hora_desde_1").mask("99:99");
      this.$("#pieza_hora_desde_2").mask("99:99");
      this.$("#pieza_hora_desde_3").mask("99:99");
      this.$("#pieza_hora_desde_4").mask("99:99");
      this.$("#pieza_hora_desde_5").mask("99:99");
      this.$("#pieza_hora_desde_6").mask("99:99");
      this.$("#pieza_hora_desde_7").mask("99:99");
      this.$("#pieza_hora_desde_8").mask("99:99");
      this.$("#pieza_hora_desde_9").mask("99:99");
      this.$("#pieza_hora_desde_10").mask("99:99");
      this.$("#pieza_hora_desde_11").mask("99:99");
      this.$("#pieza_hora_desde_12").mask("99:99");
      this.$("#pieza_hora_hasta_1").mask("99:99");
      this.$("#pieza_hora_hasta_2").mask("99:99");
      this.$("#pieza_hora_hasta_3").mask("99:99");
      this.$("#pieza_hora_hasta_4").mask("99:99");
      this.$("#pieza_hora_hasta_5").mask("99:99");
      this.$("#pieza_hora_hasta_6").mask("99:99");
      this.$("#pieza_hora_hasta_7").mask("99:99");
      this.$("#pieza_hora_hasta_8").mask("99:99");
      this.$("#pieza_hora_hasta_9").mask("99:99");
      this.$("#pieza_hora_hasta_10").mask("99:99");
      this.$("#pieza_hora_hasta_11").mask("99:99");
      this.$("#pieza_hora_hasta_12").mask("99:99");

      this.$("#pieza_fecha_hora_inicio").mask("99:99");

      $(this.el).find("#pieza_categorias_tree").fancytree({
        source: {
          url: 'categorias_entradas/function/get_arbol/'
        },
        selectMode: 3,
        checkbox: true,
        renderNode: function(event,data) {
          var node = data.node;
          
          // Controlamos si el ID esta en los relacionados
          var selected = false;
          var rel = self.model.get("categorias_relacionados");
          for(var i=0;i<rel.length;i++) {
            var o = rel[i];
            if (o.id == node.key) {
              selected = true;
              break;
            }
          }
          node.setSelected(selected);
          node.setExpanded(true);
        },
      });
    },

    calcular_horas: function() {
      var repetir = this.$("#pieza_repetir").val();
      var inicio = this.$("#pieza_fecha_hora_inicio").val();
      if (isEmpty(inicio)) {
        alert("Por favor ingrese una hora.");
        this.$("#pieza_fecha_hora_inicio").select();
        return;
      }
      var start = moment(inicio,"HH:mm");
      var end = moment(inicio,"HH:mm");

      this.$("#pieza_hora_desde_1").val("00:00");
      this.$("#pieza_hora_desde_2").val("00:00");
      this.$("#pieza_hora_desde_3").val("00:00");
      this.$("#pieza_hora_desde_4").val("00:00");
      this.$("#pieza_hora_desde_5").val("00:00");
      this.$("#pieza_hora_desde_6").val("00:00");
      this.$("#pieza_hora_desde_7").val("00:00");
      this.$("#pieza_hora_desde_8").val("00:00");
      this.$("#pieza_hora_desde_9").val("00:00");
      this.$("#pieza_hora_desde_10").val("00:00");
      this.$("#pieza_hora_desde_11").val("00:00");
      this.$("#pieza_hora_desde_12").val("00:00");
      this.$("#pieza_hora_hasta_1").val("00:00");
      this.$("#pieza_hora_hasta_2").val("00:00");
      this.$("#pieza_hora_hasta_3").val("00:00");
      this.$("#pieza_hora_hasta_4").val("00:00");
      this.$("#pieza_hora_hasta_5").val("00:00");
      this.$("#pieza_hora_hasta_6").val("00:00");
      this.$("#pieza_hora_hasta_7").val("00:00");
      this.$("#pieza_hora_hasta_8").val("00:00");
      this.$("#pieza_hora_hasta_9").val("00:00");
      this.$("#pieza_hora_hasta_10").val("00:00");
      this.$("#pieza_hora_hasta_11").val("00:00");
      this.$("#pieza_hora_hasta_12").val("00:00");

      if (repetir != 0) {

        this.$("#pieza_hora_desde_1").val(start.format("HH:mm"));
        end.add(1,'h');
        this.$("#pieza_hora_hasta_1").val(end.format("HH:mm"));

        if (repetir == 2) {

          start.add(2,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_3").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_3").val(end.format("HH:mm"));
          
          start.add(2,'h');
          this.$("#pieza_hora_desde_4").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_4").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_5").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_5").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_6").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_6").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_7").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_7").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_8").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_8").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_9").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_9").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_10").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_10").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_11").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_11").val(end.format("HH:mm"));

          start.add(2,'h');
          this.$("#pieza_hora_desde_12").val(start.format("HH:mm"));
          end.add(2,'h');
          this.$("#pieza_hora_hasta_12").val(end.format("HH:mm"));

        } else if (repetir == 3) {

          start.add(3,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));

          start.add(3,'h');
          this.$("#pieza_hora_desde_3").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_3").val(end.format("HH:mm"));
          
          start.add(3,'h');
          this.$("#pieza_hora_desde_4").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_4").val(end.format("HH:mm"));

          start.add(3,'h');
          this.$("#pieza_hora_desde_5").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_5").val(end.format("HH:mm"));

          start.add(3,'h');
          this.$("#pieza_hora_desde_6").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_6").val(end.format("HH:mm"));

          start.add(3,'h');
          this.$("#pieza_hora_desde_7").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_7").val(end.format("HH:mm"));

          start.add(3,'h');
          this.$("#pieza_hora_desde_8").val(start.format("HH:mm"));
          end.add(3,'h');
          this.$("#pieza_hora_hasta_8").val(end.format("HH:mm"));

        } else if (repetir == 4) {

          start.add(4,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(4,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));

          start.add(4,'h');
          this.$("#pieza_hora_desde_3").val(start.format("HH:mm"));
          end.add(4,'h');
          this.$("#pieza_hora_hasta_3").val(end.format("HH:mm"));
          
          start.add(4,'h');
          this.$("#pieza_hora_desde_4").val(start.format("HH:mm"));
          end.add(4,'h');
          this.$("#pieza_hora_hasta_4").val(end.format("HH:mm"));

          start.add(4,'h');
          this.$("#pieza_hora_desde_5").val(start.format("HH:mm"));
          end.add(4,'h');
          this.$("#pieza_hora_hasta_5").val(end.format("HH:mm"));

          start.add(4,'h');
          this.$("#pieza_hora_desde_6").val(start.format("HH:mm"));
          end.add(4,'h');
          this.$("#pieza_hora_hasta_6").val(end.format("HH:mm"));

        } else if (repetir == 6) {

          start.add(6,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(6,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));

          start.add(6,'h');
          this.$("#pieza_hora_desde_3").val(start.format("HH:mm"));
          end.add(6,'h');
          this.$("#pieza_hora_hasta_3").val(end.format("HH:mm"));
          
          start.add(6,'h');
          this.$("#pieza_hora_desde_4").val(start.format("HH:mm"));
          end.add(6,'h');
          this.$("#pieza_hora_hasta_4").val(end.format("HH:mm"));

        } else if (repetir == 8) {

          start.add(8,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(8,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));

          start.add(8,'h');
          this.$("#pieza_hora_desde_3").val(start.format("HH:mm"));
          end.add(8,'h');
          this.$("#pieza_hora_hasta_3").val(end.format("HH:mm"));

        } else if (repetir == 12) {

          start.add(12,'h');
          this.$("#pieza_hora_desde_2").val(start.format("HH:mm"));
          end.add(12,'h');
          this.$("#pieza_hora_hasta_2").val(end.format("HH:mm"));
          
        }
      }
    },
    
    validar: function() {
      try {
        var self = this;
        
        var fecha_desde = self.$("#pieza_fecha_desde").val();
        var fecha_hasta = self.$("#pieza_fecha_hasta").val();
        /*
        if (isEmpty(fecha_desde)) {
          alert("Por favor elija una fecha");
          self.$("#pieza_fecha_desde").focus();
          return false;
        }
        if (isEmpty(fecha_desde)) {
          alert("Por favor elija una fecha");
          self.$("#pieza_fecha_hasta").focus();
          return false;
        }
        */

        // Arbol de categorias de relacionados
        var categorias_relacionados = new Array();
        var rel = $("#pieza_categorias_tree").fancytree("getTree").getSelectedNodes();
        for(var i=0;i<rel.length;i++) {
          var o = rel[i];
          categorias_relacionados.push({
            "id":o.key,
          });
        }
        self.model.set({"categorias_relacionados":categorias_relacionados});

        this.model.set({
          "cerrar_despues":$(self.el).find("#campania_cerrar_despues").val(),
          "fecha_desde":fecha_desde,
          "fecha_hasta":fecha_hasta,
          "path":$(self.el).find("#hidden_path").val(),
          "path_2":$(self.el).find("#hidden_path_2").val(),
          "path_3":$(self.el).find("#hidden_path_3").val(),
          "path_video":((self.$("#hidden_path_video").length > 0) ? $(self.el).find("#hidden_path_video").val() : ""),
          "id_categoria":self.$("#pieza_categorias").val(),
          "categoria":self.$("#pieza_categorias option:selected").text(),
          "lunes":(self.$("#pieza_lunes").is(":checked")?1:0),
          "martes":(self.$("#pieza_martes").is(":checked")?1:0),
          "miercoles":(self.$("#pieza_miercoles").is(":checked")?1:0),
          "jueves":(self.$("#pieza_jueves").is(":checked")?1:0),
          "viernes":(self.$("#pieza_viernes").is(":checked")?1:0),
          "sabado":(self.$("#pieza_sabado").is(":checked")?1:0),
          "domingo":(self.$("#pieza_domingo").is(":checked")?1:0),
          "cerrar":(self.$("#campania_cerrar").is(":checked")?1:0),
          "repetir":(this.$("#pieza_repetir").length > 0) ? this.$("#pieza_repetir").val() : 0,
          "hora_desde_1":(this.$("#pieza_hora_desde_1").length > 0) ? this.$("#pieza_hora_desde_1").val() : "",
          "hora_desde_2":(this.$("#pieza_hora_desde_2").length > 0) ? this.$("#pieza_hora_desde_2").val() : "",
          "hora_desde_3":(this.$("#pieza_hora_desde_3").length > 0) ? this.$("#pieza_hora_desde_3").val() : "",
          "hora_desde_4":(this.$("#pieza_hora_desde_4").length > 0) ? this.$("#pieza_hora_desde_4").val() : "",
          "hora_desde_5":(this.$("#pieza_hora_desde_5").length > 0) ? this.$("#pieza_hora_desde_5").val() : "",
          "hora_desde_6":(this.$("#pieza_hora_desde_6").length > 0) ? this.$("#pieza_hora_desde_6").val() : "",
          "hora_desde_7":(this.$("#pieza_hora_desde_7").length > 0) ? this.$("#pieza_hora_desde_7").val() : "",
          "hora_desde_8":(this.$("#pieza_hora_desde_8").length > 0) ? this.$("#pieza_hora_desde_8").val() : "",
          "hora_desde_9":(this.$("#pieza_hora_desde_9").length > 0) ? this.$("#pieza_hora_desde_9").val() : "",
          "hora_desde_10":(this.$("#pieza_hora_desde_10").length > 0) ? this.$("#pieza_hora_desde_10").val() : "",
          "hora_desde_11":(this.$("#pieza_hora_desde_11").length > 0) ? this.$("#pieza_hora_desde_11").val() : "",
          "hora_desde_12":(this.$("#pieza_hora_desde_12").length > 0) ? this.$("#pieza_hora_desde_12").val() : "",
          "hora_hasta_1":(this.$("#pieza_hora_hasta_1").length > 0) ? this.$("#pieza_hora_hasta_1").val() : "",
          "hora_hasta_2":(this.$("#pieza_hora_hasta_2").length > 0) ? this.$("#pieza_hora_hasta_2").val() : "",
          "hora_hasta_3":(this.$("#pieza_hora_hasta_3").length > 0) ? this.$("#pieza_hora_hasta_3").val() : "",
          "hora_hasta_4":(this.$("#pieza_hora_hasta_4").length > 0) ? this.$("#pieza_hora_hasta_4").val() : "",
          "hora_hasta_5":(this.$("#pieza_hora_hasta_5").length > 0) ? this.$("#pieza_hora_hasta_5").val() : "",
          "hora_hasta_6":(this.$("#pieza_hora_hasta_6").length > 0) ? this.$("#pieza_hora_hasta_6").val() : "",
          "hora_hasta_7":(this.$("#pieza_hora_hasta_7").length > 0) ? this.$("#pieza_hora_hasta_7").val() : "",
          "hora_hasta_8":(this.$("#pieza_hora_hasta_8").length > 0) ? this.$("#pieza_hora_hasta_8").val() : "",
          "hora_hasta_9":(this.$("#pieza_hora_hasta_9").length > 0) ? this.$("#pieza_hora_hasta_9").val() : "",
          "hora_hasta_10":(this.$("#pieza_hora_hasta_10").length > 0) ? this.$("#pieza_hora_hasta_10").val() : "",
          "hora_hasta_11":(this.$("#pieza_hora_hasta_11").length > 0) ? this.$("#pieza_hora_hasta_11").val() : "",
          "hora_hasta_12":(this.$("#pieza_hora_hasta_12").length > 0) ? this.$("#pieza_hora_hasta_12").val() : "",
        });
        
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },  
  
    guardar:function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({
            id:0,
          });
        }

        if (self.posicion == -1) {
          // Lo agregamos al final del array
          if (!$.isArray(self.piezas)) self.piezas = new Array();
          self.piezas.push(self.model.toJSON());
        } else {
          // Reemplazamos la posicion esa
          self.piezas[self.posicion] = self.model.toJSON();
        }
        
        $(".modal").last().trigger("click");
      }
    },    
  
  });
})(app);