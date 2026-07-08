<script type="text/template" id="estadisticas_publicidades_template">
  <% var modulo = control.get("estadisticas_publicidades") %>
  <?php include("print_header.php"); ?>

  <div id="estadisticas_publicidades_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i><?php echo lang(array("es"=>"Estad&iacute;sticas","en"=>"Statistics")) ?>
            / <b><?php echo lang(array("es"=>"Publicidades","en"=>"Advertising")) ?></b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">
      <div class="panel panel-default">
        <div class="panel-body no-print">
          <div class="">
            <div class="w160 pull-left">
              <div class="input-group">
                <input type="text" id="estadisticas_publicidades_fecha_desde" value="<%= fecha_desde %>" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
            <div class="w160 pull-left m-l-xs">
              <div class="input-group">
                <input type="text" id="estadisticas_publicidades_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
            </div>
            <div class="w200 pull-left m-l-xs">
              <select class="form-control no-model" id="estadisticas_publicidades_clientes"></select>
            </div>
            <?php /*
            <div class="w200 pull-left m-l-xs">
              <select multiple class="form-control" id="estadisticas_publicidades_campanias"></select>
            </div>
            <div class="w200 pull-left m-l-xs">
              <select class="form-control" id="estadisticas_publicidades_piezas"></select>
            </div>
            */ ?>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")) ?></button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
      <div id="estadisticas_publicidades_resultado"></div>
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_publicidades_graficos_template">
  <div class="pagina">

    <% if (MILLING == 1) { %>
      <div class="row">

        <div class="col-xs-12 col-sm-3">
          <div class="row tac">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= Number(total_publicidades).toFixed(0) %></div>
                <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Impresiones totales","en"=>"Total Views")) ?></span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= Number(total_clicks).toFixed(0) %></div>
                <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Cantidad de clicks","en"=>"Total Clicks")) ?></span>
              </div>
            </div>
          </div>
          <div class="panel panel-default" style="height:300px; overflow: auto">
            <div class="panel-heading font-bold"><?php echo lang(array("es"=>"Publicidades m&aacute;s vistas","en"=>"Banners")); ?></div>
            <table class="estadisticas_web_table table table-striped m-b-none">
              <tbody>
                <tr>
                  <td class="bold"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></td>
                  <td class="tar bold"><?php echo lang(array("es"=>"Vistas","en"=>"Views")); ?></td>
                  <td class="tar bold"><?php echo lang(array("es"=>"Clicks","en"=>"Clicks")); ?></td>
                </tr>
                <% for(var i=0;i< piezas.length;i++) { %>
                  <% var o = piezas[i]; %>
                  <tr>
                    <td><a href="<%= o.link %>" target="_blank"><%= o.nombre %></a></td>
                    <td class="tar"><%= o.visitas %></td>
                    <td class="tar"><%= o.clicks %></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>


        <div class="col-xs-12 col-sm-3">
          <div class="tac mb20">
            <div class="panel padder-v item bg-default" style="height: 140px">
              <div class="h2 font-thin m-t-md"><%= Number(total_paginas).toFixed(0) %></div>
              <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Total Páginas","en"=>"Total Page Views")) ?></span>
            </div>
          </div>
          <div class="panel panel-default" style="height:300px; overflow: auto">
            <div class="panel-heading font-bold"><?php echo lang(array("es"=>"P&aacute;ginas m&aacute;s vistas","en"=>"Pages")); ?></div>
            <table class="estadisticas_web_table table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< paginas.length;i++) { %>
                  <% var o = paginas[i]; %>
                  <tr>
                    <td><a href="<%= o.link %>" target="_blank"><%= o.nombre %></a></td>
                    <td><%= o.visitas %></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-xs-12 col-sm-3">
          <div class="tac mb20">
            <div class="panel padder-v item bg-default" style="height: 140px">
              <div class="h2 font-thin m-t-md"><%= Number(total_videos).toFixed(0) %></div>
              <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"Total Videos","en"=>"Total Video Views")) ?></span>
            </div>
          </div>
          <div class="panel panel-default" style="height:300px; overflow: auto">
            <div class="panel-heading font-bold"><?php echo lang(array("es"=>"Videos m&aacute;s vistos","en"=>"Videos")); ?></div>
            <table class="estadisticas_web_table table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< videos.length;i++) { %>
                  <% var o = videos[i]; %>
                  <tr>
                    <td><a href="<%= o.link %>" target="_blank"><%= o.nombre %></a></td>
                    <td><%= o.visitas %></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-xs-12 col-sm-3">
          <div class="tac mb20">
            <div class="panel padder-v item bg-default" style="height: 140px">
              <div class="h2 font-thin m-t-md"><%= Number(total_qr).toFixed(0) %></div>
              <span class="text-muted text-md pt10 db"><?php echo lang(array("es"=>"QR Clicks","en"=>"QR Clicks")) ?></span>
            </div>
          </div>
          <div class="panel panel-default" style="height:300px; overflow: auto">
            <div class="panel-heading font-bold"><?php echo lang(array("es"=>"QR Links","en"=>"QR Links")); ?></div>
            <table class="estadisticas_web_table table table-striped m-b-none">
              <tbody>
                <% for(var i=0;i< qr_links.length;i++) { %>
                  <% var o = qr_links[i]; %>
                  <tr>
                    <td><a href="<%= o.link %>" target="_blank"><%= o.nombre %></a></td>
                    <td><%= o.visitas %></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    <% } %>

  </div>
</script>