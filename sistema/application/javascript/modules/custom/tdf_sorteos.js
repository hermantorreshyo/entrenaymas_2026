// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TdfSorteo = Backbone.Model.extend({
    urlRoot: "tdf_sorteos",
    defaults: {
      // Atributos que no se persisten directamente
      images: [],
      clientes: [],
      tipo: "",
      
      titulo: "",
      id_tipo:0,
      codigo: "",
      activo: 1,
      video: "",
      nuevo: 0,
      destacado: 0,
      texto: "",
      fecha_desde: "",
      fecha_hasta: "",
      path: "",
      path_fondo: "",
      maximo: 10000,
      
      marca: "",
      modelo: "",
      motor: "",
      kms: "",
      anio: "",
      id_ganador: 0,
      texto_ganador: "",
      path_ganador: "",
      
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
    },
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.TdfSorteos = paginator.requestPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'fecha_creacion',
      order: 'desc',
    },

    paginator_core: {
      url: function() {
        var texto = (isEmpty(this.meta("texto")) ? 0 : this.meta("texto"));
        var s = "tdf_sorteos/function/ver";
        s=s+"/"+texto;
        return s;
      }
    }
    
  });

})( app.collections, app.models.TdfSorteo, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.TdfSorteosTableView = app.mixins.View.extend({

    template: _.template($("#tdf_sorteos_resultados_template").html()),
        
    myEvents: {
      "change #tdf_sorteos_buscar":"buscar",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "keydown #tdf_sorteos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#tdf_sorteos_texto").focus(); }
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
      datos.filter = this.$("#tdf_sorteos_buscar").val();
      this.collection.server_api = datos;
      this.collection.pager();            
    },
    
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.TdfSorteosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"tdf_sorteos"
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
      window.open("tdf_sorteos/function/exportar_csv/","_blank");
    },
        
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.TdfSorteosItemResultados = app.mixins.View.extend({
    template: _.template($("#tdf_sorteos_item_resultados_template").html()),
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
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"custom_tdf_sorteos",
          "url":"tdf_sorteos/function/change_property/",
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
            "url":"tdf_sorteos/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              var d = self.model.clone();
              d.set("id",r.id);
              tdf_sorteos.add(d);
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
        window.open("tdf_sorteos/function/compartir/"+this.model.id+"/","_blank","height=250,width=450,location=no,resizable=no,scrollbars=no,titlebar=no,menubar=no,top=200,left=200");
        return false;
      },            
    },
    seleccionar: function() {
      var self = this;
      location.href="app/#tdf_sorteo/"+self.model.id;
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

  app.views.TdfSorteoEditView = app.mixins.View.extend({
    template: _.template($("#tdf_sorteo_template").html()),
    myEvents: {

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "tdf_sorteos/function/upload_images/",
          "view": self,
        });
      },

      "click .guardar": "guardar",            
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

      var fecha_desde = this.model.get("fecha_desde");
      if (isEmpty(fecha_desde)) fecha_desde = moment().startOf('month').toDate();
      createtimepicker($(this.el).find("#tdf_sorteo_fecha_desde"),fecha_desde);
      
      var fecha_hasta = this.model.get("fecha_hasta");
      if (isEmpty(fecha_hasta)) fecha_hasta = moment().endOf('month').toDate();
      createtimepicker($(this.el).find("#tdf_sorteo_fecha_hasta"),fecha_hasta);

      if (control.check("marcas_vehiculos")>0) {
        this.cargar_marcas_vehiculos(this.model.get("id_marca"));
      }
      
      // Cuando cambian las imagens, renderizamos la tabla
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      
      $(this.el).find("#images_tabla").sortable();
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
        render: "#tdf_sorteo_marcas_vehiculos",
        selected: id_marca,
        onComplete:function(c) {
          crear_select2("tdf_sorteo_marcas_vehiculos");
        }                    
      });
    },

    agregar_marca_vehiculo: function() {
      var id_marca = $("#tdf_sorteo_marcas_vehiculos").val();
      if (id_marca == 0) {
        alert("Por favor seleccione una marca");
        $("#tdf_sorteo_marcas_vehiculos").focus();
        return;
      }
      var marca_vehiculo = $("#tdf_sorteo_marcas_vehiculos option:selected").text();

      var codigo = $("#marca_vehiculo_codigo").val();
      var tr = "<tr data-id='"+id_marca+"'>";
      tr+="<td>"+marca_vehiculo+"</td>";
      tr+="<td>"+codigo+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_marca_vehiculo'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_marca_vehiculo text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#marcas_vehiculos_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#marca_vehiculo_codigo").val("");
      $("#tdf_sorteo_marcas_vehiculos").focus();
    },

    editar_marca_vehiculo: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#tdf_sorteo_marcas_vehiculos").val($(this.item).data("id")).trigger("change");
      $("#marca_vehiculo_codigo").val($(this.item).find("td:eq(1)").text());
    },
        
    validar: function() {
      try {
        var self = this;
        this.model.set({
          "id_tipo":$(self.el).find("#tdf_sorteo_tipos_vehiculo").val(),
          "tipo":$(self.el).find("#tdf_sorteo_tipos_vehiculo option:selected").text(),
          "fecha_desde":$(self.el).find("#tdf_sorteo_fecha_desde").val(),
          "fecha_hasta":$(self.el).find("#tdf_sorteo_fecha_hasta").val(),
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
          "path_fondo": ((self.$("#hidden_path_fondo").length > 0) ? self.$("#hidden_path_fondo").val() : ""),
          "path_ganador": ((self.$("#hidden_path_ganador").length > 0) ? self.$("#hidden_path_ganador").val() : ""),
          "id_ganador": ((self.$("input[name=id_ganador]:checked").length > 0) ? self.$("input[name=id_ganador]:checked").val() : 0),
        });

        if ($(self.el).find("#tdf_sorteo_marcas_vehiculos").length>0) {
          this.model.set({
            "marca":$(self.el).find("#tdf_sorteo_marcas_vehiculos option:selected").text(),
            "id_marca":$(self.el).find("#tdf_sorteo_marcas_vehiculos").val(),
          })
        }
                
        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
                        
        var cktext = CKEDITOR.instances['tdf_sorteo_texto'].getData();
        self.model.set({"texto":cktext});

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
              location.href="app/#tdf_sorteos";
            }
          }
        });
      }      
    },
      
  });
})(app);
