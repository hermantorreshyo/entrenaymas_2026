// -----------
//   MODELO
// -----------

(function ( models ) {

    models.TipoAlicuotaIva = Backbone.Model.extend({
        urlRoot: "tipos_alicuotas_iva/",
        defaults: {
            nombre: "",
            porcentaje: 0,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.TiposAlicuotasIva = paginator.requestPager.extend({

	model: model,

	paginator_core: {
	    url: "tipos_alicuotas_iva/"
	}
	    
    });

})( app.collections, app.models.TipoAlicuotaIva, Backbone.Paginator);