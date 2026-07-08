<script type="text/template" id="petips_productos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> <b>Productos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("petips_productos") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#petips_producto"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="petips_productos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
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


<script type="text/template" id="petips_productos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
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

<script type="text/template" id="petips_productos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> 
    Productos
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<form onsubmit="return false" class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="petips_productos_nombre" value="<%= nombre %>"/>
              </div>

              <div class="row">
              	<div class="col-md-6">
		              <div class="form-group">
		                <label class="control-label">Marca</label>
		                <div class="input-group">
		                  <select id="articulo_marcas" class="form-control"></select>
		                  <span class="input-group-btn">
		                    <button tabindex="-1" class="btn btn-info w100 agregar_marca">+ Marca</button>
		                  </span>
		                  <span class="input-group-btn">
		                    <a target="_blank" href="app/#petips_marcas" class="btn btn-default"><i class="fa fa-cog"></i></a>
		                  </span>
		                </div>
		              </div>
		            </div>
		            <div class="col-md-6">
		              <div class="form-group">
		                <label class="control-label">Segmento</label>
		                <div class="input-group">
		                  <select id="articulo_segmentos" class="form-control"></select>
		                  <span class="input-group-btn">
		                    <button tabindex="-1" class="btn btn-info w100 agregar_segmento">+ Segmento</button>
		                  </span>
		                  <span class="input-group-btn">
		                    <a target="_blank" href="app/#petips_segmentos" class="btn btn-default"><i class="fa fa-cog"></i></a>
		                  </span>
		                </div>
		              </div>
		            </div>
		          </div>

              <div class="row">
		          	<div class="col-md-6">
		              <div class="form-group">
		                <label class="control-label">Especie</label>
		                <div class="input-group">
		                  <select id="articulo_animales" class="form-control"></select>
		                  <span class="input-group-btn">
		                    <button tabindex="-1" class="btn btn-info w100 agregar_especie">+ Especie</button>
		                  </span>
		                  <span class="input-group-btn">
		                    <a target="_blank" href="app/#petips_animales" class="btn btn-default"><i class="fa fa-cog"></i></a>
		                  </span>
		                </div>
		              </div>
		            </div>
              	<div class="col-md-6">
		              <div class="form-group">
		                <label class="control-label">Tipo de Alimento</label>
		                <div class="input-group">
		                  <select id="articulo_tipos_alimentos" class="form-control"></select>
		                  <span class="input-group-btn">
		                    <button tabindex="-1" class="btn btn-info w100 agregar_tipo_alimento">+ Tipo</button>
		                  </span>
		                  <span class="input-group-btn">
		                    <a target="_blank" href="app/#petips_tipos_alimentos" class="btn btn-default"><i class="fa fa-cog"></i></a>
		                  </span>
		                </div>
		              </div>
		            </div>		            
		          </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Empresa</label>
                    <div class="input-group">
                      <select id="articulo_fabricantes" class="form-control"></select>
                      <span class="input-group-btn">
                        <a target="_blank" href="app/#petips_fabricantes" class="btn btn-default"><i class="fa fa-cog"></i></a>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Especialidad</label>
                    <div class="input-group">
                      <select id="articulo_especialidades" class="form-control"></select>
                      <span class="input-group-btn">
                        <a target="_blank" href="app/#petips_especialidades" class="btn btn-default"><i class="fa fa-cog"></i></a>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Reputacion del Mercado</label>
                    <input type="text" name="reputacion_mercado" id="articulo_reputacion_mercado" value="<%= reputacion_mercado %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Opiniones de Clientes</label>
                    <input type="text" name="opiniones_clientes" id="articulo_opiniones_clientes" value="<%= opiniones_clientes %>" class="form-control"/>
                  </div>
                </div>
              </div>

                             
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <?php
              $label = lang(array(
                "es"=>"Im&aacute;genes",
                "en"=>"Photos",
              )); ?>
              <?php 
              multiple_upload(array(
                "name"=>"images",
                "label"=>$label,
                "url"=>"articulos/function/save_image/",
                "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                "crop_type"=>(isset($empresa->config["producto_galeria_image_crop_type"]) ? $empresa->config["producto_galeria_image_crop_type"] : 1),
                "upload_multiple"=>true,
              )); ?>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Valores Nutricionales</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Proteina</label>
                    <input type="text" name="proteina" id="articulo_proteina" value="<%= proteina %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Grasa</label>
                    <input type="text" name="grasa" id="articulo_grasa" value="<%= grasa %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Humedad</label>
                    <input type="text" name="humedad" id="articulo_humedad" value="<%= humedad %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fibra</label>
                    <input type="text" name="fibra" id="articulo_fibra" value="<%= fibra %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Cenizas</label>
                    <input type="text" name="cenizas" id="articulo_cenizas" value="<%= cenizas %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Calcio</label>
                    <input type="text" name="calcio" id="articulo_calcio" value="<%= calcio %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Fósforo</label>
                    <input type="text" name="fosforo" id="articulo_fosforo" value="<%= fosforo %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Carbohidratos</label>
                    <input type="text" name="carbohidratos" id="articulo_carbohidratos" value="<%= carbohidratos %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" id="articulo_nutricionalmente_completo" name="nutricionalmente_completo" class="checkbox" value="1" <%= (nutricionalmente_completo == 1)?"checked":"" %> >
                  <i></i>
                  El alimiento es nutricionalmente completo.
                </label>
              </div>

              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" id="articulo_es_hipoalergenico" name="es_hipoalergenico" class="checkbox" value="1" <%= (es_hipoalergenico == 1)?"checked":"" %> >
                  <i></i>
                  El alimiento es hipoalergénico.
                </label>
              </div>

              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" id="articulo_es_natural" name="es_natural" class="checkbox" value="1" <%= (es_natural == 1)?"checked":"" %> >
                  <i></i>
                  El alimento es de origen natural
                </label>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Ingredientes</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (ingredientes.length > 0)?'display:block':'' %>">
            <div class="padder">
            	<div class="row">
            		<div class="col-md-11">
	                <div class="form-group">
	                	<select id="articulo_ingredientes" class="w100p"></select>
	                </div>
	              </div>
	              <div class="col-md-1">
	               	<a id="ingrediente_agregar" class="btn btn-block btn-info"><i class="fa ico fa-plus"></i></a>
	              </div>
	            </div>
              <div class="">
                <table id="ingredientes_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Ingrediente</th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< ingredientes.length;i++) { %>
                      <% var p = ingredientes[i] %>
                      <tr data-id="<%= p.id_ingrediente %>">
                        <td><%= p.nombre %></td>
                        <td><i class='fa fa-times eliminar_ingrediente text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Claims</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (claims.length > 0)?'display:block':'' %>">
            <div class="padder">
            	<div class="row">
            		<div class="col-md-11">
	                <div class="form-group">
	                	<select id="articulo_claims" class="w100p"></select>
	                </div>
	              </div>
	              <div class="col-md-1">
	               	<a id="claim_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
	              </div>
	            </div>            	
              <div class="">
                <table id="claims_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Claim</th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< claims.length;i++) { %>
                      <% var p = claims[i] %>
                      <tr data-id="<%= p.id_claim %>">
                        <td><%= p.nombre %></td>
                        <td><i class='fa fa-times eliminar_claim text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Edades</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (edades.length > 0)?'display:block':'' %>">
            <div class="padder">
            	<div class="row">
            		<div class="col-md-11">
	                <div class="form-group">
	                	<select id="articulo_edades" class="w100p"></select>
	                </div>
	              </div>
	              <div class="col-md-1">
	               	<a id="edad_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
	              </div>
	            </div>
              <div class="">
                <table id="edades_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Edad</th>
                      <th class="w25"></th>
                      <th class="w25"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< edades.length;i++) { %>
                      <% var p = edades[i] %>
                      <tr data-id="<%= p.id_edad %>">
                        <td><%= p.nombre %></td>
                        <td><i class='fa fa-pencil cp editar_edad'></i></td>
                        <td><i class='fa fa-times eliminar_edad text-danger cp'></i></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <% if (edicion) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</form>

</script>