// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ClasificadoAuto = Backbone.Model.extend({
    urlRoot: "autos",
    defaults: {
      // Atributos que no se persisten directamente
      images: [],
      tipo: "",
      cliente: "",
      
      id_empresa: ID_EMPRESA,
      titulo: "",
      id_cliente: 0,
      id_tipo:0,
      moneda: "",
      codigo: "",
      precio_final: 0,
      activo: 1,
      publica_precio: 1,
      video: "",
      nuevo: 0,
      destacado: 0,
      texto: "",
      valido_desde: "",
      valido_hasta: "",
      
      marca: "",
      modelo: "",
      motor: "",
      kms: "",
      anio: "",
      id_marca: 0,
      puertas: "",
      combustible: "",
      version: "",
      traccion: "",
      aire_acondicionado: 0,
      alarma:0,
      gps:0,
      sensor_lluvia: 0,
      computadora: 0,
      levanta_cristales:0,
      espejos_electricos:0,
      cierre_centralizado: 0,
      direccion: "",
      airbag: 0,
      tercer_stop: 0,
      control_traccion: 0,
      antiniebla: 0,
      control_estabilidad: 0,
      frenos_abs: 0,
      tapizado_cuero: 0,
      texto_privado: "",
      cantidad_consultas: 0,
      color: "",

      id_localidad: ((typeof ID_LOCALIDAD != "undefined") ? ID_LOCALIDAD : 0),
      id_departamento: ((typeof ID_DEPARTAMENTO != "undefined") ? ID_DEPARTAMENTO : 0),
      id_provincia: ((typeof ID_PROVINCIA != "undefined") ? ID_PROVINCIA : 0),
      id_pais: ((typeof ID_PAIS != "undefined") ? ID_PAIS : 0),

    },
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.ClasificadosAutos = paginator.requestPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'fecha',
      order: 'desc',
    },

    paginator_core: {
      url: function() {
        var texto = (isEmpty(this.meta("texto")) ? 0 : this.meta("texto"));
        var s = "autos/function/ver";
        s=s+"/"+texto;
        return s;
      }
    }
    
  });

})( app.collections, app.models.ClasificadoAuto, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ClasificadosAutosTableView = app.mixins.View.extend({

    template: _.template($("#clasificados_autos_resultados_template").html()),
        
    myEvents: {
      "change #clasificados_autos_buscar":"buscar",
      "click .buscar":"buscar",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "keydown #clasificados_autos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#clasificados_autos_texto").focus(); }
      },
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.render();      
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(pagination.el);
      return this;
    },
    
    buscar: function() {
      var datos = {};
      datos.filter = this.$("#clasificados_autos_buscar").val();
      this.collection.server_api = datos;
      this.collection.pager();            
    },
    
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.AutosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"autos"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        
        
    exportar: function(obj) {
      // Reemplazamos el ver por el exportar
      var url = this.collection.url;
      url = url.replace("/ver/","/exportar/");
      // Los parametros de orden se envian por GET
      url += "?order="+this.collection.paginator_ui.order+"&order_by="+this.collection.paginator_ui.order_by;
      window.open(url,"_blank");
    },
    
    exportar_csv: function(obj) {
      window.open("autos/function/exportar_csv/","_blank");
    },
        
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.AutosItemResultados = app.mixins.View.extend({
    template: _.template($("#clasificados_autos_item_resultados_template").html()),
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
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"veh_autos",
          "url":"autos/function/change_property/",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .nuevo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nuevo = this.model.get("nuevo");
        nuevo = (nuevo == 1)?0:1;
        self.model.set({"nuevo":nuevo});
        this.change_property({
          "table":"veh_autos",
          "url":"autos/function/change_property/",
          "attribute":"nuevo",
          "value":nuevo,
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
          "table":"veh_autos",
          "url":"autos/function/change_property/",
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
            "url":"autos/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              var d = self.model.clone();
              d.set("id",r.id);
              autos.add(d);
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
      "click .facebook":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        window.open("autos/function/compartir/"+this.model.id+"/","_blank","height=250,width=450,location=no,resizable=no,scrollbars=no,titlebar=no,menubar=no,top=200,left=200");
        return false;
      },            
    },
    seleccionar: function() {
      var self = this;
      if (this.habilitar_seleccion) {
        window.codigo_auto_seleccionado = this.model.get("codigo");
        window.auto_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#clasificado_auto/"+self.model.id;
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

  app.views.ClasificadoAutoEditView = app.mixins.View.extend({
    template: _.template($("#clasificado_auto_template").html()),
    myEvents: {

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "autos/function/upload_images/",
          "view": self,
        });
      },

      // MARCAS
      // ================================

      "click .agregar_marca_vehiculo":function(e) {
        var self = this;
        if ($(".marca_edit_mini").length > 0) return;
        var form = new app.views.MarcaVehiculoMiniEditView({
          "model": new app.models.MarcaVehiculo(),
          "callback":function(m){
            self.model.set({ "id_marca":m });
            self.cargar_marcas_vehiculos();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete marca_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#marcas_vehiculos_mini_nombre").focus();
      },

      "change #clasificado_auto_paises":function(){
        var id_provincia = this.$("#clasificado_auto_provincias").val();
        this.cambiar_paises(id_provincia);
      },
      "change #clasificado_auto_provincias":function(){
        var id_departamento = this.$("#clasificado_auto_departamentos").val();
        this.cambiar_provincias(id_departamento);
      },
      "change #clasificado_auto_departamentos":function(){
        var id_localidad = this.$("#clasificado_auto_localidades").val();
        this.cambiar_departamentos(id_localidad);
      },

      "click .guardar": "guardar",            
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
      this.options = options;

      var obj = this.model.toJSON();
      obj.edicion = (this.options.permiso > 1);
      obj.id = self.model.id;
      var edicion = false;
      $(this.el).html(this.template(obj));

      this.cargar_clientes();

      if (control.check("marcas_vehiculos")>0) {
        this.cargar_marcas_vehiculos(this.model.get("id_marca"));
      }

      if (this.$("#clasificado_auto_valido_hasta").length > 0) {
        var valido_hasta = this.model.get("valido_hasta");
        createdatepicker($(this.el).find("#clasificado_auto_valido_hasta"),valido_hasta);
      }

      this.cambiar_paises(this.model.get("id_provincia"));
      if (this.model.get("id_localidad") != 0) {
        this.cambiar_provincias(this.model.get("id_departamento"));
      }
      this.$("#clasificado_auto_paises").select2({});
      this.$("#clasificado_auto_provincias").select2({});      
      
      // Cuando cambian las imagens, renderizamos la tabla
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      
      $(this.el).find("#images_tabla").sortable();
    },

    cambiar_paises: function(id_provincia) {
      var id_pais = this.$("#clasificado_auto_paises").val();
      this.$("#clasificado_auto_provincias").empty();
      for(var i=0;i< window.provincias.length;i++) { 
        var p = provincias[i];
        if (p.id_pais == id_pais) {
          var s = '<option data-id_pais="'+p.id_pais+'" '+((id_provincia == p.id)?"selected":"")+' value="'+p.id+'">'+p.nombre+'</option>';
          this.$("#clasificado_auto_provincias").append(s);
        }
      }
      this.$("#clasificado_auto_provincias").val(id_provincia);
      crear_select2("clasificado_auto_provincias");
      this.$("#clasificado_auto_provincias").trigger("change");
    },
    cambiar_provincias: function(id_departamento){
      var self = this;
      var id_provincia = this.$("#clasificado_auto_provincias").val();
      this.$("#clasificado_auto_departamentos").val(id_departamento);
      new app.mixins.Select({
        modelClass: app.models.ComDepartamento,
        url: "com_departamentos/function/get_select/?id_provincia="+id_provincia,
        //firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#clasificado_auto_departamentos",
        selected: self.model.get("id_departamento"),
        onComplete:function(c) {
          crear_select2("clasificado_auto_departamentos");
          self.$("#clasificado_auto_departamentos").trigger("change");
        }
      });
    },
    cambiar_departamentos: function(id_localidad){
      var self = this;
      var id_departamento = this.$("#clasificado_auto_departamentos").val();
      this.$("#clasificado_auto_localidades").val(id_localidad);
      new app.mixins.Select({
        modelClass: app.models.Localidad,
        url: "localidades/function/get_select/?id_departamento="+id_departamento,
        //firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#clasificado_auto_localidades",
        selected: self.model.get("id_localidad"),
        onComplete:function(c) {
          crear_select2("clasificado_auto_localidades");
          self.$("#clasificado_auto_localidades").trigger("change");
        }
      });
    },

        
    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      for(var i=0;i<images.length;i++) {
        var path = images[i];
        var pth = path+"?t="+parseInt(Math.random()*100000);
        var li = "";
        li+="<li class='list-group-item'>";
        li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
        li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
        li+=" <span class='filename dn'>"+path+"</span>";
        li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
        li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
        li+="</li>";
        this.$("#images_tabla").append(li);
      }
    },

    cargar_marcas_vehiculos: function(id_marca) {
      var self = this;
      id_marca = (id_marca || 0);
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.MarcaVehiculo,
        url: "marcas_vehiculos/",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        render: "#clasificado_auto_marcas_vehiculos",
        selected: id_marca,
        onComplete:function(c) {
          crear_select2("clasificado_auto_marcas_vehiculos");
        }                    
      });
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
        render: "#clasificado_auto_clientes",
        selected: self.model.get("id_cliente"),
        onComplete:function(c) {
          crear_select2("clasificado_auto_clientes");
        }                    
      });
    },
            
    validar: function() {
      try {
        var self = this;
        this.model.set({
          "moneda":$(self.el).find("#clasificado_auto_monedas").val(),
          "id_tipo":$(self.el).find("#clasificado_auto_tipos_vehiculo").val(),
          "tipo":$(self.el).find("#clasificado_auto_tipos_vehiculo option:selected").text(),
          "precio_final":$(self.el).find("#clasificado_auto_precio_final").val(),
          "id_cliente":$(self.el).find("#clasificado_auto_clientes").val(),
          "valido_hasta":((self.$("#clasificado_auto_valido_hasta").length > 0) ? self.$("#clasificado_auto_valido_hasta").val() : ""),
          "nuevo":((self.$("#clasificado_auto_nuevo").length > 0) ? self.$("#clasificado_auto_nuevo").val() : 0),

          "id_localidad": (self.$("#clasificado_auto_localidades").length > 0) ? ($(self.el).find("#clasificado_auto_localidades").val() == null ? 0 : $(self.el).find("#clasificado_auto_localidades").val()) : 0,
          "id_departamento": (self.$("#clasificado_auto_departamentos").length > 0) ? ($(self.el).find("#clasificado_auto_departamentos").val() == null ? 0 : $(self.el).find("#clasificado_auto_departamentos").val()) : 0,
          "id_provincia": (self.$("#clasificado_auto_provincias").length > 0) ? ($(self.el).find("#clasificado_auto_provincias").val() == null ? 0 : $(self.el).find("#clasificado_auto_provincias").val()) : 0,
          "id_pais": (self.$("#clasificado_auto_paises").length > 0) ? ($(self.el).find("#clasificado_auto_paises").val() == null ? 0 : $(self.el).find("#clasificado_auto_paises").val()) : 0,
        });

        if ($(self.el).find("#clasificado_auto_marcas_vehiculos").length>0) {
          this.model.set({
            "marca":$(self.el).find("#clasificado_auto_marcas_vehiculos option:selected").text(),
            "id_marca":$(self.el).find("#clasificado_auto_marcas_vehiculos").val(),
          })
        }
                
        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
        
        if (images.length > 0) {
          var path = images[0];
          self.model.set({
            "path":path
          });
        }        
                
        var cktext = CKEDITOR.instances['clasificado_auto_texto'].getData();
        self.model.set({"texto":cktext});

        if ($("#clasificado_auto_texto_privado").length > 0) {
          self.model.set({
            "texto_privado":$("#clasificado_auto_texto_privado").val()
          });
        }
                
        // Coordenadas
        if (self.marker != undefined) {
          var pos = self.marker.getPosition();
          var zoom = self.map.getZoom();
          this.model.set({
            "latitud":(isNaN(pos.lat())) ? 0 : pos.lat(),
            "longitud":(isNaN(pos.lng())) ? 0 : pos.lng(),
            "zoom":zoom,
          });
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
              location.href="app/#clasificados_autos";
            }
          }
        });
      }      
    },
      
  });
})(app);
