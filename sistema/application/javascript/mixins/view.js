(function (app) {

  app.mixins.View = Backbone.View.extend({

    parentEvents: {

      /*
      "focusout .number":function(e) {
        var valor = Number($(e.currentTarget).val()).toFixed(2);
        if (isNaN(valor)) {
          show("Por favor ingrese un numero.");
        } else {
          $(e.currentTarget).val(valor);
        }
      },
      "focusin .number":function(e) {
        $(e.currentTarget).select();
      },
      */
      "click .expand-link":function(e) {
        $(e.currentTarget).parents(".panel-body").next(".expand").slideToggle();
      },

      "click .cerrar_lightbox":"cerrar_lightbox",

      "focusout .integer":function(e) {
        var valor = parseInt($(e.currentTarget).val());
        if (isNaN(valor)) {
          show("Por favor ingrese un numero.");
          $(e.currentTarget).focus();
        } else {
          $(e.currentTarget).val(valor);
        }
      },
      "focusin .integer":function(e) {
        $(e.currentTarget).select();
      },

      "change .form-control": function (e) {
        if ($(e.currentTarget).hasClass("no-model")) return;
        var id = $(e.currentTarget).attr("name");
        var value = $(e.currentTarget).val();
        var objInst = new Object();
        objInst[id] = value;
        if (this.model != undefined) this.model.set(objInst);
        if (typeof this.post_change_form_control != "undefined") this.post_change_form_control(e);
      },

      "change .radio": function(e) {
        if ($(e.currentTarget).hasClass("no-model")) return;
        var name = $(e.currentTarget).attr("name");
        var value = $("input[name='"+name+"']:checked").val();
        var objInst = new Object();
        objInst[name] = value;
        if (this.model != undefined) this.model.set(objInst);
      },

      "change .checkbox": function(e) {
        var name = $(e.currentTarget).attr("name");
        var value = ($(e.currentTarget).is(":checked") ? 1 : 0);
        var objInst = new Object();
        objInst[name] = value;
        if (this.model != undefined) this.model.set(objInst);
      },

      "keyup .esc":function(e){
        if (e.which == 27) { $('.modal:last').modal('hide'); }
      },

      "click .sorting":function(e) {
        if (this.collection == undefined) return;

        var asc = $(e.currentTarget).hasClass("sorting_asc");
        var desc = $(e.currentTarget).hasClass("sorting_desc");
        $(".sorting").removeClass("sorting_asc");
        $(".sorting").removeClass("sorting_desc");
        if (asc) $(e.currentTarget).addClass("sorting_desc");
        else if (desc) $(e.currentTarget).addClass("sorting_asc");
        else $(e.currentTarget).addClass("sorting_desc");

        var sort_by = $(e.currentTarget).data("sort-by");
        if (sort_by == undefined) return;
        var sort = (desc)?"desc":"asc";
        if (typeof this.collection.setSort != "undefined") {
          // Client
          this.collection.setSort(sort_by,sort);
        } else {
          // Request
          this.collection.server_api = {
            "order_by":sort_by,
            "order":sort,
          }
        }
        this.collection.pager();
      },

      // Para mostrar u ocultar la busqueda avanzada
      "click .advanced-search-btn":function(e) {
        var div = $(this.el).find(".advanced-search-div");
        var visible = $(div).is(":visible");
        if (visible) {
          $(div).slideUp();
          var $fa = $(e.currentTarget).find(".fa");
          if (!$fa.hasClass("fa-filter") && !$fa.hasClass("fa-plus-circle")) {
            $fa.removeClass("fa-angle-double-up");
            $fa.addClass("fa-angle-double-down");
          }
          $(e.currentTarget).removeClass("btn-default-focus");
          if (typeof this.close_advanced_search !== "undefined") this.close_advanced_search();
        } else {
          $(div).slideDown();
          var $fa = $(e.currentTarget).find(".fa");
          if (!$fa.hasClass("fa-filter") && !$fa.hasClass("fa-plus-circle")) {
            $fa.removeClass("fa-angle-double-down");
            $fa.addClass("fa-angle-double-up");
          }
          $(e.currentTarget).addClass("btn-default-focus");
          // Si existe la funcion 'open_advanced_search', la ejecutamos
          if (typeof this.open_advanced_search !== "undefined") this.open_advanced_search();
          // Indicamos que ya se abrio una vez la busqueda avanzada
          // Este flag es usado por ejemplo para consultar con AJAX solo la primera vez que se abre
          this.advanced_search_opened = 1; 
        }
      },

      // Si es una FILA de UNA TABLA
      "change .check-row":"marcar",

      "click .sel_todos": function(e) {
        var isChecked = $(e.currentTarget).is(":checked");
        $(e.currentTarget).parents("table").find("tbody .i-checks input[type=checkbox]").each(function(i,ee){
          $(ee).prop("checked",isChecked);
          $(ee).change(); // Para que se marque
        });
      },

      // CROP IMAGE
      "change .single_upload":"open_crop",
      "change .multiple_upload":"open_crop",
      "click .editar_imagen":function(e) {
        var src = $(e.currentTarget).parent().find(".img_preview").attr("src");
        if (src.indexOf("?")>=0) src = src.substr(0,src.indexOf("?"));
        this.open_crop(e,src);
      },
      "click .editar_foto_multiple":function(e) {
        var src = $(e.currentTarget).parent().find(".img_preview").attr("src");
        if (src.indexOf("?")>=0) src = src.substr(0,src.indexOf("?"));
        this.open_crop(e,src);
      },

      // SINGLE UPLOAD
      "click .eliminar_imagen":function(e) {
        $(e.currentTarget).parent().find(".bootstrap-filestyle-container").show();
        $(e.currentTarget).parent().find("img").hide();
        $(e.currentTarget).parent().find(".eliminar_imagen").css("display","none");
        $(e.currentTarget).parent().find(".editar_imagen").css("display","none");
        var id = $(e.currentTarget).data("id");
        $("#hidden_"+id).val("");
      },

      // ELIMINAR FOTO DE MULTIPLE UPLOAD
      "click .eliminar_foto":function(e) {
        if (confirm("Realmente desea eliminar la imagen?")) {
          var path = $(e.currentTarget).prev().text();
          var component = $(e.currentTarget).data("property");
          var images = this.model.get(component);
          var array = _.filter(images,function(e){
            if (typeof e.path != "undefined") return (e.path != path);
            else return (e != path);
          });
          this.model.set(component,array);
          // Enviamos el evento para que la vista vuelva a renderizar la tabla
          this.model.trigger("change_table");
        }
      },

      // SUBIR ARCHIVOS (NO IMAGENES)
      "change .single_file_upload":function(e) {
        var name = $(e.currentTarget).data("name");
        var path = $(e.currentTarget).val();
        $(".img_loading").show();
        var formData = new FormData();
        formData.append("filename",$("#"+name+"_src").val()); // Nombre del archivo
        if (!isEmpty(path)) formData.append("path",$(e.currentTarget)[0].files[0]);
        var url = $("#"+name+"_url_file").val(); // URL donde esta la funcion que guarda
        $.ajax({
          url: url,
          data: formData,
          dataType: "json",
          processData: false,
          contentType: false,
          type: 'POST',
          success: function(r){
            if (r.error == 1) {
              alert(r.mensaje);
            } else {
              if (!isEmpty(r.path)) {
                $("#hidden_"+name).val(r.path);
                if ($("#preview_"+name).hasClass("img_preview")) {
                  $("#preview_"+name).attr("src",r.path);
                } else {
                  $("#preview_"+name).text(r.path);  
                }
                $("#preview_"+name).show();
                $("#preview_"+name).nextAll("i").show();
                $("#preview_"+name).nextAll(".bootstrap-filestyle-container").hide();
              }
            }
            $(".img_loading").hide();
          }
        });
      },

      "click .eliminar_archivo":function(e) {
        $(e.currentTarget).parent().find(".bootstrap-filestyle-container").show();
        $(e.currentTarget).parent().find(".preview_file").hide();
        $(e.currentTarget).parent().find(".eliminar_archivo").hide();
        var id = $(e.currentTarget).data("id");
        $("#hidden_"+id).val("");
      },

      // Click sobre el icono de calendario
      "click .btn-cal":function(e) {
        $(e.currentTarget).parents(".input-group").find(".form-control").select();
      },

      // Click sobre una opcion de idioma
      "click .btn-lang":function(e) {
        var parent = $(e.currentTarget).parents(".lang-control");
        $(parent).find(".form-control").removeClass("active");
        $(parent).find(".form-control-cont").removeClass("active");
        $(parent).find(".btn-lang").removeClass("active");
        var id = $(e.currentTarget).data("id");
        $("#"+id).addClass("active");
        $(e.currentTarget).addClass("active");
      },

      // Vamos escribiendo sobre un INPUT que hay que calcular el texto faltante
      "keyup .text-remain":function(e){
        var id = $(e.currentTarget).data("id");
        var max = $(e.currentTarget).data("max");
        var s = $(e.currentTarget).val().length;
        var r = ((max-s)>0) ? (max-s) : 0;
        $("#"+id).html(s);
      },
    },

    // ABRIMOS EL LIGHTBOX PARA SUBIR MULTIPLES IMAGENES
    open_multiple_upload: function(config) {
      var upload = new app.views.ImageUpload(config);        
      crearLightboxHTML({
        "html":upload.el,
        "width":400,
        "height":400,
        "escapable":false,
      });
    },

    open_crop: function(e,src) {
      var self = this;
      var elem = e.currentTarget;
      e.stopPropagation();

      // Si es uno nuevo
      if (src == undefined) {

        var id = $(elem).attr("id");
        $("#"+id+"_text").val($(elem).val());

      // Si se esta editando
      } else {
        var id = $(elem).attr("data-id");
        $("#"+id+"_src").val(src);
      }

      // PARAMETROS

      if ($("#"+id+"_width").length > 0) {
        var width = $("#"+id+"_width").val();
      } else var width = 0;

      if ($("#"+id+"_height").length > 0) {
        var height = $("#"+id+"_height").val();
      } else var height = 0;

      if ($("#"+id+"_quality").length > 0) {
        var quality = $("#"+id+"_quality").val();
      } else var quality = 0;

      if ($("#"+id+"_resizable").length > 0) {
        var resizable = $("#"+id+"_resizable").val();
      } else var resizable = 1;

      if ($("#"+id+"_thumbnail_width").length > 0) {
        var thumbnail_width = $("#"+id+"_thumbnail_width").val();
      } else var thumbnail_width = 0;

      if ($("#"+id+"_thumbnail_height").length > 0) {
        var thumbnail_height = $("#"+id+"_thumbnail_height").val();
      } else var thumbnail_height = 0;

      if ($("#"+id+"_crop_type").length > 0) {
        var crop_type = $("#"+id+"_crop_type").val();
      } else var crop_type = 1;

      var aspectRadio = 0;
      if (width != 0 && height != 0) {
        aspectRadio = width / height;
      }

      // Fin parametros

      // Si es un archivo nuevo
      if (src == undefined) {
        files = $(elem).prop('files');
        if (files.length == 0) return;
        file = files[0];
        if (!this.isImageFile(file)) return;
        if (this.url) {
          URL.revokeObjectURL(this.url); // Revoke the old one
        }
        this.url = URL.createObjectURL(file);
        var ext = file.name.split('.').pop();
        ext = ext.toLowerCase();
        var d = new Date();
        $("#"+id+"_src").val(d.getTime()+"."+ext); // Nombre del archivo
        src = this.url;

      // Estamos editando un archivo
      } else {
        this.url = src;
      }

      var options = {
        "crop": function (data) {
          var json = [
            '{"x":' + data.x,
            '"y":' + data.y,
            '"height":' + data.height,
            '"width":' + data.width,
            '"rotate":' + data.rotate + '}'
          ].join();
          $("#"+id+"_data").val(json);
        }
      };
      options.strict = true;
      options.viewMode = crop_type;
      options.cropBoxResizable = (resizable==1)?true:false;
      if (resizable == 0) {
        if (aspectRadio != 0) options.aspectRatio = aspectRadio;
        if (width != 0) options.minCropBoxWidth = width;
        if (height != 0) options.minCropBoxHeight = height;
      }
      var editor = new app.views.ImageEditor({
        "model": self.model,
        "view": self,
        "options_cropper": options,
        "url": self.url,
        "component": id,
        "width":width,
        "height":height,
        "quality":quality,
        "thumbnail_height":thumbnail_height,
        "thumbnail_width":thumbnail_width,
      });

      // Abrimos el lightbox
      crearLightboxHTML({
        "html":editor.el,
        "width":700,
        "height":400,
      });
    },

    set_image: function(comp,path) {
      this.$("#"+comp+"_url").val(path);
      this.$("#preview_"+comp).attr("src",path);
      this.$("#hidden_"+comp).val(path.replace("/sistema/",""));
      this.$("#hidden_"+comp).parent().find(".bootstrap-filestyle-container").hide();
      this.$("#hidden_"+comp).parent().find(".editar_imagen").show();
      this.$("#hidden_"+comp).parent().find(".eliminar_imagen").show();
      this.$("#hidden_"+comp).parent().find(".eliminar_imagen").show();
      this.$("#hidden_"+comp).parent().find(".img_preview").show();
    },


    events: function(){
      return _.extend({},this.parentEvents,this.myEvents);
    },

    cerrar_lightbox: function() {
      $('.modal:last').modal('hide');
    },

    // Metodo usado para cambiar una propiedad de un objeto en particular
    change_property: function(data) {
      if (typeof data.table == "undefined") { console.log("change_property: falta definir table."); return false };
      if (typeof data.id == "undefined") { console.log("change_property: falta definir id."); return false };
      if (typeof data.attribute == "undefined") { console.log("change_property: falta definir attribute."); return false };
      if (typeof data.value == "undefined") { console.log("change_property: falta definir value."); return false };
      if (typeof data.id_field == "undefined") { data.id_field = "id"; }
      var url = (typeof data.url != "undefined") ? data.url : data.table+"/function/change_property/";
      var params = {
        "url":url,
        "dataType":"json",
        "type":"post",
        "data":{
          "id":data.id,
          "attribute":data.attribute,
          "value":data.value,
          "table":data.table,
          "id_field":data.id_field,
        },
      };
      if (typeof data.success != "undefined") params.success = data.success;
      $.ajax(params);
    },

        exportar_excel: function(obj) {

            var form = document.createElement("form");
            form.setAttribute("method","post");
            form.setAttribute("target","_blank");
            form.setAttribute("action","exportar/array_to_excel/");

            // Enviamos el nombre del archivo
            if (obj.filename !== undefined) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","filename");
                hidden.setAttribute("value",obj.filename);
                form.appendChild(hidden);
            }

            // Enviamos el encabezado de la tabla
            if (obj.header !== undefined && Array.isArray(obj.header)) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","header");
                hidden.setAttribute("value",JSON.stringify(obj.header));
                form.appendChild(hidden);
            }

            // Enviamos el footer de la tabla
            if (obj.footer !== undefined && Array.isArray(obj.footer)) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","footer");
                hidden.setAttribute("value",JSON.stringify(obj.footer));
                form.appendChild(hidden);
            }

            // Enviamos el titulo del reporte
            if (obj.title !== undefined) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","title");
                hidden.setAttribute("value",obj.title);
                form.appendChild(hidden);
            }

            // Enviamos la fecha del reporte
            if (obj.date !== undefined) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","date");
                hidden.setAttribute("value",obj.date);
                form.appendChild(hidden);
            }

            // Enviamos los datos
            if (obj.data !== undefined && Array.isArray(obj.data)) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","data");
                hidden.setAttribute("value",JSON.stringify(obj.data));
                form.appendChild(hidden);

            // Si no se envian datos en un array, controlamos que se envie el nombre de la tabla a exportar
            } else if (obj.table !== undefined) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","table");
                hidden.setAttribute("value",obj.table);
                form.appendChild(hidden);
            }

            // Enviamos el encabezado de la tabla
            if (obj.where !== undefined) {
                var hidden = document.createElement("input");
                hidden.setAttribute("name","where");
                hidden.setAttribute("value",obj.where);
                form.appendChild(hidden);
            }

            var hidden = document.createElement("input");
            hidden.setAttribute("name","id_empresa");
            hidden.setAttribute("value",ID_EMPRESA);
            form.appendChild(hidden);            

            $(form).css("display","none");
            document.body.appendChild(form);

            form.submit();
        },

        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },

        // Marca la fila de una tabla
        marcar : function(e) {
          e.stopPropagation();
          e.preventDefault();
          var el = e.currentTarget;
          if (el.type == "checkbox") { 
            if ($(el).is(":checked")) {
              $(this.el).addClass("seleccionado");
            } else {
              $(this.el).removeClass("seleccionado");
            }

            // Si hay alguno marcado
            var marcado = false;
            $(".check-row").each(function(i,e){
                if ($(e).is(":checked")) marcado = true;
            });
            if (marcado) $(".bulk_action").slideDown();
            else $(".bulk_action").slideUp();

          } else if (el.type == "radio") {
            $(".seleccionado").removeClass("seleccionado");
            $(this.el).addClass("seleccionado");
          }

          return false;
        },

        ordenable: function() {
            var ordenable = this.$(".ordenable");
            var tabla = $(ordenable).data("ordenable-table");
            if (isEmpty(tabla)) return;
            var array = new Array();
            ordenable.sortable({
                "stop":function(){
                    ordenable.find("li").each(function(i,e){
                        array.push($(e).find(".id").text());
                    });
                    $.ajax({
                        "url":tabla+"/function/ordenar/",
                        "dataType":"json",
                        "type":"post",
                        "data":"ids="+JSON.stringify(array),
                    });
                }
            });
        },

    });

})(app);
