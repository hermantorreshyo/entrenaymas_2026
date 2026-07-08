<script type="text/template" id="iva_compras_parametros_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Iva Compras</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="">  
          <div class="form-inline">
            <% if (control.check("razones_sociales")>0) { %>
              <label class="control-label mt7 mr15">Razon Social</label>
              <div class="form-group">
                <select class="form-control" id="iva_compras_razones_sociales"></select>
              </div>
            <% } %>
            <label class="control-label mt7 mr15">Movimiento</label>
            <div class="form-group">
              <select id="iva_compras_movimiento_mes" class="form-control">
                <option <%= (mes=="1")?"selected":"" %> value='01'>Enero</option>
                <option <%= (mes=="2")?"selected":"" %> value='02'>Febrero</option>
                <option <%= (mes=="3")?"selected":"" %> value='03'>Marzo</option>
                <option <%= (mes=="4")?"selected":"" %> value='04'>Abril</option>
                <option <%= (mes=="5")?"selected":"" %> value='05'>Mayo</option>
                <option <%= (mes=="6")?"selected":"" %> value='06'>Junio</option>
                <option <%= (mes=="7")?"selected":"" %> value='07'>Julio</option>
                <option <%= (mes=="8")?"selected":"" %> value='08'>Agosto</option>
                <option <%= (mes=="9")?"selected":"" %> value='09'>Septiembre</option>
                <option <%= (mes=="10")?"selected":"" %> value='10'>Octubre</option>
                <option <%= (mes=="11")?"selected":"" %> value='11'>Noviembre</option>
                <option <%= (mes=="0")?"selected":"" %> value='12'>Diciembre</option>
              </select>            
            </div>
            <div class="form-group w-sm">
              <input type="text" id="iva_compras_movimiento_anio" class="form-control w100" value="<%= anio %>"/>
            </div>
            <label class="control-label mt7 mr15">Pagina desde</label>
            <div class="form-group">
              <input type="number" class="form-control" value="1" id="iva_compras_desde" />
            </div>
            <div class="form-group ml10">
              <button class="btn btn-success imprimir">Generar</button>
              <button class="btn btn-success citi">Archivos</button>
            </div>
          </div>
        </div>
        <div>
          <div class="form-inline">
            <label class="control-label mt7 mr15">Cerrar el libro de IVA para ese movimiento (una vez cerrado no podra ingresar comprobantes en el mismo)</label>
            <div class="form-group">
              <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                <input type="checkbox" id="iva_compras_cerrar" class="checkbox" value="1">
                <i></i>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div> 
  </div>
</script>