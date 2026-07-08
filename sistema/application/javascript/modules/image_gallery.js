(function ( app ) {

app.views.ImageGallery = app.mixins.View.extend({

    template: _.template($("#image_gallery_template").html()),

    myEvents: {
        "click .buscar":"buscar",
    },

    initialize: function(options) {
        $(this.el).html(this.template(this.model.toJSON()));
        this.page = 0;
        this.offset = 1;
        this.buscar();
    },

    buscar: function() {
        var self = this;
        $.ajax({
            "url":"galerias_imagenes/function/ver/",
            "dataType":"json",
            "type":"post",
            "data":{
                "limit":(self.page * self.offset),
                "offset":self.offset,
            },
            "success":function(r) {
                for(var i=0;i<r.results.length;i++) {
                    var o = r.results[i];
                    var image = new app.models.GaleriaImagen();
                    image.set(o);
                    var item = new app.views.ImageGalleryItem({
                        model:image,
                    });
                    $("#image_gallery_list").append(item.el);
                }
                self.page++;
            }
        })
    },

});

})(app);


(function ( app ) {
app.views.ImageGalleryItem = app.mixins.View.extend({
    template: _.template($("#image_gallery_item_template").html()),
    className: "col-md-3",
    myEvents: {
        "click .seleccionar":"seleccionar",
    },
    seleccionar: function(e) {
        var src = $(e.currentTarget).attr("src");
        if (src.indexOf("?")>=0) src = src.substr(0,src.indexOf("?"));
        this.open_crop(e,src);
    },
});
})(app);