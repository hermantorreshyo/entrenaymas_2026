<script type="text/template" id="estadisticas_whatsapp_template">
  <div id="estadisticas_whatsapp_container" class="col">

    <?php include("print_header.php"); ?>

    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <% if (ID_PROYECTO == 14) { %>
              <i class="fa fa-bar-chart icono_principal"></i><b>Estad&iacute;sticas</b>
            <% } else { %>
              <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
              / <b>ClienApp</b>
            <% } %>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default no-print">
        <div class="panel-body">
          <div class="filtros">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_whatsapp_fecha_desde" value="<%= fecha_desde %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left m-r-xs" style="width: 140px;">
              <input type="text" id="estadisticas_whatsapp_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="pull-left m-r-xs" style="width: 200px;">
              <select id="estadisticas_whatsapp_usuarios" class="w100p form-control">
                <option value="0">Filtrar por usuario</option>
                <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                  <% var o = usuarios.models[i]; %>
                  <option value="<%= o.id %>" <%= (o.id == window.estadisticas_whatsapp_id_usuario)?"selected":"" %>><%= o.get("nombre") %></option>
                <% } %>
              </select>
            </div>
            <div class="pull-left">
              <button class="btn btn-default buscar m-r-xs"><i class="fa fa-search"></i> Buscar</button>
              <button class="btn btn-default imprimir "><i class="fa fa-print"></i></button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row pagina">
        <div class="col-md-6">
          <div class="row row-sm text-center">
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div class="h2 text-white font-thin m-t-md"><%= Number(total_clicks).toFixed(0) %></div>
                <span class="text-muted text-md pt10 db">Interacciones</span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= Number(consultas_fuera_linea).toFixed(0) %></div>
                <span class="text-muted text-md pt10 db">Consultas fuera de linea</span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md"><%= Number(promedio_por_dia).toFixed(0) %></span>
                <span class="text-muted text-md pt10 db">Promedio por dia</span>
              </div>
            </div>
          </div>
          <div class="">
            <div class="panel wrapper">
              <h4 class="font-thin m-t-none m-b text-muted">Visi&oacute;n general</h4>
              <div id="estadisticas_whatsapp_graficos" style="height: 235px;"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold"><?php echo lang(array("es"=>"P&aacute;ginas m&aacute;s vistas","en"=>"Top Pages")); ?></div>
            <table class="estadisticas_web_table table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< paginas.length;i++) { %>
                  <% var o = paginas[i]; %>
                  <tr>
                    <td><a href="<%= o.nombre %>" target="_blank"><%= o.nombre %></a></td>
                    <td><span data-toggle="tooltip" title="<%= o.cantidad %> (<%= o.porcentaje %> %)"><%= o.cantidad %> (<%= o.porcentaje %> %)</span></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_whatsapp_graficos_template">
  <div style="min-height: 250px" class="grafico"></div>
</script>