<script type="text/template" id="categorias_opcionales_tree_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
    / <b>Categor&iacute;as de opcionales</b>
  </h1>
</div>
<div class="wrapper-md pb0">
  <div class="centrado">
    <div class="panel panel-default">
      <div class="panel-heading oh">
        <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)">
          <i class="fa fa-plus"></i>
          <span class="hidden-xs">&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
        </a>
      </div>
      <div class="panel-body oh">
        <div ui-jq="nestable" class="dd">
          <%= workspace.crear_nestable(categorias_opcionales) %>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="categorias_opcionales_edit_panel_template">
  <div class="panel panel-default">
    <div class="panel-heading">
      <% if (id == undefined) { %>
      Nueva Categoria
      <% } else { %>
      <%= nombre %>
      <% } %>       
    </div>
    <div class="panel-body">
      <div class="form-group lang-control">
        <label class="control-label">Nombre</label>
        <div class="input-group">
          <input type="text" name="nombre" class="form-control active" id="categorias_opcionales_nombre" value="<%= nombre %>"/>
          <input type="text" name="nombre_en" class="form-control" id="categorias_opcionales_nombre_en" value="<%= nombre_en %>"/>
          <input type="text" name="nombre_pt" class="form-control" id="categorias_opcionales_nombre_pt" value="<%= nombre_pt %>"/>
          <div class="input-group-btn">
            <label class="btn btn-default btn-lang active" data-id="categorias_opcionales_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
            <label class="btn btn-default btn-lang" data-id="categorias_opcionales_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
            <label class="btn btn-default btn-lang" data-id="categorias_opcionales_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Pertenece a</label>
            <select class="form-control" name="id_padre" id="categorias_opcionales_padre"></select>
          </div>
        </div>
        <div class="col-md-6">
          <label class="control-label">&nbsp;</label>
          <div class="form-group">
            <div class="checkbox">
              <label class="i-checks">
                <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> ><i></i>
                La categor&iacute;a est&aacute; activa.
              </label>
            </div>
          </div>
        </div>
      </div>
      <?php
      single_upload(array(
        "name"=>"path",
        "label"=>"Imagen por defecto",
        "url"=>"/sistema/opcionals/function/save_image/",
        "width"=>(isset($empresa->config["categoria_opcional_image_width"]) ? $empresa->config["categoria_opcional_image_width"] : 256),
        "height"=>(isset($empresa->config["categoria_opcional_image_height"]) ? $empresa->config["categoria_opcional_image_height"] : 256),
        "quality"=>(isset($empresa->config["categoria_opcional_image_quality"]) ? $empresa->config["categoria_opcional_image_quality"] : 0),
        "thumbnail_width"=>(isset($empresa->config["categoria_opcional_thumbnail_width"]) ? $empresa->config["categoria_opcional_thumbnail_width"] : 0),
        "thumbnail_height"=>(isset($empresa->config["categoria_opcional_thumbnail_height"]) ? $empresa->config["categoria_opcional_thumbnail_height"] : 0),
        )); ?>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <button class="btn guardar fr btn-success">Guardar</button>
      <% if (id != undefined) { %>
      <button class="btn btn-danger eliminar fl">Eliminar</button>
      <% } %>                            
    </div>
  </div>
</script>

<script type="text/template" id="categorias_opcionales_edit_mini_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-body">
      <div class="form-group">
        <input type="text" name="nombre" class="form-control tab" id="categorias_opcionales_mini_nombre" value="<%= nombre %>"/>
      </div>
      <div class="form-group">
        <select class="form-control tab" name="id_padre" id="categorias_opcionales_mini_padre"></select>
      </div>
      <div class="form-group">
        <button class="btn guardar tab btn-success btn-block">Guardar</button>
      </div>
    </div>
  </div>
</script>