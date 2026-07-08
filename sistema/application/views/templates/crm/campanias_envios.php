<script type="text/template" id="campanias_envios_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><i class="fa fa-envelope icono_principal"></i>Comunicaci&oacute;n
    / <b>Env&iacute;os masivos</b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="panel panel-default">

      <div class="panel-heading oh">
        <div class="row">
          <div class="search_container col-lg-4 col-md-6"></div>
          <div class="col-md-6 col-lg-8">
            <a class="btn pull-right btn-info btn-addon" href="app/#campania_envio"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="campanias_envios_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w50">Estado</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Enviados</th>
                <% if (permiso > 1) { %>
                  <th class="th_acciones w120">Acciones</th>
                <% } %>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>    
</script>


<script type="text/template" id="campanias_envios_item">
  <td class='ver'>
    <% if (estado=="A" && resultado_ejecucion == '') { %>
      <span class="label bg-warning">Pendiente</span>
    <% } else if (resultado_ejecucion == 'S') { %>  
      <span class="label bg-success">Finalizado</span>
    <% } else if (resultado_ejecucion == 'F' ) { %>
      <span class="label bg-danger">Error</span>
    <% } %>
  </td>
  <td class='ver'><span class='text-info'><%= nombre %></span></td>
  <td class='ver'><span><%= (isEmpty(fecha)) ? fecha_inicio : fecha %></span></td>
  <td class='ver'><%= total_enviados %></td>
  <% if (permiso > 1) { %>
    <td class="pd td_acciones tar">
      <i class="fa fa-pencil iconito active ver"></i>
      <?php /*<i class="fa fa-paper-plane iconito success ver active"></i> */ ?>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>        
        <ul class="dropdown-menu pull-right">
          <% if (metodo == "S") { %>
            <li><a target="_blank" href="http://www.varcreative.com/sistema/application/cronjobs/sms.php?id=<%= id %>">Ejecutar ahora</a></li>
          <% } else if (metodo == "E") { %>
            <li><a target="_blank" href="http://www.varcreative.com/sistema/application/cronjobs/emails.php?id=<%= id %>">Ejecutar ahora</a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>    
    </td>
  <% } %>
</script>

<script type="text/template" id="campanias_envios_edit_panel_template">

<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-envelope icono_principal"></i>Comunicaci&oacute;n
  / Env&iacute;os masivos
  / <b><%= (id == undefined)? "Nuevo" : "Edici&oacute;n" %></b>
  </h1>
</div>

<div class="wrapper-md">
  <div class="centrado rform">

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">
          <?php 
          $clave = "Campañas Envios / Detalle / Texto 1";
          echo lang(array(
            "es"=>(isset($videos[$clave]["nombre_es"]) ? $videos[$clave]["nombre_es"] : "" ),
            "en"=>(isset($videos[$clave]["nombre_en"]) ? $videos[$clave]["nombre_en"] : "" ),
            )); ?>
        </div>
        <div class="detalle_texto_info text-muted">
          <?php echo lang(array(
            "es"=>(isset($videos[$clave]["texto_es"]) ? $videos[$clave]["texto_es"] : "" ),
            "en"=>(isset($videos[$clave]["texto_en"]) ? $videos[$clave]["texto_en"] : "" ),
            )); ?>
        </div>
        <?php if (isset($videos[$clave]["video_es"])) { ?>
          <a onclick="workspace.open_video(this)" data-iframe='<?php echo $videos[$clave]["video_es"] ?>'>
            Ver video
          </a>
        <?php } ?>
      </div>
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="">
                <label class="control-label">Destinatarios</label>
                <div id="campania_envio_destinatarios"></div>
              </div>

              <div class="form-group">
                <label class="control-label">Asunto</label>
                <div class="input-group">
                  <input type="text" name="nombre" class="form-control" id="campanias_envios_nombre" value="<%= nombre %>"/>
                  <div class="input-group-btn dropdown">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Plantillas <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a class="cargar_plantilla" href="javascript:void(0)">Cargar</a></li>
                      <li><a class="guardar_plantilla" href="javascript:void(0)">Guardar</a></li>
                    </ul>
                  </div>
                </div>
              </div>		

              <div style="display: none;" class="form-group">
                <label class="control-label">Forma de env&iacute;o</label>
                <div class="col-lg-10">
                  <select class="form-control" name="metodo">
                    <option <%= (metodo=="E")?"selected":"" %> value="E">Email</option>
                    <?php /*<option <%= (metodo=="S")?"selected":"" %> value="S">SMS</option>*/ ?>
                  </select>
                </div>
              </div>

    

              <?php /*
              ALUMNOS DE UN CURSO
              PROFESORES DE UN CURSO
              TUTORES DE UN CURSO
              PRECEPTORES DE UN CURSO

              PERTENECEN / ESTAN
              CONSULTARON
              COMPRARON


              TODO LO QUE SEA CON LA MISMA CLAVE, SE PONE UN OR EN EL MEDIO
              Ej: Pertenecen a Curso 1 OR
                  Pertenecen a Curso 2
              Y TODO LO QUE SEA CON DISTINTA CLAVE, SE PONE UN AND
              Ej: Compraron Articulo 1 AND
                  NO Compraron Articulo 2
              */ ?> 

              <div class="form-group" style="display:none" id="campania_envio_lista_predefinida">
                <label class="control-label">Lista predefinida</label>
                <div class="col-lg-10">
                  <select class="form-control no-model" id="campania_envio_listas"></select>
                </div>
              </div> 

              <div class="form-group">
                <textarea class="form-control h100" id="campanias_envios_texto" name="texto"><%= texto %></textarea>
              </div>   

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Planificaci&oacute;n",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Puede programar envíos únicos o periódicos en determinadas fechas.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="display: block;">
            <div class="padder">

              <div class="form-group">
                <label class="i-checks m-b-none">
                  <input class="radio esc tipo_envio" <%= (fecha != '') ? 'checked' : '' %> name="tipo_envio" value="unico" type="radio"><i></i>
                  Enviar una única vez
                </label>
              </div>
              <div style="<%= (fecha != '') ? '' : 'display: none;' %>" class="tipo_envio_unico row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fecha</label>
                    <div class="input-group">
                      <input type="text" name="fecha" class="form-control" id="campanias_envios_fecha" value="<%= fecha %>"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Hora</label>
                    <input type="text" name="hora" class="form-control hora" value="<%= hora %>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="i-checks m-b-none">
                  <input class="radio esc tipo_envio" name="tipo_envio" <%= (fecha_inicio != '' && fecha_fin != '') ? 'checked' : '' %> value="multiple" type="radio"><i></i>
                  Programar un envío reiterado en varias fechas
                </label>
              </div>
              <div style="<%= (fecha_inicio != '' && fecha_fin != '') ? '' : 'display: none;' %>" class="tipo_envio_multiple">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Desde</label>
                      <div class="input-group">
                        <input type="text" name="fecha_inicio" class="form-control" id="campanias_envios_fecha_inicio" value="<%= fecha_inicio %>"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Hasta</label>
                      <div class="input-group">
                        <input type="text" name="fecha_fin" class="form-control" id="campanias_envios_fecha_fin" value="<%= fecha_fin %>"/>
                        <span class="input-group-btn">
                          <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Hora</label>
                      <input type="text" name="hora" class="form-control hora" value="<%= hora %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Marque los d&iacute;as que desea realizar el env&iacute;o</label>
                  <div class="clearfix">
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="lunes" <%= (lunes==1)?"checked":"" %>><i></i>Lunes</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="martes" <%= (martes==1)?"checked":"" %>><i></i>Martes</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="miercoles" <%= (miercoles==1)?"checked":"" %>><i></i>Miercoles</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="jueves" <%= (jueves==1)?"checked":"" %>><i></i>Jueves</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="viernes" <%= (viernes==1)?"checked":"" %>><i></i>Viernes</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="sabado" <%= (sabado==1)?"checked":"" %>><i></i>Sabado</label></div>
                    <div class="checkbox pull-left mt10 mr10"><label class="i-checks"><input type="checkbox" value="1" name="domingo" <%= (sabado==1)?"checked":"" %>><i></i>Domingo</label></div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="campanias_envios_destinatarios_template">
<div class="row filtro_row">
  <div class="col-md-4">
    <div class="form-group">
      <select class="destinatarios form-control no-model">
        <% if (ID_PROYECTO == 5) { %>
          <option data-habilitar_filtro="0" <%= (table=="aca_alumnos" && filtro==0)?"selected":"" %> value="aca_alumnos">Todos los alumnos</option>
          <option data-habilitar_filtro="1" <%= (table=="aca_alumnos" && filtro!=0)?"selected":"" %> value="aca_alumnos">Alumnos de la comisi&oacute;n</option>
          <option data-habilitar_filtro="0" <%= (table=="aca_docentes" && filtro==0)?"selected":"" %> value="aca_docentes">Todos los docentes</option>
          <option data-habilitar_filtro="1" <%= (table=="aca_docentes" && filtro!=0)?"selected":"" %> value="aca_docentes">Docentes de la comisi&oacute;n</option>
          <option data-habilitar_filtro="0" <%= (table=="aca_tutores" && filtro==0)?"selected":"" %> value="aca_tutores">Todos los tutores</option>
          <option data-habilitar_filtro="1" <%= (table=="aca_tutores" && filtro!=0)?"selected":"" %> value="aca_tutores">Tutores de la comisi&oacute;n</option>
        <% } else { %>
          <option data-habilitar_filtro="0" <%= (table=="clientes" && filtro==0)?"selected":"" %> value="clientes">Todos los contactos</option>
        <% } %>
      </select>
    </div>
  </div>
  <% var filtros = ((typeof filtro == "undefined" || isEmpty(filtro)) ? new Array() : String(filtro).split("-")) %>
  <div class="col-md-5">
    <div class="form-group">
      <select multiple class="form-control filtro no-model">
        <% if (ID_PROYECTO == 5) { %>
          <% for(var i=0;i< comisiones.results.length;i++) { %>
            <% var c = comisiones.results[i] %>
            <% var fil = _.find(filtros,function(i){ return (i==c.id) }) %>
            <option <%= (fil != undefined)?"selected":"" %> value="<%= c.id %>"><%= c.nombre %></option>
          <% } %>
        <% } %>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <% if (primero==1) { %>
        <button class="btn btn-default btn-block agregar_filtro">M&aacute;s destinatarios</button>
      <% } else { %>
        <i class="fa fa-times m-t-sm text-danger cp eliminar_filtro" title="Eliminar"></i>
      <% } %>
    </div>
  </div>
</div>
</script>