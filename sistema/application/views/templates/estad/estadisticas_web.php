<script type="text/template" id="estadisticas_web_template">
<div class="col">
  <% var modulo = control.get("estadisticas") %>
  <?php include("print_header.php"); ?>
  <div class="bg-light lter b-b wrapper-md no-print">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
          / <b>Web</b>
        </h1>
      </div>
      <div class="col-lg-6 col-sm-8 col-xs-12">
        <div class="pull-right">
          <input type="text" id="estadisticas_web_fecha_desde" value="<%= fecha_desde %>" class="form-control w120 pull-left">
          <button id="fecha_desde_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
          <input type="text" id="estadisticas_web_fecha_hasta" value="<%= fecha_hasta %>" class="form-control w120 m-l-xs pull-left">
          <button id="fecha_hasta_button" type="button" class="btn btn-default pull-left"><i class="glyphicon glyphicon-calendar"></i></button>
          <button type="button" class="btn btn-default pull-left imprimir"><i class="fa fa-print"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="wrapper-md">
    <div class="row">
      <div class="col-md-5">
        <div class="row row-sm text-center">
        <div class="col-xs-6">
          <div class="panel padder-v item bg-info" style="height: 140px">
          <div class="h1 font-thin text-white m-t-md"><%= total_sesiones %></div>
          <span class="text-muted text-md"><?php echo lang(array("es"=>"Cantidad de visitas","en"=>"Number of Visits")); ?></span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="block panel padder-v item bg-success" style="height: 140px">
          <div class="h1 font-thin text-white h1 m-t-md"><%= total_usuarios %></div>
          <span class="text-muted text-md"><?php echo lang(array("es"=>"Usuarios &Uacute;nicos","en"=>"Unique Users")); ?></span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="block panel padder-v item" style="height: 140px">
          <span class="font-thin h1 block m-t-md"><%= paginas_vistas %></span>
          <span class="text-muted text-md"><?php echo lang(array("es"=>"P&aacute;ginas vistas","en"=>"Page Views")); ?></span>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="panel padder-v item" style="height: 140px">
          <div class="font-thin h1 m-t-md"><%= porcentaje_rebote %></div>
          <span class="text-muted text-md"><?php echo lang(array("es"=>"Porcentaje de rebote","en"=>"Bounce Rate")); ?></span>
          </div>
        </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="panel wrapper">
        <h4 class="font-thin m-t-none m-b text-muted"><?php echo lang(array("es"=>"Visi&oacute;n general","en"=>"General View")); ?></h4>
        <div id="vision_general_bar" style="height: 235px;"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <div class="panel panel-default" style="min-height:395px">
          <div class="panel-heading font-bold"><?php echo lang(array("es"=>"P&aacute;ginas m&aacute;s vistas","en"=>"Top Pages")); ?></div>
          <table class="estadisticas_web_table table table-striped m-b-none">
            <tbody>
              <% for(var i=0;i< paginas_mas_vistas.length;i++) { %>
                <% var o = paginas_mas_vistas[i]; %>
                <tr>
                  <td><a href="<%= o.nombre %>" target="_blank"><%= o.nombre %></a></td>
                  <td><span data-toggle="tooltip" title="<%= o.cantidad %> (<%= o.porcentaje %> %)"><%= o.cantidad %> (<%= o.porcentaje %> %)</span></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-3">
        <div class="panel panel-default" style="min-height:395px">
          <div class="panel-heading font-bold"><?php echo lang(array("es"=>"Or&iacute;genes de visitas","en"=>"Sources")); ?></div>
          <table class="estadisticas_web_table table table-striped m-b-none">
            <tbody>
              <% for(var i=0;i<fuentes.length;i++) { %>
                <% var o = fuentes[i]; %>
                <tr>
                  <td><%= o.nombre %></td>
                  <td><%= o.cantidad %> (<%= o.porcentaje %> %)</td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-3">
        <div class="panel panel-default" style="min-height:395px">
          <div class="panel-heading font-bold"><?php echo lang(array("es"=>"Ciudades","en"=>"Top Countries")); ?></div>
          <table class="estadisticas_web_table table table-striped m-b-none">
            <tbody>
              <% for(var i=0;i<ciudades.length;i++) { %>
                <% var o = ciudades[i]; %>
                <tr>
                  <td><%= o.nombre %></td>
                  <td><%= o.cantidad %> (<%= o.porcentaje %> %)</td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>      

      
      <!--
      <div class="col-md-4">
        <div class="no-padder" style="overflow: hidden">
        <div class="lt wrapper">
          <div class="h4 font-thin">Usuarios Nuevos Vs. Usuarios Recurrentes</div>
        </div>
        <div id="visitas_bar" style="height: 250px"></div>
        <div class="row">
          <div class="col-xs-6 padder-lg bg-success wrapper">
          <span class="block text-light">Usuarios Nuevos</span>
          <span class="block h1 text-info font-thin"><%= total_usuarios_nuevos %></span>
          </div>
          <div class="col-xs-6 padder bg-info wrapper">
          <span class="block text-light">Usuarios Recurrentes</span>
          <span class="block h1 text-warning font-thin"><%= total_usuarios_recurrentes %></span>
          </div>
        </div>
        </div>
      </div>
      -->
      <div class="col-xs-12 col-md-6 col-lg-3">
        <div class="panel panel-default" style="min-height:395px">
          <div class="panel-heading font-bold"><?php echo lang(array("es"=>"Dispositivos","en"=>"Screens")); ?></div>
          <div class="panel-body" style="padding-top: 0px">
            <div id="dispositivos_bar" style="height: 200px"></div>
          </div>
          <div class="panel-footer">
            <span class="label bg-success m-r-xs">1</span>
            <small><?php echo lang(array("es"=>"Computadoras","en"=>"Desktop")); ?></small>
            <small class="pull-right"><%= desktop %></small>
          </div>
          <div class="panel-footer">
            <span class="label bg-info m-r-xs">2</span>
            <small><?php echo lang(array("es"=>"Tel&eacute;fonos m&oacute;viles","en"=>"Mobile")); ?></small>
            <small class="pull-right"><%= mobile %></small>
          </div>
          <div class="panel-footer">
            <span class="label bg-warning m-r-xs">3</span>
            <small>Tablet</small>
            <small class="pull-right"><%= tablet %></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>