(function ( app ) {

app.views.ImageEditor = Backbone.View.extend({

  template: _.template($("#image_editor_template").html()),

  events: {
    "click .crop-ok":"guardar",
    "change #image_editor_filters":function(e) {
      var v = $(e.currentTarget).val();
      if (v != "reset") {
        this.caman.revert();
        this.caman[v]().render(this.start_cropper);
      } else {
        this.caman.revert();
        this.caman.render(this.start_cropper);
      }
    },
    "click .move-button":function(e) {
      $(e.currentTarget).toggleClass('active');
      if ($(e.currentTarget).hasClass("active")) {
        $("#canvas").cropper("setDragMode","move");  
      } else {
        $("#canvas").cropper("setDragMode","crop");  
      }
    },
    "click .rotate-left":function() {
      this.angle -= 90;
      if (this.angle == 360 || this.angle == -360) this.angle = 0;
      $("#canvas").cropper("rotate",this.angle);
    },
    "click .rotate-right":function() {
      this.angle += 90;
      if (this.angle == 360 || this.angle == -360) this.angle = 0;
      $("#canvas").cropper("rotate",this.angle);
    },
  },

  initialize: function(options) {
    $(this.el).html(this.template());
    this.options_cropper = options.options_cropper;
    this.url = options.url;
    this.component = options.component;
    this.quality = options.quality;
    this.view = options.view;
    this.width = options.width;
    this.height = options.height;
    this.thumbnail_width = (typeof options.thumbnail_width != "undefined") ? options.thumbnail_width : 0;
    this.thumbnail_height = (typeof options.thumbnail_height != "undefined") ? options.thumbnail_height : 0;
    this.angle = 0;
    this.render();
  },

  render: function() {
    var self = this;
    // Creamos el canvas
    this.caman = Caman('#canvas', self.url, function () {
      URL.revokeObjectURL(self.url);
      self.start_cropper();
    });

    // Pickers de colores
    $(".colorpicker-component").colorpicker({
      format: "rgb"
    });
  },

  start_cropper: function() {
    // Destroy if already initialized
    if ($("#canvas").data('cropper')) {
      $("#canvas").cropper('destroy');
    }
    // Initialize a new cropper
    $("#canvas").cropper(this.options_cropper);
    $("#canvas").cropper("rotate",90);
    setTimeout(function(){
      $("#canvas").cropper("zoom",0.05);
    },200);
  },

  dataURItoBlob:function(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
      byteString = atob(dataURI.split(',')[1]);
    else
      byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], {type:mimeString});
  },

  guardar: function() {
    var self = this;
    var width = Number(this.width).toFixed(0);
    var height = Number(this.height).toFixed(0);
    var options = {};
    if (width != 0 && height != 0) {
      options.width = width;
      options.height = height;      
    }

    // Si el archivo es un PNG, lo guardamos como tal; sino simpre JPG
    var formData = new FormData();
    var formato = "image/jpeg";
    var archivo = $("#"+this.component).val();
    var ext = (/[.]/.exec(archivo)) ? String(/[^.]+$/.exec(archivo)).toLowerCase() : undefined;
    if (ext == "png") {
      formato = "image/png";
    } else {
      // Cuando es un JPG, lo rellenamos
      options.fillColor = this.$(".colorpicker-component input").val();
    }

    // Tomamos el recorte
    var crop_canvas = $("#canvas").cropper("getCroppedCanvas",options);

    // Creamos un objeto binario con los datos exportados del canvas
    if (this.quality != 0) {
      this.quality = parseFloat(this.quality);
      var blob = this.dataURItoBlob(crop_canvas.toDataURL(formato,this.quality));
    } else {
      var blob = this.dataURItoBlob(crop_canvas.toDataURL(formato));
    }

    // Exportamos la imagen
    formData.append("imagen",blob);

    // Enviamos el path del archivo, para saber si lo estamos editando o creando
    var f = filename($("#"+this.component+"_src").val());
    formData.append("file",f);

    if (options.thumbnail_width != 0) {
      formData.append("thumbnail_width",this.thumbnail_width);
      formData.append("thumbnail_height",this.thumbnail_height);
    }

    // Para los multiples
    $("#"+this.component+"_tabla").sortable();

    // Tomamos la URL de los parametros
    var url = $("#"+this.component+"_url").val(); // URL donde esta la funcion que guarda
    url = url+"?t="+Math.round((Math.random()*1000000),0);

    // Mandamos la imagen por AJAX
    $.ajax({
      url: url,
      data: formData,
      cache: false,
      dataType: "json",
      processData: false,
      contentType: false,
      type: 'POST',
      success: function(r){
        if (r.error == 1) {
          alert(r.message);
        } else {
          $('.modal:last').trigger('click');

          if (!isEmpty(r.path)) {

            // SI ES UN UPLOAD MULTIPLE
            if ($("#"+self.component).hasClass("multiple_upload")) {

              // Agregamos el elemento al array
              var images = self.model.get(self.component);
              console.log("ARRAY DE IMAGENES")
              console.log(images);
              console.log("IMAGEN BUSCADA");
              console.log(r.path);

              var i = _.find(images,function(e){
                return (e == r.path);
              });
              // Lo agregamos al array
              if (typeof i === "undefined") {
                images.push(r.path);
                self.model.set(self.component,images);
              }
              // Enviamos el evento para que la vista vuelva a renderizar la tabla
              self.model.trigger("change_table");

            // SI ES UN UPLOAD SINGLE
            } else {

              $("#hidden_"+self.component).val(r.path);
              $("#preview_"+self.component).attr("src",r.path+"?t="+parseInt(Math.random()*100000));
              $("#preview_"+self.component).show();
              $("#preview_"+self.component).nextAll("i").show();
              $("#preview_"+self.component).nextAll(".bootstrap-filestyle-container").hide();
              self.model.trigger("upload-success");
            }
          }
          $(".img_loading").hide();
        }
      }
    });
  },

});

})(app);
