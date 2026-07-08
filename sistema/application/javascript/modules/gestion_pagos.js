(function ( views, models ) {

  views.GestionPagosView = app.mixins.View.extend({

    template: _.template($("#gestion_pagos_template").html()),

    myEvents: {
      "change #gestion_pagos_fecha_desde":"buscar",
      "change #gestion_pagos_fecha_hasta":"buscar",
      "change #gestion_pagos_proyectos":"buscar",
      "change #gestion_pagos_usuarios":"buscar",
      "change #gestion_pagos_estados":"buscar",
      "click .buscar":"buscar",
    },
      
    initialize: function() {
      _.bindAll(this);
      var self = this;
      $(this.el).html(this.template());
      var desde = moment().subtract(3,'month').format("01/MM/YYYY");
      createdatepicker(this.$("#gestion_pagos_fecha_desde"),desde);
      var hasta = moment().format("DD/MM/YYYY");
      createdatepicker(this.$("#gestion_pagos_fecha_hasta"),hasta);      
    },

    buscar: function() {
      var self = this;
      var desde = this.$("#gestion_pagos_fecha_desde").val();
      var hasta = this.$("#gestion_pagos_fecha_hasta").val();
      var id_proyecto = 0;
      var estado_empresa = 0;
      var id_vendedor = 0;
      if (ID_EMPRESA == 936) {
        id_proyecto = this.$("#gestion_pagos_proyectos").val();
        estado_empresa = this.$("#gestion_pagos_estados").val();
        id_vendedor = this.$("#gestion_pagos_usuarios").val();
      }
      $.ajax({
        "url":"clientes/function/ver_calendario_pagos/",
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
      this.$("#gestion_pagos_tabla").empty();
      var tabla = new app.views.GestionPagosTableView({
        model: new app.models.AbstractModel(r)
      });
      this.$("#gestion_pagos_tabla").append(tabla.el);
    }

  });

})(app.views, app.models);


(function ( views, models ) {

  views.GestionPagosTableView = app.mixins.View.extend({

    template: _.template($("#gestion_pagos_tabla_template").html()),

    initialize: function() {
      _.bindAll(this);
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },

  });

})(app.views, app.models);
777