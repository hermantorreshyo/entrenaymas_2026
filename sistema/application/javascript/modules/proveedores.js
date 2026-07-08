// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Proveedor = Backbone.Model.extend({
    urlRoot: "proveedores/",
    defaults: {
      nombre: "",
      codigo: "",
      direccion: "",
      id_localidad : 0,
      localidad : "",
      id_tipo_iva : 0,
      telefono: "",
      fax: "",
      contacto: "",
      contacto_telefono: "",
      contacto_email: "",
      contacto_direccion: "",
      web: "",
      horario: "",
      rubro_comercial: "",
      razon_social: "",
      activo : 1,
      cuit: "",
      convenio_multilateral: "",
      porc_ret_ib: 0,
      aplica_ret_ganancias: 1,
      id_banco: 0,
      cuenta_bancaria: "",
      cbu: "",
      tipo_proveedor: 1,
      forma_pago: "",
      email: "",
      frecuencia: "0",
      observaciones: "",
      relacionados: [],
      cuentas_bancarias: [],
      tipo: 0,
      saldo_inicial: 0,
      dias_pago: 0,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------
(function (collections, model, paginator) {
  collections.Proveedores = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "proveedores/"
    }
  });
})( app.collections, app.models.Proveedor, Backbone.Paginator);

// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ProveedorItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#proveedores_item').html()),
    myEvents: {
      "click .data":"seleccionar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",

      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"proveedores",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },

      "keyup .radio":function(e) {
        if (e.which == 13) { this.seleccionar(); }
      },

      "focus .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
      },
      "blur .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
      },
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = {
        permiso: this.permiso,
        seleccionar: this.seleccionar
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    seleccionar : function(e) {
      if (this.seleccionar) {
        window.proveedor_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#proveedor/"+this.model.id;
      }
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

  app.views.ProveedoresTableView = app.mixins.View.extend({

    template: _.template($("#proveedores_panel_template").html()),

    myEvents: {
      "keydown #proveedores_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar",
      "click .importar_excel":function() {
        this.importar_excel({
          "tabla":"proveedores",
        });
      },
      "change #proveedores_buscar":"buscar",
      "change #proveedores_tipo_proveedor":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.proveedores_filter = (typeof window.proveedores_filter != "undefined") ? window.proveedores_filter : "";
      window.proveedores_page = (typeof window.proveedores_page != "undefined") ? window.proveedores_page : 1;
      window.proveedores_tipo_proveedor = (typeof window.proveedores_tipo_proveedor != "undefined") ? window.proveedores_tipo_proveedor : ((control.check("gastos")>0)?1:0);

      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    buscar: function() {

      var cambio_parametros = false;
      if (window.proveedores_filter != this.$("#proveedores_buscar").val().trim()) {
        window.proveedores_filter = this.$("#proveedores_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.proveedores_page = 1;
      var datos = {
        "term":encodeURIComponent(window.proveedores_filter),
      };
      if (this.$("#proveedores_tipo_proveedor").length > 0) {
        window.proveedores_tipo_proveedor = this.$("#proveedores_tipo_proveedor").val();
      }
      datos.tipo_proveedor = window.proveedores_tipo_proveedor;
      this.collection.server_api = datos;
      this.collection.goTo(window.proveedores_page);
    },

    render: function() {
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"proveedores",
        "table":"proveedores",
        "header":[
          "codigo","nombre","cuit","email","direccion","telefono"
        ]
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("proveedores/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"proveedores"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },                

    addAll : function () {
      window.proveedores_page = this.pagination.getPage();
      this.$("#proveedores_table tbody").empty();
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
      var view = new app.views.ProveedorItem({
        model: item,
        permiso: this.permiso,
        seleccionar: this.habilitar_seleccion, 
      });
      this.$("tbody").append(view.render().el);
    }

  });
})(app);




// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.ProveedorEditView = app.mixins.View.extend({

    template: _.template($("#proveedores_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click #proveedores_cuentas_agregar":"agregar_cuenta_bancaria",
      "click .editar_cuenta":"editar_cuenta",
      "click .eliminar_cuenta":function(e){
        $(e.currentTarget).parents("tr").remove();
      },      
    },      

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      this.bind("limpiar",this.limpiar,this);
      this.options = options;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 0) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      
      $(this.el).html(this.template(obj));
      
      this.$("#proveedores_cuit").mask("99-99999999-9");

      var relacionados = new Array();
      _.each(this.model.get("relacionados"),function(elem){
        relacionados.push(elem.id);
      });
      
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#proveedores_relacionados",
        selected: relacionados,
        multiple: true,
        onComplete:function(c) {
          $("#proveedores_relacionados").chosen({
            "placeholder_text_multiple":"Seleccione",
          });
        }
      });

      // AUTOCOMPLETE DE LOCALIDADES
      this.$("#proveedores_localidad").autocomplete({
        "minLength":3,
        "source":function(request,response) {
          $.ajax({
            "url":"localidades/function/get_by_nombre/",
            "data":{
              "term":request.term
            },
            "dataType":"json",
            "type":"get",
            "success":function(res){
              response(res);
            }
          });
        },
        "select":function(event,ui){
          self.model.set({
            "id_localidad":ui.item.id,
            "localidad":ui.item.label,
          });
        },
      });             
      /*
      new app.mixins.Select({
          modelClass: app.models.Banco,
          url: "bancos/",
          render: "#proveedores_bancos",
          name : "id_banco",
          firstOptions: ["<option value='0'>-</option>"],
          selected: this.model.get("id_banco"),
      });
      */
      
      this.$("#proveedores_cuit").mask("99-99999999-9");
      
      if (CONFIGURACION_AUTOGENERAR_CODIGOS == 1) {
        // Estamos creando un proveedor nuevo
        if (this.model.id == undefined) {
          $.ajax({
            "url":"proveedores/function/next/",
            "dataType":"json",
            "success":function(r) {
              $(self.el).find("#proveedores_codigo").val(r.codigo);
            }
          });
        }                
      }            

      return this;
    },

    validar: function() {
      try {
        var self = this;

        var relacionados = $("#proveedores_relacionados").val();
        if (relacionados == null) relacionados = new Array();
        this.model.set({"relacionados":relacionados});          
        
        // Validamos los campos que sean necesarios
        validate_input("proveedores_nombre",IS_EMPTY,"Por favor, ingrese una razon social.");
        //validate_input("proveedores_cuit",IS_EMPTY,"Por favor, ingrese un CUIT.");
        //validate_input("proveedores_cuit",IS_CUIT,"Por favor, ingrese un CUIT válido.");

        if (this.$("#proveedor_tipo").length > 0) {
          this.model.set({
            "tipo":self.$("#proveedor_tipo").val()
          })
        }        
        
        this.model.set({
          "codigo": $(self.el).find("#proveedores_codigo").val(),
          "id_tipo_iva": $(self.el).find("#proveedores_tipo_iva").val(),
          "id_banco": $(self.el).find("#proveedores_bancos").val(),
          "tipo_proveedor": $(self.el).find("#proveedores_tipo_proveedor").val(),
          "frecuencia": $(self.el).find("#proveedores_frecuencia").val(),
          "dias_pago": $(self.el).find("#proveedores_dias_pago").val(),
        });

        // Guardamos los cuentas_bancarias
        var cuentas_bancarias = new Array();
        this.$("#proveedores_cuentas_tabla tbody tr").each(function(i,e){
          
          var cbu = $(e).find("td:eq(1)").html();
          cbu = cbu.replace(/\"/g,"");
          cbu = cbu.replace(/\'/g,"");
          cbu = cbu.replace(/\`/g,"");
          cbu = cbu.replace(/\´/g,"");

          var cuenta = $(e).find("td:eq(2)").html();
          cuenta = cuenta.replace(/\"/g,"");
          cuenta = cuenta.replace(/\'/g,"");
          cuenta = cuenta.replace(/\`/g,"");
          cuenta = cuenta.replace(/\´/g,"");

          var banco = $(e).find("td:eq(0)").html();
          banco = banco.replace(/\"/g,"");
          banco = banco.replace(/\'/g,"");
          banco = banco.replace(/\`/g,"");
          banco = banco.replace(/\´/g,"");

          cuentas_bancarias.push({
            "id_banco": $(e).data("id"),
            "cbu": cbu,
            "banco": banco,
            "cuenta":cuenta,
          });
        });
        this.model.set({"cuentas_bancarias":cuentas_bancarias});

        // No hay ningun error
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    agregar_cuenta_bancaria: function() {
        
      var id_banco = $("#proveedores_cuentas_bancos").val();
      if (id_banco == 0) {
        alert("Por favor seleccione un banco");
        $("#proveedores_cuentas_bancos").focus();
        return;
      }
      var banco = $("#proveedores_cuentas_bancos option:selected").text();
      var cbu = $("#proveedores_cuentas_cbu").val();
      var cuenta = $("#proveedores_cuentas_cuenta_bancaria").val();
      var tr = "<tr data-id='"+id_banco+"'>";
      tr+="<td>"+banco+"</td>";
      tr+="<td>"+cbu+"</td>";
      tr+="<td>"+cuenta+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_cuenta'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_cuenta text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#proveedores_cuentas_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#proveedor_codigo").val("");
      $("#proveedores_cuentas_bancos").focus();
    },

    editar_cuenta: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#proveedores_cuentas_bancos").val($(this.item).data("id")).trigger("change");
      $("#proveedores_cuentas_cbu").val($(this.item).find("td:eq(1)").text());
      $("#proveedores_cuentas_cuenta_bancaria").val($(this.item).find("td:eq(2)").text());
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{        
          success: function(model,response) {
            if (response.error == 0) {
              location.href="app/#proveedores";
            } else {
              show(response.mensaje);
            }
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Proveedor()
      this.render();
    },

  });

})(app.views, app.models);



(function ( views, models ) {

  views.ProveedorEditViewMini = app.mixins.View.extend({

    template: _.template($("#proveedores_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "keyup #proveedores_mini_nombre":function() {
                // Tenemos enlazada la referencia, por lo que cada vez que escribimos algo, debemos cambiar el input original
                if (this.input != undefined) {
                  $(this.input).val($(this.el).find("#proveedores_mini_nombre").val());
                }
              },
              "keypress .tab":function(e) {
                if (e.keyCode == 13) {
                  e.preventDefault();
                  $(e.currentTarget).parent().next().find(".tab").focus();
                }
              },
              "keyup .tab":function(e) {
                if (e.which == 27) this.cerrar();
              },
              "keypress .guardar":function(e) {
                if (e.keyCode == 13) this.guardar();
              },
            },

            initialize: function(options) {
              this.options = options;
              this.model.bind("destroy",this.render,this);
              _.bindAll(this);
              this.input = this.options.input;
              this.onSave = this.options.onSave;
              this.render();
            },

            render: function() {
              var self = this;
              $(this.el).html(this.template(this.model.toJSON()));
              if (this.input != undefined) {
                // Seteamos lo que tiene el input de referencia
                $(this.el).find("#proveedores_mini_nombre").val($(this.input).val().trim());
              }
              return this;
            },

            focus: function() {
              $(this.el).find("#proveedores_mini_nombre").focus();
            },

            validar: function() {
              try {
                validate_input("proveedores_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                validate_input("proveedores_mini_direccion",IS_EMPTY,"Por favor, ingrese una direccion.");
                validate_input("proveedores_mini_cuit",IS_EMPTY,"Por favor, ingrese el CUIT del cliente.");
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
                  "nombre":$("#proveedores_mini_nombre").val(),
                  "cuit":$("#proveedores_mini_cuit").val(),
                  "id_tipo_iva":$("#proveedores_mini_tipo_iva").val(),
                  "direccion":$("#proveedores_mini_direccion").val(),
                },{
                  success: function(model,response) {
                    if (response.error == 1) {
                      show(response.mensaje);
                    } else {
                      if(typeof proveedores != "undefined") proveedores.add(model);
                      if (typeof self.onSave != "undefined") self.onSave(model);
                      if (typeof self.callback != "undefined") self.callback(model.id);
                      self.cerrar();
                    }
                  }
                });
              }
            },

            cerrar: function() {
              $(this.el).parents(".customcomplete").remove();
            },

            limpiar : function() {
              this.model = new app.models.Proveedor()
              this.render();
            },        

          });

})(app.views, app.models);