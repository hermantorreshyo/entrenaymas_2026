<script type="text/template" id="rubros_tree_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos
      / <b>Categor&iacute;as</b>
    </h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <b class="fs16 pt5 fl">Organizar categor&iacute;as</b>
          <% if (control.check("rubros") > 1) { %>
            <a class="btn btn-info pull-right btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            <a class="btn btn-default pull-right btn-addon reordenar_todos mr10" href="javascript:void(0)"><i class="fa fa-sort"></i> Reordenar</a>
          <% } %>
        </div>
        <div class="bulk_action wrapper pb0">
          <button class="btn btn-default mover_lote btn-addon"><i class="icon fa fa-indent"></i>Mover a otra categoria</button>
          <?php /*<button class="btn btn-default unificar_lote btn-addon"><i class="icon fa fa-align-justify"></i>Unificar</button>*/ ?>
          <button class="btn btn-default eliminar_lote btn-addon"><i class="icon fa fa-times"></i>Eliminar</button>
        </div>
        <div class="panel-body clearfix">
          <div ui-jq="nestable" class="dd">
          <%= workspace.crear_nestable(rubros) %>
          </div>
        </div>
      </div>
      <% if (ID_PROYECTO == 2) { %>
        <div class="bg-success wrapper m-b">
          Crea categor&iacute;s principales y secundarias.
          Arrastra con <i class="fa fa-cross"></i>
          y ubicalas donde quieras. Puedes utilizar alguno
          de los siguientes modelos:
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">Un nivel de categorias</div>
              <div class="panel-body categoria_modelo">
                <ul>
                  <li>Accesorios</li>
                  <li>Pantalones</li>
                  <li>Camisas</li>
                  <li>Vestidos</li>
                  <li>Zapatos</li>
                  <li>Zapatillas</li>
                </ul>
              </div>
              <div class="panel-footer tac">
                <button class="btn btn-default">Elegir modelo</button>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">Dos niveles de categorias</div>
              <div class="panel-body categoria_modelo">
                <ul>
                  <li>Hombre
                    <ul>
                      <li>Camisas</li>
                      <li>Pantalones</li>
                    </ul>
                  </li>
                  <li>Mujer
                    <ul>
                      <li>Remeras</li>
                      <li>Vestidos</li>
                    </ul>
                  </li>
                </ul>
              </div>
              <div class="panel-footer tac">
                <button class="btn btn-default">Elegir modelo</button>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">Tres niveles de categorias</div>
              <div class="panel-body categoria_modelo">
                <ul>
                  <li>Hombre
                    <ul>
                      <li>Verano
                        <ul>
                          <li>Remeras</li>
                        </ul>
                      </li>
                    </ul>
                  </li>
                  <li>Mujer
                    <ul>
                      <li>Invierno
                        <ul>
                          <li>Camperas</li>
                        </ul>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
              <div class="panel-footer tac">
                <button class="btn btn-default">Elegir modelo</button>
              </div>
            </div>
          </div>
        </div>
      <% } %>
    </div>
  </div>
</script>


<script type="text/template" id="rubros_panel_template">
  
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Categorias</h1>
  </div>
  
  <div class="wrapper-md pb0">
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#tab1" role="tab" data-toggle="tab">Buscar</a>
        </li>
      </ul>
      <div class="tab-content">
        <div id="tab1" class="tab-pane active panel-body pt5 pb5">
          <div class="form-horizontal">
            <div class="form-group m-b-none">
              <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>  

  <div class="wrapper-md ng-scope pt0">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <span class="font-bold m-t-xs pull-left">Resultados de B&uacute;squeda</span>
        <a class="btn pull-right btn-success btn-addon" href="app/#rubro"><i class="fa fa-plus"></i>Nuevo</a>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="rubros_table" class="table sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Nombre</th>
                <% if (permiso > 1) { %>
                  <th class="w25"></th>
                  <th class="w25"></th>
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

<script type="text/template" id="rubros_item">
  <td><span class='ver'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
    <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
  <% } %>
</script>

<script type="text/template" id="rubros_edit_panel_template">
<div class="panel panel-default rform">
  <div class="panel-heading">
    <b><%= (id == undefined) ? "Nueva Categoria" : nombre+" ("+id+")" %></b>
    <i class="fa fa-times cerrar fr cp"></i>
  </div>

  <div class="tab-container mb0">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a id="rubro_1_link" href="#rubro_tab1" role="tab" data-toggle="tab">
          <i class="fa text-success fa-check m-r-xs"></i>
          Datos
        </a>
      </li>
      <li>
        <a id="rubro_2_link" href="#rubro_tab2" role="tab" data-toggle="tab">
          <i class="fa text-warning fa-image m-r-xs"></i>
          Im&aacute;genes
        </a>
      </li>
      <li>
        <a id="rubro_4_link" href="#rubro_tab4" role="tab" data-toggle="tab">
          <i class="fa text-info fa-globe m-r-xs"></i>
          Web
        </a>
      </li>
      <li>
        <a id="rubro_4_link" href="#rubro_tab5" role="tab" data-toggle="tab">
          <i class="fa text-primary fa-font m-r-xs"></i>
          SEO
        </a>
      </li>
      <li>
        <a id="rubro_3_link" href="#rubro_tab3" role="tab" data-toggle="tab">
          <i class="fa text-danger fa-exchange m-r-xs"></i>
          Relacionadas
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="rubro_tab1" class="tab-pane active">
        <div class="form-group">
          <label class="control-label">Nombre</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" class="form-control" id="rubros_nombre" value="<%= nombre %>"/>
        </div>
        <div class="form-group">
          <label class="control-label">Pertenece a</label>
          <select class="form-control" name="id_padre" id="rubros_padre"></select>
        </div>
        <div class="form-group cb mb0">
          <label class="i-checks">
            <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> <%= (!edicion)?"disabled":"" %>>
            <i></i>
            La categor&iacute;a esta activa.
          </label>
        </div>
        <div class="form-group cb mb0">
          <label class="i-checks">
            <input type="checkbox" name="destacado" class="checkbox" value="1" <%= (destacado == 1)?"checked":"" %> <%= (!edicion)?"disabled":"" %>>
            <i></i>
            Marcar la categor&iacute;a como destacada.
          </label>
        </div>
      </div>

      <div id="rubro_tab5" class="tab-pane">
        <div class="form-group">
          <label class="control-label">
            <?php echo lang(array(
              "es"=>"T&iacute;tulo",
              "en"=>"Title",
            )); ?>
          </label>
          <label class="control-label fr">
            <span id="rubro_seo_title_cantidad">0</span>
            <?php echo lang(array(
              "es"=>"de",
              "en"=>"of",
            )); ?>
            <span>70</span>
          </label>
          <input type="text" data-max="70" data-id="rubro_seo_title_cantidad" name="seo_title" id="rubro_seo_title" value="<%= seo_title %>" class="form-control text-remain"/>
        </div>
        <div class="form-group">
          <label class="control-label">
            <?php echo lang(array(
              "es"=>"Descripci&oacute;n",
              "en"=>"Description",
            )); ?>
          </label>
          <label class="control-label fr">
            <span id="rubro_seo_description_cantidad">0</span>
            <?php echo lang(array(
              "es"=>"de",
              "en"=>"of",
            )); ?>
            <span>160</span>
          </label>
          <textarea data-max="160" data-id="rubro_seo_description_cantidad" name="seo_description" id="rubro_seo_description" class="form-control text-remain"><%= seo_description %></textarea>
        </div>
        <div class="form-group">
          <label class="control-label">H1</label>
          <input <%= (!edicion)?"disabled":"" %> type="text" name="h1" class="form-control" id="rubros_h1" value="<%= h1 %>"/>
        </div>
      </div>

      <div id="rubro_tab4" class="tab-pane">
        <div class="form-group">
          <label class="control-label">Subtitulo</label>
          <input type="text" name="subtitulo" class="form-control" id="rubros_subtitulo" value="<%= subtitulo %>" <%= (!edicion)?"disabled":"" %>/>
        </div>
        <div class="form-group lang-control">
          <div class="clearfix">
            <label class="control-label m-t-xs">
              <?php echo lang(array(
                "es"=>"Texto",
                "en"=>"Text",
              )); ?>
            </label>
            <div class="lang-control-btn">
              <label class="btn btn-default btn-lang active" data-id="rubro_texto_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
              <label id="rubro_link_2" class="btn btn-default btn-lang" data-id="rubro_texto_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
              <label id="rubro_link_3" class="btn btn-default btn-lang" data-id="rubro_texto_pt_cont" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
            </div>
          </div>
          <div class="form-group">
            <div class="form-control-cont active" id="rubro_texto_cont">
              <textarea name="texto" id="rubro_texto"><%= texto %></textarea>
            </div>
            <div class="form-control-cont" id="rubro_texto_en_cont">
              <textarea name="texto_en" id="rubro_texto_en"><%= texto_en %></textarea>
            </div>
            <div class="form-control-cont" id="rubro_texto_pt_cont">
              <textarea name="texto_pt" id="rubro_texto_pt"><%= texto_pt %></textarea>
            </div>
          </div>
        </div>
      </div>

      <div id="rubro_tab2" class="tab-pane">

        <?php
        single_upload(array(
          "name"=>"path",
          "label"=>"Imagen",
          "url"=>"/sistema/articulos/function/save_image/",
          "url_file"=>"/sistema/entradas/function/save_file/",
          "width"=>(isset($empresa->config["categoria_articulo_image_width"]) ? $empresa->config["categoria_articulo_image_width"] : 256),
          "height"=>(isset($empresa->config["categoria_articulo_image_height"]) ? $empresa->config["categoria_articulo_image_height"] : 256),
          "quality"=>(isset($empresa->config["categoria_articulo_image_quality"]) ? $empresa->config["categoria_articulo_image_quality"] : 0),
          "thumbnail_width"=>(isset($empresa->config["categoria_articulo_thumbnail_width"]) ? $empresa->config["categoria_articulo_thumbnail_width"] : 0),
          "thumbnail_height"=>(isset($empresa->config["categoria_articulo_thumbnail_height"]) ? $empresa->config["categoria_articulo_thumbnail_height"] : 0),
        )); ?>

        <?php
        multiple_upload(array(
          "name"=>"images",
          "label"=>"Galer&iacute;a de fotos",
          "url"=>"rubros/function/save_image/",
          "width"=>(isset($empresa->config["categoria_articulo_galeria_image_width"]) ? $empresa->config["categoria_articulo_galeria_image_width"] : 800),
          "height"=>(isset($empresa->config["categoria_articulo_galeria_image_height"]) ? $empresa->config["categoria_articulo_galeria_image_height"] : 600),
          "quality"=>(isset($empresa->config["categoria_articulo_galeria_image_quality"]) ? $empresa->config["categoria_articulo_galeria_image_quality"] : 0.9),
        )); ?>

      </div>

      <div id="rubro_tab3" class="tab-pane">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Categor&iacute;as relacionadas",
            "en"=>"Related categories",
          )); ?>
        </label>
        <div id="rubros_tree" style="overflow: auto;max-height:300px"></div>
      </div>
    </div>
  </div>
  <% if (control.check("rubros")>1) { %>
    <div class="panel-footer clearfix tar" style="border-top: none">
      <% if (id != undefined && control.check("rubros")>2) { %>
        <button class="btn btn-danger eliminar fl">Eliminar</button>
      <% } %>
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  <% } %>
</div>
</script>

<script type="text/template" id="rubros_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
      <h4 class="h4 pull-left">Nueva categor&iacute;a</h4>
      <i class="pull-right fa fa-times text-muted cp cerrar"></i>
    </div>
    <div class="form-group">
      <input type="text" placeholder="Nombre" name="nombre" class="form-control tab" id="rubros_mini_nombre" value="<%= nombre %>"/>
    </div>
    <div class="form-group">
      <select class="form-control tab" name="id_padre" id="rubros_mini_padre"></select>
    </div>
    <div class="form-group mb0 tar">
      <button class="btn guardar tab btn-success">Guardar</button>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="rubro_mover_padre_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Mover a otra categoria</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Categor&iacute;a",
            "en"=>"Category",
          )); ?>
        </label>
        <div class="input-group">
          <select id="rubro_mover_padre_rubros" class="form-control no-model"></select>
          <span class="input-group-btn">
            <button tabindex="-1" class="btn btn-info w100 agregar_rubro">
              <?php echo lang(array(
                "es"=>"+ Agregar",
                "en"=>"+ Add",
              )); ?>
            </button>
          </span>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>
</script>
