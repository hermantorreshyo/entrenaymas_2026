<script type="text/template" id="resumen_caja_parametros_template">
<div class="panel">
    <div class="titulo">
        RESUMEN DE CAJA
    </div>
    <div style="margin-top: 5px; margin-bottom: 3px;">
        <div class="fl mt3" id="resumen_caja_fechas_container"></div>
        <button class="button generar verde fl ml20">Buscar</button>
        <div class="fl ml10 mt5">
            <img class="dn" id="resumen_caja_loading" src="resources/images/loading.gif" />
        </div>
    </div>
</div>
</script>


<script type="text/template" id="resumen_caja_resultados_template">
<div class="panel">
<div style="width:330px">
    <div class="subtitulo">
        ENTRADAS
    </div>
    <div class="row">
        <span class="fs12">EFECTIVO: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs16 number" id="resumen_caja_resultados_efectivo"/>
        </div>
    </div>
    <div class="row">
        <span class="fs12">PAGOS CUENTA CTE.: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs16 number" id="resumen_caja_resultados_pagos"/>
            <!--<button id="resumen_caja_resultados_pagos_boton" class="button gris">+</button>-->
        </div>
    </div>
    <div class="row">
        <span class="fs12">TARJETAS: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="w75 input ml5 bold tar w150 fs16" id="resumen_caja_resultados_tarjetas"/>
            <button id="resumen_caja_resultados_tarjetas_boton" class="button gris">+</button>
        </div>
    </div>
    <div class="row">
        <span class="fs12">CHEQUES: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs16" id="resumen_caja_resultados_cheques"/>
            <button id="resumen_caja_resultados_cheques_boton" class="button gris">+</button>
        </div>
    </div>
    
    <div class="subtitulo cb mt30">
        SALIDAS
    </div>
    <div class="row">
        <span class="fs12">GASTOS: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs16" id="resumen_caja_resultados_gastos"/>
            <button id="resumen_caja_resultados_gastos_boton" class="button gris">+</button>
        </div>
    </div>

    <div class="subtitulo cb mt30">
        TOTAL GENERAL
    </div>
    <div class="row">
        <span class="fs12">INGRESO: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs18" id="resumen_caja_resultados_total"/>
        </div>
    </div>
    <div class="row">
        <span class="fs12">A CUENTA: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs16 number" id="resumen_caja_resultados_cta_cte"/>
            <!--<button id="resumen_caja_resultados_cta_cte_boton" class="button gris">+</button>-->
        </div>
    </div>
    <div class="row">
        <span class="fs12">TOTAL: </span>
        <div class="fr w200">
            <input type="text" value="0.00" disabled class="input ml5 bold tar w150 fs18" id="resumen_caja_resultados_general"/>
        </div>
    </div>
</div>    
</div>
</script>