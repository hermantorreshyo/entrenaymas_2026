(function ( views, models ) {

	views.WebSeoEditView = app.mixins.View.extend({

		template: _.template($("#web_seo_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",

      "change .conversion_automatica":function() {
        if (this.$("input[name=conversion_automatica]:checked").val() == 1) this.$("#web_configuracion_dolar").attr("disabled","disabled");
        else this.$("#web_configuracion_dolar").removeAttr("disabled");
      },

      "click .sincronizacion_completa_articulos_meli":function() {
        workspace.esperar("Sincronizando...");
        $.ajax({
          "url":"articulos/function/sincronizacion_completa_meli/",
          "timeout":0,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) {
              location.reload();
            } else if (r.error == 1) {
              $(".modal:last").trigger('click');
              alert("Hubo un error al sincronizar con la cuenta de MercadoLibre");
            }
          },
        });
      },

      "click .importacion_meli":function() {
        workspace.esperar("Sincronizando...");
        $.ajax({
          "url":"articulos_meli/function/obtener_publicaciones/",
          "timeout":0,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) {
              location.reload();
            } else if (r.error == 1) {
              $(".modal:last").trigger('click');
              alert("Hubo un error al sincronizar con la cuenta de MercadoLibre");
            }
          },
        });
      },

      "click .borrar_sincro_meli":function() {
        $.ajax({
          "url":"web_configuracion/function/save_attribute/",
          "timeout":0,
          "dataType":"json",
          "type":"post",
          "data":{
            "attribute":"ml_access_token",
            "value":"",
          },
          "success":function() {
            window.location.reload();
          }
        })
      },
		},

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      if (control.check("emails_templates") > 0) {
        new app.mixins.Select({
          modelClass: app.models.EmailTemplate,
          url: "emails_templates/",
          render: "#web_seo_email_carrito_abandonado",
          firstOptions: ["<option value='0'>Seleccione una plantilla</option>"],
          selected: self.model.get("id_email_carrito_abandonado"),
          onComplete:function(c) {
            crear_select2("web_seo_email_carrito_abandonado");
          },
        });
        new app.mixins.Select({
          modelClass: app.models.EmailTemplate,
          url: "emails_templates/",
          render: "#web_seo_id_email_post_compra",
          firstOptions: ["<option value='0'>Seleccione una plantilla</option>"],
          selected: self.model.get("id_email_post_compra"),
          onComplete:function(c) {
            crear_select2("web_seo_id_email_post_compra");
          },
        });  
        new app.mixins.Select({
          modelClass: app.models.EmailTemplate,
          url: "emails_templates/",
          render: "#web_seo_id_email_registro",
          firstOptions: ["<option value='0'>Seleccione una plantilla</option>"],
          selected: self.model.get("id_email_registro"),
          onComplete:function(c) {
            crear_select2("web_seo_id_email_registro");
          },
        });  
      }

      if (this.$("#images_meli_tabla").length > 0) {
        this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
        this.render_tabla_fotos();            
        $(this.el).find("#images_meli_tabla").sortable();
      }

      if (TOQUE == 1) {
        this.$("#toque_hora_inicio_1").mask("99:99");
        this.$("#toque_hora_fin_1").mask("99:99");
        this.$("#toque_hora_inicio_2").mask("99:99");
        this.$("#toque_hora_fin_2").mask("99:99");
        this.$("#toque_hora_inicio_3").mask("99:99");
        this.$("#toque_hora_fin_3").mask("99:99");
      }

      return this;
    },
        
    validar: function() {
      var self = this;
      try {
        $(".error").removeClass("error");

        if (control.check("emails_templates")) { 
          this.model.set({
            "id_email_carrito_abandonado":self.$("#web_seo_email_carrito_abandonado").val(),
            "id_email_post_compra":self.$("#web_seo_id_email_post_compra").val(),
            "id_email_registro":self.$("#web_seo_id_email_registro").val(),
          });
        }

        if (this.$("#web_seo_tienda_registro_direccion").length > 0) {
          this.model.set({
            "tienda_registro_direccion":(this.$("#web_seo_tienda_registro_direccion").is(":checked")?1:0),
            "tienda_registro_ciudad":(this.$("#web_seo_tienda_registro_ciudad").is(":checked")?1:0),
            "tienda_registro_telefono":(this.$("#web_seo_tienda_registro_telefono").is(":checked")?1:0),
            "tienda_registro_documento":(this.$("#web_seo_tienda_registro_documento").is(":checked")?1:0),
            "tienda_registro_password":(this.$("#web_seo_tienda_registro_password").is(":checked")?1:0),
          });
        }
        if (this.$("#web_seo_clienapp_abierto").length > 0) {
          this.model.set({
            "clienapp_formulario":(this.$("#web_seo_clienapp_formulario").is(":checked")?0:1),
            "clienapp_mostrar_email":(this.$("#web_seo_clienapp_mostrar_email").is(":checked")?1:0),
            "clienapp_abierto":(this.$("#web_seo_clienapp_abierto").is(":checked")?1:0),
            "clienapp_sonido":(this.$("#web_seo_clienapp_sonido").is(":checked")?1:0),
          });
        }

        if (this.$("#web_seo_orden_listado").length > 0) {
          this.model.set({
            "orden_listado":(this.$("#web_seo_orden_listado").val()),
          });
        }

        if (this.$("#crm_notificar_asignaciones_usuarios").length > 0) {
          this.model.set({
            "crm_notificar_asignaciones_usuarios":(this.$("#crm_notificar_asignaciones_usuarios").is(":checked")?1:0),
            "crm_notificar_tareas":(this.$("#crm_notificar_tareas").is(":checked")?1:0),
          });
        }

        if (this.$("#crm_enviar_emails_usuarios").length > 0) {
          // Diferencia si se quiere enviar un email de una consulta al email de la empresa
          // o si a los usuarios asignados a la propiedad
          this.model.set({
            "crm_enviar_emails_usuarios":(this.$("#crm_enviar_emails_usuarios").val()),
          });
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

        if (this.$("input[name=conversion_automatica]").length > 0) {
          this.model.set({
            "conversion_automatica":self.$("input[name=conversion_automatica]:checked").val()
          });
        }

        if (this.$("#web_seo_red_inmovar").length > 0) {
          this.model.set({
            "red_inmovar":(self.$("#web_seo_red_inmovar").is(":checked") ? 1 : 0),
          });
        }

        if (this.$("#web_seo_tokko_enviar_consultas").length > 0) {
          this.model.set({
            "tokko_enviar_consultas":(self.$("#web_seo_tokko_enviar_consultas").is(":checked") ? 1 : 0),
            "tokko_importacion":(self.$("#web_seo_tokko_importacion").is(":checked") ? 1 : 0),
          });
        }

        if (this.$("#toque_cobro_efectivo").length > 0) {
          this.model.set({
            "mostrar_numeros_direccion_detalle":(self.$("#toque_cobro_efectivo").is(":checked") ? 1 : 0),
          });
        }

        this.model.set({
          "id_punto_venta":((this.$("#web_seo_puntos_venta").length > 0) ? this.$("#web_seo_puntos_venta").val() : 0),
        });
        return true;
      } catch(e) {
        return false;
      }
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

    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.save({},{
          success: function(model,response) {
            location.reload();
          }
        });
      }
		},		
	});

})(app.views, app.models);