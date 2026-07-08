<script type="text/template" id="orden_pago_preparar_template">
<div class="panel">
    <div class="titulo">
        PREPARAR ORDEN DE PAGO
    </div>
    <div class="cb mt10">
	<label class="fl mt5">Debitar de:</label>
	<div class="select_container select100 ml10">
	    <select class="select" id="ordenes_pago_pendientes_caja">
		<option value="1">Caja Chica</option>
		<option value="2">Caja Grande</option>
	    </select>
	</div>
    </div>
    <div class="row cb mt10">
	<button class="button guardar verde fr">Guardar</button>
    </div>
</div>
</script>


<script type="text/template" id="orden_pago_entregar_template">
<div class="panel">
    <div class="titulo">
        ENTREGAR ORDEN DE PAGO
    </div>
    <div class="cb mt10">
	<label class="fl mt5">Fecha:</label>
	<input type="text" class="input w100 ml10" name="fecha" id="orden_pago_entregar_fecha"/>
    </div>
    <div class="row cb mt10">
	<button class="button guardar verde fr">Guardar</button>
    </div>
</div>
</script>


<script type="text/template" id="ordenes_pago_pendientes_parametros_template">
<div class="panel">
    <div class="titulo">
        ORDENES DE PAGO
    </div>
    <div class="cb mt3">
	<div class="fl" id="ordenes_pago_pendientes_fechas_container"></div>	

	<label class="fl ml15 mt5">Mostrar:</label>
	<div class="select_container select150 ml10 mb5">
	    <select class="select">
		<option value="X">Cargadas y Preparadas</option>
		<option value="C">Solo Cargadas</option>
		<option value="P">Solo Preparadas</option>
		<option value="E">Solo Entregadas</option>
		<option value="">Todas</option>
	    </select>
	</div>

    </div>    
    <div class="cb">
	<input type="text" class="mb10 input w50 fl mr5 mt3" id="ordenes_pago_pendientes_codigo_proveedor"/>
	<div class="select_container select300" id="ordenes_pago_pendientes_select_container"></div>
    	
        <button class="button generar verde fl ml10">Buscar</button>
    </div>
</div>
</script>


<script type="text/template" id="ordenes_pago_pendientes_resultados_template">
<div class="panel">
    <div class="table">
	<table>
	    <thead class="thead">
		<tr>
		    <th style="width:80px">FECHA</th>
		    <th style="width:50px">CODIGO</th>
		    <th style="">PROVEEDOR</th>
		    <th style="width:116px">NUMERO</th>
		    <th style="width:76px">TOTAL</th>
		    <th style="width:70px">ESTADO</th>
		    <th style="width:17px"></th>
		    <th style="width:17px"></th>
		</tr>
	    </thead>
	    <tbody class="tbody" id="ordenes_pago_pendientes_tbody">
		<tr>
		    <td colspan="10">Seleccione los parámetros deseados y haga click en Buscar.</td>
		</tr>		
	    </tbody>
	    <tfoot>
		<tr>
		    <td>&nbsp;</td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td class="tar" id="ordenes_pago_pendientes_total"></td>
		    <td></td>
		    <td></td>
		    <td></td>
		</tr>
	    </tfoot>
	</table>
    </div>
</div>
</script>


<script type="text/template" id="ordenes_pago_pendientes_item_resultados_template">
    <% var clase = ""; var estado_nombre = "ENTREGADA"; %>
    <% if (estado == "C") { clase = "amarillo"; estado_nombre = "CARGADA"; } %>
    <% if (estado == "P") { clase = "verde"; estado_nombre = "PREPARADA"; } %>
    <td class="<%= clase %>"><%= fecha %></td>
    <td class="<%= clase %>"><%= id_proveedor %></td>
    <td class="<%= clase %>"><%= proveedor %></td>
    <td class="<%= clase %>"><%= numero %></td>
    <td class="<%= clase %> tar"><%= Number(total_general).format() %></td>
    <td class="<%= clase %>"><%= estado_nombre %></td>
    <td class="<%= clase %>">
	<% if (estado == "C") { %>
	    <button class="button gris preparar">Preparar</button>
	<% } else if (estado == "P") { %>
	    <button class="button gris entregar">Entregar</button>
	<% } %>
    </td>
    <td class="<%= clase %>"><img class="tooltip edit cp" src='resources/images/edit.png' title="Modificar"/></td>
</script>