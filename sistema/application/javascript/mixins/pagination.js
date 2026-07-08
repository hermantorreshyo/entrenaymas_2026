(function ( mixins ) {

	mixins.PaginationView = Backbone.View.extend({

		template: _.template($('#pagination_template').html()),

		events: {
			'click a.servernext': 'nextResultPage',
			'click a.serverprevious': 'previousResultPage',
			'click a.orderUpdate': 'updateSortBy',
			'click a.serverlast': 'gotoLast',
			'click a.page': 'gotoPage',
			'click a.serverfirst': 'gotoFirst',
			'click a.serverpage': 'gotoPage',
			'change .serverhowmany select': function(e){
				e.preventDefault();
				var per = $(e.target).val();				
				this.changeCount(per);
			},
			'change .records select': 'gotoPage2'
		},

		tagName: 'tr',

		initialize: function (options) {
			
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
			this.options = options;
			this.collection.on('all', this.render, this);
			
			// Configuracion de la paginacion
			
			if (this.options.ver_numeros_pagina !== undefined) this.numeros_pagina = this.options.ver_numeros_pagina;
			else this.ver_numeros_pagina = true;
			
			if (this.options.ver_pagina_de !== undefined) this.ver_pagina_de = this.options.ver_pagina_de;
			else this.ver_pagina_de = true;

			if (this.options.ver_botones !== undefined) this.ver_botones = this.options.ver_botones;
			else this.ver_botones = true;

			if (this.options.ver_filas_pagina !== undefined) this.ver_filas_pagina = this.options.ver_filas_pagina;
			else this.ver_filas_pagina = true;

			if (this.options.guardar_pagina !== undefined) this.guardar_pagina = this.options.guardar_pagina;
			else this.guardar_pagina = false;
			
		},

		render: function () {
			var obj = {
				ver_numeros_pagina: this.ver_numeros_pagina,
				ver_pagina_de: this.ver_pagina_de,
				ver_botones: this.ver_botones,
				ver_filas_pagina: this.ver_filas_pagina
			};
            
      var info = this.collection.info();
      if (this.guardar_pagina) {
        window.currentPage = info.currentPage;
      }
			$.extend(obj,info);
      this.$el.html(this.template(obj));
		},

    getPage: function() {
      return this.collection.info().currentPage;
    },
        
		updateSortBy: function (e) {
			e.preventDefault();
			var currentSort = this.$el.find('.sortByField').val();
			this.collection.updateOrder(currentSort);
		},

		nextResultPage: function (e) {
			e.preventDefault();
      if (!$(e.currentTarget).parent().hasClass("disabled")) {
        this.collection.nextPage();
      }
		},

		previousResultPage: function (e) {
			e.preventDefault();
			if (!$(e.currentTarget).parent().hasClass("disabled")) {
				this.collection.previousPage();
			}
		},

		gotoFirst: function (e) {
			e.preventDefault();
			if (!$(e.currentTarget).parent().hasClass("disabled")) {
				this.collection.goTo(this.collection.information.firstPage);	
			}
		},

		gotoLast: function (e) {
			e.preventDefault();
			if (!$(e.currentTarget).parent().hasClass("disabled")) {
				this.collection.goTo(this.collection.information.lastPage);
			}
		},

		gotoPage: function (e) {
			e.preventDefault();
			if (!$(e.currentTarget).parent().hasClass("disabled")) {
				var page = $(e.target).text();
				this.collection.goTo(page);
			}
		},
		
		gotoPage2: function (e) {
			e.preventDefault();
			if (!$(e.currentTarget).parent().hasClass("disabled")) {
				var page = $(e.target).val();
				this.collection.goTo(page);
			}
		},		

		changeCount: function (per) {
			this.collection.howManyPer(per);
		}

	});
})(app.mixins);