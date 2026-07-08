(function ( app ) {

  app.views.StockPorSucursalTableView = app.mixins.View.extend({

    template: _.template($("#stock_por_sucursal_resultados_template").html()),
          
    myEvents: {
      "click .exportar":"exportar",
      "change #stock_por_sucursal_buscar":"buscar",
      "click #stock_por_sucursal_buscar_avanzada_btn":"buscar",
      "change #stock_por_sucursal_con_descuento":"buscar",
      "change #stock_por_sucursal_buscar_activo":"buscar",
      "change #stock_por_sucursal_buscar_imagen":"buscar",
      "change #stock_por_sucursal_buscar_destacado":"buscar",
      "keydown #stock_por_sucursal_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#stock_por_sucursal_texto").focus(); }
      },
    },
      
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.stock_por_sucursal_filter = (typeof window.stock_por_sucursal_filter != "undefined") ? window.stock_por_sucursal_filter : "";
      window.stock_por_sucursal_id_marca = (typeof window.stock_por_sucursal_id_marca != "undefined") ? window.stock_por_sucursal_id_marca : 0;
      window.stock_por_sucursal_id_departamento = (typeof window.stock_por_sucursal_id_departamento != "undefined") ? window.stock_por_sucursal_id_departamento : 0;
      window.stock_por_sucursal_id_rubro = (typeof window.stock_por_sucursal_id_rubro != "undefined") ? window.stock_por_sucursal_id_rubro : 0;
      window.stock_por_sucursal_id_proveedor = (typeof window.stock_por_sucursal_id_proveedor != "undefined") ? window.stock_por_sucursal_id_proveedor : 0;
      window.stock_por_sucursal_fecha = (typeof window.stock_por_sucursal_fecha != "undefined") ? window.stock_por_sucursal_fecha : "";
      window.stock_por_sucursal_filtro = (typeof window.stock_por_sucursal_filtro != "undefined") ? window.stock_por_sucursal_filtro : "";
      window.stock_por_sucursal_page = (typeof window.stock_por_sucursal_page != "undefined") ? window.stock_por_sucursal_page : 1;
      window.stock_por_sucursal_con_descuento = (typeof window.stock_por_sucursal_con_descuento != "undefined") ? window.stock_por_sucursal_con_descuento : -1;
      window.stock_por_sucursal_activo = (typeof window.stock_por_sucursal_activo != "undefined") ? window.stock_por_sucursal_activo : -1;
      window.stock_por_sucursal_imagen = (typeof window.stock_por_sucursal_imagen != "undefined") ? window.stock_por_sucursal_imagen : -1;
      window.stock_por_sucursal_destacado = (typeof window.stock_por_sucursal_destacado != "undefined") ? window.stock_por_sucursal_destacado : -1;
      window.stock_por_sucursal_filtro_stock = (typeof window.stock_por_sucursal_filtro_stock != "undefined") ? window.stock_por_sucursal_filtro_stock : "";
      window.stock_por_sucursal_marcados = new Array();
      this.permiso = this.options.permiso;
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
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker($(this.el).find("#stock_por_sucursal_fecha"),window.stock_por_sucursal_fecha);
      
      if (control.check("marcas")>0) {
        new app.mixins.Select({
          modelClass: app.models.Marca,
          url: "marcas/",
          render: "#stock_por_sucursal_buscar_marcas",
          firstOptions: ["<option value='0'>Marca</option>"],
          selected: window.stock_por_sucursal_id_marca,
          onComplete:function(c) {
            $("#stock_por_sucursal_buscar_marcas").select2({}).change(function(){
              window.stock_por_sucursal_page = 1;
              window.stock_por_sucursal_id_marca = $(this).val();
            });
          }
        });
      }    

      if (control.check("departamentos_comerciales")>0) {
        new app.mixins.Select({
          modelClass: app.models.DepartamentoComercial,
          url: "departamentos_comerciales/",
          render: "#stock_por_sucursal_buscar_departamentos_comerciales",
          firstOptions: ["<option value='0'>Departamento</option>"],
          selected: window.stock_por_sucursal_id_departamento,
          onComplete:function(c) {
            $("#stock_por_sucursal_buscar_departamentos_comerciales").select2({}).change(function(){
              window.stock_por_sucursal_page = 1;
              window.stock_por_sucursal_id_departamento = $(this).val();
            });
          }
        });
      }  

      if (control.check("proveedores")>0) {
        new app.mixins.Select({
          modelClass: app.models.Proveedor,
          url: "proveedores/",
          render: "#stock_por_sucursal_buscar_proveedores",
          firstOptions: ["<option value='0'>Proveedor</option>"],
          selected: window.stock_por_sucursal_id_proveedor,
          onComplete:function(c) {
            $("#stock_por_sucursal_buscar_proveedores").select2({}).change(function(){
              window.stock_por_sucursal_page = 1;
              window.stock_por_sucursal_id_proveedor = $(this).val();
            });
          }                
        });
      }

      return this;
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.stock_por_sucursal_filter != this.$("#stock_por_sucursal_buscar").val().trim()) {
        window.stock_por_sucursal_filter = this.$("#stock_por_sucursal_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#stock_por_sucursal_fecha").length > 0) {
        if (window.stock_por_sucursal_fecha != this.$("#stock_por_sucursal_fecha").val().trim()) {
          window.stock_por_sucursal_fecha = this.$("#stock_por_sucursal_fecha").val().trim();
          cambio_parametros = true;
        }
      }
      if (window.stock_por_sucursal_id_rubro != this.$("#stock_por_sucursal_buscar_categorias").val()) {
        window.stock_por_sucursal_id_rubro = this.$("#stock_por_sucursal_buscar_categorias").val();
        cambio_parametros = true;
      }
      if (this.$("#stock_por_sucursal_filtro_stock").length > 0) {
        if (window.stock_por_sucursal_filtro_stock != this.$("#stock_por_sucursal_filtro_stock").val()) {
          window.stock_por_sucursal_filtro_stock = this.$("#stock_por_sucursal_filtro_stock").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#stock_por_sucursal_con_descuento").length > 0) {
        if (window.stock_por_sucursal_con_descuento != this.$("#stock_por_sucursal_con_descuento").val()) {
          window.stock_por_sucursal_con_descuento = this.$("#stock_por_sucursal_con_descuento").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#stock_por_sucursal_buscar_activo").length > 0) {
        if (window.stock_por_sucursal_activo != this.$("#stock_por_sucursal_buscar_activo").val().trim()) {
          window.stock_por_sucursal_activo = this.$("#stock_por_sucursal_buscar_activo").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#stock_por_sucursal_buscar_imagen").length > 0) {
        if (window.stock_por_sucursal_imagen != this.$("#stock_por_sucursal_buscar_imagen").val().trim()) {
          window.stock_por_sucursal_imagen = this.$("#stock_por_sucursal_buscar_imagen").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#stock_por_sucursal_buscar_destacado").length > 0) {
        if (window.stock_por_sucursal_destacado != this.$("#stock_por_sucursal_buscar_destacado").val().trim()) {
          window.stock_por_sucursal_destacado = this.$("#stock_por_sucursal_buscar_destacado").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#stock_por_sucursal_buscar_filtro").length > 0) {
        if (window.stock_por_sucursal_filtro != this.$("#stock_por_sucursal_buscar_filtro").val().trim()) {
          window.stock_por_sucursal_filtro = this.$("#stock_por_sucursal_buscar_filtro").val().trim();
          cambio_parametros = true;
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.stock_por_sucursal_page = 1;
      var datos = {
        "texto":encodeURIComponent(window.stock_por_sucursal_filter),
        "fecha":encodeURIComponent(window.stock_por_sucursal_fecha),
        "id_marca":window.stock_por_sucursal_id_marca,
        "id_departamento":window.stock_por_sucursal_id_departamento,
        "id_rubro":window.stock_por_sucursal_id_rubro,
        "activo":window.stock_por_sucursal_activo,
        "imagen":window.stock_por_sucursal_imagen,
        "destacado":window.stock_por_sucursal_destacado,
        "id_proveedor":window.stock_por_sucursal_id_proveedor,
        "descuento":window.stock_por_sucursal_con_descuento,
        "id_sucursal":((ID_EMPRESA != 271 && typeof ID_SUCURSAL != undefined) ? ID_SUCURSAL : 0),
        "filtro_stock":window.stock_por_sucursal_filtro_stock,
        "buscar_stock":1,
      };

      if (SOLO_USUARIO == 1 && ID_EMPRESA != 224) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.stock_por_sucursal_page);
    },
        
    addAll : function () {
      window.stock_por_sucursal_page = this.pagination.getPage();
      this.$("#stock_por_sucursal_tabla tbody").empty();
      if (ID_PROYECTO != 1) {
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
      } else {
        this.$(".seccion_vacia").hide();
        this.$(".seccion_llena").show();
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.StockPorSucursalItemResultados({
        model: item,
        collection: self.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },

    exportar: function() {
      var self = this;
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) {
          if (t.indexOf("-")) {
            t = t.substr(t.indexOf("-")+2);
          }
          header.push(t);
        }
      });
      // Acomodamos los datos
      var array = new Array();
      this.$(".table tbody tr").each(function(i,e){
        var obj = {};
        $(e).find("td").each(function(ii,ee){
          var s = $(ee).text().trim();
          s = s.replaceAll(",",".");
          s = s.replaceAll("Sin Stock","0");
          obj["s"+ii] = s;
        });
        array.push(obj);
      })
      this.exportar_excel({
        "filename":"stock",
        "title":"Stock por sucursales",
        "data":array,
        "header":header,
      });
    },
    
  });

})(app);


// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.StockPorSucursalItemResultados = app.mixins.View.extend({
        
    template: _.template($("#stock_por_sucursal_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.collection = this.options.collection;
      this.render();
    },
    render: function() {
      var self = this;
      var obj = { 
        seleccionar: this.habilitar_seleccion,
        permiso: control.check("stock_por_sucursal"),
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      $('[data-toggle="tooltip"]').tooltip();
      return this;
    },

  });
})(app);