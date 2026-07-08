// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Articulo = Backbone.Model.extend({
    urlRoot: "articulos",
    defaults: {
      // Atributos que no se persisten directamente
      marca:"",
      usuarios: [],
      proveedores: [],
      clientes: [],
      images: [],
      images_meli: [],
      relacionados: [], // Productos relacionados
      rubros_relacionados: [], // Categorias relacionadas
      etiquetas: [],
      variantes: [],
      ingredientes: [],
      marcas_vehiculos: [],
      precios_sucursales: [],
      stock_almacenes: [],
      atributos: [], // FICHA TECNICA DE ML
      componentes: [],
      
      moneda: ((ID_EMPRESA == 227 || IDIOMA == "en")?"2":"1"),
      codigo: "",
      nplu: "",
      codigo_barra: "",
      nombre: "",
      descripcion: "",
      id_rubro: 0,
      fecha_mov: "",
      fecha_ingreso: "",
      id_usuario: ID_USUARIO,
      id_marca: 0,
      id_departamento: 0,
      id_tipo_alicuota_iva: (IDIOMA == "en"?3:5),
      porc_iva: (IDIOMA == "en"?0:21), // Calculado de tipo_alicuota_iva
      costo_iva: 0,
      costo_neto: 0,
      costo_final: 0,

      // En PYMVAR: 
      //  0 = Producto
      //  1 = Servicio
      // En SHOPVAR: 
      //  0 = Habilitado para comprar
      //  1 = Habilitado para comprar y consultar
      //  2 = Solo consulta
      tipo: 0, 

      costo_neto_inicial: 0,
      dto_prov: 0,
      
      porc_ganancia: 0,
      ganancia: 0,
      precio_neto: 0,
      precio_final: 0,
      porc_bonif: 0,
      precio_final_dto: 0,

      porc_ganancia_2: 0,
      ganancia_2: 0,
      precio_neto_2: 0,
      precio_final_2: 0,
      porc_bonif_2: 0,
      precio_final_dto_2: 0,

      porc_ganancia_3: 0,
      ganancia_3: 0,
      precio_neto_3: 0,
      precio_final_3: 0,
      porc_bonif_3: 0,
      precio_final_dto_3: 0,

      porc_ganancia_4: 0,
      ganancia_4: 0,
      precio_neto_4: 0,
      precio_final_4: 0,
      porc_bonif_4: 0,
      precio_final_dto_4: 0,

      porc_ganancia_5: 0,
      ganancia_5: 0,
      precio_neto_5: 0,
      precio_final_5: 0,
      porc_bonif_5: 0,
      precio_final_dto_5: 0,

      porc_ganancia_6: 0,
      ganancia_6: 0,
      precio_neto_6: 0,
      precio_final_6: 0,
      porc_bonif_6: 0,
      precio_final_dto_6: 0,

      percep_viajes: 0, // 0/1 indicando si aplica percepcion 5% viajes al exterior
      
      lista_precios: ((typeof DOMINIO != undefined && DOMINIO != "") ? ((ID_EMPRESA == 342) ? 1 : 2) : 1),
      eliminado: 0,
      nuevo: 0,
      destacado: 0,
      fecha_eliminado: '',
      uxb: 0,
      unidad: '',
      usa_stock: 1,
      stock: 0,
      no_totalizar_reparto: 0,
      codigo_barra_bulto: "",
      caracteristicas: "",
      texto: "",
      breve: "",
      
      ancho: 0,
      alto: 0,
      profundidad: 0,
      peso: 0,
      coordinar_envio: 0,
      fragil: 0,
      
      seo_title: "",
      seo_keywords: "",
      seo_description: "",
      path: "",
      maximo_disponible: 0,
      id_promocion: 0,
      
      relacionados_tipo: "U", // U = Ultimos - A = Aleatorio
      relacionados_cantidad: 3, // Cantidad de elementos por categoria que se muestran

      custom_1: "", // 868 = costo_final central
      custom_2: "",
      custom_3: "",
      custom_4: "",
      custom_5: "", // Pertenece a la canasta basica | Compartido con LPC
      custom_6: "", // Nombre del proveedor
      custom_7: "", // Ancho neumatico
      custom_8: "", // Alto neumatico
      custom_9: "", // Perfil neumatico
      custom_10: "", // Codigo de proveedor

      porc_costo_1: 0,
      costo_1: 0,
      porc_costo_2: 0,
      costo_2: 0,
      porc_costo_3: 0,
      costo_3: 0,
      porc_costo_4: 0,
      costo_4: 0,
      porc_costo_5: 0,
      costo_5: 0,
      porc_costo_6: 0,
      costo_6: 0,

    },
  });
    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Articulos = paginator.requestPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: ((typeof window.tabla_articulos != "undefined") ? window.tabla_articulos.cant_items : 10),
      order_by: 'codigo',
      order: 'asc',
    },

    paginator_core: {
      url: "articulos/function/ver/",
    },
    
  });

})( app.collections, app.models.Articulo, Backbone.Paginator);



(function (collections, model, paginator) {

  collections.ArticulosMin = paginator.clientPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: (ID_EMPRESA == 342 ? 100:10),
      order_by: 'codigo',
      order: 'asc',
    },

    paginator_core: {
      url: "articulos/function/ver/1/?activo=1",
    },
      
  });

})( app.collections, app.models.Articulo, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ArticulosTableView = app.mixins.View.extend({

    template: _.template($("#articulos_resultados_template").html()),
          
    myEvents: {
      "change #articulos_buscar":"buscar",
      "click #articulos_buscar_avanzada_btn":"buscar",
      "click .exportar_precios": "exportar_precios",
      "click .enviar": "enviar",
      "click .cambiar_imagen":"cambiar_imagen",
      "click .compartir_meli":"compartir_meli",
      "click .meli_pausar_multiple":"meli_pausar_multiple",
      "click .meli_activar_multiple":"meli_activar_multiple",
      "click .exportar": "exportar",
      "click .importar_excel": "importar_excel",
      "click .importar_imagenes": "importar_imagenes",
      "click .importar_csv": "importar",
      "click .importar_vulca": "importar_vulca",
      "click .importar_center": "importar_center",
      "click .exportar_csv": "exportar_csv",
      "click .precios_maximos":"precios_maximos",
      "click .imprimir":"imprimir",
      "click .imprimir_etiquetas":"imprimir_etiquetas",
      "click .cambiar_fecha":"cambiar_fecha",
      "click .cambiar_rubro":"cambiar_rubro",
      "click .cambiar_marca":"cambiar_marca",
      "click .cambiar_etiqueta":"cambiar_etiqueta",
      "click .cambiar_moneda":"cambiar_moneda",
      "click .cambiar_oferta":"cambiar_oferta",
      "click .ajuste_masivo_stock":"ajuste_masivo_stock",
      "click .ajuste_masivo_canasta_basica":"ajuste_masivo_canasta_basica",
      "click .editar_masivo_proveedor":"editar_masivo_proveedor",
      "click .eliminar_lote":"eliminar_lote",
      "change #articulos_con_descuento":"buscar",
      "change #articulos_buscar_activo":"buscar",
      "change #articulos_buscar_imagen":"buscar",
      "change #articulos_buscar_custom_5":"buscar",
      "change #articulos_buscar_destacado":"buscar",
      "change #articulos_buscar_etiquetas":"buscar",
      "keydown #articulos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#articulos_texto").focus(); }
      },
      // Para configurar las columnas de la tabla
      "click .configurar_tabla":function() {
        var p = new app.views.ConfiguracionTablaView({
          titulo: "Articulos",
          tabla: window.tabla_articulos,
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });
      },
      "click .notificar":function() {
        $.ajax({
          "url":"articulos/function/notificar/",
          "dataType":"json",
        });
      }
    },
      
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.articulos_filter = (typeof window.articulos_filter != "undefined") ? window.articulos_filter : "";
      window.articulos_id_marca = (typeof window.articulos_id_marca != "undefined") ? window.articulos_id_marca : 0;
      window.articulos_id_departamento = (typeof window.articulos_id_departamento != "undefined") ? window.articulos_id_departamento : 0;
      window.articulos_id_rubro = (typeof window.articulos_id_rubro != "undefined") ? window.articulos_id_rubro : 0;
      window.articulos_id_proveedor = (typeof window.articulos_id_proveedor != "undefined") ? window.articulos_id_proveedor : 0;
      window.articulos_id_etiqueta = (typeof window.articulos_id_etiqueta != "undefined") ? window.articulos_id_etiqueta : 0;
      window.articulos_custom_5 = (typeof window.articulos_custom_5 != "undefined") ? window.articulos_custom_5 : "";
      window.articulos_codigo_proveedor = (typeof window.articulos_codigo_proveedor != "undefined") ? window.articulos_codigo_proveedor : "";
      window.articulos_fecha = (typeof window.articulos_fecha != "undefined") ? window.articulos_fecha : "";
      window.articulos_fecha_tipo = (typeof window.articulos_fecha_tipo != "undefined") ? window.articulos_fecha_tipo : "";
      window.articulos_filtro = (typeof window.articulos_filtro != "undefined") ? window.articulos_filtro : "";
      window.articulos_buscar_mercadolibre = (typeof window.articulos_buscar_mercadolibre != "undefined") ? window.articulos_buscar_mercadolibre : "";
      window.articulos_page = (typeof window.articulos_page != "undefined") ? window.articulos_page : 1;
      window.articulos_con_descuento = (typeof window.articulos_con_descuento != "undefined") ? window.articulos_con_descuento : -1;
      window.articulos_activo = (typeof window.articulos_activo != "undefined") ? window.articulos_activo : -1;
      window.articulos_imagen = (typeof window.articulos_imagen != "undefined") ? window.articulos_imagen : -1;
      window.articulos_destacado = (typeof window.articulos_destacado != "undefined") ? window.articulos_destacado : -1;
      window.articulos_filtro_stock = (typeof window.articulos_filtro_stock != "undefined") ? window.articulos_filtro_stock : "";
      window.articulos_marcados = new Array();

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

      createdatepicker($(this.el).find("#articulos_fecha"),window.articulos_fecha);
      
      if (control.check("marcas")>0) {
        new app.mixins.Select({
          modelClass: app.models.Marca,
          url: "marcas/",
          render: "#articulos_buscar_marcas",
          firstOptions: ["<option value='0'>"+((IDIOMA == "en")?"Brand":"Marca")+"</option>"],
          selected: window.articulos_id_marca,
          onComplete:function(c) {
            $("#articulos_buscar_marcas").select2({}).change(function(){
              window.articulos_page = 1;
              window.articulos_id_marca = $(this).val();
            });
          }
        });
      }    

      if (control.check("departamentos_comerciales")>0) {
        new app.mixins.Select({
          modelClass: app.models.DepartamentoComercial,
          url: "departamentos_comerciales/",
          render: "#articulos_buscar_departamentos_comerciales",
          firstOptions: ["<option value='0'>Departamento</option>"],
          selected: window.articulos_id_departamento,
          onComplete:function(c) {
            $("#articulos_buscar_departamentos_comerciales").select2({}).change(function(){
              window.articulos_page = 1;
              window.articulos_id_departamento = $(this).val();
            });
          }
        });
      }  

      this.$("#articulos_buscar_categorias").select2();
      /*
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/",
        render: "#articulos_buscar_categorias",
        firstOptions: ["<option value='0'>Categoria</option>"],
        selected: window.articulos_id_rubro,
        onComplete:function(c) {
          $("#articulos_buscar_categorias").select2({}).change(function(){
            window.articulos_page = 1;
            window.articulos_id_rubro = $(this).val();
          });
        }
      });
      */

      if (control.check("proveedores")>0) {
        new app.mixins.Select({
          modelClass: app.models.Proveedor,
          url: "proveedores/",
          render: "#articulos_buscar_proveedores",
          firstOptions: ["<option value='0'>Proveedor</option>"],
          selected: window.articulos_id_proveedor,
          onComplete:function(c) {
            $("#articulos_buscar_proveedores").select2({}).change(function(){
              window.articulos_page = 1;
              window.articulos_id_proveedor = $(this).val();
            });
          }                
        });
      }

      return this;
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.articulos_filter != this.$("#articulos_buscar").val().trim()) {
        window.articulos_filter = this.$("#articulos_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#articulos_fecha").length > 0) {
        if (window.articulos_fecha != this.$("#articulos_fecha").val().trim()) {
          window.articulos_fecha = this.$("#articulos_fecha").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_fecha_tipo").length > 0) {
        if (window.articulos_fecha_tipo != this.$("#articulos_fecha_tipo").val().trim()) {
          window.articulos_fecha_tipo = this.$("#articulos_fecha_tipo").val().trim();
          cambio_parametros = true;
        }
      }
      if (window.articulos_id_rubro != this.$("#articulos_buscar_categorias").val()) {
        window.articulos_id_rubro = this.$("#articulos_buscar_categorias").val();
        cambio_parametros = true;
      }
      if (this.$("#articulos_filtro_stock").length > 0) {
        if (window.articulos_filtro_stock != this.$("#articulos_filtro_stock").val()) {
          window.articulos_filtro_stock = this.$("#articulos_filtro_stock").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_mercadolibre").length > 0) {
        if (window.articulos_buscar_mercadolibre != this.$("#articulos_buscar_mercadolibre").val()) {
          window.articulos_buscar_mercadolibre = this.$("#articulos_buscar_mercadolibre").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_con_descuento").length > 0) {
        if (window.articulos_con_descuento != this.$("#articulos_con_descuento").val()) {
          window.articulos_con_descuento = this.$("#articulos_con_descuento").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_activo").length > 0) {
        if (window.articulos_activo != this.$("#articulos_buscar_activo").val().trim()) {
          window.articulos_activo = this.$("#articulos_buscar_activo").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_imagen").length > 0) {
        if (window.articulos_imagen != this.$("#articulos_buscar_imagen").val().trim()) {
          window.articulos_imagen = this.$("#articulos_buscar_imagen").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_destacado").length > 0) {
        if (window.articulos_destacado != this.$("#articulos_buscar_destacado").val().trim()) {
          window.articulos_destacado = this.$("#articulos_buscar_destacado").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_filtro").length > 0) {
        if (window.articulos_filtro != this.$("#articulos_buscar_filtro").val().trim()) {
          window.articulos_filtro = this.$("#articulos_buscar_filtro").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_custom_5").length > 0) {
        if (window.articulos_custom_5 != this.$("#articulos_buscar_custom_5").val()) {
          window.articulos_custom_5 = this.$("#articulos_buscar_custom_5").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_codigo_prov").length > 0) {
        if (window.articulos_codigo_proveedor != this.$("#articulos_buscar_codigo_prov").val()) {
          window.articulos_codigo_proveedor = this.$("#articulos_buscar_codigo_prov").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#articulos_buscar_etiquetas").length > 0) {
        if (window.articulos_id_etiqueta != this.$("#articulos_buscar_etiquetas").val()) {
          window.articulos_id_etiqueta = this.$("#articulos_buscar_etiquetas").val();
          cambio_parametros = true;
        }
      }

      // Si tenemos habilitado el campo stock_almacenes en la configuracion de la tabla
      var buscar_stock = 0;
      if (typeof window.tabla_articulos != "undefined") {
        for(var i=0; i<window.tabla_articulos.campos.length; i++) {
          var c = window.tabla_articulos.campos[i];
          if (c.campo == "stock_almacenes" && c.visible == 1) {
            buscar_stock = 1;
            break;
          }
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.articulos_page = 1;
      var datos = {
        "texto":encodeURIComponent(window.articulos_filter),
        "fecha":encodeURIComponent(window.articulos_fecha),
        "fecha_tipo":window.articulos_fecha_tipo,
        "id_marca":window.articulos_id_marca,
        "id_departamento":window.articulos_id_departamento,
        "id_rubro":window.articulos_id_rubro,
        "activo":window.articulos_activo,
        "imagen":window.articulos_imagen,
        "destacado":window.articulos_destacado,
        "mercadolibre":window.articulos_buscar_mercadolibre,
        "id_proveedor":window.articulos_id_proveedor,
        "id_etiqueta":window.articulos_id_etiqueta,
        "descuento":window.articulos_con_descuento,
        "id_sucursal":((ID_EMPRESA != 271 && typeof ID_SUCURSAL != undefined) ? ID_SUCURSAL : 0),
        "filtro_stock":window.articulos_filtro_stock,
        "buscar_stock":buscar_stock,
        "codigo_prov":window.articulos_codigo_proveedor,
        "custom_5":window.articulos_custom_5,
      };
      if (SOLO_USUARIO == 1 && ID_EMPRESA != 224) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.articulos_page);
    },
        
    addAll : function () {
      var self = this;
      window.articulos_page = this.pagination.getPage();
      this.$("#articulos_tabla tbody").empty();
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
      if (this.collection.length > 0) this.collection.each(function(item){
        self.addOne(item);
        // Si tiene variantes, las agregamos
        var variantes = item.get("variantes");
        for(var i=0;i<variantes.length;i++) {
          var v = variantes[i];
          var view = new app.views.ArticulosVariantesItemResultados({
            model: new app.models.AbstractModel(v),
            item: item,
            habilitar_seleccion: self.habilitar_seleccion,
          });
          self.$(".tbody").append(view.render().el);
        }
      });
      $('[data-toggle="tooltip"]').tooltip();
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ArticulosItemResultados({
        model: item,
        collection: self.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    exportar_precios: function() {
      var fecha = $("#articulos_fecha").val();
      if (isEmpty(fecha)) fecha = 0;
      else fecha = fecha.replace(/\//g,"-");
      window.open("articulos/function/exportar_mercader/"+fecha,"_blank");
    },

    imprimir_etiquetas: function() {
      var view = new app.views.ArticuloImprimirEtiquetasEditView({
        model: new app.models.AbstractModel({
          items: [],
        })
      });
      crearLightboxHTML({
        "html":view.el,
        "width":700,
        "height":400,
        "escapable":false,
      });
      $("#articulo_imprimir_etiquetas_codigo").focus();
    },

    imprimir: function(e) {
      var self = this;
      var i = $(e.currentTarget).data("tipo");
      window.lista_precios_seleccionadas = null;
      window.lista_precios_sucursal = null;
      window.lista_precios_con_ventas_desde = null;
      
      if (i == 1) {
        // IMPRIMIR PRECIOS CHICOS
        this.armar_form("articulos/function/imprimir_precios/");
      } else if (i == 2) {
        // IMPRIMIR PRECIOS GRANDES
        this.armar_form("articulos/function/imprimir_precios_grandes/");
      } else if (i == 3) {
        // IMPRIMIR OFERTAS
        this.armar_form("articulos/function/imprimir_ofertas/");
      } else if (i == 4) {
        // IMPRIMIR LISTADO DE PRECIOS
        var v = new app.views.ImprimirListadoArticulosView({
          model: new app.models.AbstractModel(),
          parent: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":400,
          "height":500,
        });
      } else if (i == 10) {
        // IMPRIMIR LISTADO DE PRECIOS POR PROVEEDOR
        var v = new app.views.ImprimirListadoArticulosPorProveedorView({
          model: new app.models.AbstractModel(),
          parent: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":400,
          "height":500,
        });
      } else if (i == 5) {
        // IMPRIMIR LISTADO DE PEDIDO
        this.armar_form("articulos/function/imprimir_stock/");
      } else if (i == 6) {
        // IMPRIMIR LISTADO DE PLUS
        this.armar_form("articulos/function/imprimir_plu/");
      } else if (i == 7) {
        // IMPRIMIR PRECIOS MEDIANOS
        this.armar_form("articulos/function/imprimir_precios_medianos/");
      } else if (i == 9) {
        // IMPRIMIR PRECIOS MEDIANOS SIN OFERTA
        this.armar_form("articulos/function/imprimir_precios_medianos_sin_oferta/");
      } else if (i == 8) {
        // IMPRIMIR COSTOS
        this.armar_form("articulos/function/imprimir_costos/");
      }
    },
    
    armar_form: function(url) {

      var self = this;
      var f = document.createElement("form");
      f.setAttribute('method',"post");
      f.setAttribute('action',url);
      f.setAttribute('target',"_blank");
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_proveedor");
      i.setAttribute('value',$("#articulos_buscar_proveedores").val());
      f.appendChild(i);
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_marca");
      i.setAttribute('value',$("#articulos_buscar_marcas").val());
      f.appendChild(i);

      if (control.check("departamentos_comerciales")>0) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"id_departamento");
        i.setAttribute('value',$("#articulos_buscar_departamentos_comerciales").val());
        f.appendChild(i);
      }

      if (window.lista_precios_seleccionadas != null) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"ver_listas");
        i.setAttribute('value',window.lista_precios_seleccionadas);
        f.appendChild(i);        
      }

      if (this.$("#articulos_filtro_stock").length > 0) {
        window.articulos_filtro_stock = this.$("#articulos_filtro_stock").val();
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"filtro_stock");
        i.setAttribute('value',window.articulos_filtro_stock);
        f.appendChild(i);
      }

      if (this.$("#articulos_buscar_activo").length > 0) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"activo");
        i.setAttribute('value',window.articulos_activo);
        f.appendChild(i);
      }      

      if (this.$("#articulos_buscar_custom_5").length > 0) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"custom_5");
        i.setAttribute('value',window.articulos_custom_5);
        f.appendChild(i);
      }      
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_rubro");
      i.setAttribute('value',$("#articulos_buscar_categorias").val());
      f.appendChild(i);
      
      var texto = $("#articulos_buscar").val();
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"texto");
      i.setAttribute('value',texto);
      f.appendChild(i);

      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"in_ids");
        i.setAttribute('value',window.articulos_marcados.join("-"));
        f.appendChild(i);
      }
      
      var id_sucursal = (window.lista_precios_sucursal != null) ? window.lista_precios_sucursal : ID_SUCURSAL;
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_sucursal");
      i.setAttribute('value',id_sucursal);
      f.appendChild(i);

      if (window.lista_precios_con_ventas_desde != null && !isEmpty(window.lista_precios_con_ventas_desde)) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"con_ventas_desde");
        i.setAttribute('value',window.lista_precios_con_ventas_desde);
        f.appendChild(i);        
      }
      
      var fecha = $("#articulos_fecha").val();
      if (isEmpty(fecha)) fecha = "";
      else fecha = fecha.replace(/\//g,"-");
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"fecha");
      i.setAttribute('value',fecha);
      f.appendChild(i);

      if (self.$("#articulos_fecha_tipo").length > 0) {
        var i = document.createElement("input");
        i.setAttribute('type',"hidden");
        i.setAttribute('name',"fecha_tipo");
        i.setAttribute('value',self.$("#articulos_fecha_tipo").val());
        f.appendChild(i);
      }

      $(f).css("display","none");
      document.body.appendChild(f);

      f.submit();      
    },
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"articulos"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        

    importar_excel: function() {
      app.views.importar = new app.views.Importar({
        "url":"articulos/function/importar_excel/",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        

    importar_imagenes: function() {
      app.views.importar = new app.views.Importar({
        "url":"articulos/function/importar_fotos_zip/",
        "titulo":"Importacion de imagenes",
        "texto":"Puede subir un archivo .zip con las fotos que desea importar al sistema",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        

    importar_vulca: function() {
      var view = new app.views.Importar({
        "table":"articulos",
        "url":"articulos/function/importar_vulca/"+ID_EMPRESA+"/1/",
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
      });
    },        

    importar_center: function() {
      var view = new app.views.Importar({
        "table":"articulos",
        "url":"articulos/function/importar_center/"+ID_EMPRESA+"/1/",
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
      });
    },        
    
    exportar: function(obj) {
      // Reemplazamos el ver por el exportar
      var url = this.collection.url;
      url = url.replace("/ver/","/exportar/");
      // Los parametros de orden se envian por GET
      url += "?order=nombre";//this.collection.paginator_ui.order+"&order_by="+this.collection.paginator_ui.order_by;
      if (!isEmpty(this.$("#articulos_buscar_categorias").val())) url+="&texto="+encodeURI(this.$("#articulos_buscar").val());
      if (this.$("#articulos_buscar_categorias").length > 0) url+="&id_rubro="+this.$("#articulos_buscar_categorias").val();
      if (this.$("#articulos_buscar_marcas").length > 0) url+="&id_marca="+this.$("#articulos_buscar_marcas").val();
      if (this.$("#articulos_buscar_proveedores").length > 0) url+="&id_proveedor="+this.$("#articulos_buscar_proveedores").val();
      if (this.$("#articulos_buscar_activo").length > 0) url+="&activo="+this.$("#articulos_buscar_activo").val();
      if (this.$("#articulos_buscar_departamentos_comerciales").length > 0) url+="&id_departamento="+this.$("#articulos_buscar_departamentos_comerciales").val();
      window.open(url,"_blank");
    },
    
    exportar_csv: function(obj) {
      window.open("articulos/function/exportar_csv/","_blank");
    },        
    
    cambiar_fecha: function() {
      app.views.articulosCambiarFecha = new app.views.ArticulosCambiarFecha();
      crearLightboxHTML({
        "html":app.views.articulosCambiarFecha.el,
        "width":300,
        "height":140,
      });
    },

    cambiar_rubro: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloRecategorizacionView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    cambiar_marca: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloCambiarMarcaView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    cambiar_oferta: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloCambiarOfertaView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    cambiar_etiqueta: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloEtiquetarView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    cambiar_moneda: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloCambiarMonedaView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    editar_masivo_proveedor: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloAgregarProveedorView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            window.articulos_marcados = new Array();
            self.buscar();
          }
        });
      }
    },    

    ajuste_masivo_stock: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        var view = new app.views.ArticuloAjusteMasivoStockView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":140,
          "callback":function() {
            self.buscar();
          }
        });
      }
    },

    ajuste_masivo_canasta_basica: function() {
      var self = this;
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        $.ajax({
          "timeout":0,
          "url":"articulos/function/ajuste_masivo_canasta_basica/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ids":window.articulos_marcados.join("-"),
          },
          "success":function() {
            self.buscar();
          },
        });
      }
    },

    cambiar_imagen: function() {
      var self = this;
      window.modelo_cambiar_imagenes = new app.models.AbstractModel({
        "images":[],
      });
      this.listenTo(window.modelo_cambiar_imagenes, 'change_table', self.cambiar_imagen_aceptar);
      this.open_multiple_upload({
        "model": window.modelo_cambiar_imagenes,
        "url": "articulos/function/upload_images/",
        "view": self,
      });
    },
    cambiar_imagen_aceptar: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var self = this;
        $.ajax({
          "url":"articulos/function/cambiar_imagenes_por_lote/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ids":window.articulos_marcados,
            "images":window.modelo_cambiar_imagenes.get("images")
          },
          "success":function(res) {
            location.reload();
            //self.buscar();
          }
        });
      }
    },
    eliminar_lote: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
          var self = this;
          $.ajax({
            "url":"articulos/function/eliminar_por_lote/",
            "dataType":"json",
            "type":"post",
            "data":{
              "ids":window.articulos_marcados,
            },
            "success":function(res) {
              self.buscar();
            }
          });
        }
      }
    },

    compartir_meli: function() {
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        if (isEmpty(ML_ACCESS_TOKEN)) {
          alert("La sincronizacion con MercadoLibre no esta habilitada. Puede hacerlo desde la configuracion avanzada.");
          location.href="app/#web_seo";
          return;
        }

        // El precio se toma de la lista correspondiente
        var precio_meli = this.model.get("precio_final_dto");
        if (ML_LISTA_BASE == 1) precio_meli = this.model.get("precio_final_dto_2");
        else if (ML_LISTA_BASE == 2) precio_meli = this.model.get("precio_final_dto_3");
        else if (ML_LISTA_BASE == 3) precio_meli = this.model.get("precio_final_dto_4");
        else if (ML_LISTA_BASE == 4) precio_meli = this.model.get("precio_final_dto_5");
        else if (ML_LISTA_BASE == 5) precio_meli = this.model.get("precio_final_dto_6");

        var view = new app.views.ArticuloMercadoLibreView({
          model: new app.models.AbstractModel({
            "titulo_meli":"",
            "precio_meli":precio_meli,
            "texto_meli":"",
            "atributos_meli":"",
            "images_meli":[],
          }),
          multiple: true,
        });
        crearLightboxHTML({
          "html":view.el,
          "width":900,
          "height":500,
          "escapable":false,
        });
      }
    },

    meli_pausar_multiple: function() {
      if (window.articulos_marcados.length == 0) return;
      var art_marcados = window.articulos_marcados.join(",");
      workspace.esperar("Pausando articulos...");
      $.ajax({
        "url":"articulos_meli/function/pausar_multiple/",
        "type":"post",
        "data":{
          "ids":art_marcados,
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) {
            location.reload();
          } else if (r.error == 1) {
            $(".modal:last").trigger('click');
            alert(r.mensaje);
          }
        },
      });
    },

    meli_activar_multiple: function() {
      if (window.articulos_marcados.length == 0) return;
      var art_marcados = window.articulos_marcados.join(",");
      workspace.esperar("Reactivando articulos...");
      $.ajax({
        "url":"articulos_meli/function/reactivar_multiple/",
        "type":"post",
        "data":{
          "ids":art_marcados,
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) {
            location.reload();
          } else if (r.error == 1) {
            $(".modal:last").trigger('click');
            alert(r.mensaje);
          }
        },
      });
    },

    precios_maximos: function() {
      var url = "articulos/function/comparar_precios_maximos/";
      window.open(url,"_blank");
    },

    enviar: function() {
      // TODO: Usar window.articulos_marcados
      var checks = this.$("#articulos_tabla .check-row:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var nombre = $(e).parents("tr").find(".nombre").text();
        links_adjuntos.push({
          "tipo": TIPO_ADJUNTO_ARTICULO,
          "id_objeto": id,
          "nombre": nombre,
        });
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Fichas de Productos",
      });
      workspace.nuevo_email(email);
    },
  
  });

})(app);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ArticulosBuscarTableView = app.mixins.View.extend({

    template: _.template($("#articulos_buscar_resultados_template").html()),
    myEvents: {
      "change #articulos_buscar":"buscar",
      "keydown #articulos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#articulos_buscar").focus(); }
      },
      "keydown #articulos_tabla tbody tr .radio":function(e) {
        // Page Up
        if (e.which == 33) { e.preventDefault(); this.collection.previousPage(); $("#articulos_buscar").focus(); }
        // Page Down
        if (e.which == 34) { e.preventDefault(); this.collection.nextPage(); $("#articulos_buscar").focus(); }
      },
      "keydown #articulos_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#articulos_tabla tbody tr .radio:first").focus(); }
      }
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      window.articulos_page = (typeof window.articulos_page != "undefined") ? window.articulos_page : 1;
      window.articulos_buscar_id_proveedor = (typeof window.articulos_buscar_id_proveedor != "undefined") ? window.articulos_buscar_id_proveedor : 0;
      window.articulos_buscar_activo = (typeof window.articulos_buscar_activo != "undefined") ? window.articulos_buscar_activo : -1;
      if (CACHE_ARTICULOS == 1) {
        this.collection.on('pager', this.addAll, this);  
        this.render();
      } else {
        this.collection.off('sync');
        this.collection.on('sync', this.addAll, this);
        this.render();
        this.buscar();
      }
    },

    render: function() {
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template());
      $(this.el).find(".pagination_container").html(this.pagination.el);
      if (CACHE_ARTICULOS == 1) {
        this.collection.pager();
      }
      return this;
    },

    buscar: function() {
      var filter = this.$("#articulos_buscar").val().trim();
      if (CACHE_ARTICULOS == 1) {
        this.collection.setFilter(["nombre"],filter);
      } else {
        var datos = {
          "texto":filter,
          "id_proveedor":window.articulos_buscar_id_proveedor,
          "activo":window.articulos_buscar_activo,
          "id_sucursal":(typeof this.options.id_sucursal != "undefined" ? this.options.id_sucursal : 0),
          "buscar_stock":(ID_EMPRESA == 342)?1:0,
        };
        if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
        this.collection.server_api = datos;
        this.collection.goTo(window.articulos_page);
      }
    },

    addAll : function () {
      this.$("#articulos_tabla tbody").empty();
      if (CACHE_ARTICULOS == 1) {
        var coleccion = this.collection.getFilterCollection();
        coleccion.each(this.addOne);
      } else {
        window.articulos_page = this.pagination.getPage();
        if (this.collection.length > 0) this.collection.each(this.addOne);
      }
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ArticulosItemResultados({
        model: item,
        collection: self.collection,
        habilitar_seleccion: true,
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
  });

})(app);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ArticulosMostrarPrecioView = app.mixins.View.extend({

    template: _.template($("#articulos_mostrar_precio_template").html()),
    myEvents: {
      "change #articulos_mostrar_precio_buscar":"buscar",
    },
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template());
      return this;
    },

    buscar: function() {
      var self = this;
      var filter = this.$("#articulos_mostrar_precio_buscar").val().trim();
      if ($.isNumeric(filter)) {
        filter = parseFloat(filter);
      }
      filter = encodeURIComponent(filter);
      $.ajax({
        "url":"articulos/function/get_by_codigo/"+filter,
        "dataType":"json",
        "type":"post",
        "data":{
          "id_sucursal":ID_SUCURSAL,
        },
        "success":function(result) {
          if (result.error == 1) {
            if (FACTURACION_TIPO == "pv") {
              self.$("#articulos_mostrar_precio_texto").html("El producto no se encuentra cargado.");
              self.$("#articulos_mostrar_precio_precio").html("");
            }
          } else {
            self.$("#articulos_mostrar_precio_texto").html(result.articulo.nombre);
            self.$("#articulos_mostrar_precio_precio").html("$ "+Number(result.articulo.precio_final_dto).toFixed(2));
          }
          $("#articulos_mostrar_precio_buscar").select();
        }
      });
    },
  });

})(app);


// -----------------------------------------
//   BUSQUEDA POR RUBROS (USADO EN RESTOVAR)
// -----------------------------------------
(function ( app ) {

  app.views.BuscarArticulosPorRubroView = app.mixins.View.extend({

    template: _.template($("#articulos_buscar_por_rubros_template").html()),
        
    myEvents: {
      "change #articulos_buscar":"buscar",
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .rubro":function(e) {
        $(".rubro").removeClass('active');
        $(e.currentTarget).addClass('active');
        this.id_rubro = $(e.currentTarget).data("id");
        this.buscar();
      },
      // Mas y menos de Input group
      "click .addon_minus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var min = $(el).attr("min");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (min != undefined && (valor-step < min)) valor = min;
        else valor = valor - step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .addon_plus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var max = $(el).attr("max");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (max != undefined && (valor+step > max)) valor = max;
        else valor = valor + step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .aceptar":function() {
        window.pedidos = this.pedidos;
        $('.modal:last').modal('hide');
      },
    },
    
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_rubro = 0;
      this.id_usuario = (typeof options.id_usuario != "undefined") ? options.id_usuario : 0;
      // Array que guarda el ID y la cantidad de los elementos pedidos
      this.pedidos = new Array();
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template({
        "id_usuario":self.id_usuario,
      }));
      // Seleccionamos la primera
      this.$("#articulos_rubros .rubro").first().trigger('click');
      return this;
    },

    buscar: function() {
      var self = this;
      var arts = articulos.filter(function(articulo){
        control_usuario = true;
        if (self.id_usuario != 0) control_usuario = (articulo.get("id_usuario") == self.id_usuario);
        return (articulo.get("id_rubro") == self.id_rubro && control_usuario);
      });
      this.collection = new app.collections.Articulos(arts);
      this.addAll();
    },
    
    addAll : function () {
      this.$("#articulos_listado").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.BuscarArticulosPorRubroItemView({
        model: item,
        collection: self.collection,
        pedidos: self.pedidos,
      });
      this.$("#articulos_listado").append(view.render().el);
    },
              
  });

})(app);



(function ( app ) {

  app.views.BuscarArticulosCartaCompletaView = app.mixins.View.extend({

    template: _.template($("#articulos_buscar_carta_completa_template").html()),
        
    myEvents: {
      "change #articulos_buscar":"buscar",
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      // Mas y menos de Input group
      "click .addon_minus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var min = $(el).attr("min");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (min != undefined && (valor-step < min)) valor = min;
        else valor = valor - step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .addon_plus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var max = $(el).attr("max");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (max != undefined && (valor+step > max)) valor = max;
        else valor = valor + step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .aceptar":function() {
        window.pedidos = this.pedidos;
        $('.modal:last').modal('hide');
      },
    },
    
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_rubro = 0;
      // Array que guarda el ID y la cantidad de los elementos pedidos
      this.pedidos = new Array();
      this.render();
      this.buscar();
    },

    render: function() {
      var self = this;
      self.articulos_rubros = new Array();
      _.each(rubros,function(rubro){
        var arts = articulos.filter(function(articulo){
          return (articulo.get("id_rubro") == rubro.id);
        });
        self.articulos_rubros.push({
          "id_rubro":rubro.id,
          "nombre":rubro.title,
          "articulos":arts,
        })
      });
      $(this.el).html(this.template());
      return this;
    },

    buscar: function() {
      var self = this;
      _.each(self.articulos_rubros,function(articulo){
        var that = self;
        var div = $("<div class='col'/>");
        $(div).append("<h2>"+articulo.nombre+"</h2>");
        var coleccion = new app.collections.Articulos(articulo.articulos);
        coleccion.each(function(item){
          var v = that.addOne(item);
          $(div).append(v.el);
        });
        self.$("#articulos_buscar_carta_completa_container").append(div);
      });
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.BuscarArticulosPorRubroItemView({
        model: item,
        pedidos: self.pedidos,
      });
      return view;
    },
              
  });

})(app);



(function ( app ) {

  app.views.BuscarArticulosPorRubroItemView = app.mixins.View.extend({

    template: _.template($("#articulos_buscar_por_rubros_item_template").html()),
    tagName:"li",
    className: "list-group-item",
    myEvents: {
      "click .expand-link-ing":function() {
        $(".expandable").hide();
        this.$(".expandable").slideToggle();
      },

      "click .radio_ingrediente":function(){
        var cantidad = this.$(".cantidad").val();
        if (cantidad == 0 || isEmpty(cantidad)) {
          this.$(".cantidad").val("1");
        }
        this.$(".cantidad").trigger("change");
      },

      "change .cantidad":function(e) {
        var self = this;
        var encontro = false;
        var cantidad = $(e.currentTarget).val();
        var descripcion = "";
        var adicional = 0;
        // Si se seleccionaron valores 
        self.$(".radio_ingrediente:checked").each(function(i,e){
          if ($(e).hasClass('valor_sel')) {
            var nombre = $(e).data("nombre");
            adicional += parseFloat($(e).data("adicional"));
            descripcion += ((!isEmpty(descripcion)) ? " - " : "") + nombre+": "+$(e).val();
          }
        });
        if (adicional > 0) {
          self.model.set({
            "precio_final_dto_2":adicional,
          });
        } else {
          self.model.set({
            "precio_final_dto_2":0,
          });
        }
        _.each(self.pedidos,function(p){
          if (p.id == self.model.id) {
            p.cantidad = cantidad;
            p.descripcion = descripcion;
            encontro = true;
          }
        });
        if (!encontro) {
          self.pedidos.push({
            "id":self.model.id,
            "cantidad":cantidad,
            "articulo":self.model,
            "descripcion":descripcion,
          });
        }
      }
    },

    initialize : function (options) {        
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.pedidos = options.pedidos;
      this.render();
    },

    render: function() {
      var cantidad = 0;
      var self = this;
      // Controlamos la cantidad que habiamos pedido para ese elemento
      _.each(this.pedidos,function(p){
        if (p.id == self.model.id) {
          cantidad = p.cantidad; return;
        }
      });
      var obj = { "id":this.model.id, "cantidad":cantidad };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})(app);



(function ( app ) {

app.views.ArticulosCambiarFecha = Backbone.View.extend({

  template: _.template($("#articulos_cambiar_fecha_template").html()),

  events: {
    "click .guardar": "guardar",
  },
  
  initialize: function() {
    _.bindAll(this);
    var self = this;
    this.render();
  },
  
  render: function() {
    var self = this;
    $(this.el).html(this.template());
    var fecha = new Date();
    $(this.el).find("#articulos_cambiar_fecha_actual").datepicker({
      "dateFormat":"dd/mm/yy",
      "currentText":"Hoy",
      "buttonImage": "/resources/images/datepicker.png",
      "buttonImageOnly": true,
      "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
      "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
      "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
      "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
      "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
      "nextText":"Proximo",
      "prevText":"Anterior",
      "defaultDate":fecha
    });
    var fecha = new Date();
    $(this.el).find("#articulos_cambiar_fecha_posterior").datepicker({
      "dateFormat":"dd/mm/yy",
      "currentText":"Hoy",
      "buttonImage": "/resources/images/datepicker.png",
      "buttonImageOnly": true,
      "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
      "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
      "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
      "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
      "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
      "nextText":"Proximo",
      "prevText":"Anterior",
      "defaultDate":fecha
    });
    
    var fecha_f = $.datepicker.formatDate("dd/mm/yy",fecha);
    $(this.el).find("#articulos_cambiar_fecha_posterior, #articulos_cambiar_fecha_actual").mask("99/99/9999");
    $(this.el).find("#articulos_cambiar_fecha_posterior, #articulos_cambiar_fecha_actual").val(fecha_f);
    return this;
  },
  
  guardar: function() {

    var fecha_posterior = $("#articulos_cambiar_fecha_posterior").val();
    var fecha_actual = $("#articulos_cambiar_fecha_actual").val();
    
    if (isEmpty(fecha_posterior)) {
      show("Por favor ingrese una fecha.");
      $("#articulos_cambiar_fecha_posterior").focus();
      return;
    } else {
      fecha_posterior = fecha_posterior.replace(/\//g,"-");
    }
    if (isEmpty(fecha_actual)) {
      show("Por favor ingrese una fecha.");
      $("#articulos_cambiar_fecha_actual").focus();
      return;
    } else {
      fecha_actual = fecha_actual.replace(/\//g,"-");
    }
    
    $.ajax({
      "url":"articulos/function/cambiar_fecha/"+fecha_actual+"/"+fecha_posterior,
      "dataType":"json",
      "success":function(r) {
        if (r.error == 0) {
          show(r.mensaje);
        } else {
          show("Ocurrio un error cuando se actualizaba la fecha.");
        }
        $(".modal").trigger("click");
      }
    });
  },
    
});

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ArticulosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#articulos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .meli_eliminar":"meli_eliminar",
      "click .meli_republicar":"meli_republicar",
      "click .meli_pausar":"meli_pausar",
      "click .meli_reactivar":"meli_reactivar",
      "click .meli_finalizar":"meli_finalizar",
      "click .enviar_ficha":"enviar_ficha",
      "click .compartir_meli":"compartir_meli",

      "click .modificar_stock":function(e){
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var id_sucursal = $(e.currentTarget).data("id_sucursal");
        var id_variante = $(e.currentTarget).data("id_variante");
        var cont = $(e.currentTarget).find(".inline-text-cont");
        var text = $(e.currentTarget).find(".inline-text");
        var stock_anterior = parseFloat($(text).val());
        $(cont).hide();
        $(text).one("focusout",function(){
          var stock = parseFloat($(text).val());
          if (isNaN(stock)) {
            alert("Por favor ingrese un numero");
            $(text).empty();
            $(text).hide();
            $(cont).show();
            return;
          }
          if (stock_anterior == stock) {
            $(text).hide();
            $(cont).show();
            return;            
          }
          $.ajax({
            "url":"stock/function/edicion_stock/",
            "dataType":"json",
            "type":"post",
            "data":{
              "id_articulo":self.model.id,
              "id_sucursal":id_sucursal,
              "id_variante":id_variante,
              "stock":stock
            },
            "success":function(r){
              app.views.articulosTableView.buscar();
            },
            "error":function(){
              $(cont).show();
              $(text).hide();              
            }
          });
        });
        $(text).show();
        $(text).select();
        /*
        var titulo = self.model.get("nombre");
        var v = new app.views.StockEditView({
          titulo: titulo,
          codigo_default: self.model.get("codigo"),
          id_sucursal: id_sucursal,
          model: new app.models.Stock({
            items: [],
            id_sucursal: id_sucursal,
          }),
        });
        crearLightboxHTML({
          "html":v.el,
          "width":800,
          "height":400,
          "escapable":false,
          "callback":function(){
            app.views.articulosTableView.buscar();
          },
        });
        */
      },

      "click .orden_destacado":function(e){
        var self = this;
        e.stopPropagation();
        e.preventDefault();        
        var o = prompt("Posicion de destaque (mientras mayor sea el numero mas arriba aparecera): ");
        var f = parseFloat(o);
        if (isNaN(f)) return;
        self.model.set({"destacado":f});
        this.change_property({
          "table":"articulos",
          "attribute":"destacado",
          "value":f,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
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
      "click .edicion_rapida":"edicion_rapida",
      "click .cocina":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var no_totalizar_reparto = this.model.get("no_totalizar_reparto");
        no_totalizar_reparto = (no_totalizar_reparto == 1)?0:1;
        self.model.set({"no_totalizar_reparto":no_totalizar_reparto});
        this.change_property({
          "table":"articulos",
          "attribute":"no_totalizar_reparto",
          "value":no_totalizar_reparto,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },

      "click .canasta_basica":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var custom_5 = this.model.get("custom_5");
        custom_5 = (custom_5 == "1")?"":"1";
        self.model.set({"custom_5":custom_5});
        $.ajax({
          "timeout":0,
          "url":"articulos/function/ajuste_masivo_canasta_basica/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ids":self.model.id,
            "estado":custom_5,
          },
          "success":function() {
            self.render();
          },
        });        
        return false;
      },

      "click .estado_articulo":function(e) {
        var estado_actual = this.model.get("lista_precios");
        estado_actual++;
        if (estado_actual > 3) estado_actual = 0;
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        self.model.set({"lista_precios":estado_actual});
        this.change_property({
          "table":"articulos",
          "attribute":"lista_precios",
          "value":estado_actual,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },

      "click .ver_variantes":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        $(".fila_variante_"+self.model.id).toggle();
      },

      "click .nuevo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var nuevo = this.model.get("nuevo");
        nuevo = (nuevo == 1)?0:1;
        self.model.set({"nuevo":nuevo});
        this.change_property({
          "table":"articulos",
          "attribute":"nuevo",
          "value":nuevo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .inactivo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        self.model.set({"lista_precios":0});
        this.change_property({
          "table":"articulos",
          "attribute":"lista_precios",
          "value":0,
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
        self.model.set({"lista_precios":1});
        this.change_property({
          "table":"articulos",
          "attribute":"lista_precios",
          "value":1,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .mostrar_web":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        self.model.set({"lista_precios":2});
        this.change_property({
          "table":"articulos",
          "attribute":"lista_precios",
          "value":2,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        self.model.set({"lista_precios":3});
        this.change_property({
          "table":"articulos",
          "attribute":"lista_precios",
          "value":3,
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
            "url":"articulos/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              //window.location.href = "app/#articulo/"+r.id;
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
        window.codigo_articulo_seleccionado = this.model.get("codigo");
        window.articulo_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        if (ID_EMPRESA == 229 || ID_EMPRESA == 230 || ID_EMPRESA == 1355) {
          window.open("app/#articulo/"+this.model.id,"_blank");
        } else {
          location.href="app/#articulo/"+this.model.id;
        }
      }
    },
    marcar: function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();

      var el = e.currentTarget;
      if ($(el).is(":checked")) {
        $(this.el).addClass("seleccionado");
        window.articulos_marcados.push(this.model.id);
      } else {
        $(this.el).removeClass("seleccionado");
        window.articulos_marcados = _.reject(window.articulos_marcados,function(m){
          return (m == self.model.id);
        });
      }

      // Si hay alguno marcado
      var marcado = false;
      $(".check-row").each(function(i,e){
        if ($(e).is(":checked")) marcado = true;
      });
      if (marcado) $(".bulk_action").slideDown();
      else $(".bulk_action").slideUp();
      return false;
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
        permiso: control.check("articulos"),
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // Controlamos si el articulos fue marcado o no
      if (typeof window.articulos_marcados != "undefined" && window.articulos_marcados.length > 0) {
        var res = _.find(window.articulos_marcados,function(m){
          return (m == self.model.id)
        });
        if (typeof res != "undefined") {
          $(this.el).addClass("seleccionado");
          $(this.el).find(".check-row").prop("checked",true);
        }
      }
      $('[data-toggle="tooltip"]').tooltip();
      return this;
    },
    edicion_rapida: function() {
      var self = this;
      var articulo = new app.models.Articulo({
        "id":self.model.id,
        "images":[],
      });
      articulo.fetch({
        "success":function() {
          var that = self;
          var view = new app.views.ArticuloEdicionRapidaView({
            model: articulo,
          });
          crearLightboxHTML({
            "html":view.el,
            "width":450,
            "height":140,
            "callback":function() {
              that.model.fetch();
            },
          });
        }
      });
    },
    enviar_ficha: function() {
      var links_adjuntos = new Array();
      var id = this.model.id;
      var nombre = this.model.get("nombre");
      links_adjuntos.push({
        "tipo": TIPO_ADJUNTO_ARTICULO,
        "id_objeto": id,
        "nombre": nombre,
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Fichas de Productos",
      });
      workspace.nuevo_email(email);
    },
    compartir_meli: function() {
      if (isEmpty(ML_ACCESS_TOKEN)) {
        alert("La sincronizacion con MercadoLibre no esta habilitada. Puede hacerlo desde la configuracion avanzada.");
        location.href="app/#web_seo";
        return;
      }
      var that = this;
      var articulo = new app.models.Articulo({
        "id":that.model.id
      });
      articulo.fetch({
        "success":function(){

          // El articulo no esta activo
          if (articulo.get("lista_precios") == 0) {
            alert("El articulo se encuentra desactivado, por favor activelo antes de publicar.")
            return;
          }

          // Si no tiene cargadas images
          if (articulo.get("images").length == 0) {
            alert("El articulo no tiene imagenes cargadas. Por favor ingrese alguna para poder publicar.");
            return;            
          }

          // No esta habilitado para gestionar el stock
          if (articulo.get("usa_stock") == 0) {
            alert("Para compartir en MercadoLibre, es necesario que el producto tenga habilitado el stock.");
            return;
          }

          // Controlamos si tiene STOCK
          if (typeof articulo.get("stock_almacenes") != "undefined" && articulo.get("stock_almacenes").length > 0) {
            var stock_total = 0;
            _.each(articulo.get("stock_almacenes"),function(it){
              stock_total += parseFloat(it.stock_actual);
            });
            if (stock_total == 0) {
              alert("ERROR: No es posible compartir un producto con STOCK igual a cero.");
              return;
            }
          }

          var view = new app.views.ArticuloMercadoLibreView({
            model: articulo,
          });
          crearLightboxHTML({
            "html":view.el,
            "width":900,
            "height":500,
            "escapable":false,
          });          
        }
      });
      //window.open("/share_meli.php?id_articulo="+this.model.id+"&id_empresa="+this.model.get("id_empresa"),"_blank");
    },

    meli_reactivar: function() {
      var id_meli = this.model.get("id_meli");
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/reactivar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_articulo":self.model.id,
          "id_meli":id_meli,
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else alert(r.mensaje);
        },
      });
    },

    meli_pausar: function() {
      var id_meli = this.model.get("id_meli");
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/pausar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_articulo":self.model.id,
          "id_meli":id_meli,
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else alert(r.mensaje);
        },
      });
    },

    meli_finalizar: function() {
      var id_meli = this.model.get("id_meli");
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/finalizar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_articulo":self.model.id,
          "id_meli":id_meli,
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else alert(r.mensaje);
        },
      });
    },

    meli_eliminar: function() {
      var id_meli = this.model.get("id_meli");
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/eliminar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_articulo":self.model.id,
          "id_meli":id_meli,
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else alert(r.mensaje);
        },
      });
    },

    meli_republicar: function() {
      var id_meli = this.model.get("id_meli");
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/republicar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_articulo":self.model.id,
          "id_meli":id_meli,
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else alert(r.mensaje);
        },
      });
    },

  });
})(app);


(function ( app ) {

  app.views.ArticulosVariantesItemResultados = app.mixins.View.extend({
        
    template: _.template($("#articulos_variantes_item_resultados_template").html()),
    tagName: "tr",
    className: function(){
      return "fila_variante fila_variante_"+this.model.get("id_articulo");
    },
    myEvents: {
      "click .modificar_stock_variante":function(e){
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var id_sucursal = $(e.currentTarget).data("id_sucursal");
        var cont = $(e.currentTarget).find(".inline-text-cont");
        var text = $(e.currentTarget).find(".inline-text");
        var stock_anterior = parseFloat($(text).val());
        $(cont).hide();
        $(text).one("focusout",function(){
          var stock = parseFloat($(text).val());
          if (isNaN(stock)) {
            alert("Por favor ingrese un numero");
            $(text).empty();
            $(text).hide();
            $(cont).show();
            return;
          }
          if (stock_anterior == stock) {
            $(text).hide();
            $(cont).show();
            return;            
          }
          $.ajax({
            "url":"stock/function/edicion_stock/",
            "dataType":"json",
            "type":"post",
            "data":{
              "id_articulo":self.model.get("id_articulo"),
              "id_sucursal":id_sucursal,
              "id_variante":self.model.id,
              "stock":stock
            },
            "success":function(r){
              app.views.articulosTableView.buscar();
            },
            "error":function(){
              $(cont).show();
              $(text).hide();              
            }
          });
        });
        $(text).show();
        $(text).select();
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.item = options.item;
      this.collection = this.options.collection;
      this.render();
    },
    render: function() {
      var self = this;
      var obj = { 
        seleccionar: this.habilitar_seleccion,
        permiso: control.check("articulos"),
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      $('[data-toggle="tooltip"]').tooltip();
      return this;
    },
  });
})(app);



// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.ArticuloEditView = app.mixins.View.extend({

    template: _.template($("#articulo_template").html()),
        
    myEvents: {

      // ABRIMOS MODAL PARA LA GALERIA
      "click .abrir_galeria":function(e) {
        var self = this;
        var view = new app.views.GaleriaView({
          model: self.model,
          view: self,
        });
        crearLightboxHTML({
          "html":view.el,
          "width":700,
          "height":400,
        });
      },

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "articulos/function/upload_images/",
          "view": self,
        });
      },

      // Para configurar las listas de precios
      "click .configurar_listas":function() {
        var p = new app.views.ListaPreciosConfiguracionView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });
      },

      "change #articulo_rubros":"cambiar_rubros",
      "click .agregar_usuario": "agregar_usuario",
      "click .eliminar_usuario": "eliminar_usuario",

      // Redondea el precio final
      "click .redondear_precio_final_dto":function() {
        var precio_final_dto = parseFloat(this.$("#articulo_precio_final_dto").val());
        if (isNaN(precio_final_dto)) return;
        precio_final_dto = Math.round(precio_final_dto * 2,0) / 2;
        this.$("#articulo_precio_final_dto").val(precio_final_dto);
      },

      //"focusout .numerico": "es_numero",
      //"focusout .calc_total": "calcular_precios",
      "click .guardar": "guardar",
      "click #articulo_eliminado":function(e) {
        $("#articulo_fecha_eliminado").attr("disabled",(!$(e.currentTarget).is(":checked")));
      },

      "click #articulo_ingrediente_agregar":"agregar_ingrediente",
      "click .editar_ingrediente":"editar_ingrediente",
      "click .eliminar_ingrediente":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #articulo_ingrediente_adicional":function(e) {
        if (e.which == 13) this.agregar_ingrediente();
      },

      "change #articulo_lista":"cambiar_lista",
      
      "change #articulo_tipos_alicuotas_iva":function() {
        var porc_iva = $("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje");
        var id_tipo_alicuota_iva = $("#articulo_tipos_alicuotas_iva").val();
        this.model.set({ "porc_iva":porc_iva, "id_tipo_alicuota_iva":id_tipo_alicuota_iva });
        this.calcular_precios();
      },
      
      // Se modifican los costos, se calculan los precios
      "keypress #articulo_costo_neto_inicial":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat($("#articulo_costo_neto_inicial").val());
          var dto_prov = parseFloat($("#articulo_dto_prov").val());
          var costo_neto = costo_neto_inicial * (100-dto_prov)/100;
          $("#articulo_costo_neto").val(Number(costo_neto).toFixed(2));
          this.calcular_precios();
          $("#articulo_dto_prov").focus();
        }
      },
      "keypress #articulo_dto_prov":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat($("#articulo_costo_neto_inicial").val());
          var dto_prov = parseFloat($("#articulo_dto_prov").val());
          var costo_neto = costo_neto_inicial * (100-dto_prov)/100;
          $("#articulo_costo_neto").val(Number(costo_neto).toFixed(2));
          this.calcular_precios();
          $("#articulo_costo_neto").focus();
        }
      },
      
      "keypress #articulo_costo_neto":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          $("#articulo_tipos_alicuotas_iva").focus();
        }
      },
      "keypress #articulo_iva":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          $("#articulo_porc_ganancia").select();
        }
      },
      "keypress #articulo_porc_ganancia":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final").select(); }
      },
      "keypress #articulo_porc_ganancia_2":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final_2").select(); }
      },
      "keypress #articulo_porc_ganancia_3":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final_3").select(); }
      },
      "keypress #articulo_porc_ganancia_4":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final_4").select(); }
      },
      "keypress #articulo_porc_ganancia_5":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final_5").select(); }
      },
      "keypress #articulo_porc_ganancia_6":function(e){
        if (e.keyCode == 13) { this.calcular_precios(); $("#articulo_precio_final_6").select(); }
      },
      
      // Se modifica el COSTO FINAL
      "keypress #articulo_costo_final":function(e){
        if (e.keyCode == 13) {
            
          // En base al precio neto, calculamos los costos
          var costo_final = $("#articulo_costo_final").val();
          var porc_iva = $("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje");
          var adicionales = (this.$("#articulo_costo_agregado").length > 0) ? parseFloat(this.$("#articulo_costo_agregado").val()) : 0;
          var costo_neto = (costo_final - adicionales) / parseFloat(1+(porc_iva/100));
          $("#articulo_costo_neto").val(costo_neto.toFixed(3));
          
          var porc_ganancia = $("#articulo_porc_ganancia").val();
          var costo_iva = costo_neto * (porc_iva / 100);
          $("#articulo_iva").val(Number(costo_iva).toFixed(2));
          var ganancia = costo_final * (porc_ganancia / 100);
          $("#articulo_ganancia").val(Number(ganancia).toFixed(2));
          var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
          $("#articulo_precio_neto").val(Number(precio_neto).toFixed(2));
          var precio_final = parseFloat(precio_neto) * (1+(porc_iva/100));
          $("#articulo_precio_final").val(Number(precio_final).toFixed(2));
          if ($("#articulo_costo_neto_inicial").length>0) {
            var dto_prov = $("#articulo_dto_prov").val();
            var costo_neto_inicial = parseFloat(costo_neto) / ((100-dto_prov)/100);
            $("#articulo_costo_neto_inicial").val(Number(costo_neto_inicial).toFixed(2));
          }
          var porc_bonif = $("#articulo_porc_bonif").val();
          var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
          $("#articulo_precio_final_dto").val(Number(precio_final_dto).toFixed(2));  

          this.editar_lista_precios(2);
          this.editar_lista_precios(3);
          this.editar_lista_precios(4);
          this.editar_lista_precios(5);
          this.editar_lista_precios(6);
          
          this.model.set({
            "costo_neto":costo_neto,
            "costo_iva":costo_iva,
            "costo_final":costo_final,
            "porc_ganancia":porc_ganancia,
            "ganancia":ganancia,
            "precio_final":precio_final,
            "precio_neto":precio_neto
          });
          this.ver_costo_final();
          
          $("#articulo_porc_ganancia").select();
        }
      },
      
      // Se modifican los precios, se calculan los costos
      "change #articulo_precio_final":"editar_precio_final",
      "change #articulo_precio_final_2":function(){ this.editar_lista_precio_final(2); },
      "change #articulo_precio_final_3":function(){ this.editar_lista_precio_final(3); },
      "change #articulo_precio_final_4":function(){ this.editar_lista_precio_final(4); },
      "change #articulo_precio_final_5":function(){ this.editar_lista_precio_final(5); },
      "change #articulo_precio_final_6":function(){ this.editar_lista_precio_final(6); },

      "change #articulo_porc_bonif": function(e){
        var porc_bonif = this.$("#articulo_porc_bonif").val();
        if (porc_bonif != 0) this.$("#articulo_promociones_cont").slideDown();
        else {
          this.$("#articulo_promociones").val(0).trigger("change");
          this.$("#articulo_promociones_cont").slideUp();
        }
        this.editar_precio_final();
      },
      "change #articulo_porc_bonif_2":function(){ this.editar_lista_precio_final(2); },
      "change #articulo_porc_bonif_3":function(){ this.editar_lista_precio_final(3); },
      "change #articulo_porc_bonif_4":function(){ this.editar_lista_precio_final(4); },
      "change #articulo_porc_bonif_5":function(){ this.editar_lista_precio_final(5); },
      "change #articulo_porc_bonif_6":function(){ this.editar_lista_precio_final(6); },
      
      "click .eliminar_relacionado":function(e) {
        if (confirm("Realmente desea eliminar la relacion?")) {
          $(e.currentTarget).parents("li").remove();
        }
      },   

      // COSTOS AGREGADOS
      "click #articulo_costo_agregado_btn":function(){
        this.$("#articulo_costos_agregados").slideToggle();
      },
      "change .calc_porc_costo_adicional":function(e) {
        var costo_neto = parseFloat(this.$("#articulo_costo_neto").val());
        if (isNaN(costo_neto)) costo_neto = 0;
        var porc_costo = parseFloat($(e.currentTarget).val());
        if (isNaN(porc_costo)) porc_costo = 0;
        var costo_adicional = parseFloat(costo_neto * (porc_costo / 100));
        $(e.currentTarget).parents(".costo_adicional_item").find(".calc_costo_adicional").val(Number(costo_adicional).toFixed(2));
        this.calcular_costos_adicionales();
      },
      "change .calc_costo_adicional":function(e) {
        var costo_neto = parseFloat(this.$("#articulo_costo_neto").val());
        if (isNaN(costo_neto)) costo_neto = 0;
        var costo_adicional = parseFloat($(e.currentTarget).val());
        if (isNaN(costo_adicional)) costo_adicional = 0;
        var porc_costo_adicional = (costo_neto != 0) ? parseFloat(costo_adicional * 100 / costo_neto) : 0;
        $(e.currentTarget).parents(".costo_adicional_item").find(".calc_porc_costo_adicional").val(Number(porc_costo_adicional).toFixed(2));
        this.calcular_costos_adicionales();
      },

      "click #expand_principal":"expand_principal",

      // PROPIEDADES Y VARIANTES
      // ================================
      
      "click .nueva_propiedad":"nueva_propiedad",
      "click .eliminar_propiedad":function(e) {
        if (!confirm("Realmente desea eliminar la propiedad?")) return;
        var id = $(e.currentTarget).data("id");
        $.ajax({
          "url":"articulos_propiedades/function/eliminar/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":id,
            "id_empresa":ID_EMPRESA,
          },
          "success":function(r) {
            if (r.error == 0) location.reload();
            else alert(r.mensaje);
          }
        });
      },
      "change .opciones":"cambiar_opcion",
      "change .campo_variante":"guardar_variante",

      // RUBROS
      // ================================

      "click .agregar_rubro":function(e) {
        var self = this;
        if ($(".rubro_edit_mini").length > 0) return;
        var df = {
          "id_usuario": ((ID_EMPRESA == 571 && PERFIL == 661) ? ID_USUARIO : 0),
        };
        var form = new app.views.RubroMiniEditView({
          "model": new app.models.Rubro(df),
          "callback":function(m){
            self.model.set({ "id_rubro":m });
            self.cargar_rubros();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete rubro_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#rubros_mini_nombre").focus();
      },


      // DEPARTAMENTOS
      // ================================

      "click .agregar_departamento":function(e) {
        var self = this;
        if ($(".departamento_edit_mini").length > 0) return;
        var form = new app.views.DepartamentoComercialMiniEditView({
          "model": new app.models.DepartamentoComercial(),
          "callback":function(m){
            self.model.set({ "id_departamento":m });
            self.cargar_departamentos_comerciales();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete departamento_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#departamentos_comerciales_mini_nombre").focus();
      },

      // MARCAS
      // ================================

      "click .agregar_marca":function(e) {
        var self = this;
        if ($(".marca_edit_mini").length > 0) return;
        var form = new app.views.MarcaMiniEditView({
          "model": new app.models.Marca(),
          "callback":function(m){
            self.model.set({ "id_marca":m });
            self.cargar_marcas();
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
        $("#marcas_mini_nombre").focus();
      },


      // PROVEEDORES
      // ================================

      "click .agregar_proveedor": function(e) {
        var self = this;
        if ($(".proveedor_edit_mini").length > 0) return;
        var form = new app.views.ProveedorEditViewMini({
          "model": new app.models.Proveedor(),
          "onSave":function(m) {
            self.cargar_proveedores(m.id);
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete proveedor_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#proveedores_mini_nombre").focus();
      },
      "keypress #proveedor_codigo":function(e) {
        if (e.which == 13) {
          this.agregar_proveedor();
          this.$("#articulo_proveedores").focus();
        }
      },
      "click #proveedor_agregar":"agregar_proveedor",
      "click .editar_proveedor":"editar_proveedor",
      "click .eliminar_proveedor":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      // MARCAS DE VEHICULOS
      // ================================
      "keypress #marca_vehiculo_codigo":function(e) {
        if (e.which == 13) {
          this.agregar_marca_vehiculo();
          this.$("#articulo_marcas_vehiculos").focus();
        }
      },
      "click #marca_vehiculo_agregar":"agregar_marca_vehiculo",
      "click .editar_marca_vehiculo":"editar_marca_vehiculo",
      "click .eliminar_marca_vehiculo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      // ATRIBUTOS ESPECIALES
      "click .no_aplica":"no_aplica_atributo",

      // COMPONENTES
      "keypress #articulo_componentes_cantidad":function(e) {
        if (e.which == 13) this.agregar_componente();
      },
      "click .agregar_componente":"agregar_componente",
      "click .editar_componente":"editar_componente",
      "click .eliminar_componente":function(e) {
        if (confirm("Realmente desea eliminar el articulo?")) {
          $(e.currentTarget).parents("tr").remove();
        }
      },      
    },

    agregar_componente: function() {
      var self = this;
      var id = this.$("#articulo_componentes_id").val();
      if (id == 0) {
        alert("Por favor busque un producto de la lista");
        $("#articulo_componentes_buscar").focus();
        return;
      }
      var nombre = this.$("#articulo_componentes_buscar").val();
      var cantidad = $("#articulo_componentes_cantidad").val();
      if (cantidad == 0) {
        alert("Por favor ingrese una cantidad");
        $("#articulo_componentes_cantidad").focus();
        return;
      }
      var tr = "<tr data-id='"+id+"'>";
      tr+="<td>"+nombre+"</td>";
      tr+="<td>"+cantidad+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_componente'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_componente text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item_componente == null) {
        this.$("#articulos_tabla_componentes tbody").append(tr);
      } else {
        $(this.item_componente).replaceWith(tr);
        this.item_componente = null;
      }
      this.$("#articulo_componentes_id").val("0");
      this.$("#articulo_componentes_buscar").val("");
      this.$("#articulo_componentes_cantidad").val("");
      this.$("#articulo_componentes_buscar").focus();
    },

    editar_componente: function(e) {
      this.item_componente = $(e.currentTarget).parents("tr");
      this.$("#articulo_componentes_id").val($(this.item_componente).data("id"));
      this.$("#articulo_componentes_buscar").val($(this.item_componente).find("td:eq(0)").text());
      this.$("#articulo_componentes_cantidad").val($(this.item_componente).find("td:eq(1)").text());
    },

    calcular_costos_adicionales: function() {
      var self = this;
      var costo_1 = parseFloat(this.$("#articulo_costo_1").val());
      if (isNaN(costo_1)) costo_1 = 0;
      var costo_2 = parseFloat(this.$("#articulo_costo_2").val());
      if (isNaN(costo_2)) costo_2 = 0;
      var costo_3 = parseFloat(this.$("#articulo_costo_3").val());
      if (isNaN(costo_3)) costo_3 = 0;
      var costo_4 = parseFloat(this.$("#articulo_costo_4").val());
      if (isNaN(costo_4)) costo_4 = 0;
      this.model.set({
        "costo_1":costo_1,
        "porc_costo_1":self.$("#articulo_porc_costo_1").val(),
        "costo_2":costo_2,
        "porc_costo_2":self.$("#articulo_porc_costo_2").val(),
        "costo_3":costo_3,
        "porc_costo_3":self.$("#articulo_porc_costo_3").val(),
        "costo_4":costo_4,
        "porc_costo_4":self.$("#articulo_porc_costo_4").val(),
      });
      var adicionales = costo_1 + costo_2 + costo_3 + costo_4;
      this.$("#articulo_costo_agregado").val(Number(adicionales).toFixed(2));
      var costo_neto = parseFloat($("#articulo_costo_neto").val());
      var porc_iva = parseFloat($("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje"));
      var costo_final = Number(((costo_neto) * (1+(porc_iva / 100))) + adicionales).toFixed(2);

      this.$("#articulo_costo_final").val(costo_final);
      this.calcular_precios();
    },

    editar_lista_precios: function(numero) {

      var costo_final = parseFloat(this.$("#articulo_costo_final").val());
      var costo_neto = parseFloat(this.$("#articulo_costo_neto").val());

      var porc_gcia = this.$("#articulo_porc_ganancia_"+numero).val();
      var ganancia = costo_final * (porc_gcia / 100);
      this.$("#articulo_ganancia_"+numero).val(Number(ganancia).toFixed(2));

      var obj = {};
      obj["porc_ganancia_"+numero] = porc_gcia;
      obj["ganancia_"+numero] = ganancia;
      this.model.set(obj);

      var precio_neto = parseFloat(costo_neto) * (1+(porc_gcia / 100));
      var precio_final = parseFloat(costo_final) * (1+(porc_gcia / 100));
      this.$("#articulo_precio_neto_"+numero).val(Number(precio_neto).toFixed(2));
      this.$("#articulo_precio_final_"+numero).val(Number(precio_final).toFixed(2));
      var porc_bonif = this.$("#articulo_porc_bonif_"+numero).val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      this.$("#articulo_precio_final_dto_"+numero).val(Number(precio_final_dto).toFixed(2)); 
    },

    cargar_rubros: function() {
      var self = this;
      var disabled = (self.options.permiso <= 1);
      if (MEGASHOP == 1 && PERFIL == 595) disabled = false;
      var pars = (ID_EMPRESA == 571 && PERFIL == 661) ? "id_usuario="+ID_USUARIO+"&" : "";
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/function/get_select/?"+pars,
        render: "#articulo_rubros",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_rubro"),
        fields: ["id_padre"],
        disabled: disabled,
        onComplete: function() {
          crear_select2("articulo_rubros");
          self.cambiar_rubros();
        }
      });
    },

    cargar_clientes: function() {
      var self = this;
      var id_cliente = 0;
      var clientes = this.model.get("clientes");
      if (clientes.length > 0) {
        var c = clientes[0];
        id_cliente = c.id_cliente;
      }
      var url = "clientes/";
      if (MILLING == 1) url = "clientes/?filter=&limit=0&offset=9999999&order_by=nombre&order=asc&term=&codigo_propiedad=&id_vendedor=0&id_etiqueta=&tipo=0&custom_3=&custom_4=&custom_5=&id_proyecto=4"
      new app.mixins.Select({
        "modelClass": app.models.Cliente,
        "url": url,
        "disabled": (self.options.permiso <= 1),
        "render": "#articulo_clientes",
        "firstOptions": ["<option value='0'>-</option>"],
        "selected": id_cliente,
      });
    },

    cambiar_rubros: function() {
      if (this.$("#articulo_rubros").length == 0) return;
      var id = this.$("#articulo_rubros").val();
      if (typeof this.$("#articulo_rubros").find("option:selected").data("id_padre") != "undefined") {
        id = this.$("#articulo_rubros").find("option:selected").data("id_padre");
      }
      var width = (typeof PRODUCTO_GALERIA_IMAGE_WIDTH === "undefined") ? 400 : PRODUCTO_GALERIA_IMAGE_WIDTH;
      var height = (typeof PRODUCTO_GALERIA_IMAGE_HEIGHT === "undefined") ? 400 : PRODUCTO_GALERIA_IMAGE_HEIGHT;
      try {
        width = eval("PRODUCTO_GALERIA_IMAGE_WIDTH_CATEGORIA_"+id);
        height = eval("PRODUCTO_GALERIA_IMAGE_HEIGHT_CATEGORIA_"+id);
      } catch(e) {}
      this.$("#images_width").val(width);
      this.$("#images_height").val(height);
    },
    
    cargar_marcas: function() {
      var self = this;
      var disabled = (self.options.permiso <= 1);
      if (MEGASHOP == 1 && PERFIL == 595) disabled = false;
      new app.mixins.Select({
        modelClass: app.models.Marca,
        url: "marcas/",
        render: "#articulo_marcas",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_marca"),
        disabled: disabled,
        onComplete:function(c) {
          crear_select2("articulo_marcas");
        }                    
      });
    },

    cargar_departamentos_comerciales: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.DepartamentoComercial,
        url: "departamentos_comerciales/",
        render: "#articulo_departamentos_comerciales",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_departamento"),
        disabled: (self.options.permiso <= 1),
        onComplete:function(c) {
          crear_select2("articulo_departamentos_comerciales");
        }                    
      });
    },

    cargar_proveedores: function(id_proveedor) {
      var self = this;
      id_proveedor = (id_proveedor || 0);
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        render: "#articulo_proveedores",
        selected: id_proveedor,
        onComplete:function(c) {
          crear_select2("articulo_proveedores");
        }                    
      });
    },

    agregar_proveedor: function() {
        
      var id_proveedor = $("#articulo_proveedores").val();
      if (id_proveedor == 0) {
        alert("Por favor seleccione un proveedor");
        $("#articulo_proveedores").focus();
        return;
      }
      var proveedor = $("#articulo_proveedores option:selected").text();

      var codigo = $("#proveedor_codigo").val();
      /*if (isEmpty(codigo)) {
        alert("Por favor ingrese un codigo");
        $("#proveedor_codigo").focus();
        return;
      }*/
      var tr = "<tr data-id='"+id_proveedor+"'>";
      tr+="<td>"+proveedor+"</td>";
      tr+="<td>"+codigo+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_proveedor'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_proveedor text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#proveedores_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#proveedor_codigo").val("");
      $("#articulo_proveedores").focus();
    },

    editar_proveedor: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#articulo_proveedores").val($(this.item).data("id")).trigger("change");
      $("#proveedor_codigo").val($(this.item).find("td:eq(1)").text());
    },


    // Cuando expandimos por primera vez el panel principal
    expand_principal: function() {
      var self = this;
      if (this.expand_principal_key == 1) return;
      this.expand_principal_key = 1;
    },    

    cargar_articulos_etiquetas: function() {
      var self = this;
      if (self.$("#articulo_etiquetas").length > 0) { 
        if (typeof ARTICULOS_ETIQUETAS_NO_CREAR_NUEVAS != "undefined" || (ID_EMPRESA == 571 && PERFIL != 660)) {
          self.$("#articulo_etiquetas").select2({});
        } else {
          self.$("#articulo_etiquetas").select2({
            tags: true,
            minimumInputLength: 3,
            ajax: {
              url: "articulos_etiquetas/function/get_by_nombre/",
              dataType: 'json',
              delay: 1000,
              data: function (params) {
                return {
                  term: params.term,
                  page: params.page
                };
              },
              processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;
                return {
                  results: data,
                  pagination: {
                    more: (params.page * 30) < data.total_count
                  }
                };
              },
              cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
          });
        }
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
        render: "#articulo_marcas_vehiculos",
        selected: id_marca,
        onComplete:function(c) {
          crear_select2("articulo_marcas_vehiculos");
        }                    
      });
    },

    agregar_marca_vehiculo: function() {
      var id_marca = $("#articulo_marcas_vehiculos").val();
      if (id_marca == 0) {
        alert("Por favor seleccione una marca");
        $("#articulo_marcas_vehiculos").focus();
        return;
      }
      var marca_vehiculo = $("#articulo_marcas_vehiculos option:selected").text();

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
      $("#articulo_marcas_vehiculos").focus();
    },

    editar_marca_vehiculo: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#articulo_marcas_vehiculos").val($(this.item).data("id")).trigger("change");
      $("#marca_vehiculo_codigo").val($(this.item).find("td:eq(1)").text());
    },

    agregar_ingrediente: function() {
      // Controlamos los valores
      var nombre = this.$("#articulo_ingrediente_nombre").val();
      if (isEmpty(nombre)) {
        alert("Por favor ingrese un nombre");
        $("#articulo_ingrediente_nombre").focus();
        return;
      }
      var valores = this.$("#articulo_ingrediente_valores").select2("val");
      if (valores.length == 0) {
        alert("Por favor ingrese algun valor");
        $("#articulo_ingrediente_valores").focus();
        return;
      }
      var adicional = this.$("#articulo_ingrediente_adicional").val();
      var activo = this.$("#articulo_ingrediente_activo").val();
      var tr = "<tr>";
      tr+="<td><label class='i-checks'><input type='checkbox' "+((activo==1)?"checked":"")+" class='checkbox' value='1'><i></i></label></td>";
      tr+="<td>"+nombre+"</td>";
      tr+="<td>"+valores.join(",")+"</td>";
      tr+="<td>"+adicional+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_ingrediente'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_ingrediente text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.ingrediente == null) {
        $("#ingredientes_tabla tbody").append(tr);
      } else {
        $(this.ingrediente).replaceWith(tr);
        this.ingrediente = null;
      }
      $("#articulo_ingrediente_nombre").val("");
      $("#articulo_ingrediente_adicional").val(0);
      $("#articulo_ingrediente_activo").val(1);
      $("#articulo_ingrediente_valores option").remove();
      $("#articulo_ingrediente_valores").trigger("change");
      $("#articulo_ingrediente_nombre").focus();
    },

    editar_ingrediente: function(e) {
      this.ingrediente = $(e.currentTarget).parents("tr");
      this.$("#articulo_ingrediente_activo").val(($(this.ingrediente).find("td:eq(0) .checkbox").is(":checked")?1:0));
      $("#articulo_ingrediente_nombre").val($(this.ingrediente).find("td:eq(1)").text());
      $("#articulo_ingrediente_adicional").val($(this.ingrediente).find("td:eq(3)").text());
      var ingr = $(this.ingrediente).find("td:eq(2)").text().split(",");
      $("#articulo_ingrediente_valores option").remove();
      for(var i=0;i<ingr.length;i++) {
        var e = ingr[i];
        $("#articulo_ingrediente_valores").append("<option selected>"+e+"</option>");
      }
      $("#articulo_ingrediente_valores").trigger("change");
    },

    // Actualiza el costo final en todos los inputs donde se muestra
    ver_costo_final : function() {
      $(".costo_final").val(Number(this.model.get("costo_final")).toFixed(2));
    },
        
    cambiar_lista: function() {
      var lista = $(this.el).find("#articulo_lista").val()
      $(this.el).find(".lista_container").hide();
      $(this.el).find("#lista_"+lista).show();            
    },
        
    es_numero:function(e) {
      var valor = $(e.currentTarget).val();
      if (isEmpty(valor)) {
        valor = "0";
        $(e.currentTarget).val("0");
      }
      if (!(isInteger(valor) || isDecimal(valor))) {
        show("Por favor ingrese un numero.");
        $(e.currentTarget).focus();
      }
    },
        
    calcular_precios: function() {
      
      var costo_neto = $("#articulo_costo_neto").val();
      var porc_iva = $("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje");
      var costo_iva = costo_neto * (porc_iva / 100);
      $("#articulo_iva").val(Number(costo_iva).toFixed(2));
      this.model.set({"costo_iva":costo_iva});

      var adicionales = (this.$("#articulo_costo_agregado").length > 0) ? parseFloat(this.$("#articulo_costo_agregado").val()) : 0;
      
      var costo_final = (parseFloat(costo_neto) * (1+(porc_iva / 100))) + adicionales;
      $("#articulo_costo_final").val(Number(costo_final).toFixed(2));
      this.model.set({"costo_neto":costo_neto});
      this.model.set({"costo_final":costo_final});
      
      var porc_gcia = $("#articulo_porc_ganancia").val();
      var ganancia = costo_final * (porc_gcia / 100);
      $("#articulo_ganancia").val(Number(ganancia).toFixed(2));
      this.model.set({"porc_ganancia":porc_gcia});
      this.model.set({"ganancia":ganancia});
      var precio_final = parseFloat(costo_final) * (1+(porc_gcia / 100));
      var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
      
      $("#articulo_precio_neto").val(Number(precio_neto).toFixed(2));
      $("#articulo_precio_final").val(Number(precio_final).toFixed(2));
      var porc_bonif = $("#articulo_porc_bonif").val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      $("#articulo_precio_final_dto").val(Number(precio_final_dto).toFixed(2));
      
      var porc_gcia_2 = $("#articulo_porc_ganancia_2").val();
      var ganancia_2 = costo_final * (porc_gcia_2 / 100);
      $("#articulo_ganancia_2").val(Number(ganancia_2).toFixed(2));
      this.model.set({"porc_ganancia_2":porc_gcia_2});
      this.model.set({"ganancia_2":ganancia_2});
      var precio_final_2 = parseFloat(costo_final) * (1+(porc_gcia_2 / 100));
      var precio_neto_2 = Number(precio_final_2 / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_2").val(Number(precio_neto_2).toFixed(2));
      $("#articulo_precio_final_2").val(Number(precio_final_2).toFixed(2));
      var porc_bonif_2 = $("#articulo_porc_bonif_2").val();
      var precio_final_dto_2 = parseFloat(precio_final_2) * (1-(porc_bonif_2 / 100));
      $("#articulo_precio_final_dto_2").val(Number(precio_final_dto_2).toFixed(2));
      
      var porc_gcia_3 = $("#articulo_porc_ganancia_3").val();
      var ganancia_3 = costo_final * (porc_gcia_3 / 100);
      $("#articulo_ganancia_3").val(Number(ganancia_3).toFixed(2));
      this.model.set({"porc_ganancia_3":porc_gcia_3});
      this.model.set({"ganancia_3":ganancia_3});
      var precio_final_3 = parseFloat(costo_final) * (1+(porc_gcia_3 / 100));
      var precio_neto_3 = Number(precio_final_3 / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_3").val(Number(precio_neto_3).toFixed(2));
      $("#articulo_precio_final_3").val(Number(precio_final_3).toFixed(2));
      var porc_bonif_3 = $("#articulo_porc_bonif_3").val();
      var precio_final_dto_3 = parseFloat(precio_final_3) * (1-(porc_bonif_3 / 100));
      $("#articulo_precio_final_dto_3").val(Number(precio_final_dto_3).toFixed(2));            

      var porc_gcia_4 = $("#articulo_porc_ganancia_4").val();
      var ganancia_4 = costo_final * (porc_gcia_4 / 100);
      $("#articulo_ganancia_4").val(Number(ganancia_4).toFixed(2));
      this.model.set({"porc_ganancia_4":porc_gcia_4});
      this.model.set({"ganancia_4":ganancia_4});
      var precio_final_4 = parseFloat(costo_final) * (1+(porc_gcia_4 / 100));
      var precio_neto_4 = Number(precio_final_4 / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_4").val(Number(precio_neto_4).toFixed(2));
      $("#articulo_precio_final_4").val(Number(precio_final_4).toFixed(2));
      var porc_bonif_4 = $("#articulo_porc_bonif_4").val();
      var precio_final_dto_4 = parseFloat(precio_final_4) * (1-(porc_bonif_4 / 100));
      $("#articulo_precio_final_dto_4").val(Number(precio_final_dto_4).toFixed(2));            

      var porc_gcia_5 = $("#articulo_porc_ganancia_5").val();
      var ganancia_5 = costo_final * (porc_gcia_5 / 100);
      $("#articulo_ganancia_5").val(Number(ganancia_5).toFixed(2));
      this.model.set({"porc_ganancia_5":porc_gcia_5});
      this.model.set({"ganancia_5":ganancia_5});
      var precio_final_5 = parseFloat(costo_final) * (1+(porc_gcia_5 / 100));
      var precio_neto_5 = Number(precio_final_5 / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_5").val(Number(precio_neto_5).toFixed(2));
      $("#articulo_precio_final_5").val(Number(precio_final_5).toFixed(2));
      var porc_bonif_5 = $("#articulo_porc_bonif_5").val();
      var precio_final_dto_5 = parseFloat(precio_final_5) * (1-(porc_bonif_5 / 100));
      $("#articulo_precio_final_dto_5").val(Number(precio_final_dto_5).toFixed(2));            

      var porc_gcia_6 = $("#articulo_porc_ganancia_6").val();
      var ganancia_6 = costo_final * (porc_gcia_6 / 100);
      $("#articulo_ganancia_6").val(Number(ganancia_6).toFixed(2));
      this.model.set({"porc_ganancia_6":porc_gcia_6});
      this.model.set({"ganancia_6":ganancia_6});
      var precio_final_6 = parseFloat(costo_final) * (1+(porc_gcia_6 / 100));
      var precio_neto_6 = Number(precio_final_6 / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_6").val(Number(precio_neto_6).toFixed(2));
      $("#articulo_precio_final_6").val(Number(precio_final_6).toFixed(2));
      var porc_bonif_6 = $("#articulo_porc_bonif_6").val();
      var precio_final_dto_6 = parseFloat(precio_final_6) * (1-(porc_bonif_6 / 100));
      $("#articulo_precio_final_dto_6").val(Number(precio_final_dto_6).toFixed(2));            

      if ($("#articulo_costo_neto_inicial").length>0) {
        var dto_prov = parseFloat($("#articulo_dto_prov").val());
        var costo_neto_inicial = parseFloat(costo_neto) / ((100-dto_prov)/100);
        $("#articulo_costo_neto_inicial").val(Number(costo_neto_inicial).toFixed(2));
      }
      this.ver_costo_final();
    },
    
    editar_precio_final: function() {

      var precio_final = parseFloat($("#articulo_precio_final").val());
      var porc_iva = $("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje");
      
      var costo_neto = parseFloat($("#articulo_costo_neto").val());
      var costo_final = parseFloat($("#articulo_costo_final").val());
      
      // Si el costo final es distinto de cero, entonces cambiamos el PORCENTAJE DE GANANCIA
      if (costo_final != 0) {
          
        var costo_iva = costo_neto * (porc_iva / 100);
        
        var porc_ganancia = parseFloat( ((precio_final / costo_final) - 1) * 100);
        $("#articulo_porc_ganancia").val(porc_ganancia.toFixed(4));
        
        var ganancia = costo_final * (porc_ganancia / 100);
        $("#articulo_ganancia").val(Number(ganancia).toFixed(2));
        
        var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
        $("#articulo_precio_neto").val(Number(precio_neto).toFixed(2));                
          
      // Si el costo final es igual a cero, entonces lo ponemos igual al precio final
      } else {
          
        var porc_ganancia = 0;
        var ganancia = 0;
        costo_final = precio_final;
        var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
        costo_neto = precio_neto;
        var costo_iva = precio_neto * (porc_iva / 100);
        
        $("#articulo_costo_neto").val(Number(costo_neto).toFixed(2));
        $("#articulo_costo_final").val(Number(costo_final).toFixed(2));
        $(".costo_final").val(Number(costo_final).toFixed(2));
        $("#articulo_iva").val(Number(costo_iva).toFixed(2));
        $("#articulo_precio_neto").val(Number(precio_neto).toFixed(2));
        $("#articulo_porc_ganancia").val(porc_ganancia.toFixed(2));
        $("#articulo_ganancia").val(Number(ganancia).toFixed(2));
      }
      
      var porc_bonif = $("#articulo_porc_bonif").val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      $("#articulo_precio_final_dto").val(Number(precio_final_dto).toFixed(2));            

      if ($("#articulo_costo_neto_inicial").length>0) {
        var dto_prov = $("#articulo_dto_prov").val();
        var costo_neto_inicial = parseFloat(costo_neto) / ((100-dto_prov)/100);
        $("#articulo_costo_neto_inicial").val(Number(costo_neto_inicial).toFixed(2));
      }
      
      this.model.set({
        "costo_neto":costo_neto,
        "costo_iva":costo_iva,
        "costo_final":costo_final,
        "ganancia":ganancia,
        "porc_ganancia":porc_ganancia,
        "precio_final":precio_final,
        "precio_neto":precio_neto,
        "porc_bonif":porc_bonif,
        "precio_final_dto":precio_final_dto,
      });
      this.ver_costo_final();            
    },
    
    editar_lista_precio_final: function(numero) {

      var precio_final = parseFloat($("#articulo_precio_final_"+numero).val());
      var porc_iva = $("#articulo_tipos_alicuotas_iva option:selected").data("porcentaje");

      // CAMBIAMOS EL PORCENTAJE DE GANANCIA
      
      var costo_neto = parseFloat($("#articulo_costo_neto").val());
      var costo_final = parseFloat($("#articulo_costo_final").val());
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var porc_ganancia = parseFloat( ((precio_final / costo_final) - 1) * 100);
      $("#articulo_porc_ganancia_"+numero).val(porc_ganancia.toFixed(4));
      
      var ganancia = costo_final * (porc_ganancia / 100);
      $("#articulo_ganancia_"+numero).val(Number(ganancia).toFixed(2));
      
      var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
      $("#articulo_precio_neto_"+numero).val(Number(precio_neto).toFixed(2));
      
      var porc_bonif = $("#articulo_porc_bonif_"+numero).val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      $("#articulo_precio_final_dto_"+numero).val(Number(precio_final_dto).toFixed(2));                   

      var obj = {};
      obj["costo_neto"] = costo_neto;
      obj["costo_iva"] = costo_iva;
      obj["costo_final"] = costo_final;
      obj["ganancia_"+numero] = ganancia;
      obj["porc_ganancia_"+numero] = porc_ganancia;
      obj["precio_final_"+numero] = precio_final;
      obj["precio_neto_"+numero] = precio_neto;
      obj["porc_bonif_"+numero] = porc_bonif;
      obj["precio_final_dto_"+numero] = precio_final_dto;
      this.model.set(obj);
      this.ver_costo_final();            
    },

    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
        
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        "edicion": edicion,
        "id":this.model.id,
        "permiso": self.options.permiso,
      };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    
      this.cargar_rubros();

      if (control.check("marcas")>0) {
        this.cargar_marcas();
      }
      if (control.check("proveedores")>0) {
        this.cargar_proveedores();
      }
      if (control.check("marcas_vehiculos")>0) {
        this.cargar_marcas_vehiculos();
      }
      if (control.check("departamentos_comerciales")>0) {
        this.cargar_departamentos_comerciales();
      }

      // Milling and Grain
      if (MILLING == 1) {
        this.cargar_clientes();
      }

      if (control.check("promociones")) {
        new app.mixins.Select({
          modelClass: app.models.Promocion,
          url: "promociones/",
          render: "#articulo_promociones",
          firstOptions: ["<option value='0'>-</option>"],
          selected: self.model.get("id_promocion"),
          onComplete:function(c) {
            crear_select2("articulo_promociones");
          }                    
        });
      }


      if (self.$("#articulo_ingrediente_valores").length > 0) { 
        $(this.el).find("#articulo_ingrediente_valores").select2({
          tags: true,
        });
      }
      
      $(this.el).find("#articulo_caracteristicas").select2({
        tags: true
      });

      $(this.el).find("#articulo_codigos_barra").select2({
        tags: true
      });

      if (MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421 || ID_EMPRESA == 445 || (typeof MOSTRAR_PRECIOS_SUCURSALES != "undefined")) {
        this.precios_sucursales = new Array();
        for(var i=0; i<window.almacenes.length;i++) {
          var almacen = window.almacenes[i];
          var precio_sucursal = _.find(this.model.get("precios_sucursales"),function(p){
            return (p.id_sucursal == almacen.id);
          });
          if (typeof precio_sucursal == "undefined") {
            precio_sucursal = {
              "id_sucursal":almacen.id,
              "sucursal":almacen.nombre              
            };
          } else {
            precio_sucursal.id_sucursal = almacen.id;
            precio_sucursal.sucursal = almacen.nombre;
          }
          var modelo = new app.models.ArticuloPrecioSucursal(precio_sucursal);
          this.precios_sucursales.push(modelo);

          var view = new app.views.ArticuloPrecioSucursalView({
            model:modelo,
            container:self,
            edicion: edicion,
          });
          this.$("#articulo_costos_sucursales").append(view.el);
        }
      }

      if (this.$("#articulo_etiquetas").length > 0) this.cargar_articulos_etiquetas();
      this.expand_principal_key = 0;
      
      /*
      if (self.model.get("etiquetas").length>0) {
        $(this.el).find("#articulo_etiquetas").val(self.model.get("etiquetas").join(","));                
      }
      // Cargamos las etiquetas con AJAX
      $.ajax({
        "url":"articulos_etiquetas/",
        "dataType":"json",
        "success":function(r) {
          var articulos_etiquetas = new Array();
          for(var i=0;i<r.results.length;i++) {
            var a = r.results[i];
            articulos_etiquetas.push(a.nombre);
          }
          $(self.el).find("#articulo_etiquetas").select2({
            tags: articulos_etiquetas,
          });
        }
      });
      */

      if (this.$("#articulo_fecha_ingreso").length > 0) {
        var fecha_ingreso = this.model.get("fecha_ingreso");
        createdatepicker($(this.el).find("#articulo_fecha_ingreso"),fecha_ingreso);
      }

      $(this.el).find("#articulos_rubros_tree").fancytree({
        source: {
          url: 'rubros/function/get_arbol/'
        },
        selectMode: 3,
        checkbox: true,
        renderNode: function(event,data) {
          var node = data.node;
          // Controlamos si el ID esta en los relacionados
          var selected = false;
          var rel = self.model.get("rubros_relacionados");
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
        
      
      // AUTOCOMPLETE DE ARTICULOS
      // -------------------------
      var input = $(this.el).find("#articulos_buscar_productos");
      $(input).customcomplete({
        "url":"/sistema/articulos/function/get_by_descripcion/",
        "form":null, // No quiero que se creen nuevos productos
        "info":"descripcion",
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          var tr = "";
          tr+="<li class='list-group-item'>";
          tr+="<span class='id dn'>"+item.value+"</span>";
          tr+="<span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          tr+="<img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/"+item.path+"'/>";
          tr+="<span class='filename'>"+item.label+"</span>";
          tr+="<span class='pull-right btn btn-white eliminar_relacionado'><i class='fa fa-trash'></i> </span>";
          tr+="</li>";
          $("#articulos_tabla_relacionados").append(tr);
          self.$("#articulos_buscar_productos").val("");
        }
      });           
    

      if (this.$("#articulo_componentes_buscar").length > 0) {
        var input = $(this.el).find("#articulo_componentes_buscar");
        $(input).customcomplete({
          "url":"/sistema/articulos/function/get_by_descripcion/?ver_codigo=1",
          "form":null, // No quiero que se creen nuevos productos
          "image_field":"path",
          "image_path":"/sistema",
          "onSelect":function(item){
            self.$("#articulo_componentes_id").val(item.value);
            self.$("#articulo_componentes_buscar").val(item.label);
            self.$("#articulo_componentes_path").val(item.path);
            self.$("#articulo_componentes_cantidad").focus();
          }
        });
      }
        
      var fecha_mov = this.model.get("fecha_mov");
      createdatepicker($(this.el).find("#articulo_fecha_mov"),fecha_mov);
      
      var fecha = this.model.get("fecha_eliminado");
      createdatepicker($(this.el).find("#articulo_fecha_eliminado"),fecha);
      
      if (CONFIGURACION_AUTOGENERAR_CODIGOS == 1) {
        // Estamos creando un cliente nuevo
        if (this.model.id == undefined) {
          $.ajax({
            "url":"articulos/function/next/",
            "dataType":"json",
            "success":function(r) {
              self.$("#articulo_codigo").val(r.codigo);
            }
          });
        }                
      }

      // Cuando cambian las imagens, renderizamos la tabla
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();

      if (ID_PROYECTO != 1) {      
        
        this.cambiar_lista();

        // Armamos la estructura en el array de propiedades
        this.propiedades = new Array();
        var variantes = this.model.get("variantes");
        for(var i=0;i<variantes.length;i++) {
          var v = variantes[i];
          for(var k=1;k<4;k++) {
            var propiedad = _.find(this.propiedades,function(e){
              return (e.id == v["id_propiedad_"+k]);
            });
            if (typeof propiedad == "undefined") {
              var propiedad = {
                "id":v["id_propiedad_"+k],
                "nombre":v["nombre_propiedad_"+k],
                "opciones":[],
              };
              if (v["id_propiedad_"+k] != 0) {
                this.propiedades.push(propiedad);
              }
            }
            var opcion = _.find(propiedad.opciones,function(e){
              return (e.nombre == v["nombre_opcion_"+k]);
            });
            if (typeof opcion == "undefined") {
              propiedad.opciones.push({
                "nombre":v["nombre_opcion_"+k],
                "etiqueta":v["etiqueta_opcion_"+k]
              });                                                        
            }
          }
        }
        console.log(this.propiedades);
        this.render_propiedades();
        this.render_variantes();
        $(this.el).find("#articulos_tabla_relacionados").sortable();
      } else {
        this.cargar_articulos_etiquetas();
        if (this.model.get("images").length > 0) {
          this.expand_principal();
        }
      }
      $(this.el).find("#images_tabla").sortable();

      if (TIPO_EMPRESA == 1) {
        this.cargar_atributos();
      } else if (TIPO_EMPRESA == 4) {
        this.$("#articulo_custom_1").select2();
      }

      this.$('[data-toggle="tooltip"]').tooltip();
    },


    // ATRIBUTOS ESPECIALES

    cargar_atributos: function() {
      var self = this;
      // Recorremos los campos que estan en la vista
      this.$(".atributo_meli").each(function(i,e){
        var id_atributo = $(e).data("id_atributo");
        _.each(self.model.get("atributos"),function(atr){
          if (atr.id_atributo == id_atributo) {
            if (atr.no_aplica == 1) {
              $(e).attr("disabled","disabled");
            } else { 
              if ($(e).is("select")) $(e).val(atr.value_id);
              else $(e).val(atr.value_name);
            }
          }
        });
      })
    },

    agregar_usuario : function() {
      var usuario = $("#buscar_usuarios option:selected").val();
      var nombre = $("#buscar_usuarios option:selected").text();
      var categoria = $("#buscar_categoria option:selected").val();
      var categoria_nombre = $("#buscar_categoria option:selected").text();

      var tr = "<tr data-id='"+usuario+"' data-id_categoria='"+categoria+"'>";
      tr+="<td></td>";
      tr+="<td>"+nombre+"</td>";
      tr+="<td>"+categoria_nombre+"</td>";
      tr+="<td><i class='fa fa-times eliminar_usuario text-danger cp'></i></td>";
      tr+="</tr>";

      $("#usuario_entrena_tabla tbody").append(tr);

    },

    eliminar_usuario : function(e) {
      $(e.currentTarget).parents("tr").remove();
    }, 

    no_aplica_atributo: function(e) {
      var at = $(e.currentTarget).parent().parent().find(".atributo_meli");
      if ($(at).is(":disabled")) {
        $(at).removeAttr("disabled");
      } else {
        $(at).val("");
        $(at).attr("disabled","disabled");
      }
    },


    // Abre el dialogo para cargar una nueva propiedad
    nueva_propiedad: function() {
      var self = this;
      var nombre = prompt("Nombre de la propiedad:");
      if (!nombre) return;
      var prop = new app.models.ArticuloPropiedad({
        "nombre":nombre,
      });
      prop.save({},{
        "success":function() {
          self.cargar_propiedades();
        }
      });
    },

    get_color_propiedades:function() {
      var j = this.$("#articulo_propiedades > div").length;
      if (j==0) return "success";
      else if (j==1) return "primary";
      else if (j==2) return "info";
      else if (j==3) return "danger";
      else if (j==4) return "warning";
      else if (j==5) return "dark";
      else return "default";
    },

    cargar_propiedades:function() {
      var that = this;
      $.ajax({
        "url":"articulos_propiedades/",
        "dataType":"json",
        "success":function(r) {
          articulos_propiedades = r.results;
          that.render_propiedades();
        }
      });
    },

    render_propiedades:function() {
      this.$("#articulo_propiedades").empty();
      console.log(articulos_propiedades);
      for(var i=0;i<articulos_propiedades.length;i++) {
        var propiedad = articulos_propiedades[i];

        propiedad.seleccionadas = new Array();
        for(j=0;j<this.propiedades.length;j++) {
          var p = this.propiedades[j];
          if (p.id == propiedad.id) {
            for(k=0;k<p.opciones.length;k++) {
              var opc = p.opciones[k];
              propiedad.seleccionadas.push(opc.nombre);
            }
          }
        }
        
        propiedad.color = this.get_color_propiedades();
        var a = new app.views.ArticuloPropiedadItemView({
          "model":new app.models.AbstractModel(propiedad),
        });
        this.$("#articulo_propiedades").append(a.el);
      }
    },

    cambiar_opcion: function(e) {
      var self = this;
      var propiedades = new Array();
      this.$(".opciones").each(function(i,elem){
        var opciones = new Array();
        $(elem).find("option:selected").each(function(j,o){
          opciones.push({
            "nombre":filename($(o).val()), // Se utiliza para identificar los tags
            "etiqueta":$(o).html(), // Lo que escribio tal cual el usuario
          });
        });
        if (opciones.length>0) {
          // Solo agregamos las propiedades que se hayan seleccionado opciones
          if (propiedades.length == 3) {
            alert("ATENCION: Se pueden combinar como maximo 3 propiedades.");
          } else {
            propiedades.push({
              "id":$(elem).data("id_propiedad"),
              "nombre":$(elem).data("nombre_propiedad"),
              "opciones":opciones
            });
          }
        }
      });
      this.propiedades = propiedades;
      this.render_variantes();
    },

    // Busca la variante dentro del array de "variantes". Si no la encuentra, devuelve un objeto vacio para ser insertado
    buscar_variante: function(nombre_opcion_1,nombre_opcion_2,nombre_opcion_3) {
      var obj = _.find(this.model.get("variantes"),function(e){
        return (e.nombre_opcion_1 == nombre_opcion_1 && e.nombre_opcion_2 == nombre_opcion_2 && e.nombre_opcion_3 == nombre_opcion_3);
      });
      if (typeof obj == "undefined") {
        obj = {
          "path":"",
        }
      }
      return obj;
    },

    // Cuando se edita el STOCK o algun campo de una variante
    guardar_variante: function(e) {
      var campo = $(e.currentTarget).data("campo");
      var value = $(e.currentTarget).val();
      var nombre_opcion_1 = $(e.currentTarget).data("nombre_opcion_1");
      var nombre_opcion_2 = $(e.currentTarget).data("nombre_opcion_2");
      var nombre_opcion_3 = $(e.currentTarget).data("nombre_opcion_3");
      _.each(this.model.get("variantes"),function(j){
        if (j.nombre_opcion_1 == nombre_opcion_1 && j.nombre_opcion_2 == nombre_opcion_2 && j.nombre_opcion_3 == nombre_opcion_3) {
          j[campo] = value;
        }
      });
    },

    // Renderiza la tabla de variantes de articulos
    render_variantes: function() {

      var self = this;
      this.$("#articulo_variantes_tabla tbody").empty();

      // Realizamos las combinaciones
      var p1 = this.propiedades[0];
      var p2 = this.propiedades[1];
      var p3 = this.propiedades[2];

      var variantes = new Array();
      if (typeof p1 != "undefined") {

        for(var i=0;i<p1.opciones.length;i++) {
          var o1 = p1.opciones[i];

          if (typeof p2 != "undefined") {

            for(var j=0;j<p2.opciones.length;j++) {
              var o2 = p2.opciones[j];

              if (typeof p3 != "undefined") {

                for(var k=0;k<p3.opciones.length;k++) {
                  var o3 = p3.opciones[k];
                  var v = this.buscar_variante(o1.nombre,o2.nombre,o3.nombre);
                  variantes.push({
                    "id_propiedad_1":p1.id,
                    "id_propiedad_2":p2.id,
                    "id_propiedad_3":p3.id,
                    "nombre_opcion_1":o1.nombre,
                    "nombre_opcion_2":o2.nombre,
                    "nombre_opcion_3":o3.nombre,
                    "etiqueta_opcion_1":o1.etiqueta,
                    "etiqueta_opcion_2":o2.etiqueta,
                    "etiqueta_opcion_3":o3.etiqueta,
                    "nombre":"<span class='text-success'>"+o1.etiqueta+"</span> - <span class='text-primary'>"+o2.etiqueta+"</span> - <span class='text-info'>"+o3.etiqueta+"</span>",
                    "path":v.path,
                  });
                }

              } else {
                var v = this.buscar_variante(o1.nombre,o2.nombre,"");
                variantes.push({
                  "id_propiedad_1":p1.id,
                  "id_propiedad_2":p2.id,
                  "id_propiedad_3":0,
                  "nombre_opcion_1":o1.nombre,
                  "nombre_opcion_2":o2.nombre,
                  "nombre_opcion_3":"",
                  "etiqueta_opcion_1":o1.etiqueta,
                  "etiqueta_opcion_2":o2.etiqueta,
                  "etiqueta_opcion_3":"",
                  "nombre":"<span class='text-success'>"+o1.etiqueta+"</span> - <span class='text-primary'>"+o2.etiqueta+"</span>",
                  "path":v.path,
                });                                
              }
            }
          } else {
            var v = this.buscar_variante(o1.nombre,"","");
            variantes.push({
              "id_propiedad_1":p1.id,
              "id_propiedad_2":0,
              "id_propiedad_3":0,
              "nombre_opcion_1":o1.nombre,
              "nombre_opcion_2":"",
              "nombre_opcion_3":"",
              "etiqueta_opcion_1":o1.etiqueta,
              "etiqueta_opcion_2":"",
              "etiqueta_opcion_3":"",
              "nombre":"<span class='text-success'>"+o1.etiqueta+"</span>",
              "path":v.path,
            });                        
          }
        }
      }
      this.model.set({"variantes":variantes});            

      // Recorremos las variantes
      var variantes = self.model.get("variantes");
      if (variantes.length > 0) self.$("#articulo_variantes_tabla_cont").show();
      _.each(variantes,function(v){
        v.name = "path_"+v.nombre_opcion_1+"_"+v.nombre_opcion_2+"_"+v.nombre_opcion_3;
        var tr = new app.views.VarianteItemView({
          model: new app.models.AbstractModel(v),
          view: self,
        });
        self.$("#articulo_variantes_tabla tbody").append(tr.el);
      });
    },

    totalizar_stock: function() {
      var self = this;
      var stock = 0;
      self.$("#articulo_variantes_tabla tbody .stock").each(function(i,e){
        stock += parseFloat($(e).val());
      });
      self.$("#articulo_stock").val(stock).trigger("change");
    },

    guardar_paths_variantes: function() {
      var self = this;
      self.$(".path_campo_variante").each(function(i,e){
        if (!isEmpty($(e).val())) {
          var value = $(e).val();
          var nombre_opcion_1 = $(e).data("nombre_opcion_1");
          var nombre_opcion_2 = $(e).data("nombre_opcion_2");
          var nombre_opcion_3 = $(e).data("nombre_opcion_3");
          _.each(self.model.get("variantes"),function(j){
            if (j.nombre_opcion_1 == nombre_opcion_1 && j.nombre_opcion_2 == nombre_opcion_2 && j.nombre_opcion_3 == nombre_opcion_3) {
              if (isEmpty(self.path_primera_variante)) self.path_primera_variante = value;
              j["path"] = value;
            }
          });
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
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
          /*
          var p = images[i];
          if (typeof p.path != "undefined") {
            var path = p.path;
            var activo_web = p.activo_web;
            var activo_meli = p.activo_meli;
          } else {
            var path = p;
            var activo_web = 1;
            var activo_meli = 1;
          }
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='dn filename'>"+path+"</span>";
          li+=" <span class='btn btn-white pull-right eliminar_foto' data-property='images'><i class='fa fa-trash'></i> </span>";
          li+=" <span data-id='images' class='cp m-r-xs btn btn-info pull-right editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          if (typeof ML_ACCESS_TOKEN != undefined && !isEmpty(ML_ACCESS_TOKEN)) {
            li+=" <span class='mt5 mr10 pull-right'><label class='i-checks'><input type='checkbox' class='activo_web' "+((activo_web == 1)?"checked":"")+" value='1'><i></i>Web</label></span>";
            li+=" <span class='mt5 mr10 pull-right'><label class='i-checks'><input type='checkbox' class='activo_meli' "+((activo_meli == 1)?"checked":"")+" value='1'><i></i>Mercadolibre</label></span>";
          }
          li+="</li>";
          this.$("#images_tabla").append(li);
          */
        }                
      }
    },
        
    validar: function() {
      try {
        var self = this;
        var profesionales = new Array();
        $("#usuario_entrena_tabla tbody tr").each(function(f, g){
          var id_usuario = $(g).data('id'); 
          var id_categoria = $(g).data('id_categoria');  
          profesionales.push({
            "id_usuario": id_usuario,
            "id_categoria": id_categoria,
          });
        })
        this.model.set({
          "profesionales":profesionales,
        });
        // Los codigos de barra van todas juntas separadas por ###
        if (self.$("#articulo_codigos_barra").length > 0) {
          var c = self.$("#articulo_codigos_barra").select2("val");
          if (c != null) {
            var cod = c.join("###");
            // Limpiamos el codigo para que no tenga ningun ' o "
            cod = cod.replace(/\'/g,"");
            cod = cod.replace(/\"/g,"");            
            this.model.set({
              "codigo_barra":cod,
            });
          } else {
            this.model.set({
              "codigo_barra":"",
            });            
          }
        }

        if (this.$("#articulo_tipo").length > 0) {
          this.model.set({
            "tipo":self.$("#articulo_tipo").val()
          })
        }

        if (this.$("#articulo_codigo").length > 0 && ID_EMPRESA != 571) {
          var codigo = this.$("#articulo_codigo").val();
          codigo = codigo.replace(/\'/g,"");
          codigo = codigo.replace(/\"/g,"");
          if (isEmpty(codigo)) {
            alert("Por favor ingrese un codigo");
            this.$("#articulo_codigo").focus();
            return false;
          }          
        }

        if (ID_EMPRESA == 868) {
          var precio_central = this.$(".articulo_precio_sucursal_precio_final_dto_531").val();
          var costo_pinamar = this.$(".articulo_precio_sucursal_costo_final_529").val();
          var costo_central = this.$(".articulo_precio_sucursal_costo_final_531").val();
          if (precio_central != costo_pinamar) {
            alert("ATENCION: El precio de venta de CENTRAL es distinto al costo de la sucursal.");
            this.$(".articulo_precio_sucursal_costo_final_529").select();
            return false;
          }
          this.model.set({
            "custom_1":costo_central,
          });
        }

        if (self.$("#articulo_clientes").length > 0) {
          var id_cliente = self.$("#articulo_clientes").val();
          if (id_cliente != 0) {
            this.model.set({
              "clientes":[{
                "id_cliente":id_cliente,
              }]
            });
          }
        }

        // Las caracteristicas van todas juntas separadas por ###
        if (self.$("#articulo_caracteristicas").length > 0) {
          var c = self.$("#articulo_caracteristicas").select2("val");
          if (c != null) {
            this.model.set({
              "caracteristicas":c.join(";;;"),
            });
          }
        }

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

        // Guardamos los paths de las variantes
        this.path_primera_variante = "";
        if (self.model.get("variantes").length > 0) self.guardar_paths_variantes();

        // Guardamos los ingredientes
        if (self.$("#ingredientes_tabla").length > 0) {
          var ingredientes = new Array();
          self.$("#ingredientes_tabla tbody tr").each(function(i,e){
            ingredientes.push({
              "activo":($(e).find("td:eq(0)").find(".checkbox").is(":checked")?1:0),
              "nombre":$(e).find("td:eq(1)").text(),
              "valores":$(e).find("td:eq(2)").text(),
              "adicional":$(e).find("td:eq(3)").text(),
            });
          });
          this.model.set({
            "ingredientes":ingredientes,
          });
        }
        
        // Las etiquetas se tratan como array porque son entidades separadas
        if (self.$("#articulo_etiquetas").length > 0) {
          var c = self.$("#articulo_etiquetas").select2("val");
          if (c != null) this.model.set({ "etiquetas":c });
          else this.model.set({ "etiquetas":[] });
        }

        this.model.set({
          "moneda": ((self.$("#articulo_monedas").length > 0) ? self.$("#articulo_monedas").val() : "1"),
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : self.path_primera_variante),
          "tipo": ((self.$("#articulo_tipo").length > 0) ? self.$("#articulo_tipo").val() : ""),
          "coordinar_envio": ((self.$("#articulo_coordinar_envio").length > 0) ? (self.$("#articulo_coordinar_envio").is(":checked")?1:0) : 0),
          "fragil": ((self.$("#articulo_fragil").length > 0) ? (self.$("#articulo_fragil").is(":checked")?1:0) : 0),
        });

        // Listado de Imagenes
        if ($(this.el).find("#images_tabla").length > 0) { 
          var images = new Array();
          $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
            images.push($(e).text());
          });
          self.model.set({"images":images});
        }

        // Si no existe la imagen unica
        if (this.$("#hidden_path").length == 0 && images.length > 0) {
          var primera_img = images[0];
          this.model.set({
            "path":primera_img,
          });
        }

        // Si estamos editando por algun motivo la fecha de ingreso
        if (this.$("#articulo_fecha_ingreso").length > 0) {
          this.model.set({
            "fecha_ingreso":this.$("#articulo_fecha_ingreso").val(),
          });
        }
        
        // Listado de Articulos Relacionados
        if (this.$("#articulos_tabla_relacionados").length > 0) {
          var relacionados = new Array();
          $(this.el).find("#articulos_tabla_relacionados .list-group-item").each(function(i,e){
            relacionados.push({
              "id":$(e).find(".id").text(),
              "destacado":0,
            });
          });
          self.model.set({"relacionados":relacionados});            
        }
        
        // Arbol de categorias de relacionados
        if (this.$("#articulos_rubros_tree").length > 0) {
          var rubros_relacionados = new Array();
          if ($("#articulos_rubros_tree").length > 0) {
            var rel = $("#articulos_rubros_tree").fancytree("getTree").getSelectedNodes();
            for(var i=0;i<rel.length;i++) {
              var o = rel[i];
              rubros_relacionados.push({
                "id":o.key,
              });
            }
            self.model.set({"rubros_relacionados":rubros_relacionados});
          }
        }
        
        // Texto del articulo
        if (self.$("#articulo_texto").length > 0) {
          var cktext = CKEDITOR.instances['articulo_texto'].getData();
          self.model.set({"texto":cktext});
        }

        // Texto adicional
        if (self.$("#articulo_breve").length > 0) {
          var cktext = CKEDITOR.instances['articulo_breve'].getData();
          self.model.set({"breve":cktext});
        }

        // Si el campo esta habilitado
        if ($("#articulo_costo_neto_inicial").length>0) {
          var dto_prov = $("#articulo_dto_prov").val();
          var costo_neto_inicial = $("#articulo_costo_neto_inicial").val();
          this.model.set({
            "dto_prov":dto_prov,
            "costo_neto_inicial":costo_neto_inicial,
          });
        } else {
          this.model.set({
            "dto_prov":0,
            "costo_neto_inicial":$("#articulo_costo_neto").val(),
          });                    
        }

        if (TIPO_EMPRESA == 4) {
          // Rubro de LA PLATA CONSTRUYE
          var custom_1 = this.$("#articulo_custom_1").val();
          if (isEmpty(custom_1)) {
            alert("Por favor seleccione una categoria para el Portal de La Plata Construye.");
            return false;
          }
          this.model.set({
            "custom_1":this.$("#articulo_custom_1").val(),
          });
        }

        var unidad = ((self.$("#articulo_unidades").length > 0) ? self.$("#articulo_unidades").val() : "U");
        /*
        if (ID_PROYECTO == 10) {
          // Indica si el articulo va a la cocina
          var no_totalizar_reparto = (self.$("#articulo_no_totalizar_reparto").is(":checked") ? 1 : 0);
        } else if (ID_PROYECTO == 2) {
          // Indica el ENVIO GRATIS del articulo
          var no_totalizar_reparto = (self.$("#articulo_no_totalizar_reparto").is(":checked") ? 1 : 0);
        } else {
          var no_totalizar_reparto = (unidad != "U")?1:0;    
        }
        */

        if (self.$("#articulo_codigo").length > 0) {
          var codigo = self.$("#articulo_codigo").val();
          codigo = codigo.replace(/\"/g,"");
          codigo = codigo.replace(/\'/g,"");
          codigo = codigo.replace(/\`/g,"");
          codigo = codigo.replace(/\´/g,"");
        } else {
          var codigo = "";
        }

        if (self.$("#articulo_nombre").length > 0) {
          var nombre = self.$("#articulo_nombre").val();
          nombre = nombre.replace(/\"/g,"");
          nombre = nombre.replace(/\'/g,"");
          nombre = nombre.replace(/\`/g,"");
          nombre = nombre.replace(/\´/g,"");
        } else {
          var nombre = "";
        }

        if (self.$("#articulo_descripcion").length > 0) {
          var descripcion = self.$("#articulo_descripcion").val();
          descripcion = descripcion.replace(/\"/g,"");
          descripcion = descripcion.replace(/\'/g,"");
          descripcion = descripcion.replace(/\`/g,"");
          descripcion = descripcion.replace(/\´/g,"");
        } else {
          var descripcion = "";
        }
        
        this.model.set({
          "nombre":nombre,
          "codigo":codigo,
          "unidad":unidad,
          "usa_stock": ((self.$("#articulo_usa_stock").length > 0) ? (self.$("#articulo_usa_stock").is(":checked")?1:0) : 1),
          "stock": ((self.$("#articulo_stock").length > 0) ? self.$("#articulo_stock").val() : 0),

          "no_totalizar_reparto": ((self.$("#articulo_no_totalizar_reparto").length > 0) ? (self.$("#articulo_no_totalizar_reparto").is(":checked")?1:0) : 1),

          "descripcion": descripcion,
          "id_departamento": ((self.$("#articulo_departamentos_comerciales").length > 0) ? self.$("#articulo_departamentos_comerciales").val() : 0),
          "id_marca": ((self.$("#articulo_marcas").length > 0) ? self.$("#articulo_marcas").val() : 0),
          "id_rubro": ((self.$("#articulo_rubros").length > 0) ? self.$("#articulo_rubros").val() : 0),
          "rubro": ((self.$("#articulo_rubros option:selected").length > 0) ? self.$("#articulo_rubros option:selected").val() : 0),
          "id_promocion": ((self.$("#articulo_promociones option:selected").length > 0) ? self.$("#articulo_promociones option:selected").val() : 0),
          
          "precio_neto": ((self.$("#articulo_precio_neto").length > 0) ? self.$("#articulo_precio_neto").val() : 0),
          "precio_final": ((self.$("#articulo_precio_final").length > 0) ? self.$("#articulo_precio_final").val() : 0),
          "porc_bonif": ((self.$("#articulo_porc_bonif").length > 0) ? self.$("#articulo_porc_bonif").val() : 0),
          "precio_final_dto": ((self.$("#articulo_precio_final_dto").length > 0) ? self.$("#articulo_precio_final_dto").val() : 0),

          "precio_neto_2": ((self.$("#articulo_precio_neto_2").length > 0) ? self.$("#articulo_precio_neto_2").val() : 0),
          "precio_final_2": ((self.$("#articulo_precio_final_2").length > 0) ? self.$("#articulo_precio_final_2").val() : 0),
          "porc_bonif_2": ((self.$("#articulo_porc_bonif_2").length > 0) ? self.$("#articulo_porc_bonif_2").val() : 0),
          "precio_final_dto_2": ((self.$("#articulo_precio_final_dto_2").length > 0) ? self.$("#articulo_precio_final_dto_2").val() : 0),

          "precio_neto_3": ((self.$("#articulo_precio_neto_3").length > 0) ? self.$("#articulo_precio_neto_3").val() : 0),
          "precio_final_3": ((self.$("#articulo_precio_final_3").length > 0) ? self.$("#articulo_precio_final_3").val() : 0),
          "porc_bonif_3": ((self.$("#articulo_porc_bonif_3").length > 0) ? self.$("#articulo_porc_bonif_3").val() : 0),
          "precio_final_dto_3": ((self.$("#articulo_precio_final_dto_3").length > 0) ? self.$("#articulo_precio_final_dto_3").val() : 0),

          "precio_neto_4": ((self.$("#articulo_precio_neto_4").length > 0) ? self.$("#articulo_precio_neto_4").val() : 0),
          "precio_final_4": ((self.$("#articulo_precio_final_4").length > 0) ? self.$("#articulo_precio_final_4").val() : 0),
          "porc_bonif_4": ((self.$("#articulo_porc_bonif_4").length > 0) ? self.$("#articulo_porc_bonif_4").val() : 0),
          "precio_final_dto_4": ((self.$("#articulo_precio_final_dto_4").length > 0) ? self.$("#articulo_precio_final_dto_4").val() : 0),

          "precio_neto_5": ((self.$("#articulo_precio_neto_5").length > 0) ? self.$("#articulo_precio_neto_5").val() : 0),
          "precio_final_5": ((self.$("#articulo_precio_final_5").length > 0) ? self.$("#articulo_precio_final_5").val() : 0),
          "porc_bonif_5": ((self.$("#articulo_porc_bonif_5").length > 0) ? self.$("#articulo_porc_bonif_5").val() : 0),
          "precio_final_dto_5": ((self.$("#articulo_precio_final_dto_5").length > 0) ? self.$("#articulo_precio_final_dto_5").val() : 0),

          "precio_neto_6": ((self.$("#articulo_precio_neto_6").length > 0) ? self.$("#articulo_precio_neto_6").val() : 0),
          "precio_final_6": ((self.$("#articulo_precio_final_6").length > 0) ? self.$("#articulo_precio_final_6").val() : 0),
          "porc_bonif_6": ((self.$("#articulo_porc_bonif_6").length > 0) ? self.$("#articulo_porc_bonif_6").val() : 0),
          "precio_final_dto_6": ((self.$("#articulo_precio_final_dto_6").length > 0) ? self.$("#articulo_precio_final_dto_6").val() : 0),

          "id_tipo_alicuota_iva": ((self.$("#articulo_tipos_alicuotas_iva").length > 0) ? self.$("#articulo_tipos_alicuotas_iva").val() : 0),
          "seo_title": ((self.$("#articulo_seo_title").length > 0) ? self.$("#articulo_seo_title").val() : ""),
          "seo_description": ((self.$("#articulo_seo_description").length > 0) ? self.$("#articulo_seo_description").val() : ""),
          "seo_keywords": ((self.$("#articulo_seo_keywords").length > 0) ? self.$("#articulo_seo_keywords").val() : ""),
        });

        if (control.check("proveedores")>0) {
          // Guardamos los proveedores
          var proveedores = new Array();
          $("#proveedores_tabla tbody tr").each(function(i,e){
            
            var cod_prov = $(e).find("td:eq(1)").html();
            cod_prov = cod_prov.replace(/\"/g,"");
            cod_prov = cod_prov.replace(/\'/g,"");
            cod_prov = cod_prov.replace(/\`/g,"");
            cod_prov = cod_prov.replace(/\´/g,"");

            var nombre_prov = $(e).find("td:eq(0)").html();
            nombre_prov = nombre_prov.replace(/\"/g,"");
            nombre_prov = nombre_prov.replace(/\'/g,"");
            nombre_prov = nombre_prov.replace(/\`/g,"");
            nombre_prov = nombre_prov.replace(/\´/g,"");

            proveedores.push({
              "id_proveedor": $(e).data("id"),
              "codigo": cod_prov,
              "nombre": nombre_prov,
            });
          });
          this.model.set({"proveedores":proveedores});
        }  

        if (this.$("#articulos_tabla_componentes").length > 0) {
          var componentes = new Array();
          this.$("#articulos_tabla_componentes tbody tr").each(function(i,e){
            var id_articulo_componente = $(e).data("id");
            var cantidad = $(e).find("td:eq(1)").html();
            componentes.push({
              "id_articulo_componente":id_articulo_componente,
              "cantidad":cantidad,
            })
          });
          this.model.set({"componentes":componentes});
        }

        if (control.check("marcas_vehiculos")>0) {
          // Guardamos los marcas_vehiculos
          var marcas_vehiculos = new Array();
          $("#marcas_vehiculos_tabla tbody tr").each(function(i,e){
            marcas_vehiculos.push({
              "id_marca_vehiculo": $(e).data("id"),
              "modelo": $(e).find("td:eq(1)").html(),
            });
          });
          this.model.set({"marcas_vehiculos":marcas_vehiculos});
        }     

        // Si tenemos una tabla de costos de acuerdo a las sucursales
        if (this.$("#articulo_costos_sucursales").length > 0) {
          var precios = new Array();
          _.each(this.precios_sucursales,function(e){
            precios.push(e.toJSON());
          });
          this.model.set({
            "precios_sucursales":precios,
          });
        }   


        // Si tenemos campos con atributos especiales
        if (this.$(".atributo_meli").length > 0) {
          var atributos = new Array();

          // En los neumaticos, las medidas que estan por separadas se juntan en un solo campo
          // TODO: Rocha tiene servicios, no controlar las medidas
          if (TIPO_EMPRESA == 1 && ID_EMPRESA != 614) {
            var ancho = this.$("#articulo_custom_7").val();
            if (isEmpty(ancho)) {
              alert("Por favor ingrese una medida.");
              this.$("#articulo_custom_7").focus();
              return false;
            }
            var perfil = this.$("#articulo_custom_8").val();
            if (isEmpty(perfil)) {
              alert("Por favor ingrese una medida.");
              this.$("#articulo_custom_8").focus();
              return false;
            }
            var rodado = this.$("#articulo_custom_9").val();
            if (isEmpty(rodado)) {
              alert("Por favor ingrese una medida.");
              this.$("#articulo_custom_9").focus();
              return false;
            }
            atributos.push({
              "id_atributo":"TIRE_SIZE",
              "value_name":ancho+"/"+perfil+" R"+rodado,
            });
          }

          var ocurrio_error = false;
          this.$(".atributo_meli").each(function(i,e){
            var id_atributo = $(e).data("id_atributo");
            var tipo = (typeof $(e).data("type") != "undefined") ? $(e).data("type") : "";
            if ($(e).is(":disabled")) {
              atributos.push({
                "id_atributo":id_atributo,
                "no_aplica":1,
                "tipo":tipo,
              });
            } else {
              var valor = $(e).val();  
              if (!isEmpty(valor)) {

                if (tipo == "number") {
                  valor = parseFloat(valor);
                  if (isNaN(valor)) {
                    alert("Por favor ingrese un numero");
                    $(e).select();
                    ocurrio_error = true;
                    return false;
                  }
                }

                if ($(e).is("select")) {
                  atributos.push({
                    "id_atributo":id_atributo,
                    "value_id":valor,
                    "value_name":$(e).find("option:selected").text(),
                    "no_aplica":0,
                    "tipo":tipo,
                  });
                } else {
                  atributos.push({
                    "id_atributo":id_atributo,
                    "value_name":valor,
                    "no_aplica":0,
                    "tipo":tipo,
                  });
                }                
              }
            }
          });
          this.model.set({
            "atributos":atributos,
          });
        }
        if (ocurrio_error) return false;


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
              if (ID_EMPRESA == 134 || ID_EMPRESA == 86 || CACHE_ARTICULOS == 0) location.href="app/#articulos";
              else location.reload();
            }
          }
        });
      }      
    },        
  
  });
})(app);


// =================================================

(function ( models ) {

  models.ArticuloPropiedad = Backbone.Model.extend({
    urlRoot: "articulos_propiedades/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      opciones: [],
    }
  });
        
})( app.models );

(function (collections, model, paginator) {
  collections.ArticulosPropiedades = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "articulos_propiedades/"
    }
  });
})( app.collections, app.models.ArticuloPropiedad, Backbone.Paginator);


(function ( views, models ) {

  views.ArticuloPropiedadItemView = app.mixins.View.extend({
    template: _.template($("#articulos_propiedades_item_template").html()),
    className: "dtr propiedad_fila",
    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = this.model.toJSON();
      obj.edicion = (this.options.permiso > 1);
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));

      this.$(".opciones").select2({
        tags: true,
      });
      return this;
    },
  });

})(app.views, app.models);


(function ( views, models ) {
  views.VarianteItemView = app.mixins.View.extend({
    template: _.template($("#articulos_variante_item_template").html()),
    tagName: "tr",
    myEvents: {
      "change .stock":function() {
        if (this.view != null) this.view.totalizar_stock();
      },
    },
    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.view = (typeof options.view != "undefined") ? options.view : null;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app.views, app.models);


(function ( views, models ) {
  views.ArticuloEdicionRapidaView = app.mixins.View.extend({
    template: _.template($("#articulos_edicion_rapida_template").html()),
    myEvents: {
      "click .guardar":"guardar",
      "change #articulo_edicion_rapida_precio_final":"editar_precio_final",
      "change #articulo_edicion_rapida_porc_bonif":"editar_precio_final",
      "click .cerrar":function() {
        $(".modal:last").trigger('click');
      },
    },
    initialize: function(options) {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    editar_precio_final:function() {
      var precio_final = $("#articulo_edicion_rapida_precio_final").val();
      var porc_bonif = $("#articulo_edicion_rapida_porc_bonif").val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      $("#articulo_edicion_rapida_precio_final_dto").val(Number(precio_final_dto).toFixed(2));
      this.model.set({
      });
    },
    validar: function() {
      return true;
    },
    guardar:function() {
      if (this.validar()) {
        var precio_final = $("#articulo_edicion_rapida_precio_final").val();
        var porc_bonif = $("#articulo_edicion_rapida_porc_bonif").val();
        var precio_final_dto = $("#articulo_edicion_rapida_precio_final_dto").val();
        this.model.save({
          "precio_final":precio_final,
          "porc_bonif":porc_bonif,
          "precio_final_dto":precio_final_dto,
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              $(".modal:last").trigger('click');
            }
          }
        });
      }      
    }, 
  });
})(app.views, app.models);



(function ( views, models ) {
  views.ArticuloMercadoLibreView = app.mixins.View.extend({
    template: _.template($("#articulo_mercado_libre_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .predecir_categoria":"predecir_categoria",
      "click .ir_paso_1":"ir_paso_1",
      "click .ir_paso_2":"ir_paso_2",
      "click .ir_paso_3":"ir_paso_3",
      "click .guardar_paso_4":"guardar_paso_4",
      "click .publicar":"publicar",
      "click #articulo_mercado_libre_paso_1_link":function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      },
      "click #articulo_mercado_libre_paso_2_link":function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      },
      "click #articulo_mercado_libre_paso_3_link":function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.multiple = (typeof options.multiple != "undefined") ? options.multiple : false;
      this.render();
      this.predijo_categoria = false;
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();            
      $(this.el).find("#images_meli_tabla").sortable();
    },
    render: function() {
      var self = this;
      var obj = this.model.toJSON();
      obj.multiple = this.multiple;
      $(this.el).html(this.template(obj));
      return this;
    },
    ir_paso_1: function() {
      this.$(".nav-tabs li").removeClass('active');
      this.$("#articulo_mercado_libre_paso_1_link").parent().addClass("active");
      this.$(".tab-pane.active").removeClass('active');
      this.$("#articulo_mercado_libre_tab1").addClass('active');
    },
    ir_paso_2: function() {
      var self = this;
      if (this.multiple) {
        $.ajax({
          "url":"https://api.mercadolibre.com/sites/MLA/",
          "dataType":"json",
          "type":"get",
          "data":{
            "access_token":ML_ACCESS_TOKEN,
          },
          "success":function(r) {
            var modelo = new app.models.AbstractModel({
              "categories":r.categories,
              "nivel":0,
              "selected":0,
            });
            var v = new app.views.ArticuloMercadoLibreCategoriaView({
              container: self,
              model: modelo,
            });
            self.$(".loading_grande").hide();
            self.agregar_categoria_contenedor(v.el);

            self.$(".nav-tabs li").removeClass('active');
            self.$("#articulo_mercado_libre_paso_2_link").parent().addClass("active");
            self.$(".tab-pane.active").removeClass('active');
            self.$("#articulo_mercado_libre_tab2").addClass('active');

          },
        });
      } else {

        // Controlamos que haya ingresado los valores
        var titulo = this.$("#articulo_mercado_libre_titulo_meli").val();
        if (isEmpty(titulo)) {
          alert("Por favor ingrese un titulo");
          this.$("#articulo_mercado_libre_titulo_meli").focus();
          return;
        }
        var precio = this.$("#articulo_mercado_libre_precio_meli").val();
        if (isEmpty(precio) || precio == 0) {
          alert("Por favor ingrese un precio");
          this.$("#articulo_mercado_libre_precio_meli").focus();
          return;
        }
        var texto = this.$("#articulo_mercado_libre_texto_meli").val();
        if (isEmpty(texto)) {
          alert("Por favor ingrese un texto");
          this.$("#articulo_mercado_libre_texto_meli").focus();
          return;
        }

        this.$(".nav-tabs li").removeClass('active');
        this.$("#articulo_mercado_libre_paso_2_link").parent().addClass("active");
        this.$(".tab-pane.active").removeClass('active');
        this.$("#articulo_mercado_libre_tab2").addClass('active');

        // Si no esta definida una categoria, intentamos predecirla primero
        if (isEmpty(this.model.get("categoria_meli"))) {
          if (!this.predijo_categoria) this.predecir_categoria();
        } else {
          // TODO: Poner el arbol de categorias desde la categoria actual
          $.ajax({
            "url":"/sistema/articulos_meli/function/get_categorias/"+self.model.get("categoria_meli"),
            "dataType":"json",
            "success":function(r) {
              if (typeof r.categorias != "undefined") {
                for(var i=0; i<r.categorias.length; i++) {
                  var cat = r.categorias[i];
                  var v = new app.views.ArticuloMercadoLibreCategoriaView({
                    model: new app.models.AbstractModel({
                      "categories":cat.children,
                      "selected":cat.selected,
                      "nivel":i,
                    }),
                    container: self,
                  });
                  self.agregar_categoria_contenedor(v.el);
                }
                self.agregar_final(r.id);
                self.$(".loading_grande").hide();
              }
            },
          });

        }        
      }
    },
    ir_paso_3: function(e) {

      // Validamos si la categoria a la que estamos publicando debe tener algunos atributos especiales
      var self = this;
      var id_categoria = $(e.currentTarget).data("id_categoria");
      this.model.set({
        "categoria_meli":id_categoria,
      });

      $.ajax({
        "url":"articulos_meli/function/controlar_atributos/"+self.model.id+"/"+id_categoria+"/",
        "dataType":"json",
        "success":function(res) {
          if (res.error == 1) {
            alert(res.mensaje);
          } else {
            if (res.atributos.length == 0) {
              self.$(".nav-tabs li").removeClass('active');
              self.$("#articulo_mercado_libre_paso_3_link").parent().addClass("active");
              self.$(".tab-pane.active").removeClass('active');
              self.$("#articulo_mercado_libre_tab3").addClass('active');              
            } else {
              // Vamos al paso 4 para completar la ficha tecnica
              self.ir_paso_4(res.atributos);
            }
          }
        }
      });
    },

    ir_paso_4: function(atributos) {
      var self = this;

      // Mostramos la ficha tecnica
      self.$(".nav-tabs li").removeClass('active');
      self.$("#articulo_mercado_libre_paso_4_link").show();
      self.$("#articulo_mercado_libre_paso_4_link").parent().addClass("active");
      self.$(".tab-pane.active").removeClass('active');
      self.$("#articulo_mercado_libre_tab4").addClass('active');

      // Separamos los atributos en obligatorios y no obligatorios
      var obligatorios = new Array();
      var no_obligatorios = new Array();
      for(var i=0;i<atributos.length;i++) {
        var atributo = atributos[i];
        if (typeof atributo.tags.required != "undefined") {
          obligatorios.push(atributo);
        } else {
          no_obligatorios.push(atributo);
        }
      }

      this.$("#articulo_mercado_libre_paso4_container").empty();

      // Cargamos lo que tenemos guardado en el modelo
      var atributos_meli = self.model.get("atributos_meli");
      if (isEmpty(atributos_meli)) {
        // TODO: Puede ser que sea un arreglo de gomerias y este en otro campo
        if (!isEmpty(self.model.get("seo_description"))) {
          try {
            console.log(self.model.get("seo_description"));
            atributos_meli = JSON.parse(self.model.get("seo_description"));
          } catch(e) {
            atributos_meli = new Array();
          }
        } else atributos_meli = new Array();
      }
      else atributos_meli = JSON.parse(atributos_meli);

      // Renderizamos los obligatorios primero
      if (obligatorios.length > 0) {
        var subtitulo = "<div class='fs16 bold mb20'>Datos Obligatorios:</div>";
        this.$("#articulo_mercado_libre_paso4_container").append(subtitulo);
        var row = $("<div class='row mb20 obligatorios_container'/>");
        for(var i=0;i<obligatorios.length;i++) {
          var atributo = obligatorios[i];

          // [{"id":"BRAND","value_id":"505800","value_name":"Ares"},{"id":"MODEL","value_name":"AC58"}]

          // Buscamos si ya estaba cargado anteriormente
          atributo.selected_value = "";
          atributo.selected_id = "";
          for(var j=0;j<atributos_meli.length;j++) {
            var atributo_meli = atributos_meli[j];
            if (atributo_meli.id == atributo.id) {
              atributo.selected_value = atributo_meli.value_name;
              if (typeof atributo_meli.value_id != "undefined") atributo.selected_id = atributo_meli.value_id;
              break;
            }
          }   
          var at = new app.views.AtributoMeliView({
            "model":new app.models.AbstractModel(atributo),
          });
          row.append(at.el);
        }
        this.$("#articulo_mercado_libre_paso4_container").append(row);
      }

      // Renderimos el resto de los atributos
      if (no_obligatorios.length > 0) {
        var subtitulo = "<div class='fs16 bold mb20'>Otros Datos:</div>";
        this.$("#articulo_mercado_libre_paso4_container").append(subtitulo);
        var row = $("<div class='row mb20 no_obligatorios_container'/>");
        for(var i=0;i<no_obligatorios.length;i++) {
          var atributo = no_obligatorios[i];

          // Buscamos si ya estaba cargado anteriormente
          atributo.selected_value = "";
          atributo.selected_id = "";
          for(var j=0;j<atributos_meli.length;j++) {
            var atributo_meli = atributos_meli[j];
            if (atributo_meli.id == atributo.id) {
              atributo.selected_value = atributo_meli.value_name;
              if (typeof atributo_meli.value_id != "undefined") atributo.selected_id = atributo_meli.value_id;
              break;
            }
          }          
          var at = new app.views.AtributoMeliView({
            "model":new app.models.AbstractModel(atributo),
          });
          row.append(at.el);
        }
        this.$("#articulo_mercado_libre_paso4_container").append(row);
      }

    },

    // Con esto guardamos todos los atributos dinamicos de la ficha tecnica
    // Y pasamos al paso 3 (FINAL)
    guardar_paso_4: function() {
      var self = this;

      // Recorremos todos los atributos
      var atributos = new Array();
      var error = false;

      $(".obligatorios_container .atributo-meli").each(function(i,e){
        var value = $(e).val();
        if (isEmpty(value) || value == "0") {
          alert("Por favor ingrese un valor.");
          $(e).focus();
          error = true;
          return false;
        }
      });
      if (error) return;

      // En este punto completo todos los valores obligatorios
      // armamos el array de atributos para guardar
      $(".atributo-meli").each(function(i,e){
        var name = $(e).attr("name");
        var value = $(e).val();
        if (!isEmpty(value) && value != 0) {
          if ($(e).is("select")) {
            var text = $(e).find("option:selected").text();
            var at = {
              "id":name,
            }
            // Si el text coincide con el value, es un elemento nuevo
            if (value == text) {
              at.value_id = value;
            } else {
              at.value_id = value;
              at.value_name = text;
            }
            atributos.push(at);
          } else {
            atributos.push({
              "id":name,
              "value_name":value,
            });
          }
        }
      });
      this.model.set({"atributos_meli":JSON.stringify(atributos)});

      self.$(".nav-tabs li").removeClass('active');
      self.$("#articulo_mercado_libre_paso_3_link").parent().addClass("active");
      self.$(".tab-pane.active").removeClass('active');
      self.$("#articulo_mercado_libre_tab3").addClass('active');
    },

    render_tabla_fotos: function() {
      var images_meli = this.model.get("images_meli");
      this.$("#images_meli_tabla").empty();
      if (images_meli.length == 0) {
        this.$("#images_meli_container").removeClass('tiene');
      } else {
        this.$("#images_meli_container").addClass('tiene');
        for(var i=0;i<images_meli.length;i++) {
          var path = images_meli[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images_meli'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images_meli' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_meli_tabla").append(li);
        }                
      }
    },

    /*
    cargar_categoria: function(id_categoria) {
      var self = this;
      $.ajax({
        "url":"/sistema/articulos_meli/function/get_categorias/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_categoria":id_categoria,
        },
        "success":function(r) {
          if (typeof r.body != "undefined") {
            var modelo = new app.models.AbstractModel({
              "categories":r.body,
              "nivel":self.nivel,
            });
            var v = new app.views.ArticuloMercadoLibreCategoriaView({
              container: self,
              model: modelo,
            });
            self.agregar_categoria_contenedor(v.el);
          }
        },
      });
    },
    */
    cargar_tipos_publicaciones: function() {
      $.ajax({
        "url":"https://api.mercadolibre.com/sites/MLA/listing_types/",
        "dataType":"json",
        "success":function(res) {
          $("#articulo_mercado_libre_tipo_publicacion").empty();
          for(var i=0; i<res.length; i++) {
            var r = res[i];
            var option = "<option value='"+r.id+"'>";
            option+=r.name;
            option+="</option>";
            $("#articulo_mercado_libre_tipo_publicacion").append(option);
          }
        }
      })
    },
    cambiar_categoria: function(view,model) {

      var self = this;
      var nivel = $(view.el).find("select").data("nivel");
      var id_categoria = $(view.el).find("select").val();
      
      // Eliminamos los selects que se encuentran a la derecha
      $(view.el).nextAll().remove();

      // Vamos a buscar los hijos de la nueva categoria seleccionada
      $.ajax({
        "url":"/sistema/articulos_meli/function/get_categorias_hijas/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_categoria":id_categoria,
        },
        "success":function(r) {
          // Si tiene categorias hijas
          if (r.children.length > 0) {
            var modelo = new app.models.AbstractModel({
              "categories":r.children,
              "nivel":nivel+1,
              "selected":"",
            });
            var v = new app.views.ArticuloMercadoLibreCategoriaView({
              container: self,
              model: modelo,
            });
            self.agregar_categoria_contenedor(v.el);
          } else {
            self.agregar_final(id_categoria);
          }
        },
      });
    },
    predecir_categoria: function() {
      var self = this;
      var titulo = this.$("#articulo_mercado_libre_titulo_meli").val();
      if (isEmpty(titulo)) {
        alert("Por favor escriba un titulo para poder predecir la categoria");
        this.$("#articulo_mercado_libre_titulo_meli").select();
        return;
      }
      self.$("#articulo_mercado_libre_categorias").empty();
      $.ajax({
        "url":"/sistema/articulos_meli/function/predecir_categoria/",
        "dataType":"json",
        "type":"post",
        "data":{
          "titulo":titulo,
        },
        "success":function(r) {
          if (typeof r.categorias != "undefined") {
            for(var i=0; i<r.categorias.length; i++) {
              var cat = r.categorias[i];
              var v = new app.views.ArticuloMercadoLibreCategoriaView({
                model: new app.models.AbstractModel({
                  "categories":cat.children,
                  "selected":cat.selected,
                  "nivel":i,
                }),
                container: self,
              });
              self.agregar_categoria_contenedor(v.el);
            }
            self.agregar_final(r.id);
            self.$(".loading_grande").hide();
            self.predijo_categoria = true;
          }
        }
      })
    },
    agregar_final: function(id_categoria) {
      // Si no tiene categorias hijas, es la ultima seleccionada
      var sig = "<div class='categoria_meli categoria_meli_ok'>";
      sig+= "<div>";
      sig+= "<div class='categoria_meli_ok_icon'><i class='fa fa-check'></i></div>";
      sig+= "<div class='h3'>¡Listo!</div>";
      sig+= "<div><button data-id_categoria="+id_categoria+" class='btn btn-success mt20 mb20 ir_paso_3'>Siguiente</button></div>";
      sig+= "</div>";
      sig+= "</div>";
      this.agregar_categoria_contenedor(sig);
    },
    agregar_categoria_contenedor: function(v) {
      this.$("#articulo_mercado_libre_categorias").append(v);
      var ancho = 200;
      this.$("#articulo_mercado_libre_categorias > div").each(function(index, el) {
        ancho += $(el).outerWidth();
      });
      this.$("#articulo_mercado_libre_categorias").css("width",ancho);
      this.$("#articulo_mercado_libre_categorias").parent()[0].scrollLeft = ancho;
    },
    validar: function() {
      var self = this;
      var list_type_id = $("#articulo_mercado_libre_tipo_publicacion").val();
      if (list_type_id == 0) {
        alert("Seleccione un tipo de publicacion");
        return false;
      }
      var formas_pago = new Array();
      $(".articulo_mercado_libre_forma_pago:checked").each(function(i,e){
        formas_pago.push($(e).val());
      });
      if (formas_pago.length == 0) {
        alert("Por favor seleccione al menos una forma de pago");
        return false;
      }

      if (!this.multiple) {
        var forma_envio = this.$("#articulo_mercado_libre_forma_envio").val();
        // Si vamos a usar MERCADOENVIO, pero no tiene PESO o DIMENSIONES
        if (forma_envio == "me2" && (self.model.get("peso") == 0 || self.model.get("ancho") == 0 || self.model.get("alto") == 0 || self.model.get("profundidad") == 0)) {
          alert("Para compartir un producto en MercadoLibre, es necesario que el articulo tenga configurado el peso y sus medidas. Ingrese a los datos del articulo y cargue esos valores dentro de la subseccion de Envio.");
          return false;
        }        
      }

      // Listado de Imagenes
      if ($(this.el).find("#images_meli_tabla").length > 0) {
        var images_meli = new Array();
        $(this.el).find("#images_meli_tabla .list-group-item .filename").each(function(i,e){
          images_meli.push($(e).text());
        });
        this.model.set({
          "images_meli":images_meli,  
        });        
      }

      this.model.set({
        "list_type_id":list_type_id,
        "forma_envio_meli":self.$("#articulo_mercado_libre_forma_envio").val(),
        "forma_pago_meli":formas_pago.join(","),
        "retiro_sucursal_meli":(self.$("#articulo_mercado_libre_retiro_sucursal").is(":checked")?1:0),
      });
      return true;
    }, 
    publicar: function() {
      // Primero guardamos el modelo, y luego llamamos a publicar el producto
      var self = this;
      if (!this.validar()) return;
      if (this.multiple) {
        if (window.articulos_marcados.length == 0) return;
        var art_marcados = window.articulos_marcados.join(",");
        var images_meli = self.model.get("images_meli").join(";;;");
        // Si estamos publicando multiples productos
        $.ajax({
          "url":"articulos_meli/function/publicar_multiple/",
          "type":"post",
          "data":{
            "ids":art_marcados,
            "categoria_meli":self.model.get("categoria_meli"),
            "list_type_id":self.model.get("list_type_id"),
            "forma_envio_meli":self.model.get("forma_envio_meli"),
            "forma_pago_meli":self.model.get("forma_pago_meli"),
            "retiro_sucursal_meli":self.model.get("retiro_sucursal_meli"),
            "images_meli":images_meli,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) {
              location.reload();
            } else if (r.error == 1) {
              if (r.mensaje == "address_pending") {
                alert("ERROR: Falta configurar la direccion en su usuario de MercadoLibre. Puede hacerlo desde Mi Cuenta / Mis Datos / Domicilios.");
              } else {
                alert(r.mensaje);
              }
            }
          },
        });
      } else {
        // Estamos publicando un producto en particular
        this.model.save({},{
          "success":function(){
            var that = self;
            $.ajax({
              "url":"articulos_meli/function/publicar/",
              "type":"post",
              "data":{
                "id_articulo":self.model.id,
              },
              "dataType":"json",
              "success":function(r) {
                if (r.error == 0) {
                  window.open(r.link,"_blank");
                  location.reload();
                } else if (r.error == 1) {
                  if (r.mensaje == "address_pending") {
                    alert("ERROR: Falta configurar la direccion en su usuario de MercadoLibre. Puede hacerlo desde Mi Cuenta / Mis Datos / Domicilios.");
                  } else {
                    alert(r.mensaje);
                  }
                }
              },
            })
          }
        });
      }
    },
  });
})(app.views, app.models);

// ATRIBUTOS PARA LA FICHA TECNICA
(function ( views, models ) {
  views.AtributoMeliView = app.mixins.View.extend({
    template: _.template($("#articulo_mercado_libre_atributo_template").html()),
    myEvents: {
    },
    className: "col-md-4",
    initialize: function(options) {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      if (this.$("select").length > 0) {
        this.$("select").select2({"tags":true});
      }
      return this;
    },
  });
})(app.views, app.models);


(function ( views, models ) {
  views.ArticuloMercadoLibreCategoriaView = app.mixins.View.extend({
    template: _.template($("#articulo_mercado_libre_categoria_template").html()),
    myEvents: {
      "change .categoria_mercado_libre":function(){
        this.container.cambiar_categoria(this,this.model);
      },
    },
    className: "categoria_meli",
    initialize: function(options) {
      _.bindAll(this);
      this.container = options.container;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app.views, app.models);


(function ( models ) {

  models.ArticuloPrecioSucursal = Backbone.Model.extend({
    urlRoot: "articulos",
    defaults: {
      id_sucursal: 0,
      sucursal: "",
      fecha_mov: "",
      id_tipo_alicuota_iva: 5,
      porc_iva: 21,
      costo_iva: 0,
      costo_neto: 0,
      costo_final: 0,
      porc_ganancia: 0,
      ganancia: 0,
      precio_neto: 0,
      precio_final: 0,
      porc_bonif: 0,
      precio_final_dto: 0,
      moneda: "",
      costo_neto_inicial: 0,
      dto_prov: 0,
      custom_1: "",
      activo: 1,
      porc_ganancia_2: 0,
      precio_final_2: 0,
      porc_bonif_2: 0,
      precio_final_dto_2: 0,
      porc_ganancia_3: 0,
      precio_final_3: 0,
      porc_bonif_3: 0,
      precio_final_dto_3: 0,
    },
  });
    
})( app.models );

(function ( app ) {

  app.views.ArticuloPrecioSucursalView = app.mixins.View.extend({

    template: _.template($("#articulo_precio_sucursal_template").html()),
        
    myEvents: {

      "click .articulo_precio_nombre_sucursal":function(e) {
        $(e.currentTarget).next(".articulo_precio_info").slideToggle();
        this.collapsed = ($(e.currentTarget).next(".articulo_precio_info").is(":visible") ? 1 : 0);
      },

      "click .abrir_listas":function(e) {
        this.$(".articulo_precio_sucursal_lista_precios_cont").slideToggle();
        this.collapsed_lista_precios = (this.$(".articulo_precio_sucursal_lista_precios_cont").is(":visible") ? 1 : 0);
      },

      "keypress #articulo_precio_sucursal_costo_neto_inicial":function(e){
        if (e.keyCode == 13) {
          var costo_neto_prov = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
          if (isNaN(costo_neto_prov)) costo_neto_prov = 0;
          var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var costo_neto = parseFloat(costo_neto_prov) * ((100 - dto_prov) / 100);
          this.$("#articulo_precio_sucursal_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.calcular_precios_2();
          this.calcular_precios_3();
          this.$("#articulo_precio_sucursal_dto_prov").select();
        }
      },

      "keypress #articulo_precio_sucursal_dto_prov":function(e){
        if (e.keyCode == 13) {
          var costo_neto_prov = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
          if (isNaN(costo_neto_prov)) costo_neto_prov = 0;
          var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var costo_neto = parseFloat(costo_neto_prov) * ((100 - dto_prov) / 100);
          this.$("#articulo_precio_sucursal_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.calcular_precios_2();
          this.calcular_precios_3();
          this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").focus();
        }
      },

      "keydown #articulo_precio_sucursal_tipos_alicuotas_iva":function(e) {
        if (e.which == 13) {
          e.preventDefault(); e.stopPropagation();
          this.$("#articulo_precio_sucursal_costo_final").select();
        }
      },

      "change #articulo_precio_sucursal_tipos_alicuotas_iva":function() {
        var porc_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje");
        var id_tipo_alicuota_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").val();
        this.model.set({ "porc_iva":porc_iva, "id_tipo_alicuota_iva":id_tipo_alicuota_iva });
        this.calcular_precios();
        this.calcular_precios_2();
        this.calcular_precios_3();
      },
      
      "keypress #articulo_precio_sucursal_costo_neto":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.calcular_precios_2();
          this.calcular_precios_3();
          this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").focus();
        }
      },
      "keypress #articulo_precio_sucursal_iva":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.calcular_precios_2();
          this.calcular_precios_3();
          this.$("#articulo_precio_sucursal_porc_ganancia").select();
        }
      },
      "keypress #articulo_precio_sucursal_porc_ganancia":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#articulo_precio_sucursal_precio_final").select();
        }
      },
      "keypress #articulo_precio_sucursal_porc_ganancia_2":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios_2();
          this.$("#articulo_precio_sucursal_precio_final_2").select();
        }
      },
      "keypress #articulo_precio_sucursal_porc_ganancia_3":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios_3();
          this.$("#articulo_precio_sucursal_precio_final_3").select();
        }
      },

      // Se modifica el COSTO FINAL
      "keypress #articulo_precio_sucursal_costo_final":function(e){
        if (e.keyCode == 13) {
            
          // En base al precio neto, calculamos los costos
          var costo_final = this.$("#articulo_precio_sucursal_costo_final").val();
          var porc_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje");
          var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var id_tipo_alicuota_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").val();
          var costo_neto = costo_final / parseFloat(1+(porc_iva/100));
          var costo_neto_inicial = (costo_neto * 100 / (100-dto_prov));
          var porc_ganancia = this.$("#articulo_precio_sucursal_porc_ganancia").val();
          var costo_iva = costo_neto * (porc_iva / 100);
          var ganancia = costo_final * (porc_ganancia / 100);
          var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
          var precio_final = parseFloat(precio_neto) * (1+(porc_iva/100));
          var porc_bonif = this.$("#articulo_precio_sucursal_porc_bonif").val();
          var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));

          var porc_ganancia_2 = this.$("#articulo_precio_sucursal_porc_ganancia_2").val();
          var precio_final_2 = parseFloat(costo_final) * (1+(porc_ganancia_2/100));
          var porc_bonif_2 = this.$("#articulo_precio_sucursal_porc_bonif_2").val();
          var precio_final_dto_2 = parseFloat(precio_final_2) * (1-(porc_bonif_2 / 100));

          var porc_ganancia_3 = this.$("#articulo_precio_sucursal_porc_ganancia_3").val();
          var precio_final_3 = parseFloat(costo_final) * (1+(porc_ganancia_3/100));
          var porc_bonif_3 = this.$("#articulo_precio_sucursal_porc_bonif_3").val();
          var precio_final_dto_3 = parseFloat(precio_final_3) * (1-(porc_bonif_3 / 100));

          this.model.set({
            "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
            "porc_iva":Number(porc_iva).toFixed(2),
            "dto_prov":Number(dto_prov).toFixed(2),
            "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
            "costo_iva":Number(costo_iva).toFixed(2),
            "costo_neto":Number(costo_neto).toFixed(3),
            "costo_final":Number(costo_final).toFixed(2),
            "porc_ganancia":Number(porc_ganancia).toFixed(4),
            "ganancia":Number(ganancia).toFixed(2),
            "precio_neto":Number(precio_neto).toFixed(2),
            "precio_final":Number(precio_final).toFixed(2),
            "porc_bonif":Number(porc_bonif).toFixed(2),
            "precio_final_dto":Number(precio_final_dto).toFixed(2),

            "porc_ganancia_2":Number(porc_ganancia_2).toFixed(4),
            "precio_final_2":Number(precio_final_2).toFixed(2),
            "porc_bonif_2":Number(porc_bonif_2).toFixed(2),
            "precio_final_dto_2":Number(precio_final_dto_2).toFixed(2),

            "porc_ganancia_3":Number(porc_ganancia_3).toFixed(4),
            "precio_final_3":Number(precio_final_3).toFixed(2),
            "porc_bonif_3":Number(porc_bonif_3).toFixed(2),
            "precio_final_dto_3":Number(precio_final_dto_3).toFixed(2),

          });
          this.render();
          this.actualizar_todos();
          this.$("#articulo_precio_sucursal_porc_ganancia").select();
        }
      },
      
      // Se modifican los precios, se calculan los costos
      "change #articulo_precio_sucursal_precio_final":"editar_precio_final",
      "change #articulo_precio_sucursal_precio_final_2":"editar_precio_final_2",
      "change #articulo_precio_sucursal_precio_final_3":"editar_precio_final_3",
      "keypress #articulo_precio_sucursal_precio_final":function(e){
        if (e.keyCode == 13) this.$("#articulo_precio_sucursal_porc_bonif").select();
      },
      "keypress #articulo_precio_sucursal_precio_final_2":function(e){
        if (e.keyCode == 13) this.$("#articulo_precio_sucursal_porc_bonif_2").select();
      },
      "keypress #articulo_precio_sucursal_precio_final_3":function(e){
        if (e.keyCode == 13) this.$("#articulo_precio_sucursal_porc_bonif_3").select();
      },

      "change .sucursal_activo": function(e){
        this.model.set({
          "activo":($(e.currentTarget).is(":checked")?1:0),
        })
      },

      "change #articulo_precio_sucursal_porc_bonif": function(e){
        var porc_bonif = this.$("#articulo_precio_sucursal_porc_bonif").val();
        if (porc_bonif != 0) this.$("#articulo_precio_sucursal_promociones_cont").slideDown();
        else this.$("#articulo_precio_sucursal_promociones_cont").slideUp();
        this.editar_precio_final();
        this.$("#articulo_precio_sucursal_precio_final_dto").select();
      },
      "change #articulo_precio_sucursal_porc_bonif_2": function(e){
        var porc_bonif_2 = this.$("#articulo_precio_sucursal_porc_bonif_2").val();
        this.editar_precio_final_2();
        this.$("#articulo_precio_sucursal_precio_final_dto_2").select();
      },
      "change #articulo_precio_sucursal_porc_bonif_3": function(e){
        var porc_bonif_3 = this.$("#articulo_precio_sucursal_porc_bonif_3").val();
        this.editar_precio_final_3();
        this.$("#articulo_precio_sucursal_precio_final_dto_3").select();
      },


      // Se modifica el PRECIO FINAL con DESCUENTO
      "change #articulo_precio_sucursal_precio_final_dto":function(e){
        var precio_final_dto = this.$("#articulo_precio_sucursal_precio_final_dto").val();
        var precio_final = this.$("#articulo_precio_sucursal_precio_final").val();
        var porc_bonif = (precio_final > 0) ? (100 - ((precio_final_dto * 100) / precio_final)) : 0;
        this.model.set({
          "porc_bonif":Number(porc_bonif).toFixed(2),
          "precio_final_dto":Number(precio_final_dto).toFixed(2),
        });
        this.render();
        this.actualizar_todos();
      },

      // Se modifica el PRECIO FINAL con DESCUENTO
      "change #articulo_precio_sucursal_precio_final_dto_2":function(e){
        var precio_final_dto_2 = this.$("#articulo_precio_sucursal_precio_final_dto_2").val();
        var precio_final_2 = this.$("#articulo_precio_sucursal_precio_final_2").val();
        var porc_bonif_2 = (precio_final_2 > 0) ? (100 - ((precio_final_dto_2 * 100) / precio_final_2)) : 0;
        if (porc_bonif_2 == 0) precio_final_2 = precio_final_dto_2;
        this.model.set({
          "porc_bonif_2":Number(porc_bonif_2).toFixed(2),
          "precio_final_2":Number(precio_final_2).toFixed(2),
          "precio_final_dto_2":Number(precio_final_dto_2).toFixed(2),
        });
        this.render();
      },

      // Se modifica el PRECIO FINAL con DESCUENTO
      "change #articulo_precio_sucursal_precio_final_dto_3":function(e){
        var precio_final_dto_3 = this.$("#articulo_precio_sucursal_precio_final_dto_3").val();
        var precio_final_3 = this.$("#articulo_precio_sucursal_precio_final_3").val();
        var porc_bonif_3 = (precio_final_3 > 0) ? (100 - ((precio_final_dto_3 * 100) / precio_final_3)) : 0;
        if (porc_bonif_3 == 0) precio_final_3 = precio_final_dto_3;
        this.model.set({
          "porc_bonif_3":Number(porc_bonif_3).toFixed(2),
          "precio_final_3":Number(precio_final_3).toFixed(2),
          "precio_final_dto_3":Number(precio_final_dto_3).toFixed(2),
        });
        this.render();
      },
      
      "click .eliminar_relacionado":function(e) {
        if (confirm("Realmente desea eliminar la relacion?")) {
          $(e.currentTarget).parents("li").remove();
        }
      },   
    },

    actualizar_todos : function() {
      var self = this;
      // Si los campos estan todos enlazados, actualizamos el resto
      if ($("#articulo_enlazar_costo").is(":checked")) {
        var id_sucursal = self.model.get("id_sucursal");
        _.each(self.container.precios_sucursales,function(e){
          if (e.get("id_sucursal") != id_sucursal) {
            e.set({
              "costo_neto_inicial":self.model.get("costo_neto_inicial"),
              "dto_prov":self.model.get("dto_prov"),
              "costo_neto":self.model.get("costo_neto"),
              "id_tipo_alicuota_iva":self.model.get("id_tipo_alicuota_iva"),
              "costo_final":self.model.get("costo_final"),
              "porc_ganancia":self.model.get("porc_ganancia"),
              "precio_final":self.model.get("precio_final"),
              "porc_bonif":self.model.get("porc_bonif"),
              "precio_final_dto":self.model.get("precio_final_dto"),
              "ganancia":self.model.get("ganancia"),
              "precio_neto":self.model.get("precio_neto"),
              "precio_final_dto":self.model.get("precio_final_dto"),
              "porc_iva":self.model.get("porc_iva"),
              "iva":self.model.get("iva"),
            });
            e.trigger("render_view");
          }
        });
      }
    },
        
    es_numero:function(e) {
      var valor = $(e.currentTarget).val();
      if (isEmpty(valor)) {
        valor = "0";
        $(e.currentTarget).val("0");
      }
      if (!(isInteger(valor) || isDecimal(valor))) {
        show("Por favor ingrese un numero.");
        $(e.currentTarget).focus();
      }
    },
        
    calcular_precios: function() {
      
      var costo_neto_inicial = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
      var costo_neto = parseFloat(this.$("#articulo_precio_sucursal_costo_neto").val());
      var porc_iva = parseFloat(this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje"));
      var id_tipo_alicuota_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").val();
      if (isNaN(costo_neto)) costo_neto = 0;
      if (isNaN(porc_iva)) porc_iva = 0;
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var costo_final = parseFloat(costo_neto) * (1+(porc_iva / 100));
      var porc_ganancia = this.$("#articulo_precio_sucursal_porc_ganancia").val();
      if (isNaN(porc_ganancia)) porc_ganancia = 0;
      var ganancia = costo_final * (porc_ganancia / 100);
      var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
      var precio_final = parseFloat(costo_final) * (1+(porc_ganancia / 100));
      var porc_bonif = this.$("#articulo_precio_sucursal_porc_bonif").val();
      if (isNaN(porc_bonif)) porc_bonif = 0;
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));
      
      this.model.set({
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "costo_iva":Number(costo_iva).toFixed(2),
        "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
        "dto_prov":Number(dto_prov).toFixed(2),
        "costo_neto":Number(costo_neto).toFixed(3),
        "costo_final":Number(costo_final).toFixed(2),
        "porc_ganancia":Number(porc_ganancia).toFixed(4),
        "ganancia":Number(ganancia).toFixed(2),
        "precio_neto":Number(precio_neto).toFixed(2),
        "precio_final":Number(precio_final).toFixed(2),
        "porc_bonif":Number(porc_bonif).toFixed(2),
        "precio_final_dto":Number(precio_final_dto).toFixed(2),
        "porc_iva":Number(porc_iva).toFixed(2),
      });
      this.render();
    },
    
    calcular_precios_2: function() {
      
      var costo_neto_inicial = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
      var costo_neto = parseFloat(this.$("#articulo_precio_sucursal_costo_neto").val());
      var porc_iva = parseFloat(this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje"));
      if (isNaN(costo_neto)) costo_neto = 0;
      if (isNaN(porc_iva)) porc_iva = 0;
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var costo_final = parseFloat(costo_neto) * (1+(porc_iva / 100));
      var porc_ganancia_2 = this.$("#articulo_precio_sucursal_porc_ganancia_2").val();
      if (isNaN(porc_ganancia_2)) porc_ganancia_2 = 0;
      var ganancia = costo_final * (porc_ganancia_2 / 100);
      //var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia_2 / 100));
      var precio_final_2 = parseFloat(costo_final) * (1+(porc_ganancia_2 / 100));
      var porc_bonif_2 = this.$("#articulo_precio_sucursal_porc_bonif_2").val();
      if (isNaN(porc_bonif_2)) porc_bonif_2 = 0;
      var precio_final_dto_2 = parseFloat(precio_final_2) * (1-(porc_bonif_2 / 100));
      
      this.model.set({
        "porc_ganancia_2":Number(porc_ganancia_2).toFixed(4),
        "precio_final_2":Number(precio_final_2).toFixed(2),
        "porc_bonif_2":Number(porc_bonif_2).toFixed(2),
        "precio_final_dto_2":Number(precio_final_dto_2).toFixed(2),
      });
      this.render();
    },

    calcular_precios_3: function() {
      
      var costo_neto_inicial = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
      var costo_neto = parseFloat(this.$("#articulo_precio_sucursal_costo_neto").val());
      var porc_iva = parseFloat(this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje"));
      if (isNaN(costo_neto)) costo_neto = 0;
      if (isNaN(porc_iva)) porc_iva = 0;
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var costo_final = parseFloat(costo_neto) * (1+(porc_iva / 100));
      var porc_ganancia_3 = this.$("#articulo_precio_sucursal_porc_ganancia_3").val();
      if (isNaN(porc_ganancia_3)) porc_ganancia_3 = 0;
      var ganancia = costo_final * (porc_ganancia_3 / 100);
      //var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia_3 / 100));
      var precio_final_3 = parseFloat(costo_final) * (1+(porc_ganancia_3 / 100));
      var porc_bonif_3 = this.$("#articulo_precio_sucursal_porc_bonif_3").val();
      if (isNaN(porc_bonif_3)) porc_bonif_3 = 0;
      var precio_final_dto_3 = parseFloat(precio_final_3) * (1-(porc_bonif_3 / 100));
      
      this.model.set({
        "porc_ganancia_3":Number(porc_ganancia_3).toFixed(4),
        "precio_final_3":Number(precio_final_3).toFixed(2),
        "porc_bonif_3":Number(porc_bonif_3).toFixed(2),
        "precio_final_dto_3":Number(precio_final_dto_3).toFixed(2),
      });
      this.render();
    },

    editar_precio_final: function() {

      var precio_final = parseFloat(this.$("#articulo_precio_sucursal_precio_final").val());
      var porc_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje");
      var id_tipo_alicuota_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").val();
      var costo_neto = parseFloat(this.$("#articulo_precio_sucursal_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
      var costo_final = parseFloat(this.$("#articulo_precio_sucursal_costo_final").val());
      
      // Si el costo final es distinto de cero, entonces cambiamos el PORCENTAJE DE GANANCIA
      if (costo_final != 0) {
        var costo_iva = costo_neto * (porc_iva / 100);
        var porc_ganancia = parseFloat( ((precio_final / costo_final) - 1) * 100);
        var ganancia = costo_final * (porc_ganancia / 100);
        var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
          
      // Si el costo final es igual a cero, entonces lo ponemos igual al precio final
      } else {
          
        var porc_ganancia = 0;
        var ganancia = 0;
        costo_final = precio_final;
        var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
        costo_neto = precio_neto;
        costo_neto_inicial = costo_neto;
        var costo_iva = precio_neto * (porc_iva / 100);
      }
      
      var porc_bonif = this.$("#articulo_precio_sucursal_porc_bonif").val();
      var precio_final_dto = parseFloat(precio_final) * (1-(porc_bonif / 100));

      this.model.set({
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "dto_prov":Number(dto_prov).toFixed(2),
        "costo_iva":Number(costo_iva).toFixed(2),
        "costo_neto":Number(costo_neto).toFixed(3),
        "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
        "costo_final":Number(costo_final).toFixed(2),
        "porc_ganancia":Number(porc_ganancia).toFixed(4),
        "ganancia":Number(ganancia).toFixed(2),
        "precio_neto":Number(precio_neto).toFixed(2),
        "precio_final":Number(precio_final).toFixed(2),
        "porc_bonif":Number(porc_bonif).toFixed(2),
        "precio_final_dto":Number(precio_final_dto).toFixed(2),
        "porc_iva":Number(porc_iva).toFixed(2),
      });
      this.render();
      this.actualizar_todos();            
    },

    editar_precio_final_2: function() {

      var precio_final_2 = parseFloat(this.$("#articulo_precio_sucursal_precio_final_2").val());
      var porc_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva option:selected").data("porcentaje");
      var id_tipo_alicuota_iva = this.$("#articulo_precio_sucursal_tipos_alicuotas_iva").val();
      var costo_neto = parseFloat(this.$("#articulo_precio_sucursal_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#articulo_precio_sucursal_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#articulo_precio_sucursal_dto_prov").val());
      var costo_final = parseFloat(this.$("#articulo_precio_sucursal_costo_final").val());
      
      // Si el costo final es distinto de cero, entonces cambiamos el PORCENTAJE DE GANANCIA
      if (costo_final != 0) {
        var costo_iva = costo_neto * (porc_iva / 100);
        var porc_ganancia_2 = parseFloat( ((precio_final_2 / costo_final) - 1) * 100);
        var ganancia = costo_final * (porc_ganancia_2 / 100);
        var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia_2 / 100));
          
      // Si el costo final es igual a cero, entonces lo ponemos igual al precio final
      } else {
          
        var porc_ganancia_2 = 0;
        var ganancia = 0;
        costo_final = precio_final_2;
        var precio_neto = Number(precio_final_2 / (1+(porc_iva / 100))).toFixed(2);
        costo_neto = precio_neto;
        costo_neto_inicial = costo_neto;
        var costo_iva = precio_neto * (porc_iva / 100);
      }
      
      var porc_bonif_2 = this.$("#articulo_precio_sucursal_porc_bonif_2").val();
      var precio_final_dto_2 = parseFloat(precio_final_2) * (1-(porc_bonif_2 / 100));

      this.model.set({
        "porc_ganancia_2":Number(porc_ganancia_2).toFixed(4),
        "precio_final_2":Number(precio_final_2).toFixed(2),
        "porc_bonif_2":Number(porc_bonif_2).toFixed(2),
        "precio_final_dto_2":Number(precio_final_dto_2).toFixed(2),
      });
      this.render();     
    },

    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      this.container = options.container;
      this.visible = (typeof options.visible != "undefined") ? options.visible : true;
      this.collapsed = (typeof options.collapsed != "undefined") ? options.collapsed : 0;
      this.collapsed_lista_precios = (typeof options.collapsed_lista_precios != "undefined") ? options.collapsed_lista_precios : 0;
      this.edicion = (typeof options.edicion != "undefined") ? options.edicion : true;
      this.listenTo(self.model,"render_view",self.render);
      this.render();  
    },

    render: function() {
      if (!this.edicion && ID_SUCURSAL > 0 && ID_SUCURSAL != this.model.get("id_sucursal")) {
        // Si no tiene permisos para editar, el usuario logueado tiene una sucursal cargada, pero es distinta a la que se esta viendo
        // que oculte el precio
        this.visible = false;
      }
      var obj = { 
        "edicion":this.edicion,
        "id":this.model.id, 
        "visible":this.visible,
        "collapsed":this.collapsed,
        "collapsed_lista_precios":this.collapsed_lista_precios,
      }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },

  });
})(app);


// -----------------------------------------
//   IMPRESION DE ETIQUETAS
// -----------------------------------------
(function ( app ) {

  app.views.ArticuloImprimirEtiquetasEditView = Backbone.View.extend({

  template: _.template($("#articulo_imprimir_etiquetas_template").html()),
    
  events: {
    "click .cerrar":function() {
      if (this.$("#articulo_imprimir_etiquetas_tabla tbody tr").length > 0) {
        if (confirm("Hay articulos cargados, desea cerrar igualmente?")) {
          $('.modal:last').modal('hide');
        }
      } else {
        $('.modal:last').modal('hide');
      }
    },
    "click .imprimir": "imprimir",
    "click .imprimir_directo": "imprimir_directo",
    "click #articulo_imprimir_etiquetas_agregar":"agregar_articulo",
    "click #articulo_imprimir_etiquetas_buscar":"ver_buscar_articulo",
    "click #articulo_imprimir_etiquetas_tabla .delete":"eliminar_articulo",
    "click #articulo_imprimir_etiquetas_tabla .edit":"editar_articulo",
    "click .imprimir_carteles":"imprimir_carteles",
    
    // BUSQUEDA DE ARTICULOS
    "keypress #articulo_imprimir_etiquetas_codigo": function(e) {
      if (e.keyCode == 13) { this.buscar_articulo(); }
    },
    "keypress #articulo_imprimir_etiquetas_cantidad": function(e) {
      if (e.keyCode == 13) { this.agregar_articulo(); }
    },    
  },
  
  buscar_articulo : function() {
    var self = this;    
    var codigo = $("#articulo_imprimir_etiquetas_codigo").val();
    if (isEmpty(codigo)) return;

    if (CACHE_ARTICULOS == 1 || FACTURACION_USA_CACHE_ARTICULOS == 1) { 
    
      // Lo buscamos en el array
      var r = window.articulos.find(function(c){
        // Si tenemos codigo de barra
        var encontro_codigo_barra = false;
        var codigos = c.get("codigos");
        for(var cc = 0; cc < codigos.length; cc++) {
          var codigo_barra = codigos[cc];
          if (codigo_barra == codigo) {
            encontro_codigo_barra = true;
            break;
          }
        }
        if (encontro_codigo_barra) return true;

        // Sino buscamos por codigo o codigo de barra
        return (c.get("codigo") == codigo);
      });
      if (typeof r === "undefined") {
        self.articulo = null;
        alert("No se encuentra el articulo con codigo '"+codigo+"'.");
        this.$("#articulo_imprimir_etiquetas_codigo").select();
      } else {
        this.seleccionar_articulo(r);
      }

    // Los articulos no se encuentran cacheados en un array de JS, por lo que hay que buscarlo con AJAX
    } else {

      $.ajax({
        "url":"articulos/function/get_by_codigo/"+codigo,
        "dataType":"json",
        "type":"post",
        "data":{
          "id_sucursal":ID_SUCURSAL,
        },
        "success":function(result) {
          if (result.error == 1) {
            self.articulo = null;
            alert("No se encuentra el articulo con codigo '"+codigo+"'.");
          } else {
            var art = new app.models.Articulo(result.articulo);
            self.seleccionar_articulo(art);
          }
        }
      });
    }
  },

  imprimir_directo: function() {
    var self = this;
    if (this.validar()) {

      // Hacemos un array con solo los datos que necesitamos enviar
      var salida = new Array();
      _.each(self.model.get("items"),function(e){
        salida.push({
          "id_articulo":e.id,
          "cantidad":e.get("cantidad"),
        });
      });
      var f = document.createElement("form");
      f.setAttribute('method',"post");
      f.setAttribute('target',"_blank");
      f.setAttribute('action',"/sistema/articulos/function/imprimir_sato_directo/");
      $(f).css("display","none");
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"items");
      i.setAttribute('value',JSON.stringify(salida));
      f.appendChild(i);
      if (typeof ID_SUCURSAL != undefined && ID_SUCURSAL != 0) {
        var j = document.createElement("input");
        j.setAttribute('type',"hidden");
        j.setAttribute('name',"id_sucursal");
        j.setAttribute('value',ID_SUCURSAL);
        f.appendChild(j);
      }
      document.getElementsByTagName('body')[0].appendChild(f);
      $(f).submit();
    }
  },

  seleccionar_articulo: function(res) {
    this.articulo = res;
    this.$("#articulo_imprimir_etiquetas_nombre").val(res.get("nombre"));
    this.$("#articulo_imprimir_etiquetas_precio").val(res.get("precio_final_dto"));
    this.$("#articulo_imprimir_etiquetas_cantidad").select();
  },

  ver_buscar_articulo : function() {
    var self = this;
    var buscar = new app.views.ArticulosBuscarTableView({
      collection: articulos,
      habilitar_seleccion: true,
    });
    delete window.codigo_articulo_seleccionado;
    var d = $("<div/>").append(buscar.el);
    crearLightboxHTML({
      "html":d,
      "width":860,
      "height":500,
      "callback":function() {
        if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
          $("#articulo_imprimir_etiquetas_codigo").val($("#articulo_imprimir_etiquetas_codigo").val()+window.codigo_articulo_seleccionado);
          self.buscar_articulo();
        } else {
          $("#articulo_imprimir_etiquetas_codigo").focus();
        }                    
      }
    });
    $("#articulo_imprimir_etiquetas_codigo").focus();
  },  
  
  initialize: function() {
    var self = this;
    _.bindAll(this);
    this.articulo = null;
    $(this.el).html(this.template(this.model.toJSON()));
  },
  
  agregar_articulo : function() {
    
    var self = this;
    if (this.articulo == null) {
      show("Por favor ingrese un articulo.");
      return;
    }
    
    var cantidad = $("#articulo_imprimir_etiquetas_cantidad").val();
    this.articulo.set({"cantidad":cantidad});
    
    // Controlamos que el articulo no exista ya en la lista de items
    var art = _.filter(this.model.get("items"),function(item){
      return (item.id == self.articulo.id);
    });
    if (art.length == 0) {
      // El articulo no se encuentra en la lista,
      // debemos agregarlo
      this.model.get("items").push(this.articulo);
      
      // Actualizamos la vista
      var a = this.articulo;
      var tr = "<tr id='articulo_"+a.id+"'>";
      tr+= "<td>"+a.get("codigo")+"</td>";
      tr+= "<td>"+a.get("nombre")+"</td>";
      tr+= "<td>"+Number(cantidad).toFixed(0)+"</td>";
      tr+= "<td>"+Number(a.get("precio_final")).toFixed(2)+"</td>";
      tr+= "<td><i title='Editar' class='fa fa-file-text-o edit text-dark'></i></td>";
      tr+= "<td><i title='Eliminar' class='glyphicon glyphicon-remove delete text-danger'></i></td>";
      tr+= "</tr>";
      this.$("#articulo_imprimir_etiquetas_tabla tbody").append(tr);
      
      // Movemos el contenedor hasta el final
      this.$("#articulo_imprimir_etiquetas_tabla").scrollTo('+=30px');
      
    } else {
      // El articulo ya se encuentra en la lista
      var a = art[0];
      a.cantidad = self.articulo.get("cantidad");
      var id_tr = "#articulo_"+self.articulo.id;    
      $(id_tr).find("td:eq(2)").html(Number(cantidad).toFixed(0));
    }    
    this.limpiar_articulo();
  },
  
  eliminar_articulo : function(e) {
    var id = $(e.currentTarget).parent().parent().attr("id");
    id = id.replace("articulo_","");
    var ids = id.split("_");
    var id = ids[0];
    
    // Lo eliminamos de la lista
    $(e.currentTarget).parent().parent().remove();
    
    // Lo eliminamos del array
    var items = this.model.get("items");
    var items2 = _.filter(items,function(item){
    return !(item.id == id);
    });
    this.model.set({ "items":items2 });
  },
  
  editar_articulo : function(e) {
    var id = $(e.currentTarget).parent().parent().attr("id");
    id = id.replace("articulo_","");
    var ids = id.split("_");
    var id = ids[0];
    
    // Buscamos el articulo
    var articulo = _.find(this.model.get("items"),function(item){
      return (item.id == id);
    });
    this.articulo = articulo;
    this.mostrar_articulo();
  },
  
  limpiar_articulo : function() {
    this.articulo = null;
    $("#articulo_imprimir_etiquetas_codigo").val("");
    $("#articulo_imprimir_etiquetas_nombre").val("");
    $("#articulo_imprimir_etiquetas_cantidad").val("");
    $("#articulo_imprimir_etiquetas_precio").val("");
    $("#articulo_imprimir_etiquetas_codigo").focus();
  },
  
  mostrar_articulo : function() {
    $("#articulo_imprimir_etiquetas_codigo").val(this.articulo.get("codigo"));
    $("#articulo_imprimir_etiquetas_nombre").val(this.articulo.get("nombre"));
    $("#articulo_imprimir_etiquetas_cantidad").val(this.articulo.get("cantidad"));
    $("#articulo_imprimir_etiquetas_precio").val(this.articulo.get("precio_final_dto"));
    $("#articulo_imprimir_etiquetas_cantidad").select();    
  },
    
  validar: function() {
    try {
      var self = this;
      if (this.model.get("items").length == 0) {
        show("Por favor ingrese algun articulo.");
        return false;
      }
      $(".error").removeClass("error");
      return true;
    } catch(e) {
      return false;
    }
  },  

  imprimir_carteles: function(e) {
    var self = this;
    if (!this.validar()) return;
    var url = $(e.currentTarget).data("url");
    var salida = new Array();
    _.each(self.model.get("items"),function(e){
      salida.push(e.id);
    });
    var in_ids = salida.join("-");

    var f = document.createElement("form");
    f.setAttribute('method',"post");
    f.setAttribute('target',"_blank");
    f.setAttribute('action',"/sistema/articulos/function/"+url+"/");
    $(f).css("display","none");
    var i = document.createElement("input");
    i.setAttribute('type',"hidden");
    i.setAttribute('name',"in_ids");
    i.setAttribute('value',in_ids);
    f.appendChild(i);
    if (typeof ID_SUCURSAL != undefined && ID_SUCURSAL != 0) {
      var j = document.createElement("input");
      j.setAttribute('type',"hidden");
      j.setAttribute('name',"id_sucursal");
      j.setAttribute('value',ID_SUCURSAL);
      f.appendChild(j);
    }
    document.getElementsByTagName('body')[0].appendChild(f);
    $(f).submit();
  },
  
  imprimir:function() {
    var self = this;
    if (this.validar()) {

      // Hacemos un array con solo los datos que necesitamos enviar
      var salida = new Array();
      _.each(self.model.get("items"),function(e){
        salida.push({
          "id_articulo":e.id,
          "cantidad":e.get("cantidad"),
        });
      });
      var f = document.createElement("form");
      f.setAttribute('method',"post");
      f.setAttribute('target',"_blank");
      f.setAttribute('action',"/sistema/articulos/function/imprimir_sato/");
      $(f).css("display","none");
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"items");
      i.setAttribute('value',JSON.stringify(salida));
      f.appendChild(i);
      if (typeof ID_SUCURSAL != undefined && ID_SUCURSAL != 0) {
        var j = document.createElement("input");
        j.setAttribute('type',"hidden");
        j.setAttribute('name',"id_sucursal");
        j.setAttribute('value',ID_SUCURSAL);
        f.appendChild(j);
      }
      document.getElementsByTagName('body')[0].appendChild(f);
      $(f).submit();
    }
  },
  
  });
})(app);


(function ( app ) {
  app.views.ArticuloAjusteMasivoStockView = app.mixins.View.extend({
    template: _.template($("#articulo_ajuste_masivo_stock_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_sucursal = self.$("#ajuste_masivo_stock_sucursales").val();
        var cantidad = self.$("#ajuste_masivo_stock_cantidad").val();
        cantidad = parseFloat(cantidad);
        if (isNaN(cantidad)) {
          alert("Por favor ingrese un numero.");
          self.$("#ajuste_masivo_stock_cantidad").select();
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"stock/function/masivo/",
          "dataType":"json",
          "type":"post",
          "data":{
            "operacion":"ajuste",
            "ids":window.articulos_marcados.join("-"),
            "id_sucursal":id_sucursal,
            "cantidad":cantidad,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al editar el stock de los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());
    },
  });
})(app);


(function ( app ) {
  app.views.ArticuloCambiarMonedaView = app.mixins.View.extend({
    template: _.template($("#articulo_cambiar_moneda_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var moneda = self.$("#articulo_cambiar_moneda_moneda").val();
        $.ajax({
          "timeout":0,
          "url":"articulos/function/cambiar_moneda/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "moneda":moneda,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al modificar los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());
    },
  });
})(app);

(function ( app ) {
  app.views.ArticuloCambiarMarcaView = app.mixins.View.extend({
    template: _.template($("#articulo_cambiar_marca_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_marca = self.$("#articulo_cambiar_marca_marcas").val();
        $.ajax({
          "timeout":0,
          "url":"articulos/function/cambiar_marca/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "id_marca":id_marca,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al modificar los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());

      new app.mixins.Select({
        modelClass: app.models.Marca,
        url: "marcas/",
        render: "#articulo_cambiar_marca_marcas",
        firstOptions: ["<option value='0'>Sin Marca</option>"],
        onComplete:function(c) {
          $("#articulo_cambiar_marca_marcas").select2();
        }
      });
    },
  });
})(app);

(function ( app ) {
  app.views.ArticuloCambiarOfertaView = app.mixins.View.extend({
    template: _.template($("#articulo_cambiar_oferta_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_promocion = self.$("#articulo_cambiar_oferta_promociones").val();
        var descuento = self.$("#articulo_cambiar_oferta_descuento").val();
        descuento = parseFloat(descuento);
        if (isNaN(descuento)) {
          alert("Por favor ingrese un numero.");
          self.$("#articulo_cambiar_oferta_descuento").select();
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"articulos/function/cambiar_promocion/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "id_promocion":id_promocion,
            "descuento":descuento,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al modificar los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());

      new app.mixins.Select({
        modelClass: app.models.Promocion,
        url: "promociones/",
        render: "#articulo_cambiar_oferta_promociones",
        firstOptions: ["<option value='0'>-</option>"],
        onComplete:function(c) {
          crear_select2("articulo_cambiar_oferta_promociones");
        }                    
      });      
    },
  });
})(app);


(function ( app ) {
  app.views.ArticuloAgregarProveedorView = app.mixins.View.extend({
    template: _.template($("#articulo_agregar_proveedor_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_proveedor = self.$("#articulo_agregar_proveedor_proveedores").val();
        if (id_proveedor == 0) {
          alert("Por favor seleccione un proveedor.");
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"articulos/function/editar_masivo_proveedor/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "id_proveedor":id_proveedor,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al modificar los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      $(this.el).html(this.template());

      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#articulo_agregar_proveedor_proveedores",
        firstOptions: ["<option value='0'>Proveedor</option>"],
        onComplete:function(c) {
          $("#articulo_agregar_proveedor_proveedores").select2();
        }
      });

    },
  });
})(app);


(function ( app ) {
  app.views.ArticuloRecategorizacionView = app.mixins.View.extend({
    template: _.template($("#articulo_recategorizacion_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .agregar_rubro":function(e) {
        var self = this;
        if ($(".rubro_edit_mini").length > 0) return;
        var form = new app.views.RubroMiniEditView({
          "model": new app.models.Rubro(),
          "callback":function(m){
            self.id_rubro = m;
            self.cargar_rubros();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete rubro_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#rubros_mini_nombre").focus();
      },
      "click .guardar":function() {
        var self = this;
        var id_rubro = self.$("#articulo_recategorizacion_rubros").val();
        if (id_rubro == 0) {
          alert("Por favor seleccione una categoria.");
          self.$("#articulo_recategorizacion_rubros").focus();
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"articulos/function/cambiar_rubro/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "id_rubro":id_rubro,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al cambiar la categoria de los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());
      this.cargar_rubros();
    },
    cargar_rubros: function() {
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/function/get_select/",
        render: "#articulo_recategorizacion_rubros",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        selected: self.id_rubro,
      });      
    }
  });
})(app);


(function ( app ) {
  app.views.ArticuloEtiquetarView = app.mixins.View.extend({
    template: _.template($("#articulo_etiquetar_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var etiquetas = new Array();
        if (self.$("#articulo_etiquetar_etiquetas").length > 0) {
          var c = self.$("#articulo_etiquetar_etiquetas").select2("val");
          if (c != null) etiquetas = c;
        }
        if (etiquetas.length == 0) {
          alert("Por favor ingrese alguna etiqueta.");
          return false;
        }
        $.ajax({
          "timeout":0,
          "url":"articulos/function/cambiar_etiqueta/",
          "dataType":"json",
          "type":"post",
          "data":{
            "articulos":window.articulos_marcados,
            "etiquetas":etiquetas,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al etiquetar los productos.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.id_rubro = 0;
      $(this.el).html(this.template());
      this.cargar_articulos_etiquetas();
    },
    cargar_articulos_etiquetas: function() {
      var self = this;
      if (self.$("#articulo_etiquetar_etiquetas").length > 0) { 
        self.$("#articulo_etiquetar_etiquetas").select2({
          tags: true,
          minimumInputLength: 3,
          ajax: {
            url: "articulos_etiquetas/function/get_by_nombre/",
            dataType: 'json',
            delay: 1000,
            data: function (params) {
              return {
                term: params.term,
                page: params.page
              };
            },
            processResults: function (data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              params.page = params.page || 1;
              return {
                results: data,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
        });
      }
    },    
  });
})(app);


(function ( app ) {

  app.views.ListaPreciosConfiguracionView = app.mixins.View.extend({

    template: _.template($("#lista_precios_configuracion_template").html()),
      
    myEvents: {
      "click .guardar":"guardar",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
    },
    guardar : function() {
      var self = this;
      $.ajax({
        "url":"articulos/function/set_lista_precios_configuracion/",
        "dataType":"json",
        "type":"post",
        "data":{
          "lista_1_nombre":self.$("#lista_precios_configuracion_1").val(),
          "lista_2_nombre":self.$("#lista_precios_configuracion_2").val(),
          "lista_3_nombre":self.$("#lista_precios_configuracion_3").val(),
          "lista_4_nombre":self.$("#lista_precios_configuracion_4").val(),
          "lista_5_nombre":self.$("#lista_precios_configuracion_5").val(),
          "lista_6_nombre":self.$("#lista_precios_configuracion_6").val(),
        },
        "success":function(r) {
          if (r.error == 0) location.reload();
          else {
            alert("Hubo un error al guardar la configuracion.");
          }
        }
      })
    },
  });

})(app);



(function ( app ) {
  app.views.ImprimirListadoArticulosView = app.mixins.View.extend({
    template: _.template($("#imprimir_listado_articulos_template").html()),
    myEvents: {
      "click .imprimir":"imprimir",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.parent = options.parent;
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#imprimir_lista_precios_con_ventas_desde"));
    },
    imprimir: function() {
      var lista_precios = "";
      lista_precios += this.$("#imprimir_lista_precios_1").is(":checked") ? "1" : "0";
      lista_precios += "-";
      lista_precios += this.$("#imprimir_lista_precios_2").is(":checked") ? "1" : "0";
      lista_precios += "-";
      lista_precios += this.$("#imprimir_lista_precios_3").is(":checked") ? "1" : "0";
      lista_precios += "-";
      lista_precios += this.$("#imprimir_lista_precios_4").is(":checked") ? "1" : "0";
      lista_precios += "-";
      lista_precios += this.$("#imprimir_lista_precios_5").is(":checked") ? "1" : "0";
      lista_precios += "-";
      lista_precios += this.$("#imprimir_lista_precios_6").is(":checked") ? "1" : "0";
      window.lista_precios_seleccionadas = lista_precios;
      if (this.$("#imprimir_lista_precios_sucursales").length > 0) {
        window.lista_precios_sucursal = this.$("#imprimir_lista_precios_sucursales").val();
      }
      window.lista_precios_con_ventas_desde = this.$("#imprimir_lista_precios_sucursales").val();
      this.parent.armar_form("articulos/function/imprimir/");
    }
  });
})(app);


(function ( app ) {
  app.views.ImprimirListadoArticulosPorProveedorView = app.mixins.View.extend({
    template: _.template($("#imprimir_listado_articulos_por_proveedor_template").html()),
    myEvents: {
      "click .imprimir":"imprimir",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.parent = options.parent;
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#imprimir_lista_precios_por_proveedor_con_ventas_desde"));
    },
    imprimir: function() {
      var id_sucursal = this.$("#imprimir_lista_precios_por_proveedor_sucursales").val();
      var con_ventas_desde = this.$("#imprimir_lista_precios_por_proveedor_con_ventas_desde").val();
      if (!isEmpty(con_ventas_desde)) con_ventas_desde = con_ventas_desde.replace(/\//g,"-");
      var url = "articulos/function/imprimir_por_proveedor/";
      url+= "?id_sucursal="+id_sucursal;
      url+= "&con_ventas_desde="+con_ventas_desde;
      window.open(url,"_blank");
    }
  });
})(app);


(function ( models ) {

  models.ArticuloEtiqueta = Backbone.Model.extend({
    urlRoot: "articulos_etiquetas/",
    defaults: {
      nombre: "",
      texto: "",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.ArticulosEtiquetas = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "articulos_etiquetas/"
    }
    
  });

})( app.collections, app.models.ArticuloEtiqueta, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ArticuloEtiquetaItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#articulos_etiquetas_item').html()),
        events: {
        "click": "editar",
        "click .ver": "editar",
        "click .delete": "borrar",
        "click .duplicar": "duplicar"
      },
        initialize: function(options) {
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.permiso = this.options.permiso;
            _.bindAll(this);
        },
        render: function()
        {
          // Creamos un objeto para agregarle las otras propiedades que no son el modelo
          var obj = { permiso: this.permiso };
          // Extendemos el objeto creado con el modelo de datos
          $.extend(obj,this.model.toJSON());

            $(this.el).html(this.template(obj));
            return this;
        },
        editar: function() {
          // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
          location.href="app/#articulo_etiqueta/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar la etiqueta?")) {
                this.model.destroy(); // Eliminamos el modelo
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

    app.views.ArticulosEtiquetasTableView = app.mixins.View.extend({

      template: _.template($("#articulos_etiquetas_panel_template").html()),

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
            this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: lista
      });

      lista.on('add', this.addOne, this);
      lista.on('all', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ArticuloEtiquetaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.ArticuloEtiquetaEditView = app.mixins.View.extend({

    template: _.template($("#articulos_etiquetas_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
    },

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.options = options;
            this.render();
        },

        render: function()
        {
          // Creamos un objeto para agregarle las otras propiedades que no son el modelo
          var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
          // Extendemos el objeto creado con el modelo de datos
          $.extend(obj,this.model.toJSON());

          $(this.el).html(this.template(obj));

            return this;
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("articulos_etiquetas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },
        

        guardar: function() 
        {
            var self = this;
            if (this.validar()) {
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({
                        "id_empresa":ID_EMPRESA,
                    },{
                    success: function(model,response) {
                        location.reload();
                    }
                });
            }
    },
    
        limpiar : function() {
            this.model = new app.models.ArticuloEtiqueta()
            this.render();
        },
    
  });

})(app.views, app.models);