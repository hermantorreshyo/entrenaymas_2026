<script type="text/template" id="iva_ventas_parametros_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Iva Ventas</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="panel panel-default">
      <div class="panel-body pb0">
        <div class="row m-b">
          <div class="col-md-2 col-xs-12">
            <label class="text-muted">Fecha desde</label>
            <input type="text" class="form-control" id="iva_ventas_fecha_desde" />
          </div>
          <div class="col-md-2 col-xs-12">
            <label class="text-muted">Fecha hasta</label>
            <input type="text" class="form-control" id="iva_ventas_fecha_hasta" />
          </div>
          <div class="col-md-2 col-xs-12">
            <label class="text-muted">Pagina desde</label>
            <input type="number" class="form-control" value="1" id="iva_ventas_desde" />
          </div>
          <div class="col-md-2 col-xs-12">
            <label class="text-muted">&nbsp;</label>
            <div>
              <button id="iva_ventas_buscar" class="btn btn-success generar btn-addon">
                <i class="fa fa-print"></i>
                <span class="hidden-xs">Imprimir Libro</span>
              </button>
            </div>
          </div>        
          <div class="col-md-2 col-xs-12">
            <label class="text-muted">&nbsp;</label>
            <div>
              <button id="iva_ventas_citi" class="btn btn-success citi">
                Exportar Archivo
              </button>
            </div>
          </div>  
          <% if (ID_EMPRESA == 135) { %>
            <div class="col-md-2 col-xs-12">
              <label class="text-muted">&nbsp;</label>
              <div>
                <button id="iva_ventas_por_concepto" class="btn btn-success">
                  Ventas por concepto
                </button>
              </div>
            </div>      
          <% } %>
        </div>
      </div>
    </div>
  </div>
</script>