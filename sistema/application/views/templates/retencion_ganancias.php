<script type="text/template" id="retencion_ganancias_parametros_template">

<div class="panel">

    <div class="titulo">EXPORTAR RETENCION DE GANANCIAS</div>

    <div style="padding: 10px 0px 10px 0px" class="oh">

        <div class="cb mb10">
            Seleccione las fechas para generar el archivo de retenciones de ganancias. <br/>
            Si desea informar solo la segunda quincena, ingrese en las fechas
            el mes completo (del 1er dia al ultimo) y seleccione la opcion
            "Solo 2da Quincena".<br/>            
            El archivo se descargará en una pestaña aparte.
        </div>

        <div id="retencion_ganancias_fechas_container" class="fl"></div>
        
        <div class="fl ml10 mt3">
            <input type="checkbox" name="2da_quincena" /> Solo 2da Quincena
        </div>
        
   	<span class="fl mt3 ml15">Empresa:</span>
	<div class="select_container fl ml10 pr t-3">
	    <select id="retencion_ganancias_empresa" class="select w200">
		<option value='1'>Reynaldo Ernesto Pedro</option>
		<option value='2'>Reynaldo Ana Gracia</option>
	    </select>
	</div>        
        
        <button class="button generar rosa fl ml10">Generar</button>
    </div>
    
</div>

</script>