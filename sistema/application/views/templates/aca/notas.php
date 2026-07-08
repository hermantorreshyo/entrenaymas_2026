<script type="text/template" id="notas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Planilla de Notas de Ex&aacute;menes</h1>
  </div>
  <div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	  
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-xs-12 sm-m-b">
                <div style="width: 250px; display: inline-block">
                    <select id="notas_buscar_comisiones" class="w100p"></select>
                </div>
                <div style="width: 250px; display: inline-block">
                    <select id="notas_buscar_materias" class="w100p form-control">
                      <option value="0">Seleccionar materia</option>
                    </select>
                </div>
                <button class="btn btn-default buscar">Buscar</button>
                <button class="btn btn-success nueva_nota fr btn-addon">
                  <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo Examen</span>
                </button>
              </div>
            </div>
          </div>
    	  <div class="panel-body">
    		  <div class="b-a">
    			  <table id="notas_table" class="table table-striped sortable m-b-none default footable">
    				  <thead></thead>
    				  <tbody class="tbody"></tbody>
              <tfoot></tfoot>
    			  </table>
    		  </div>
    	  </div>
          <div class="panel-footer">
            <button class="btn btn-success guardar">Guardar</button>
            <button class="btn btn-default imprimir">Imprimir Planilla</button>
          </div>
	</div>
  </div>    
</script>

<script type="text/template" id="nota_concepto_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    <% if (id == undefined) { %>
      Nueva Calificaci&oacute;n
    <% } else { %>
      Editar Calificaci&oacute;n
    <% } %>        
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-xs-12">
          <label>Nombre</label>
          <input value="<%= nombre %>" name="nombre" type="text" id="nota_concepto_nombre" placeholder="Ej: Primer Parcial" class="w100p form-control" />
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-6 col-xs-12">
          <label>Tipo de calificaci&oacute;n</label>
          <select class="form-control" id="nota_concepto_numerico">
            <option <%= (numerico==1)?"selected":"" %> value="1">Num&eacute;rica</option>
            <option <%= (numerico==0)?"selected":"" %> value="0">Valores predefinidos</option>
          </select>
        </div>
        <div class="col-md-6 col-xs-12 valores">
          <label>Valores</label>
          <input type="text" id="nota_concepto_valores" class="w100p" />
        </div>
        <div class="col-md-6 col-xs-12 aprueba_con">
          <label>Aprueba con</label>
          <input type="text" id="nota_concepto_aprueba_con" value="<%= aprueba_con %>" class="w100p form-control" />
        </div>
      </div>
      <div class="form-group aprueba_con">
        <div class="col-xs-12">
          <div class="checkbox">
            <label class="i-checks">
              <input type="checkbox" name="utilizada_en_promedio" <% (utilizada_en_promedio == 1) ? 'checked=""' : '' %>><i></i> Utilizar la nota para calcular el promedio final
            </label>
          </div>          
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <button class="btn btn-success guardar">Guardar</button>
  </div>
</div>
     
</script>
