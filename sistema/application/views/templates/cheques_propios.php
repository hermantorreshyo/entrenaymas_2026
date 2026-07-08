<script type="text/template" id="cheques_calendario_template">
    
    <div class="titulo mb10">
        Cheques Entregados
    </div>
    
    <div id="calendario"></div>
	
</script>

<script type="text/template" id="cheques_propios_panel_template">
    
    <h2 class="titulo">Cheques Propios</h2>
	
    <div class="mb10">
	<div class="row mb5">
	    <span class="fl fwn w50 pt0">Mostrar: </span>
	    <input type="radio" class="ml5 fl" name="entregado" value="0" checked/>
	    <span class="fl fwn ml5 pt0">No Entregados</span>
	    <input type="radio" class="ml15 fl" name="entregado" value="-1"/>
	    <span class="fl fwn ml5 pt0">Todos</span>
	    
	    <span class="fl fwn ml5 pt0 ml15">Numero: </span>
	    <input class="w80 pr t-3 ml5 fl input" type="text" id="cheques_propios_buscar_numero"/>
	</div>
	<div class="row oh pt0">
	    <span class="fl fwn w50">Cliente:&nbsp;&nbsp;&nbsp;</span>
	    <div class="select_container select300" id="cheques_propios_table_clientes_select_container"></div>
	</div>
	<div class="row oh">
	    <span class="fl fwn w50">Banco:&nbsp;&nbsp;&nbsp;</span>
	    <div class="select_container" id="cheques_propios_table_bancos_select_container">
	    	
	    </div>
	    <button class="button azul fs12 fl ml10 buscar">Buscar</button>
	</div>
    </div>

    <div class="tabla_container">
	<table id="cheques_propios_table">
	    <thead>
		<tr>
		    <th>Banco</th>
		    <th>Numero</th>
		    <th>Monto</th>
		    <% if (permiso > 1) { %>
			<th class="col_chica"></th>
			<th class="col_chica"></th>
		    <% } %>
		</tr>
	    </thead>
	    <tbody>
		<tr>
		    <td colspan="5" style="text-align:center"><img src="/resources/images/loading.gif"/></td>
		</tr>            
	    </tbody>
	</table>
	<div class="pagination_container"></div>
    </div>
	
    <div class="row">
	<div class="fr">
	    <span class="fl ml10 bold">Leyenda: </span>
	    <div class="fl ml5">
		<div class="fwn">No Entregados</div>
		<div class="fila_verde fwn">Entregados</div>
		<div class="fila_rojo fwn">Devueltos</div>
		<div class="fila_rojo fwn tachado">Anulados</div>
	    </div>
	</div>
    </div>
</script>


<script type="text/template" id="cheques_propios_item">
    <% var clase = "";
    if (entregado == 1) clase = "fila_verde";
    if (devuelto == 1) clase = "fila_roja";
    if (anulado == 1) clase = "fila_roja tachado";
    %>
    <td><span class='ver <%= clase %>'><%= banco %></span></td>
    <td><span class='ver <%= clase %>'><%= numero %></span></td>
    <td class="tar"><span class='ver <%= clase %>'><%= Number(monto).format() %></span></td>
    <% if (permiso > 1) { %>
	<td><img src="resources/images/edit.png" class="edit" alt="Edit"/></td>
	<td><img src="resources/images/delete.png" class="delete" alt="Delete"/></td>
    <% } %>
</script>

<script type="text/template" id="cheques_propios_edit_panel_template">

    <div class="titulo">
	<% if (numero == "") { %>
	    Nuevo Cheque Propio
	<% } else { %>
	    Modificar Cheque Propio
	<% } %>	
    </div>
    
    <div class="row cb">
	<label class="fwn mr10 w50 mt3">Numero: </label>
	<% if (edicion) { %>
	    <input type="text" name="numero" class="input w100" id="cheques_propios_numero" value="<%= numero %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= numero %></span>
	<% } %>
    </div>
    
    <div class="row cb">
	<label class="fwn mr10 fl w40 mt3">Banco: </label>
	<div class="select_container fl ml10" id="cheques_propios_banco_select_container"></div>
    </div>
	    
    <div class="row cb">
	<label class="fwn mr10 w50 mt3">Monto: </label>
	<% if (edicion) { %>
	    <input type="text" name="monto" class="input w100" id="cheques_propios_monto" value="<%= monto %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= monto %></span>
	<% } %>
    </div>
	    
    <div class="row cb">
	<label class="fwn mr10 w100 mt3">Fecha de Emision: </label>
	<% if (edicion) { %>
	    <input type="text" name="fecha_emision" class="input w100" id="cheques_propios_fecha_emision" value="<%= fecha_emision %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= fecha_emision %></span>
	<% } %>
    </div>

    <div class="row cb">
	<label class="fwn mr10 w100 mt3">Fecha de Cobro: </label>
	<% if (edicion) { %>
	    <input type="text" name="fecha_cobro" class="input w100" id="cheques_propios_fecha_cobro" value="<%= fecha_cobro %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= fecha_cobro %></span>
	<% } %>
    </div>
    
    <div class="row cb">
	<label class="fwn mr10 w100 mt3">Fecha Debitado: </label>
	<% if (edicion) { %>
		<input type="text" name="fecha_debitado" class="input w100" id="cheques_propios_fecha_debitado" value="<%= fecha_debitado %>"/>
	<% } else { %>
		<span><%= fecha_debitado %></span>
	<% } %>
    </div>
    
    
    <div class="row cb">
	<input class="pr t2" type="checkbox" name="entregado" id="cheques_propios_entregado" <%= (entregado==1)?"checked":"" %> />
	<span class="fwn ml5">Cheque Entregado</span>
    </div>	
    
    <div class="row cb">
	<input class="pr t2" type="checkbox" name="anulado" id="cheques_propios_anulado" <%= (anulado==1)?"checked":"" %> />
	<span class="fwn ml5">Cheque Anulado</span>
    </div>
    
    
    <% if (numero != "") { %>
	    
	<div class="row cb">
	    <input class="pr t2" type="checkbox" name="devuelto" id="cheques_propios_devuelto" <%= (devuelto==1)?"checked":"" %> />
	    <span class="fwn ml5">El cheque fue devuelto:</span>
	</div>

	<div class="row cb">
	    <label class="fwn mr10 w50">Motivo: </label>
	    <% if (edicion) { %>
		<input type="text" name="motivo" class="input" id="cheques_propios_motivo" value="<%= motivo %>"/>
	    <% } else { %>
		<span><%= motivo %></span>
	    <% } %>
	</div>
	    
    <% } %>
    
    
    <% if (edicion) { %>
	<div class="row">
	    <button class="button verde guardar">Guardar</button>
	    <button class="button verde limpiar">Limpiar</button>
	    <% if (id_orden_pago != 0) { %>
		<button class="button rosa orden_pago fr">Ver Orden de Pago</button>
	    <% } %>
	</div>
    <% } %>

</script>



<script type="text/template" id="cheques_propios_busqueda_template">
	
    <div class="titulo mb10">
	Entregar Cheques Propios
    </div>
    
    <div class="panel_m0">
    
	<div class="row cb mb10">
	    <label class="w110">Banco:</label>
	    <div class="select_container" id="cheques_propios_busqueda_banco"></div>
	</div>
	
	<div class="row cb mb10">
	    <label class="w110">Nro. de Cheque:</label>
	    <div class="select_container" id="cheques_propios_busqueda_cheque">
		<select class="select"></select>
	    </div>
	</div>
	
	<div class="row cb mb10">
	    <label class="w110">Monto:</label>
	    <input type="text" name="monto" id="cheques_propios_busqueda_monto" class="input w90 fs14 bold"/>
	</div>
	
	<div class="row cb mb10">
	    <label class="w110">Fecha de Emisi&oacute;n:</label>
	    <input type="text" name="fecha_emision" id="cheques_propios_busqueda_fecha_emision" class="input w90 fs14 bold"/>
	</div>
	
	<div class="row cb mb10">
	    <label class="w110"	>Fecha de Cobro:</label>
	    <input type="text" name="fecha_cobro" id="cheques_propios_busqueda_fecha_cobro" class="input w90 fs14 bold"/>
	    <a href="/app/#listado_cheques" class="button verde ml5" target="_blank">Listado</a>
	    <a href="/app/#calendario_cheques" class="button verde" target="_blank">Calendario</a>
	</div>
	    
    </div>
    
    <button class="button fr verde entregar mt10 mb10">Entregar</button>
	
</script>