// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Ocupacion = Backbone.Model.extend({
        urlRoot: "ocupaciones",
        defaults: {
            id_habitacion: 0,
            id_empresa: ID_EMPRESA,
            fecha: "",
            disponible: 0,
        },
    });
	    
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.OcupacionesTableView = app.mixins.View.extend({

        template: _.template($("#ocupaciones_template").html()),
            
        myEvents: {
            "click .fc-prev-button":function() {
                this.desde.subtract(this.cantidad_dias,'days');
                this.hasta.subtract(this.cantidad_dias,'days');
                this.render();
            },
            "click .fc-next-button":function() {
                this.desde.add(this.cantidad_dias,'days');
                this.hasta.add(this.cantidad_dias,'days');
                this.render();
            },
            "click .semana":function(e) {
                this.cantidad_dias = 7;
                this.hasta = moment(this.desde).add(this.cantidad_dias,'days');
                this.$(".fc-state-active").removeClass('fc-state-active');
                this.$(e.currentTarget).addClass('fc-state-active');
                this.render();
            },
            "click .quincena":function(e) {
                this.cantidad_dias = 14;
                this.hasta = moment(this.desde).add(this.cantidad_dias,'days');
                this.$(".fc-state-active").removeClass('fc-state-active');
                this.$(e.currentTarget).addClass('fc-state-active');
                this.render();
            },
            "click .mes":function(e) {
                this.cantidad_dias = 30;
                this.hasta = moment(this.desde).add(this.cantidad_dias,'days');
                this.$(".fc-state-active").removeClass('fc-state-active');
                this.$(e.currentTarget).addClass('fc-state-active');
                this.render();
            },
            "change .disp_input":function(e){
                var id = $(e.currentTarget).data("id");
                var fecha = $(e.currentTarget).data("fecha");
                var id_habitacion = $(e.currentTarget).data("id_habitacion");
                var valor = $(e.currentTarget).val();
                if (valor > 0) {
                    $(e.currentTarget).removeClass("bg-danger");
                    $(e.currentTarget).addClass("bg-success");
                } else {
                    $(e.currentTarget).removeClass("bg-success");
                    $(e.currentTarget).addClass("bg-danger");
                }

                // Guardamos el modelo
                var modelo = new app.models.Ocupacion({
                    "id":id,
                    "fecha":fecha,
                    "disponible":valor,
                    "id_empresa":ID_EMPRESA,
                    "id_habitacion":id_habitacion,
                });
                modelo.save({
                    "success":function(r) {
                        console.log(r);
                    }
                });
            },
            "keyup .disp_input":function(e){
                if (e.which == 13) { 
                    var next = parseInt($(e.currentTarget).attr("tabindex"))+1;
                    this.$(".disp_input[tabindex="+next+"]").select();
                }
            },
        },
        
        initialize : function (options) {
            
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.permiso = this.options.permiso;

            this.cantidad_dias = 14;
            this.desde = moment();
            this.hasta = moment().add(this.cantidad_dias,'days');
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "desde":this.desde,
                "hasta":this.hasta,
            }));
            this.render();
        },
        
        render: function() {

            var self = this;
            var thead="<tr>";
            thead+="<th style='width:150px'>Habitaci&oacute;n</th>";
            var desde = moment(this.desde);
            var hasta = moment(this.hasta);
            for (var m = moment(desde); m.isBefore(hasta); m.add(1, 'days')) {
                var c = (m.format('d') == 0 || m.format('d') == 6) ? "finde":"";
                thead+="<th class='"+c+"'>"+m.format('dd')+"<br/>"+m.format("DD/MM")+"</th>";
            }
            thead+="</tr>";
            this.$("#ocupaciones_table thead").html(thead);

            $.ajax({
                "url":"ocupaciones/function/ver/",
                "dataType":"json",
                "data":{
                    "desde":desde.format("YYYY-MM-DD"),
                    "hasta":hasta.format("YYYY-MM-DD"),
                },
                "type":"post",
                "success":function(r) {

                    self.$("#ocupaciones_table tbody").empty();
                    var tbody = "";
                    for(var i=0;i<r.length;i++) {
                        var o = r[i];
                        tbody+="<tr>";
                        tbody+="<td class='hab_nombre'><span>"+o.nombre+"</span></td>";
                        for(var j=0;j<o.disponibilidad.length;j++) {
                            var u = o.disponibilidad[j];
                            var tabindex = ((j*r.length)+i)+1;
                            var c = (u.disponible==0)?"bg-danger":"bg-success";
                            var input = "<input data-id_habitacion='"+o.id+"' data-fecha='"+u.fecha+"' data-id='"+u.id+"' tabindex='"+tabindex+"' type='number' min='0' max='"+o.capacidad+"' class='"+c+" form-control disp_input' value='"+u.disponible+"'/>";
                            tbody+="<td>"+input+"</td>";
                        }
                        tbody+="</tr>";
                    }
                    self.$("#ocupaciones_table tbody").append(tbody);
                }
            })
        },
        
    });

})(app);