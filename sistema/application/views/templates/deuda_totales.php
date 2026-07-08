<script type="text/template" id="deuda_totales_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
  <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-users icono_principal"></i>Proveedores
    / <b>Deuda General</b>
  </h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="clearfix">
        <div style="display: inline-block">
          <div class="dib w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_totales_fecha" placeholder="Fecha">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="dib">
            <button tabindex="-1" type="button" class="btn pull-left btn-default generar"><i class="fa fa-search"></i></button>
          </div>
        </div>
        <div class="pull-right">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="deuda_totales_tabla" class="table table-small table-striped sortable m-b-none default footable">
          <thead></thead>
          <tbody id="deuda_totales_tbody" class="tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</script>