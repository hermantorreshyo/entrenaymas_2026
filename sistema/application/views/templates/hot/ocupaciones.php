<script type="text/template" id="ocupaciones_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-bed icono_principal"></i>Disponibilidad</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-body">
    	<div class="fc">
			<div class="fc-toolbar oh">
				<div class="fc-left">
				  <div class="fc-button-group">
				    <button type="button" class="fc-prev-button fc-button fc-state-default fc-corner-left"><span class="fc-icon fc-icon-left-single-arrow"></span></button>
				    <button type="button" class="fc-next-button fc-button fc-state-default fc-corner-right"><span class="fc-icon fc-icon-right-single-arrow"></span></button>
				  </div>
				</div>
				<div class="fc-right">
				  <div class="fc-button-group">
				    <button type="button" class="semana fc-timelineMyWeek-button fc-button fc-state-default fc-corner-left">Semana</button>
				    <button type="button" class="quincena fc-timeline2Weeks-button fc-button fc-state-default fc-state-active">Quincena</button>
				    <button type="button" class="mes fc-timelineMonth-button fc-button fc-state-default fc-corner-right">Mes</button>
				  </div>
				</div>
			</div>
	      	<table id="ocupaciones_table">
		        <thead></thead>
		        <tbody></tbody>
		    </table>
	    </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="ocupaciones_item_template">
<input type="number" class="form-control" value="<%= disponible %>">
</script>