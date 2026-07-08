  <div class="col">
    <?php include("print_header.php"); ?>
    <div class="bg-light lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black"><i class="fa fa-bar-chart icono_principal"></i>Estadisticas</h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <input type="text" id="estadisticas_consultas_fecha_desde" value="<%= desde %>" class="form-control w120 pull-left">
            <button id="fecha_desde_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
            <input type="text" id="estadisticas_consultas_fecha_hasta" value="<%= hasta %>" class="form-control w120 m-l-xs pull-left">
            <button id="fecha_hasta_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
            <button type="button" class="btn btn-default pull-left imprimir"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
    </div>
    <div class="wrapper-md">
      <div class="centrado rform">

        <% if (POSICION_PROMEDIO != '0.00') { %>

          <div class="row">
            <div class="col-xs-12 col-md-12">
              <div class="panel panel-default tac" style="min-height:0px">
                <div class="panel-heading font-bold fs-22">Promedio de posicion en las busquedas de tus localidades: <%= Math.floor(POSICION_PROMEDIO) %></div>
              </div>
            </div>
          </div>

        <% } %>

        <div class="row">
          <div class="col-md-4">
            <div class="text-center">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div id="estadisticas_consultas_total_consultas" class="h1 font-thin text-white m-t-md">0</div>
                <span class="text-muted text-md"><?php echo lang(array("es"=>"Cantidad de consultas","en"=>"Total of enquires")); ?></span>
              </div>
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div id="estadisticas_consultas_total_vistas" class="h1 font-thin text-white h1 m-t-md">0</div>
                <span class="text-muted text-md">Cantidad de Visualizaciones</span>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="panel wrapper">
              <h4 class="font-thin m-t-none m-b text-muted"><?php echo lang(array("es"=>"Visi&oacute;n general","en"=>"General View")); ?></h4>
              <div id="vision_general_bar" style="height: 235px;"></div>
            </div>
          </div>
        </div>
 
        <div class="row">
          <div class="col-xs-12 col-md-12">
            <div class="panel panel-default" style="min-height:200px">
              <div class="panel-heading font-bold">Vistas de redes sociales</div>
              <div class="row">
                <div class="col-md-6 col-xs-12 mt20">
                  <div class="panel-footer">
                    <span class="label bg-success m-r-xs"><i class="fa fa-globe"></i></span>
                    <small>Web</small>
                    <small id="grafico_redes_sociales_web" class="pull-right">0</small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-info m-r-xs"><i class="fa fa-facebook"></i></span>
                    <small>Facebook</small>
                    <small id="grafico_redes_sociales_facebook" class="pull-right">0</small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-warning m-r-xs"><i class="fa fa-instagram"></i></span>
                    <small>Instagram</small>
                    <small id="grafico_redes_sociales_instagram" class="pull-right">0</small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-info m-r-xs"><i class="fa fa-twitter"></i></span>
                    <small>Twitter</small>
                    <small id="grafico_redes_sociales_twitter" class="pull-right">0</small>
                  </div>
                </div>
                <div class="col-md-6 col-xs-12">
                  <div class="panel-body" style="padding-top: 0px">
                    <div id="grafico_redes_sociales" style="height: 200px"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
 
        <div class="row">

          <div class="col-xs-12 col-md-6">
            <div class="panel panel-default" style="min-height:395px">
              <div class="panel-heading font-bold">Por Origen</div>
              <div class="panel-body" style="padding-top: 0px">
                <div id="grafico_por_origen" style="height: 200px"></div>
              </div>
              <div class="panel-footer">
                <span class="label bg-success m-r-xs"><i class="fa fa-whatsapp"></i></span>
                <small>Whatsapp</small>
                <small id="grafico_por_origen_whatsapp" class="pull-right">0</small>
              </div>
              <div class="panel-footer">
                <span class="label bg-info m-r-xs"><i class="fa fa-globe"></i></span>
                <small>Web</small>
                <small id="grafico_por_origen_web" class="pull-right">0</small>
              </div>
              <div class="panel-footer">
                <span class="label bg-warning m-r-xs"><i class="fa fa-user"></i></span>
                <small>Turnos</small>
                <small id="grafico_por_origen_turno" class="pull-right">0</small>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-md-6">
            <div class="panel panel-default" style="min-height:395px">
              <div class="panel-heading font-bold">Por Estado</div>
              <div class="panel-body" style="padding-top: 0px">
                <div id="grafico_por_estado" style="height: 200px"></div>
              </div>
              <% for(var i=0;i< consultas_tipos.length;i++) { %>
                <% var c = consultas_tipos[i] %>
                <div class="panel-footer">
                  <span class="label bg-<%= c.color %> m-r-xs">&nbsp;&nbsp;&nbsp;</span>
                  <small><%= c.nombre %></small>
                  <small id="grafico_por_estado_<%= c.id %>" class="pull-right">0</small>
                </div>
              <% } %>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>