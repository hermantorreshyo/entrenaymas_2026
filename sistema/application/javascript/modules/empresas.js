// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Empresa = Backbone.Model.extend({
    urlRoot: "empresas/",
    defaults: {
      nombre: "",
      razon_social: "",
      plan: "",
      id_plan: 0,
      cuit: "",
      email: "",
      telefono_empresa: "",
      id_proyecto: 1,
      id_tipo_contribuyente: 1,
      direccion_empresa: "",
      id_localidad: 0,
      localidad: "",
      id_provincia: 0,
      path: "",
      logo: "",
      comentarios: "",
      configuracion_menu: 1,
      configuracion_menu_iconos: 1,
      configuracion_sonido: 0,
      configuracion_autogenerar_codigos: 1,
      dominios: [],
      vendedores: [],
      facturas: [],
      estado_empresa:0,
      usuario: "",
      fecha_prox_venc: "",
      fecha_suspension: "",
      costo: 0,
      activo: 1,
      periodo_fact: "+1 month",
      dominio_ppal: "",
      numero_ib: "",
      fecha_inicio: "",
      percibe_ib:0,
      retiene_ib:0,
      retiene_ganancias:0,
      servidor_local: "",
      administrar_pagos: 0,
      configuraciones_especiales: "",
      limite: 0,
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Empresas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 50,
    },        
    paginator_core: {
      url: "empresas/"
    }
  });

})( app.collections, app.models.Empresa, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.EmpresaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#empresas_item').html()),
    events: {
      "click .ver": "editar",
      "click .user": "login",
      "click .delete": "borrar",
      "click .enviar_whatsapp":function(){
        var mensaje = "Hola "+this.model.get("nombre")+"\n";
        if (this.model.get("tipo_empresa") == 4) mensaje += "Gracias por registrarte en La Plata Construye.\n";
        mensaje += "Mi nombre es "+NOMBRE+" y te voy a ayudar en el proceso de configuracion de tu cuenta.\n";
        mensaje += "Avisame cuando puedo llamarte asi coordinamos.\n";
        mensaje += "Muchas gracias!!";
        var tel = this.model.get("telefono_empresa");
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel+"?text="+encodeURIComponent(mensaje);
        window.open(link_ws,"_blank");
      }
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      location.href="app/#empresa/"+this.model.id;
    },
    borrar: function(e) {
      e.stopPropagation();
      var clave = prompt("Ingrese la clave del administrador");
      if (!clave) return;
      clave = hex_md5(clave);
      if (clave == "f61860ee7bdc9f46a16cff42ebd570a0") {
        if (confirmar("Realmente desea eliminar este elemento?")) {
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
      } else {
        alert("Clave incorrecta");
      }
    },
    login: function(e) {
      var self = this;
      e.stopPropagation();
      $.ajax({
        "url":"login/cambiar_empresa/"+self.model.id,
        "dataType":"json",
        "success":function(r){
          if (r.error == true) {
            alert(r.mensaje)
          } else {
            location.reload();
          }
        }
      });
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.EmpresasTableView = app.mixins.View.extend({

    myEvents: {
      "change #empresas_estado":"buscar",
    },

    template: _.template($("#empresas_panel_template").html()),

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;

      this.options = options;
      this.permiso = this.options.permiso;
      this.id_proyecto = this.options.id_proyecto;
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });
      var search = new app.mixins.SearchView({
        collection: lista
      });

      lista.on('add', this.addOne, this);
      lista.on('all', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso, id_proyecto: this.id_proyecto };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      this.buscar();
    },

    buscar: function() {
      var self = this;
      var datos = {
        "estado_empresa":self.$("#empresas_estado").val(),
        "id_proyecto":self.id_proyecto,
        "id_usuario":ID_USUARIO,
      };
      this.collection.server_api = datos;
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip(); 
    },

    addOne : function ( item ) {
      var view = new app.views.EmpresaItem({
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

  views.EmpresaEditView = app.mixins.View.extend({

    template: _.template($("#empresas_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .login":"login",

      "click #vendedor_agregar":"agregar_vendedor",
      "click .editar_vendedor":"editar_vendedor",
      "click .eliminar_vendedor":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      "change #empresas_plan":function() {
        if (this.model.isNew()) {
          var self = this;  
          var costo = this.$("#empresas_plan option:selected").data("costo");
          var limite = this.$("#empresas_plan option:selected").data("limite");
          this.$("#empresas_costo").val(costo);
          this.$("#empresas_limite").val(limite);
        }
      },

      "change #empresas_provincia":function(e) {
        var self = this;
        var id_provincia = $("#empresas_provincia").val();
        new app.mixins.Select({
          modelClass: app.models.Localidad,
          url: "localidades/function/get_by_provincia/"+id_provincia,
          render: "#empresas_localidad",
          firstOptions: ["<option value='-1'>-</option>"],
          selected: self.model.get("id_localidad"),
        });      
      },
      
      "change #configuracion_menu":function(e) {
        $(".app").removeClass("app-aside-fixed");
        $(".app").removeClass("app-aside-dock");
        if ($(e.currentTarget).is(":checked")) {
          $(".app").addClass("app-aside-fixed");
        } else {
          $(".app").addClass("app-aside-dock");
        }
      },
      "change #configuracion_menu_iconos":function(e) {
        $(".app").removeClass("sin-iconos");
        if (!$(e.currentTarget).is(":checked")) {
          $(".app").addClass("sin-iconos");
        }
      },
    },
        
    initialize: function() {
      this.model.bind("destroy",this.render,this);
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      if (PERFIL == -1) {
        var id_proyecto = self.model.get("id_proyecto");

        new app.mixins.Select({
          modelClass: app.models.WebTemplate,
          url: "web_templates/",
          render: "#empresas_templates",
          name : "id_web_template",
          selected: self.model.get("id_web_template"),
        });

        new app.mixins.Select({
          modelClass: app.models.Empresa,
          url: "empresas/",
          render: "#empresas_modelos",
          name : "id_empresa_modelo",
          campoSelect: "id,nombre",
          firstOptions: ["<option value='0'>-</option>"],
          onComplete:function(c) {
            $("#empresas_modelos").select2({}).change();
          }
        });
      }
            
      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#empresas_localidad").autocomplete({
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

      if (this.$("#empresa_facturas_tabla").length > 0) {
        this.$("#empresa_facturas_tabla .fecha_pago").each(function(index, el) {
          createdatepicker(el,$(el).data("value"));
        });
      }       
            
      $(this.el).find("#empresas_dominios").select2({
        tags: true,
      });
      
      var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
      $(this.el).find("#empresas_fecha_inicio").datepicker({
        "dateFormat":"dd/mm/yy",
        "currentText":"Hoy",
        "buttonImage": "resources/images/datepicker.png",
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
      $(this.el).find("#empresas_fecha_inicio").mask("99/99/9999");
      
      var fecha_prox_venc = this.model.get("fecha_prox_venc");
      if (isEmpty(fecha_prox_venc)) fecha_prox_venc = new Date();
      createdatepicker($(this.el).find("#empresas_fecha_prox_venc"),fecha_prox_venc);

      var fecha_suspension = this.model.get("fecha_suspension");
      if (isEmpty(fecha_suspension)) fecha_suspension = new Date();
      createdatepicker($(this.el).find("#empresas_fecha_suspension"),fecha_suspension);            
      
      $(this.el).find("#empresas_cuit").mask("99-99999999-9");
      
      if (PERFIL == -1) {
        $.ajax({
          "url":'empresas/function/get_modulos/'+self.model.id,
          "dataType":"json",
          "success":function(r) {
            $("#empresa_modulos_tabla tbody").empty();
            for(var i=0;i<r.length;i++) {
              var modulo = r[i];
              var tr = "";
              if (modulo.id_modulo != 0) {
                tr+="<tr class='modulo' data-id='"+modulo.id_modulo+"'>";
                tr+="<td>";
                tr+=((modulo.orden_2 != 0 && modulo.orden_1 != 0)?"<span class='dib w30'></span>":"");
                tr+='<input class="visible" '+((typeof modulo.visible != "undefined" && modulo.visible == 1) ? "checked":"")+' type="checkbox"/>';
                tr+="<input type='text' style='display:inline-block;width:auto;border:none' class='form-control no-model nombre' value='"+modulo.title+"'/>";
                tr+="</td>";
                tr+="<td>";
                tr+=modulo.modulo;
                tr+="</td>";
                tr+="<td>";
                tr+="<select class='permiso form-control no-model'>";
                tr+="<option "+((modulo.habilitado==1)?"selected":"")+" value='1'>Habilitado</option>";
                tr+="<option "+((modulo.habilitado==0)?"selected":"")+" value='0'>Deshabilitado</option>";
                tr+="</select>";
                tr+="</td>";
                tr+="</tr>";
              } else {
                tr+="<tr>";
                tr+="<td colspan='3'>";
                tr+="<span class='bold'>"+modulo.title+"</td>";
                tr+="</td>";
                tr+="</tr>";
              }
              $("#empresa_modulos_tabla tbody").append(tr);
            }
          }
        });
      }
      return this;
    },

    login: function() {
      var self = this;
      $.ajax({
        "url":"login/cambiar_empresa/"+self.model.id,
        "dataType":"json",
        "success":function(r){
          if (r.error == true) {
            alert(r.mensaje)
          } else {
            location.reload();
          }
        }
      });
    },

    agregar_vendedor: function() {
      var id_vendedor = $("#empresa_vendedores").val();
      if (id_vendedor == 0) {
        alert("Por favor seleccione un vendedor");
        return;
      }
      var vendedor = $("#empresa_vendedores option:selected").text();
      var monto = ((this.$("#empresa_monto").length > 0) ? this.$("#empresa_monto").val() : 0);
      var tr = "<tr id='vendedor_"+id_vendedor+"' data-id='"+id_vendedor+"'>";
      tr+="<td class='editar_vendedor'>"+vendedor+"</td>";
      tr+="<td class='editar_vendedor monto'>"+monto+"</td>";
      tr+="<td><i class='fa fa-times eliminar_vendedor text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#vendedores_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      
      $("#empresa_vendedores").val(0);
      $("#empresa_monto").val("");
      $("#empresa_vendedores").focus();
    },

    editar_vendedor: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#empresa_vendedores").val($(this.item).data("id"));
      $("#empresa_monto").val($(this.item).find(".monto").text());
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("empresas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        if (!validateEmail($("#empresas_email").val())) {
          alert("Por favor, ingrese un email.");
          return false;
        }
        
        // Los dominios van todas juntos separadas por ;;;
        if ($("#empresas_dominios").length > 0) {
          var c = $("#empresas_dominios").select2("val");
          if (c != null) {
            this.model.set({
              "dominios":c.join(";;;"),
            });
          }
        }

        if (this.$("#empresa_facturas_tabla").length > 0) {
          var tiene_error = false;
          var facturas = new Array();
          this.$("#empresa_facturas_tabla tbody tr").each(function(index, el) {
            var id = $(el).data("id");
            var pagada = ($(el).find(".pagada").is(":checked") ? 1 : 0);
            var observaciones = $(el).find(".observaciones").val();
            var fecha_pago = $(el).find(".fecha_pago").val();
            if (pagada == 1 && isEmpty(fecha_pago)) tiene_error = 1;
            facturas.push({
              "id":id,
              "pagada":pagada,
              "fecha_pago":fecha_pago,
              "observaciones":observaciones,
            });
          });
          if (tiene_error) {
            alert("Por favor ingrese una fecha de pago");
            return false;
          }
          this.model.set({
            "facturas":facturas,
          })
        }

        if (this.$("#empresa_activo").length > 0) {
          this.model.set({
            "activo":($("#empresa_activo").is(":checked")?1:0),
          });
        }        

        if ($("#empresa_administrar_pagos").length > 0) {
          this.model.set({
            "administrar_pagos":($("#empresa_administrar_pagos").is(":checked")?1:0),
          });
        }

        if ($("#empresas_estado_empresa").length > 0) {
          this.model.set({
            "estado_empresa":($("#empresas_estado_empresa").val()),
          });
        }

        // Si estos campos estan para editar
        if (self.$("#empresas_fecha_prox_venc").length > 0) {
          this.model.set({
            "fecha_prox_venc":self.$("#empresas_fecha_prox_venc").val(),
            "fecha_suspension":self.$("#empresas_fecha_suspension").val(),
          });
        }     

        if (self.$("#vendedores_tabla").length > 0) {
          var vendedores = new Array();
          $("#vendedores_tabla tbody tr").each(function(i,e){
            vendedores.push({
              "id_usuario": $(e).data("id"),
              "monto": $(e).find(".monto").text(),
            });
          });
          this.model.set({"vendedores":vendedores});
        }
        
        // No hay ningun error
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        
        if (PERFIL == -1) {
          // Obtenemos los IDS de los permisos seleccionados por el usuario
          var ids = new Array();
          $("#empresa_modulos_tabla tbody .modulo").each(function(i,e){
            var id = $(e).data("id");
            var permiso = $(e).find(".permiso").val();
            if (permiso == null) permiso = 0;
            var nombre = $(e).find(".nombre").val();
            var visible = ($(e).find(".visible").is(":checked")?1:0);
            var o = { 
              "id":id, 
              "habilitado":permiso, 
              "nombre":nombre,
              "visible":visible,
            };
            ids.push(o);
          });
          // Lo agregamos el modelo
          this.model.set({"modulos":ids});

          // Si ademas es un vendedor nuestro
          if (ID_USUARIO != 0) {
            this.model.set({
              "vendedores":[{
                "id_usuario":ID_USUARIO,
                "monto":0,
              }]
            })
          }

          this.model.set({
            "id_empresa_modelo":self.$("#empresas_modelos").val(),
          });
        }
        
        this.model.save({
            "logo":$("#hidden_logo").val(),
            "path":$("#hidden_path").val(),
            "id_tipo_contribuyente":$("#empresas_tipo_contribuyente").val(),
          },{
            success: function(model,response) {
              if (response.error != undefined && response.error == true) {
                show("Hubo un error al crear la empresa.");
              } else {
                window.location.reload();                        
              }
            }
        });  
      }
    },
    
  });

})(app.views, app.models);





(function ( models ) {

  models.EmpresaRestovar = Backbone.Model.extend({
    urlRoot: "empresas/",
  });
        
})( app.models );


(function ( views, models ) {

  views.EmpresaRestovarEditView = app.mixins.View.extend({

    template: _.template($("#empresa_restovar_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #expand_mapa":"expand_mapa",
      "keydown #empresas_detalle_direccion":function(e) {
        if (e.which == 13) this.get_coords_by_address();
      },
    },
      
    initialize: function() {
      _.bindAll(this);

      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      this.expand_mapa_key = 0;

      $(this.el).html(this.template(obj));

      $(this.el).find("#empresas_detalle_horario_lunes_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_lunes_2").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_martes_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_martes_2").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_miercoles_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_miercoles_2").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_jueves_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_jueves_2").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_viernes_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_viernes_2").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_sabado_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_sabado_2").mask("99:99");            
      $(this.el).find("#empresas_detalle_horario_domingo_1").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_domingo_2").mask("99:99");

      $(this.el).find("#empresas_detalle_horario_lunes_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_lunes_4").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_martes_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_martes_4").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_miercoles_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_miercoles_4").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_jueves_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_jueves_4").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_viernes_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_viernes_4").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_sabado_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_sabado_4").mask("99:99");            
      $(this.el).find("#empresas_detalle_horario_domingo_3").mask("99:99");
      $(this.el).find("#empresas_detalle_horario_domingo_4").mask("99:99");            
      
      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#empresas_localidad").autocomplete({
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

      $(this.el).find("#empresas_detalle_cuit").mask("99-99999999-9");

      /*
      if (self.model.get("categorias").length>0) {
        $(this.el).find("#empresas_detalle_categorias").val(self.model.get("categorias").join(","));
      }
      // Cargamos las categorias con AJAX
      $.ajax({
        "url":"delivery_categorias/",
        "dataType":"json",
        "success":function(r) {
          var categorias = new Array();
          for(var i=0;i<r.results.length;i++) {
            var a = r.results[i];
            categorias.push(a.nombre);
          }
          $(self.el).find("#empresas_detalle_categorias").select2({
            tags: categorias,
          });
        }
      });
      */

      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {}
    },

    // Cuando expandimos por primera vez el panel de ubicacion
    expand_mapa: function() {
      var self = this;
      setTimeout(function(){
        if (self.map == undefined) self.render_map();
        google.maps.event.trigger(self.map, "resize");
        self.map.setCenter(self.coor);
      },100);
    },

    get_coords_by_address: function() {
      var self = this;
      if (self.map == undefined) return;
      var direccion = $("#empresas_detalle_direccion").val();
      if (isEmpty(direccion)) {
        alert("Por favor ingrese una direccion");
        $("#empresas_detalle_direccion").focus();
        return;
      }
      var localidad = $("#empresas_detalle_localidad").val();
      if (isEmpty(localidad)) {
        alert("Por favor ingrese una localidad");
        $("#empresas_detalle_localidad").focus();
        return;
      }
      var address = direccion+", "+localidad;
      self.geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          var location = results[0].geometry.location;
          var latitud = location.lat();
          var longitud = location.lng();
          self.add_marker(latitud,longitud);
          self.map.setCenter(location);
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },

    add_marker: function(latitud,longitud) {
      var self = this;
      var coord = new google.maps.LatLng(latitud,longitud);    
      this.marker = new google.maps.Marker({
        position: coord,
        map: self.map,
        draggable:true,
        title:"Arrastralo a la direccion correcta"
      });
      google.maps.event.addListener(this.marker, "dblclick", function (e) { 
        if (confirm("Realmente desea eliminar el marcador?")) {
          self.marker.setMap(null);
        }
      });
    },

    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud_comercio");
      var longitud = self.model.get("longitud_comercio");
      var zoom = parseInt(self.model.get("zoom_comercio"));
      if (latitud == 0 && longitud == 0) {
        latitud = LATITUD;
        longitud = LONGITUD;
        zoom = 12;
      }
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(self.$("#empresa_detalle_mapa")[0], mapOptions);
      self.geocoder = new google.maps.Geocoder();

      // Si tiene seteado coordenadas, ponemos el marcador
      if (typeof self.model.get("latitud_comercio") != "undefined" && typeof self.model.get("longitud_comercio") != "undefined") {
        self.add_marker(self.model.get("latitud_comercio"),self.model.get("longitud_comercio"));
      }
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("empresas_detalle_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var categorias = self.$("#empresas_detalle_categorias").select2("val");
        this.model.set({
          "categorias":categorias,
        });

        // Coordenadas
        if (self.marker != undefined) {
          var pos = self.marker.getPosition();
          var zoom = self.map.getZoom();
          this.model.set({
            "latitud_comercio":(isNaN(pos.lat())) ? 0 : pos.lat(),
            "longitud_comercio":(isNaN(pos.lng())) ? 0 : pos.lng(),
            "zoom_comercio":zoom,
          });
        }                
        // Texto del entrada
        self.model.set({
          "texto_comercio":CKEDITOR.instances['empresas_detalle_texto_comercio'].getData(),
        });
        return true;
      } catch(e) {
        console.log(e);
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
            "logo":$("#hidden_logo").val(),
            "path":$("#hidden_path").val(),
            "id_tipo_contribuyente":$("#empresas_detalle_tipo_contribuyente").val(),
          },{
          success: function(model,response) {
            if (response.error != undefined && response.error == true) {
              show("Hubo un error al crear la empresa.");
            } else {
              window.location.reload();                        
            }
          }
        }); 
      }
    },
    
  });

})(app.views, app.models);



(function ( models ) {

  models.EmpresaColvar = Backbone.Model.extend({
    urlRoot: "empresas/",
  });
        
})( app.models );


(function ( views, models ) {

  views.EmpresaColvarEditView = app.mixins.View.extend({

    template: _.template($("#empresa_colvar_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "change #empresas_detalle_perfil_escuela":function(e) {
        var v = $(e.currentTarget).val();
        if (v == 0) {
          // Primaria
          $("#empresas_detalle_asistencia_docente_por_materia").prop("checked",false);
          $("#empresas_detalle_asistencia_alumno_por_materia").prop("checked",false);
        } else if (v == 1) {
          // Secundaria
          $("#empresas_detalle_asistencia_docente_por_materia").prop("checked",true);
          $("#empresas_detalle_asistencia_alumno_por_materia").prop("checked",false);
        } else {
          // Terciario/Univ
          $("#empresas_detalle_asistencia_docente_por_materia").prop("checked",true);
          $("#empresas_detalle_asistencia_alumno_por_materia").prop("checked",true);          
        }
      }
    },
      
    initialize: function() {
      _.bindAll(this);

      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      this.expand_mapa_key = 0;

      $(this.el).html(this.template(obj));

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#empresas_localidad").autocomplete({
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
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("empresas_detalle_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        this.model.set({
          "asistencia_docente_por_materia": ($("#empresas_detalle_asistencia_docente_por_materia").is(":checked")?1:0),
          "asistencia_alumno_por_materia": ($("#empresas_detalle_asistencia_alumno_por_materia").is(":checked")?1:0),
        });

        return true;
      } catch(e) {
        console.log(e);
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
            "logo":$("#hidden_logo").val(),
            "path":$("#hidden_path").val(),
          },{
          success: function(model,response) {
            if (response.error != undefined && response.error == true) {
              show("Hubo un error al crear la empresa.");
            } else {
              window.location.reload();                        
            }
          }
        }); 
      }
    },
    
  });

})(app.views, app.models);



(function ( app ) {

  app.views.ConfiguracionTablaView = app.mixins.View.extend({

    template: _.template($("#configuracion_tabla_template").html()),
      
    myEvents: {
      "click .guardar":"guardar",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.tabla = options.tabla;
      this.titulo = options.titulo;
      $(this.el).html(this.template({
        "titulo":options.titulo,
        "tabla":options.tabla,
      }));
      this.$('.dd').nestable();
    },
    guardar : function() {
      var self = this;
      var datos = new Array();
      this.$(".columna_editable").each(function(i,e){
        var t = $(e).find(".form-control");
        var c = {
          "visible": ($(e).find("input[type=checkbox]").is(":checked")?1:0),
          "campo": t.data("campo"),
          "ordenable": t.data("ordenable"),
          "ocultable": t.data("ocultable"),
          "clases": t.data("clases"),
          "titulo": t.val(),
        }
        datos.push(c);
      });
      $.ajax({
        "url":"configuracion/function/set_tablas_configuracion/",
        "dataType":"json",
        "type":"post",
        "data":{
          "tabla":self.tabla.tabla,
          "configuracion":JSON.stringify(datos),
          "cant_items":self.$("#configuracion_tabla_cant_items").val(),
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



(function ( views, models ) {

  views.EmpresasGestionPagosView = app.mixins.View.extend({

    template: _.template($("#empresas_gestion_pagos_template").html()),

    myEvents: {
      "change #empresas_gestion_pagos_fecha_desde":"buscar",
      "change #empresas_gestion_pagos_fecha_hasta":"buscar",
      "change #empresas_gestion_pagos_proyectos":"buscar",
      "change #empresas_gestion_pagos_vendedores":"buscar",
      "change #empresas_gestion_pagos_estados":"buscar",
    },
      
    initialize: function() {
      _.bindAll(this);
      var self = this;
      $(this.el).html(this.template());
      var desde = moment().subtract(6,'month').format("01/MM/YYYY");
      createdatepicker(this.$("#empresas_gestion_pagos_fecha_desde"),desde);
      var hasta = moment().add(2,'month').format("DD/MM/YYYY");
      createdatepicker(this.$("#empresas_gestion_pagos_fecha_hasta"),hasta);      
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var desde = this.$("#empresas_gestion_pagos_fecha_desde").val();
      var hasta = this.$("#empresas_gestion_pagos_fecha_hasta").val();
      var id_proyecto = this.$("#empresas_gestion_pagos_proyectos").val();
      var estado_empresa = this.$("#empresas_gestion_pagos_estados").val();
      var id_vendedor = (ID_USUARIO != 0) ? ID_USUARIO : this.$("#empresas_gestion_pagos_vendedores").val();
      $.ajax({
        "url":"empresas/function/ver_calendario_pagos/",
        "type":"post",
        "data":{
          "desde":desde,
          "hasta":hasta,
          "id_vendedor":id_vendedor,
          "id_proyecto":id_proyecto,
          "estado_empresa":estado_empresa,
        },
        "dataType":"json",
        "success":function(r) {
          self.render_data(r);
        }
      });
    },

    render_data: function(r) {
      this.$("#empresas_gestion_pagos_tabla").empty();
      var tabla = new app.views.EmpresasGestionPagosTableView({
        model: new app.models.AbstractModel(r)
      });
      this.$("#empresas_gestion_pagos_tabla").append(tabla.el);
    }

  });

})(app.views, app.models);


(function ( views, models ) {

  views.EmpresasGestionPagosTableView = app.mixins.View.extend({

    template: _.template($("#empresas_gestion_pagos_tabla_template").html()),

    initialize: function() {
      _.bindAll(this);
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },

  });

})(app.views, app.models);
