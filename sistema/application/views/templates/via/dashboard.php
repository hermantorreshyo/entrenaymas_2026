<script type="text/template" id="viajes_dashboard_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col" style="padding:20px 10px;">
    <div class="col-md-9 col-xs-12">
      <% if (configurar_disenio != 0 && subir_elemento != 0 && datos_empresa != 0) { %>
        <div class="row" id="viajes_dashboard_cajitas">          
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
            <div class="media">
              <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_1_11.png"/>
              <span class="texto">Viajes</span>
              <span class="numero" id="dashboard_total_viajes"><%= total_viajes %></span>
            </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
            <div class="media">
              <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_3_11.png"/>
              <span class="texto">Consultas</span>
              <span class="numero" id="dashboard_total_consultas"><%= total_consultas %></span>
            </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
            <div class="media">
              <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_4_11.png"/>
              <span class="texto">Visitas</span>
              <span class="numero" id="dashboard_total_visitas"><%= total_sesiones %></span>
            </div>
            </div>
          </div>
        </div>
      <% } %>
      <div class="">
        <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_viajes_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>            
            </div>
          </div>
        <% } %>
        
        <% if (datos_empresa == 0) { %>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Informaci&oacute;n de tu empresa</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/info-empresa.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Complet&aacute; los datos de tu empresa: direcci&oacute;n, localidad, etc. </div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_empresa_si" href="javascript:void(0)" class="btn btn-info btn16">Cargar informaci&oacute;n</a>
                    <a class="text-info m-l conf_empresa_no" href="javascript:void(0)">Lo har&eacute; m&aacute;s tarde</a>
                  </div>
                </div>
              </div>            
            </div>
          </div>
        <% } %>
        
        <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_viajes_primer_elemento">
            <div class="panel-heading">Agreg&aacute; el primer viaje a tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube el primer viaje a tu sitio web</div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 subir_elemento_si">Agregar un nuevo viaje</a>
                    <a href="javascript:void(0)" class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir un viaje</a>
                  </div>
                </div>
              </div>            
            </div>
          </div>
        <% } %>
        
        <div id="dashboard_viajes_consultas"></div>
        
      </div>
      <% if (configurar_disenio != 0 && subir_elemento != 0 && datos_empresa != 0) { %>
        <div class="row">
        <div class="col-md-6">
          <div class="form-group">
          <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
          <a href="app/#reservas_viajes" class="btn btn18 btn-success btn-block">Ver todas las reservas</a>
          </div>
        </div>
        </div>
      <% } %>
    </div>
      
    <div class="col-md-3 col-xs-12">
      
      <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
      <% } %>
      
      <div id="dashboard_ayuda"></div>
      
    </div>
  </div>
</div>
</script>