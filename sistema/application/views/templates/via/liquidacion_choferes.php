<script type="text/template" id="liquidacion_choferes_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>
    <b>Liquidaci&oacute;n</b>
  </h1>
</div>  
<div class="wrapper-md">
  <div class="panel panel-default">
  
    <div class="panel-heading clearfix">
      <div class="row pl10 pr10">

        <div class="col-md-2 col-sm-4 col-xs-12 pr5 pl5">
          <select class="form-control no-model" id="liquidacion_choferes_tripulantes"></select>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 pr5 pl5">
          <div class="input-group">
            <span class="input-group-btn">
              <select class="form-control key w130" id="liquidacion_choferes_mes">
                <option value="01">Enero</option>
                <option value="02">Febrero</option>
                <option value="03">Marzo</option>
                <option value="04">Abril</option>
                <option value="05">Mayo</option>
                <option value="06">Junio</option>
                <option value="07">Julio</option>
                <option value="08">Agosto</option>
                <option value="09">Sep</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>            
              </select>
            </span>
            <input type="number" min="0" class="enterToNext form-control key" id="liquidacion_choferes_anio"/>
          </div>
        </div>
        <div class="col-sm-4 col-xs-12 pr5 pl5">
          <button class="btn pull-left btn-default buscar"><i class="fa fa-search"></i> Buscar</button>
          <button class="btn pull-left btn-default exportar"><i class="fa fa-upload"></i> Exportar</button>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Concepto</th>
              <th class="tar">Base</th>
              <th class="tar">Porcentaje</th>
              <th class="tar">Subtotal</th>  
            </tr>
          </thead>
          <tbody id="liquidacion_choferes_tbody" class="tbody"></tbody>
          <tfoot class="bg-important">
            <tr>
              <td class="tar bold"></td>
              <td class="tar bold"></td>
              <td class="tar bold">TOTAL</td>
              <td class="tar bold" id="liquidacion_choferes_total"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="liquidacion_choferes_item_resultados_template">
  <td class=""><span class="text-info"><%= concepto %></span></td>
  <td class="tar"><%= Number(base).toFixed(2) %></td>
  <td class="tar"><%= Number(porcentaje).toFixed(2) %></td>
  <td class="tar"><%= Number(monto).toFixed(2) %></td>
</script>