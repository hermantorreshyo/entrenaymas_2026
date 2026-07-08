<script type="text/template" id="calm_cursos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-graduation-cap icono_principal"></i>Cursos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("calm_cursos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#calm_curso"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="calm_cursos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="categoria">Categoria</th>
                <% if (permiso > 1) { %>
                  <th class="w100"></th>
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


<script type="text/template" id="calm_cursos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= categoria %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <i title="Destacado" class="fa-star iconito fa destacado <%= (destacado == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="calm_cursos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-graduation-cap icono_principal"></i>Cursos
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="calm_cursos_nombre" value="<%= nombre %>"/>
              </div>

              <div class="form-group">
                <label class="control-label">Subtitulo</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="subtitulo" class="form-control" id="calm_cursos_subtitulo" value="<%= subtitulo %>"/>
              </div>

              <div class="form-group">
                <label class="control-label">Autor</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="autor" class="form-control" id="calm_cursos_autor" value="<%= autor %>"/>
              </div>

              <div class="form-group">
                <label class="control-label">Tipo de Curso</label>
                <select class="form-control" name="premium" id="calm_curso_premium">
                  <option <%= (premium==0)?"selected":"" %> value="0">Gratuito</option>
                  <option <%= (premium==1)?"selected":"" %> value="1">Premium</option>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label">Categoria</label>
                <select <%= (!edicion)?"disabled":"" %> id="calm_curso_categorias" name="id_categoria" class="form-control"></select>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Descripcion</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="calm_curso_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="calm_curso_texto_cont">
                    <textarea name="texto" name="texto" id="calm_curso_texto"><%= texto %></textarea>
                  </div>
                </div>
              </div>              
              
              <?php
              single_file_upload(array(
                "name"=>"path",
                "label"=>"Imagen",
                "url"=>"/sistema/calm_cursos/function/save_file/",
              )); ?>
              
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Lista de Audios</label>
                <button class="btn btn-info nuevo_audio fr">+ Agregar</button>
              </div>
              <div id="calm_cursos_audios" class="mt10"></div>
            </div>
          </div>
        </div>  
              
        <% if (edicion) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>


<script type="text/template" id="calm_cursos_audios_resultados_template">
<table id="audios_tabla" class="table table-small table-striped sortable m-b-none default footable">
  <thead>
    <tr>
      <th>Nombre</th>
      <th class="th_acciones w50"></th>
    </tr>
  </thead>
  <tbody class="tbody"></tbody>
</table>
</script>

<script type="text/template" id="calm_cursos_audios_item_resultados_template">
<td class="text-info data"><%= nombre %></td>
<td class="tar td_acciones">
  <button class="btn btn-white eliminar"><i class="fa fa-trash"></i></button>
</td>
</script>

<script type="text/template" id="calm_curso_audio_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Editar audio</b>
  </div>
  <div class="panel-body">

    <div class="form-group">
      <label class="control-label">Nombre</label>
      <input type="text" required name="nombre" id="calm_curso_audio_nombre" value="<%= nombre %>" class="form-control"/>
    </div>

    <div class="form-group">
      <label class="control-label">Duracion</label>
      <input type="text" required name="duracion" id="calm_curso_audio_duracion" value="<%= duracion %>" class="form-control"/>
    </div>

    <?php
    single_file_upload(array(
      "name"=>"path_audio",
      "label"=>"Audio",
      "url"=>"/sistema/calm_cursos/function/save_file/",
    )); ?>

  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn guardar btn-success">Guardar</button>
  </div>
</div>
</script>
