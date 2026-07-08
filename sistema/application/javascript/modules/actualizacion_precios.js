// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ActualizacionPreciosResultados = app.mixins.View.extend({

    template: _.template($("#actualizacion_precios_resultados_template").html()),
      
    myEvents: {
      "change #actualizacion_precios_buscar":"buscar",
      "click #actualizacion_precios_buscar_avanzada_btn":"buscar",
      "click .confirmar":"confirmar",
      "change #actualizacion_precios_campo":function() {
        var campo = this.$("#actualizacion_precios_campo").val();
        if (campo == "P" || campo == "P2" || campo == "P3" || campo == "P4") {
          this.$("#actualizacion_precios_base_cont").show();
          this.$("#actualizacion_precios_base").empty();
          this.$("#actualizacion_precios_base").append('<option value="">Usar como base</option>');
          if (campo == "P") {
            this.$("#actualizacion_precios_base").append('<option value="P2">Lista 2</option>');
            this.$("#actualizacion_precios_base").append('<option value="P3">Lista 3</option>');
            this.$("#actualizacion_precios_base").append('<option value="P4">Lista 4</option>');
          } else if (campo == "P2") {
            this.$("#actualizacion_precios_base").append('<option value="P">Lista 1</option>');
            this.$("#actualizacion_precios_base").append('<option value="P3">Lista 3</option>');
            this.$("#actualizacion_precios_base").append('<option value="P4">Lista 4</option>');            
          } else if (campo == "P3") {
            this.$("#actualizacion_precios_base").append('<option value="P">Lista 1</option>');
            this.$("#actualizacion_precios_base").append('<option value="P2">Lista 2</option>');
            this.$("#actualizacion_precios_base").append('<option value="P4">Lista 4</option>');            
          } else if (campo == "P4") {
            this.$("#actualizacion_precios_base").append('<option value="P">Lista 1</option>');
            this.$("#actualizacion_precios_base").append('<option value="P2">Lista 2</option>');
            this.$("#actualizacion_precios_base").append('<option value="P3">Lista 3</option>');
          }
        }
        else this.$("#actualizacion_precios_base_cont").hide();
      },
      "click .generar":function(){
        var monto = parseFloat(this.$("#actualizacion_precios_monto").val());
        if (isNaN(monto) || monto == 0) {
          alert("Por favor ingrese una cantidad.");
          this.$("#actualizacion_precios_monto").select();
          return;          
        }
        this.buscar();
      },
      "keypress #actualizacion_precios_monto":function(e) {
        if (e.which == 13) { this.buscar(); }
      },
      "click #actualizacion_precios_not_proveedores":function(e) {
        if ($(e.currentTarget).hasClass("btn-danger")) {
          $(e.currentTarget).removeClass("btn-danger");
          $(e.currentTarget).addClass("btn-default");
        } else {
          $(e.currentTarget).removeClass("btn-default");
          $(e.currentTarget).addClass("btn-danger");
        }
      }
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.id_marca = (this.options.id_marca == undefined) ? this.options.id_marca : 0;
      this.id_rubro = (this.options.id_rubro == undefined) ? this.options.id_rubro : 0;
      this.id_proveedor = (this.options.id_proveedor == undefined) ? this.options.id_proveedor : 0;
			this.permiso = this.options.permiso;
      this.procesando = 0;

      this.render();
      this.collection.on('sync', this.addAll, this);
      this.collection.goTo(1);      
    },

    render: function() {

      var self = this;
			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
				collection: this.collection
			});

      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
      
      if (control.check("marcas")>0) {
        new app.mixins.Select({
          modelClass: app.models.Marca,
          url: "marcas/",
          render: "#actualizacion_precios_buscar_marcas",
          firstOptions: ["<option value='0'>Marca</option>"],
          selected: self.id_marca,
          onComplete:function(c) {
            $("#actualizacion_precios_buscar_marcas").select2({});
          }
        });
      }

      if (control.check("departamentos_comerciales")>0) {
        new app.mixins.Select({
          modelClass: app.models.DepartamentoComercial,
          url: "departamentos_comerciales/",
          render: "#actualizacion_precios_buscar_departamentos",
          firstOptions: ["<option value='0'>Departamento</option>"],
          onComplete:function(c) {
            $("#actualizacion_precios_buscar_departamentos").select2({});
          }
        });
      }  

      if (control.check("proveedores")>0) {
        new app.mixins.Select({
          modelClass: app.models.Proveedor,
          url: "proveedores/",
          render: "#actualizacion_precios_buscar_proveedores",
          firstOptions: ["<option value='0'>Proveedor</option>"],
          //selected: self.id_proveedor,
          onComplete:function(c) {
            self.$("#actualizacion_precios_buscar_proveedores").select2({
              multiple: true,
              language: "es",
              placeholder: {
                id: 0,
                text: "Proveedores",
              }
            });
            self.$("#actualizacion_precios_buscar_proveedores").parent().find(".select2-search__field").attr("placeholder","Proveedores");
            self.$("#actualizacion_precios_buscar_proveedores").parent().find(".select2-search__field").css("width","100%");
            self.$("#actualizacion_precios_buscar_proveedores").on("change.select2",function(e){
              self.$("#actualizacion_precios_buscar_proveedores").parent().find(".select2-search__field").css("width","100%");
            });        
            //$("#actualizacion_precios_buscar_proveedores").select2({});
          }
        });

      }
      
      // BUSQUEDA AVANZADA POR CATEGORIAS
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/",
        render: "#actualizacion_precios_buscar_categorias",
        firstOptions: ["<option value='0'>Categoria</option>"],
        //selected: self.id_rubro,
        onComplete:function(c) {
          self.$("#actualizacion_precios_buscar_categorias").select2({
            multiple: true,
            language: "es",
            placeholder: {
              id: 0,
              text: "Categorias",
            }
          });
          self.$("#actualizacion_precios_buscar_categorias").parent().find(".select2-search__field").attr("placeholder","Categorias");
          self.$("#actualizacion_precios_buscar_categorias").parent().find(".select2-search__field").css("width","100%");
          self.$("#actualizacion_precios_buscar_categorias").on("change.select2",function(e){
            self.$("#actualizacion_precios_buscar_categorias").parent().find(".select2-search__field").css("width","100%");
          });        
          //$("#actualizacion_precios_buscar_categorias").select2({});
        }
      });

      createdatepicker($(this.el).find("#actualizacion_precios_fecha"));

      $('[data-toggle="tooltip"]').tooltip(); 

      return this;
		},
    
    buscar: function() {
      var self = this;
      var datos = {}
      datos.texto = self.$("#actualizacion_precios_buscar").val().trim();
      datos.id_marca = (self.$("#actualizacion_precios_buscar_marcas").length > 0) ? self.$("#actualizacion_precios_buscar_marcas").val() : 0;

      if (self.$("#actualizacion_precios_buscar_proveedores").length > 0) {
        var ids_proveedores = (this.$("#actualizacion_precios_buscar_proveedores").val() == null) ? 0 : this.$("#actualizacion_precios_buscar_proveedores").val();
        if ($.isArray(ids_proveedores)) ids_proveedores = ids_proveedores.join("-");
        if (this.$("#actualizacion_precios_not_proveedores").hasClass("btn-danger")) {
          datos.not_ids_proveedores = ids_proveedores;
        } else {
          datos.ids_proveedores = ids_proveedores;
        }
      }

      if (self.$("#actualizacion_precios_buscar_categorias").length > 0) {
        var ids_rubros = (this.$("#actualizacion_precios_buscar_categorias").val() == null) ? 0 : this.$("#actualizacion_precios_buscar_categorias").val();
        if ($.isArray(ids_rubros)) ids_rubros = ids_rubros.join("-");
        datos.ids_rubros = ids_rubros;
      }

      datos.id_sucursal = (self.$("#actualizacion_precios_sucursal").length > 0) ? self.$("#actualizacion_precios_sucursal").val() : 0;
      datos.id_departamento = (self.$("#actualizacion_precios_buscar_departamentos").length > 0) ? self.$("#actualizacion_precios_buscar_departamentos").val() : 0;
      datos.fecha = encodeURIComponent(self.$("#actualizacion_precios_fecha").val());
      datos.fecha_tipo = self.$("#actualizacion_precios_fecha_tipo").val();
      this.collection.server_api = datos;
      this.collection.pager();
    },
    
    addAll : function () {
      this.$(".tbody").empty();
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
      var view = new app.views.ActualizacionPreciosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
        
    confirmar: function() {
      
      if (this.procesando == 1) return;
      var self = this;
      var data = {};
      data.redondeo = $("#actualizacion_precios_redondeo").val();
      data.id_marca = (self.$("#actualizacion_precios_buscar_marcas").length > 0) ? self.$("#actualizacion_precios_buscar_marcas").val() : 0;

      if (self.$("#actualizacion_precios_buscar_proveedores").length > 0) {
        var ids_proveedores = (this.$("#actualizacion_precios_buscar_proveedores").val() == null) ? 0 : this.$("#actualizacion_precios_buscar_proveedores").val();
        if ($.isArray(ids_proveedores)) ids_proveedores = ids_proveedores.join("-");
        if (this.$("#actualizacion_precios_not_proveedores").hasClass("btn-danger")) {
          data.not_ids_proveedores = ids_proveedores;
        } else {
          data.ids_proveedores = ids_proveedores;
        }
      }

      if (self.$("#actualizacion_precios_buscar_categorias").length > 0) {
        var ids_rubros = (this.$("#actualizacion_precios_buscar_categorias").val() == null) ? 0 : this.$("#actualizacion_precios_buscar_categorias").val();
        if ($.isArray(ids_rubros)) ids_rubros = ids_rubros.join("-");
        data.ids_rubros = ids_rubros;
      }

      data.id_departamento = (self.$("#actualizacion_precios_buscar_departamentos").length > 0) ? self.$("#actualizacion_precios_buscar_departamentos").val() : 0;
      data.id_sucursal = (self.$("#actualizacion_precios_sucursal").length > 0) ? self.$("#actualizacion_precios_sucursal").val() : -1;
      data.texto = $("#actualizacion_precios_buscar").val();
      data.fecha = encodeURIComponent(self.$("#actualizacion_precios_fecha").val());
      data.fecha_tipo = self.$("#actualizacion_precios_fecha_tipo").val();

      var campo = $("#actualizacion_precios_campo").val();
      if (campo == 0) {
        show("Por favor seleccione un campo.");
        $("#actualizacion_precios_campo").focus();
        return;
      }
      
      data.tipo = $("#actualizacion_precios_tipo").val();
      var monto = parseFloat($("#actualizacion_precios_monto").val());
      if (isNaN(monto)) {
        show("Por favor ingrese un monto valido.");
        $("#actualizacion_precios_monto").focus();
        return;
      }

      if (campo == "P" || campo == "P2" || campo == "P3" || campo == "P4") data.base = self.$("#actualizacion_precios_base").val();
      
      data.campo = campo;
      data.monto = monto;
      
      if (confirm("Esta seguro de realizar estas modificaciones de precios?")) {
        this.procesando = 1;
        workspace.esperar("Actualizando...");
        $.ajax({
          "url":"articulos/function/actualizar/",
          "type":"get",
          "data":data,
          "timeout":0,
          "dataType":"json",
          "success":function(r) {
            self.procesando = 0;
            $(".modal:last").trigger('click');
            alert("Los precios se actualizaron correctamente.");
            location.reload();
          }
        });        
      }
      
    },    
    
	
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ActualizacionPreciosItemResultados = Backbone.View.extend({
    template: _.template($("#actualizacion_precios_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click .edit":"editar",
      "click .checkbox":"seleccionar",
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
      $(this.el).addClass("seleccionado");
      } else {
      $(this.el).removeClass("seleccionado");
      }
    },
    editar : function() {
      var self = this;
      var articulo = new app.models.Articulo({
      "id":self.model.id
      });
      articulo.fetch({
        "success":function() {
          app.views.articuloEditView = new app.views.ArticuloEditView({
          model: articulo
          });
          crearLightboxHTML({
            "html":app.views.articuloEditView.el,
            "width":670,
            "height":500,
          });
        }
      });
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
	  
      var campo = $("#actualizacion_precios_campo").val();
      var tipo = $("#actualizacion_precios_tipo").val();
      var redondeo = $("#actualizacion_precios_redondeo").val();
      var monto = parseFloat($("#actualizacion_precios_monto").val());
      var base = $("#actualizacion_precios_base").val();
			if (isNaN(monto)) monto = 0;
      
      var obj = this.model.toJSON();
      
      if (campo == "C") {
      
        // Se actualiza el costo
        var costo = parseFloat(this.model.get("costo_neto"));
        if (tipo == "P") {
          costo = parseFloat(costo + (costo * monto / 100));
        } else if (tipo == "F") {
          costo = parseFloat(costo + monto);
        } else if (tipo == "I") {
          costo = monto;
        }
        var porc_iva = parseFloat(this.model.get("porc_iva"));
        var costo_iva = parseFloat(costo * (porc_iva / 100));
        var costo_final = parseFloat(costo) + parseFloat(costo_iva);
        
        var porc_ganancia = parseFloat(this.model.get("porc_ganancia"));
        var ganancia = parseFloat(costo_final * (porc_ganancia / 100));
        var precio_neto = parseFloat(costo) * (1+(porc_ganancia / 100));
        var precio_final_dto = parseFloat(costo_final) * (1+(porc_ganancia / 100));
        if (redondeo > 0) precio_final_dto = Math.round(precio_final_dto * redondeo,0) / redondeo;
        
        var porc_ganancia_2 = parseFloat(this.model.get("porc_ganancia_2"));
        var ganancia_2 = parseFloat(costo_final * (porc_ganancia_2 / 100));
        var precio_neto_2 = parseFloat(costo) * (1+(porc_ganancia_2 / 100));
        var precio_final_dto_2 = parseFloat(costo_final) * (1+(porc_ganancia_2 / 100));
        if (redondeo > 0) precio_final_dto_2 = Math.round(precio_final_dto_2 * redondeo,0) / redondeo;

        var porc_ganancia_3 = parseFloat(this.model.get("porc_ganancia_3"));
        var ganancia_3 = parseFloat(costo_final * (porc_ganancia_3 / 100));
        var precio_neto_3 = parseFloat(costo) * (1+(porc_ganancia_3 / 100));
        var precio_final_dto_3 = parseFloat(costo_final) * (1+(porc_ganancia_3 / 100));
        if (redondeo > 0) precio_final_dto_3 = Math.round(precio_final_dto_3 * redondeo,0) / redondeo;

        var porc_ganancia_4 = parseFloat(this.model.get("porc_ganancia_4"));
        var ganancia_4 = parseFloat(costo_final * (porc_ganancia_4 / 100));
        var precio_neto_4 = parseFloat(costo) * (1+(porc_ganancia_4 / 100));
        var precio_final_dto_4 = parseFloat(costo_final) * (1+(porc_ganancia_4 / 100));
        if (redondeo > 0) precio_final_dto_4 = Math.round(precio_final_dto_4 * redondeo,0) / redondeo;

        _.extend(obj,{
          "costo_nuevo":costo,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });        
      
      } else if (campo == "P") {
      
        // Se actualiza el precio final
        if (base == "P2") var precio_final_dto = parseFloat(this.model.get("precio_final_dto_2"));
        else if (base == "P3") var precio_final_dto = parseFloat(this.model.get("precio_final_dto_3"));
        else if (base == "P4") var precio_final_dto = parseFloat(this.model.get("precio_final_dto_4"));
        else var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));

        if (tipo == "P") {
          precio_final_dto = parseFloat(precio_final_dto + (precio_final_dto * monto / 100));
        } else if (tipo == "F") {
          precio_final_dto = parseFloat(precio_final_dto + monto);
        } else if (tipo == "I") {
          precio_final_dto = parseFloat(monto);
        }
        var porc_iva = parseFloat(this.model.get("porc_iva"));
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        if (costo_final != 0) var porc_ganancia = parseFloat( ((precio_final_dto / costo_final) - 1) * 100);
        else var porc_ganancia = 0;
        var ganancia = parseFloat(costo_final * (porc_ganancia / 100));
        var precio_neto = costo_neto * (1+(porc_ganancia / 100));
        
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        if (redondeo > 0) precio_final_dto = Math.round(precio_final_dto * redondeo,0) / redondeo;
        
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });         
        
      } else if (campo == "P2") {
      
        // Se actualiza el precio final
        if (base == "P") var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto"));
        else if (base == "P3") var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_3"));
        else if (base == "P4") var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_4"));
        else var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));

        if (tipo == "P") {
          precio_final_dto_2 = parseFloat(precio_final_dto_2 + (precio_final_dto_2 * monto / 100));
        } else if (tipo == "F") {
          precio_final_dto_2 = parseFloat(precio_final_dto_2 + monto);
        } else if (tipo == "I") {
          precio_final_dto_2 = parseFloat(monto);
        }
        var porc_iva = parseFloat(this.model.get("porc_iva"));
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        if (costo_final != 0) var porc_ganancia_2 = parseFloat( ((precio_final_dto_2 / costo_final) - 1) * 100);
        else var porc_ganancia_2 = 0;
        var ganancia_2 = parseFloat(costo_final * (porc_ganancia_2 / 100));
        var precio_neto_2 = costo_neto * (1+(porc_ganancia_2 / 100));
        
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        if (redondeo > 0) precio_final_dto_2 = Math.round(precio_final_dto_2 * redondeo,0) / redondeo;
        
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });
        
      } else if (campo == "P3") {
      
        // Se actualiza LISTA 3
        if (base == "P") var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto"));
        else if (base == "P2") var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_2"));
        else if (base == "P4") var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_4"));
        else var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));

        if (tipo == "P") {
          precio_final_dto_3 = parseFloat(precio_final_dto_3 + (precio_final_dto_3 * monto / 100));
        } else if (tipo == "F") {
          precio_final_dto_3 = parseFloat(precio_final_dto_3 + monto);
        } else if (tipo == "I") {
          precio_final_dto_3 = parseFloat(monto);
        }
        var porc_iva = parseFloat(this.model.get("porc_iva"));
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        if (costo_final != 0) var porc_ganancia_3 = parseFloat( ((precio_final_dto_3 / costo_final) - 1) * 100);
        else var porc_ganancia_3 = 0;
        var ganancia_3 = parseFloat(costo_final * (porc_ganancia_3 / 100));
        var precio_neto_3 = costo_neto * (1+(porc_ganancia_3 / 100));
        
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        if (redondeo > 0) precio_final_dto_3 = Math.round(precio_final_dto_3 * redondeo,0) / redondeo;
        
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });    

      } else if (campo == "P4") {
      
        // Se actualiza LISTA 4
        if (base == "P") var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto"));
        else if (base == "P2") var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_2"));
        else if (base == "P3") var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_3"));
        else var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        if (tipo == "P") {
          precio_final_dto_4 = parseFloat(precio_final_dto_4 + (precio_final_dto_4 * monto / 100));
        } else if (tipo == "F") {
          precio_final_dto_4 = parseFloat(precio_final_dto_4 + monto);
        } else if (tipo == "I") {
          precio_final_dto_4 = parseFloat(monto);
        }
        var porc_iva = parseFloat(this.model.get("porc_iva"));
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        if (costo_final != 0) var porc_ganancia_4 = parseFloat( ((precio_final_dto_4 / costo_final) - 1) * 100);
        else var porc_ganancia_4 = 0;
        var ganancia_4 = parseFloat(costo_final * (porc_ganancia_4 / 100));
        var precio_neto_4 = costo_neto * (1+(porc_ganancia_4 / 100));
        
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));

        if (redondeo > 0) precio_final_dto_4 = Math.round(precio_final_dto_4 * redondeo,0) / redondeo;
        
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });  
        
      } else if (campo == "D") {
      
        // Se actualiza el PORCENTAJE DE DESCUENTO
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));
        precio_final_dto = parseFloat(precio_final_dto * ((100-monto)/100));
        if (redondeo > 0) precio_final_dto = Math.round(precio_final_dto * redondeo) / redondeo;
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        }); 

      } else if (campo == "D2") {
      
        // Se actualiza el PORCENTAJE DE DESCUENTO
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));
        precio_final_dto_2 = parseFloat(precio_final_dto_2 * ((100-monto)/100));
        if (redondeo > 0) precio_final_dto_2 = Math.round(precio_final_dto_2 * redondeo) / redondeo;
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        }); 

      } else if (campo == "D3") {
      
        // Se actualiza el PORCENTAJE DE DESCUENTO
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));
        precio_final_dto_3 = parseFloat(precio_final_dto_3 * ((100-monto)/100));
        if (redondeo > 0) precio_final_dto_3 = Math.round(precio_final_dto_3 * redondeo) / redondeo;
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        }); 

      } else if (campo == "D4") {
      
        // Se actualiza el PORCENTAJE DE DESCUENTO
        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));
        precio_final_dto_4 = parseFloat(precio_final_dto_4 * ((100-monto)/100));
        if (redondeo > 0) precio_final_dto_4 = Math.round(precio_final_dto_4 * redondeo) / redondeo;
        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        }); 

      } else if (campo == "M1") {
      
        // Se actualiza el PORCENTAJE DE GANANCIA
        var porc_ganancia = monto;
        var porc_bonif = parseFloat(this.model.get("porc_bonif"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        var precio_final_dto = parseFloat(costo_final * ((100+porc_ganancia)/100) * ((100-porc_bonif)/100));
        if (redondeo > 0) precio_final_dto = Math.round(precio_final_dto * redondeo) / redondeo;

        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });

      } else if (campo == "M2") {
      
        // Se actualiza el PORCENTAJE DE GANANCIA
        var porc_ganancia_2 = monto;
        var porc_bonif_2 = parseFloat(this.model.get("porc_bonif_2"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        var precio_final_dto_2 = parseFloat(costo_final * ((100+porc_ganancia_2)/100) * ((100-porc_bonif_2)/100));
        if (redondeo > 0) precio_final_dto_2 = Math.round(precio_final_dto_2 * redondeo) / redondeo;

        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });   

      } else if (campo == "M3") {
      
        // Se actualiza el PORCENTAJE DE GANANCIA
        var porc_ganancia_3 = monto;
        var porc_bonif_3 = parseFloat(this.model.get("porc_bonif_3"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        var precio_final_dto_3 = parseFloat(costo_final * ((100+porc_ganancia_3)/100) * ((100-porc_bonif_3)/100));
        if (redondeo > 0) precio_final_dto_3 = Math.round(precio_final_dto_3 * redondeo) / redondeo;

        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));

        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });  

      } else if (campo == "M4") {
      
        // Se actualiza el PORCENTAJE DE GANANCIA
        var porc_ganancia_4 = monto;
        var porc_bonif_4 = parseFloat(this.model.get("porc_bonif_4"));
        var costo_final = parseFloat(this.model.get("costo_final"));
        var precio_final_dto_4 = parseFloat(costo_final * ((100+porc_ganancia_4)/100) * ((100-porc_bonif_4)/100));
        if (redondeo > 0) precio_final_dto_4 = Math.round(precio_final_dto_4 * redondeo) / redondeo;

        var costo_neto = parseFloat(this.model.get("costo_neto"));
        var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));

        _.extend(obj,{
          "costo_nuevo":costo_neto,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });  

      } else {
        // No modifica ningun precio
				var costo = parseFloat(this.model.get("costo_neto"));
				var porc_ganancia = parseFloat(this.model.get("porc_ganancia"));
				var precio_final_dto = parseFloat(this.model.get("precio_final_dto"));
        var precio_final_dto_2 = parseFloat(this.model.get("precio_final_dto_2"));
        var precio_final_dto_3 = parseFloat(this.model.get("precio_final_dto_3"));
        var precio_final_dto_4 = parseFloat(this.model.get("precio_final_dto_4"));
        _.extend(obj,{
          "costo_nuevo":costo,
          "precio_nuevo":precio_final_dto,
          "precio_nuevo_2":precio_final_dto_2,
          "precio_nuevo_3":precio_final_dto_3,
          "precio_nuevo_4":precio_final_dto_4,
        });          
			}
	  
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);