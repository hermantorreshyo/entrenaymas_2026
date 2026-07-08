<script type="text/template" id="propiedades_dashboard_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col-md-8 col-md-offset-2 dashboard">

    <% var total_pasos = 3 %>
    <% var paso = 0 %>
    <% paso = paso + ((isEmpty(LOGO_1)) ? 0 : 1) %>
    <% paso = paso + ((total_propiedades == 0) ? 0 : 1) %>
    <% paso = paso + ((total_consultas == 0) ? 0 : 1) %>

    <h3 class="subtitulo mt40"><b>¡Hola <%= NOMBRE %>!</b>
      <% if (paso < total_pasos) { %>
        Vamos a preparar juntos tu negocio en la nube
      <% } %>
    </h3>

    <% if (isEmpty(LOGO_1)) { %>
      <div id="sugerencia-configurar-web" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
            </span>
            <div class="media-body">
              <h4>Personaliz&aacute; tu sitio</h4>
              <p>Subí el logo de tu inmobiliaria y configurá los colores de tu sitio web.</p>
            </div>
          </div>                        
        </div>
      </div>
    <% } %> 
    <% if (total_propiedades == 0) { %>
      <div id="sugerencia-primera-propiedad" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img class="img-circle" src="/sistema/resources/images/propiedades.png"/>
            </span>
            <div class="media-body">
              <h4>Public&aacute; tu primera propiedad</h4>
              <p>Cargá el título, dirección e imágenes de tu primera propiedad.</p>
            </div>
          </div>
        </div>
      </div>
    <% } %>
    <% if (total_consultas == 0) { %>
      <div id="sugerencia-primer-contacto" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img class="img-circle" src="/sistema/resources/images/configurar-contactos.png"/>
            </span>
            <div class="media-body">
              <h4>Carg&aacute; tu primer contacto</h4>
              <p>Podés cargar tanto un contacto de un cliente, un inquilino o un propietario.</p>
            </div>
          </div>                        
        </div>
      </div>
    <% } %>

    <% if (paso < total_pasos) { %>
      <h4 class="subtitulo mt20"><b>Completa las tareas de tu inmobiliaria:</b> <%= paso %> de <%= total_pasos %></h4>
      <div class="progress mb0">
        <div class="progress-bar" role="progressbar" style="width:<%= Number(paso / total_pasos * 100).toFixed(5) %>%"></div>
      </div>
    <% } %>

    <h3 class="subtitulo mt40"><b>Necesitas ayuda?</b></h3>

    <div class="row">
      <div class="col-md-6">
        <div id="sugerencia-llamar-atencion-cliente" class="panel sugerencia-nueva panel-default">
          <div class="panel-body">
            <div class="media">
              <span class="thumb-lg pull-left tac mr0">
                <img class="img-circle" src="/sistema/resources/images/ayuda-whatsapp.png"/>
              </span>
              <div class="media-body">
                <h4>Atención al cliente</h4>
                <p>Solicitá ayuda para configurar tu sitio y ponerlo en línea.</p>
                <a class="fs16 db mt10 bold mb15"><i class="fa fa-whatsapp text-success mr5 fs22 pr t3"></i>Llamar por Whatsapp</a>
                <button class="btn btn-sm btn-dark">SOLICITAR AYUDA</button>
              </div>
            </div>                        
          </div>
        </div>        
      </div>    
      <div class="col-md-6">
        <div id="sugerencia-llamar-soporte-tecnico" class="panel sugerencia-nueva panel-default">
          <div class="panel-body">
            <div class="media">
              <span class="thumb-lg pull-left tac mr0">
                <img class="img-circle" src="/sistema/resources/images/ayuda-soporte.png"/>
              </span>
              <div class="media-body">
                <h4>Soporte técnico</h4>
                <p>Solicitá ayuda respecto a cuestiones técnicas o errores.</p>
                <a class="fs16 db mt10 bold mb15"><i class="fa fa-whatsapp text-success mr5 fs22 pr t3"></i>Llamar por Whatsapp</a>
                <button class="btn btn-sm btn-dark">SOLICITAR AYUDA</button>
              </div>
            </div>                        
          </div>
        </div>        
      </div>    
    </div>

  </div>
</div>

<?php /*
  <div class="col" style="padding:20px 10px;">
    <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento != 0 && datos_empresa != 0) { %>
          <div class="row" id="propiedades_dashboard_cajitas">                    
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item">
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_1.png"/>
                  <span class="texto">Propiedades</span>
                  <span class="numero" id="dashboard_total_propiedades"><%= total_propiedades %></span>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item" >
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_3.png"/>
                  <span class="texto">Consultas</span>
                  <span class="numero" id="dashboard_total_consultas"><%= total_consultas %></span>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item" >
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/prop_4.png"/>
                  <span class="texto">Visitas</span>
                  <span class="numero" id="dashboard_total_visitas"><%= total_sesiones %></span>
                </div>
              </div>
            </div>
          </div>
        <% } %>
      <div class="">

          



        
        <div id="dashboard_propiedades_consultas"></div>
      </div>
      <% if (configurar_disenio != 0 && subir_elemento != 0 && !isEmpty(DIRECCION)) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
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
*/ ?>
</script>