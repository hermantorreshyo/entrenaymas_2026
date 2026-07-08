<script type="text/template" id="sindi_recetarios_panel_template">
<% if (small == 0) { %>
  <div class="panel-body clearfix">
    <div class="row">
      <div class="col-md-6 col-lg-3 sm-m-b">
        <div class="search_container">
        </div>
      </div>
      <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
        <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
      </div>
    </div>
  </div>
<% } %>
<div class="<%= (small==0) ? "panel-body":"panel-footer" %>">
  <% if (small == 1) { %>
    <p class="tac">Ultimos Recetarios del Afiliado</p>
  <% } %>
  <div class="b-a table-responsive">
    <table id="sindi_recetarios_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th class="tac" data-sort-by="numero">Recetario Nº</th>
          <th class="tac" data-sort-by="fecha">Fecha</th>
          <th colspan="2" class="tac" data-sort-by="id_afiliado">Afiliado</th>
          <% if (small==0) { %>
            <th class="tac" data-sort-by="id_condicion_especial">Condición Especial</th>
          <% } %>
          <th class="tac" data-sort-by="porcentaje">Porcentaje</th>
          <th class="tac">Estado</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot class="pagination_container hide-if-no-paging"></tfoot>
    </table>
  </div>
</div>
</script>


<script type="text/template" id="sindi_recetarios_item">
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= numero %></span></td>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= moment(fecha).format('DD/MM/YYYY') %></span></td>
  <td class="<%= (small==0)?"ver":"" %> tar"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= codigoafiliado %>-<%= identificadorafiliado %></span></td>
	<td class="<%= (small==0)?"ver":"" %>"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= nombreafiliado %></span></td>
  <% if (small==0) { %>
    <td class="ver tac"><span class='text-info'><%= condicionespecial %></span></td>
  <% } %>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= porcentaje %>%</span></td>
	<td class="<%= (small==0)?"ver":"" %> tac"><span style="display: block" class="label label-<%= (anulada == 0)?'success">Entregada':'danger">Anulada' %></span></td>
</script>

<script type="text/template" id="sindi_recetarios_edit_panel_template">
<form onsubmit="return false" class="modal-content">

  <div class='modal-header' style="height: 53px">
    <div class="row">
      <div class="col-md-3">
         <b><%= (id == undefined) ? 'Nueva Recetario' : 'Recetario Nº '+numero %><b>
      </div>
      <div class="col-md-3 tac">
        <%= (id == undefined) ? '' : fecha %>
      </div>
      <div class="col-md-6 tar">
          <% if (id == undefined) { %>
            (<%= (window.afiliado == null)?'':window.afiliado.codigo %>/<%= (window.afiliado == null)?'':window.afiliado.identificador %>)<b> <%= (window.afiliado == null)?'':window.afiliado.nombre %></b>
          <% } else { %>
            (<%= codigoafiliado %>/<%= identificadorafiliado %>)<b> <%= nombreafiliado %></b>
          <% } %>
      </div>
    </div>
  </div>
  <div class="modal-body pb0 <%= (supero_limite_40_50 == 1 && supero_limite_70 == 1 && supero_limite_100 == 1 )?"":"dn" %>">
    <div class="dt pl10">
      <div class="dtc vam">
        <span><i class="fa fa-exclamation-circle text-danger fa-2x" aria-hidden="true"></i></span>
      </div>
      <div class="dtc vam pl10">
        <span>ALCANZADOS LOS LIMITES MENSUALES ASIGNADOS. NO PUEDE DAR RECETARIOS A ESTE AFILIADO. </span>
      </div>
    </div>
       
  </div>  
  <div class="modal-body pb0 <%= (supero_limite_40_50 == 1 && supero_limite_70 == 1 && supero_limite_100 == 1 )?"dn":"" %>">
      <div class="row">            
        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label">Porcentaje</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="porcentaje" class="form-control" id="sindi_recetarios_porcentaje">
              <% if (id != undefined) { %>
                <% if (supero_limite_40_50 == 0) { %>
                  <option value="40" <%= (porcentaje==40)?"selected":"" %>> 40%</option>
                  <option value="50" <%= (porcentaje==50)?"selected":"" %>> 50%</option>
                <% } %>  
                <% if (supero_limite_70 == 0) { %>                
                  <option value="70" <%= (porcentaje==70)?"selected":"" %>> 70%</option>
                <% } %>  
                <% if (supero_limite_100 == 0) { %>                    
                  <option value="100" <%= (porcentaje==100)?"selected":"" %>> 100%</option>
                <% } %>  
              <% } else { %>
                <% if ((typeof window.afiliado != "undefined") && (window.afiliado.id_tipo_afiliado == 2 || window.afiliado.id_tipo_afiliado == 6)) { %>
                  <% if (supero_limite_40_50 == 0) { %>                 
                    <option value="40" selected> 40%</option>
                  <% } %>                       
                <% } else { %>
                  <% if (supero_limite_40_50 == 0) { %>                  
                    <option value="50" selected> 50%</option>
                  <% } %>                        
                <% } %>
                <% if (supero_limite_70 == 0) { %>                          
                  <option value="70"> 70%</option>
                <% } %>  
                <% if (supero_limite_100 == 0) { %>                     
                  <option value="100">100%</option>
                <% } %>    
              <% } %>
            </select>
          </div>
        </div>
        <% if (id == undefined) { %>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Cantidad</label>
              <input <%= (id!=undefined)?"disabled":"" %> type="number" min="1" step="1" name="cantidad" autocomplete="off" class="form-control tac no-spinner" id="sindi_recetarios_cantidad" value="<%= cantidad %>"/>
            </div>
          </div>
        <% } %>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Condición Especial <i id="condicion_especial_alerta" style="display:none" class="fa fa-exclamation-triangle text-warning"></i></label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_condicion_especial" class="form-control" id="sindi_recetarios_id_condicion_especial">
            </select>
          </div>
        </div>
      </div>
   </div>
    <div class="tac text-muted fs12">
      <span>(<%= res45real %> Recetarios realizados este mes de <%= res45cant %> que tiene asignados como limite)</span>
    </div>    
    <div class="tac text-muted fs12">
      <span>(<%= res70real %> Recetarios 70% realizados este mes de <%= res70cant %> que tiene asignados como limite)</span>
    </div>  
    <div class="tac text-muted fs12">
      <span>(<%= res100real %> Recetarios 100% realizados este mes de <%= res100cant %> que tiene asignados como limite)</span>
    </div>  
  <div id="tabla_footer_recetarios"></div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
    <% if (id == undefined && !(supero_limite_40_50 == 1 && supero_limite_70 == 1 && supero_limite_100 == 1 ) ) { %>
      <button class="btn guardar btn-success">Guardar</button>
    <% } else { %>
      <% if (id != undefined && permiso > 2 && anulada == 0) { %>
        <button class="btn anular btn-danger">Anular</button>
      <% } %>
    <% } %>
  </div>

</form>

</script>