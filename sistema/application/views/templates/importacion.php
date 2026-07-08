<script type="text/template" id="importacion_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Importacion de datos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row pl10 pr10">

          <% if (tabla == "articulos" || tabla == "importaciones_articulos_items") { %>
            <div class="col-sm-4 col-xs-12 pr5 pl5">
              <div class="">
                <select id="importacion_proveedores"></select>
              </div>
            </div>
          <% } %>

          <% if (tabla == "articulos" || tabla == "importaciones_articulos_items") { %>
            <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5" style="<%= (ID_EMPRESA == 444)?"display:none":"" %>">
              <select class="form-control" id="importacion_sucursales">
                <% for(var i=0; i< almacenes.length; i++) { %>
                  <% var alm = almacenes[i] %>
                  <% if (ID_SUCURSAL == 0) { %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } else if (ID_SUCURSAL == alm.id) { %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                <% } %>
              </select>
            </div>
          <% } %>

          <% if ((tabla == "articulos" || tabla == "importaciones_articulos_items") && ID_EMPRESA == 444) { %>
            <div class="col-md-2 col-sm-3 col-xs-12 pr5 pl5">
              <select class="form-control" id="importacion_moneda">
                <option value="-1">Seleccione</option>
                <option value="$">Pesos</option>
                <option value="U$S">Dolares</option>
              </select>
            </div>
          <% } %>

          <% if (estado == 0) { %>
            <button class="btn btn-info continuar fr">Continuar</button>
          <% } %>

        </div>
      </div>
      <div class="panel-body">

        <% if (estado == 1) { %>
          <div class="bg-success">
            Insertados: <b><%= cant_insertados %></b>.
            Modificados: <b><%= cant_modificados %></b>.
          </div>
        <% } %>

        <div class="b-a table-responsive" style="overflow: auto; max-height: 400px;">
          <table id="importacion_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="w30"></th>
                <% for (var i=0;i< columnas;i++) { %>
                  <% var nro_columna = i+1 %>
                  <th style="min-width: 170px">
                    <div class="input-group">
                      <span class="input-group-btn">
                        <label class="i-checks m-b-none" data-toggle="tooltip" title="Columna obligatoria para ingresar un nuevo registro.">
                          <input class="check_columna" type="checkbox"><i></i>
                        </label>
                      </span>
                      <select data-col="<%= nro_columna %>" class="form-control no-model select_columna">
                        <option value="">Campo</option>
                        <% if (tabla == "articulos" || tabla == "importaciones_articulos_items") { %>
                          <% if (ID_EMPRESA == 444) { %>
                            <option data-tipo="texto" data-obligatorio="0" value="codigo">Cod. Interno</option>
                            <option data-tipo="texto" data-obligatorio="1" value="codigo_prov">Cod. Proveedor</option>
                            <option data-tipo="texto" data-obligatorio="1" value="nombre">Nombre</option>
                            <option data-tipo="numero" data-obligatorio="1" value="costo_neto_inicial">Lista</option>
                            <option data-tipo="numero" data-obligatorio="0" value="modif_costo_1">Desc. 1</option>
                            <option data-tipo="numero" data-obligatorio="0" value="modif_costo_2">Desc. 2</option>
                            <option data-tipo="numero" data-obligatorio="0" value="modif_costo_3">Desc. 3</option>
                            <option data-tipo="numero" data-obligatorio="0" value="modif_costo_4">Desc. 4</option>
                            <option data-tipo="numero" data-obligatorio="0" value="modif_costo_5">Desc. 5</option>
                            <option data-tipo="numero" data-obligatorio="0" value="coeficiente">Coeficiente</option>
                            <option data-tipo="numero" data-obligatorio="0" value="bulto">Bulto</option>
                            <option data-tipo="numero" data-obligatorio="0" value="cantidad">Cantidad</option>
                          <% } else { %>
                            <option data-tipo="texto" value="nombre">Nombre</option>
                            <option data-tipo="texto" data-obligatorio="1" value="codigo">Cod. Interno</option>
                            <option data-tipo="texto" value="codigo_barra">Cod. Barra</option>
                            <option data-tipo="texto" value="custom_10">Cod. Proveedor</option>
                            <option data-tipo="numero" value="stock">Stock</option>
                            <option data-tipo="texto" value="texto">Descripcion</option>
                            <option data-tipo="numero" value="costo_neto">Costo Neto</option>
                            <option data-tipo="numero" value="costo_final">Costo Final</option>
                            <option data-tipo="numero" value="porc_ganancia">% Marcacion</option>
                            <option data-tipo="numero" value="porc_iva">Alicuota IVA</option>
                            <option data-tipo="numero" value="precio_neto">Precio Neto</option>
                            <option data-tipo="numero" value="precio_final">Precio Lista 1</option>
                            <option data-tipo="numero" value="precio_final_2">Precio Lista 2</option>
                            <option data-tipo="numero" value="precio_final_3">Precio Lista 3</option>
                            <option data-tipo="numero" value="precio_final_4">Precio Lista 4</option>
                            <option data-tipo="numero" value="precio_final_5">Precio Lista 5</option>
                            <option data-tipo="numero" value="precio_final_6">Precio Lista 6</option>
                            <option data-tipo="texto" value="marca">Marca</option>
                            <option data-tipo="texto" value="rubro">Categoria</option>
                            <option data-tipo="texto" value="subrubro">Subcategoria</option>
                            <option data-tipo="texto" value="subsubrubro">Sub-Subcategoria</option>
                            <option data-tipo="texto" value="proveedor">Proveedor</option>
                            <option data-tipo="texto" value="path">Imagen Principal</option>
                            <!--<option data-tipo="texto" value="paths">Imagen Principal</option>-->

                            <option data-tipo="texto" value="titulo_meli">Titulo MercadoLibre</option>
                            <option data-tipo="texto" value="texto_meli">Texto MercadoLibre</option>
                            <option data-tipo="texto" value="permalink">Link MercadoLibre</option>

                            <option data-tipo="numero" value="ancho">Ancho (Mts)</option>
                            <option data-tipo="numero" value="alto">Alto (Mts)</option>
                            <option data-tipo="numero" value="profundidad">Profundidad (Mts)</option>


                            <% if (TIPO_EMPRESA == 1) { %>
                              <option data-tipo="numero" value="custom_7">Ancho Neumatico</option>
                              <option data-tipo="numero" value="custom_8">Perfil</option>
                              <option data-tipo="numero" value="custom_9">Rodado</option>
                            <% } %>

                            <% if (ID_EMPRESA == 186 || ID_EMPRESA == 252 || ID_EMPRESA == 120) { %>
                              <option data-tipo="texto" value="marca_vehiculo">Marca Vehiculo</option>
                              <option data-tipo="texto" value="modelo_vehiculo">Modelo Vehiculo</option>
                            <% } %>
                            
                            <option data-tipo="numero" value="peso">Peso</option>
                            <% if (VOLVER_SUPERADMIN == 1) { %>
                              <option data-tipo="texto" value="custom_1">Custom 1</option>
                              <option data-tipo="texto" value="custom_2">Custom 2</option>
                              <option data-tipo="texto" value="custom_3">Custom 3</option>
                              <option data-tipo="texto" value="custom_4">Custom 4</option>
                              <option data-tipo="texto" value="custom_5">Custom 5</option>
                              <option data-tipo="texto" value="custom_6">Custom 6</option>
                              <option data-tipo="texto" value="custom_7">Custom 7</option>
                              <option data-tipo="texto" value="custom_8">Custom 8</option>
                              <option data-tipo="texto" value="custom_9">Custom 9</option>
                            <% } %>
                          <% } %>

                        <% } else if (tabla == "clientes") { %>
                          <option data-tipo="texto" data-obligatorio="1" value="codigo">Cod. Interno</option>
                          <option data-tipo="texto" value="nombre">Nombre</option>
                          <option data-tipo="texto" value="email">Email</option>
                          <option data-tipo="texto" value="id_tipo_iva">Tipo IVA</option>
                          <option data-tipo="texto" value="cuit">Nro. CUIT/DNI</option>
                          <option data-tipo="texto" value="direccion">Direccion</option>
                          <option data-tipo="texto" value="codigo_postal">Cod. Postal</option>
                          <option data-tipo="texto" value="localidad">Localidad</option>
                          <option data-tipo="texto" value="vendedor">Vendedor</option>
                          <option data-tipo="texto" value="telefono">Telefono</option>
                          <option data-tipo="texto" value="celular">Celular</option>
                          <option data-tipo="texto" value="fax">FAX</option>
                          <option data-tipo="texto" value="facebook">Facebook</option>
                          <option data-tipo="texto" value="twitter">Twitter</option>
                          <option data-tipo="texto" value="instagram">Instagram</option>
                          <option data-tipo="texto" value="youtube">Youtube</option>
                          <option data-tipo="texto" value="linkedin">Linkedin</option>
                          <option data-tipo="texto" value="observaciones">Observaciones</option>
                          <option data-tipo="texto" value="descuento">% Descuento</option>
                          <option data-tipo="texto" value="contacto_nombre">Contacto Nombre</option>
                          <option data-tipo="texto" value="contacto_email">Contacto Email</option>
                          <option data-tipo="texto" value="contacto_telefono">Contacto Telefono</option>
                          <option data-tipo="texto" value="contacto_celular">Contacto Celular</option>

                        <% } %>
                      </select>
                    </div>
                  </th>
                <% } %>
              </tr>
            </thead>
            <tbody><%= preview %></tbody>
          </table>
        </div>
        <div style="<%= (ID_EMPRESA == 444)?"display:none":"" %>">
          <div>
            <div class="checkbox">
              <label class="i-checks">
                <input <%= (MEGASHOP == 1)?"checked":"" %> type="checkbox" id="importacion_ignorar_primera_fila"><i></i> Ignorar la primera fila del archivo.
              </label>
            </div>          
          </div>
          <div>
            <div class="checkbox">
              <label class="i-checks">
                <input <%= (MEGASHOP == 1)?"checked":"" %> type="checkbox" id="importacion_solo_actualizar"><i></i> Solo actualizar productos ya cargados, no agrega productos nuevos.
              </label>
            </div>          
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Prefijo de codigo</label>
                <input type="text" class="form-control no-model" id="importacion_prefijo_codigo" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Fecha Stock</label>
                <div class="input-group">
                  <input type="text" class="form-control no-model" id="importacion_fecha_stock" />
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>              
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</script>