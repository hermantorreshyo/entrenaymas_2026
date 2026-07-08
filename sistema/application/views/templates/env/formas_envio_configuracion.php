<script type="text/template" id="formas_envio_configuracion_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
    / <b>M&eacute;todos de env&iacute;o</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">MercadoEnvíos</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Habilite esta opción si desea integrar MercadoEnvíos.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (forma_envio == "MERCADOENVIOS")?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="forma_envio_mercadoenvios" class="checkbox" value="1" <%= (forma_envio == "MERCADOENVIOS")?"checked":"" %> ><i></i>
                    Utilizar MercadoEnvíos en su web.
                  </label>
                </div>
                <div class="text-muted fs14 mt5 ml7">
                  Asegurate que tengas activa la cuenta de MercadoEnvios desde <a href="https://www.mercadopago.com.ar/envios" target="_blank" class="link">este link</a>.
                </div>                
              </div>

              <label class="bold mb10">Excepciones:</label>
              <p class="text-muted">Utilice las excepciones para enviar por otros medios que no sea MercadoEnvio en los códigos postales especificados a continuación.</p>

              <div class="row m-b clearfix">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo Postal</label>
                    <input type="text" class="form-control" id="excepcion_codigo_postal" />
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Costo Envio (0=Gratis)</label>
                    <input id="excepcion_costo_envio" value="0" type="number" class="form-control"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Monto desde</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="excepcion_monto_desde" />
                      <span class="input-group-btn">
                        <a id="excepcion_agregar" class="btn btn-info">Agregar +</a>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mb30">
                <table id="excepciones_tabla" class="table m-b-none default footable">
                  <thead>
                  <tr>
                    <th>C&oacute;digo Postal</th>
                    <th>Costo Envio</th>
                    <th>Monto desde</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< excepciones.length;i++) { %>
                      <% var p = excepciones[i] %>
                      <tr data-id="<%= p.id %>">
                        <td><%= p.codigo_postal %></td>
                        <td><%= p.costo_envio %></td>
                        <td><%= p.monto_desde %></td>
                        <td><a href="javascript:void(0)" class="btn btn-white"><i class='fa fa-pencil cp editar_excepcion'></i></a></td>
                        <td><a href="javascript:void(0)" class="btn btn-white"><i class='fa fa-times eliminar_excepcion cp'></i></a></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

              <div class="form-group" style="<%= (ID_EMPRESA != 342)?"display:none":"" %>">
                <label class="control-label">Utilizar las reglas anteriores en:</label>
                <select name="uso_excepciones" class="form-control">
                  <option value="0" <%= (uso_excepciones == 0)?"selected":"" %>>Todos los productos</option>
                  <option value="1" <%= (uso_excepciones == 1)?"selected":"" %>>Solamente aquellos articulos marcados como coordinar envio.</option>
                </select>
              </div>

            </div>
          </div>
        </div>    

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Reparto Propio</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Seleccione esta opción si el reparto estará a cargo por su cuenta, configurando distintos valores y zonas de envío.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (forma_envio == "REPARTO")?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="forma_envio_reparto" class="checkbox" value="1" <%= (forma_envio == "REPARTO")?"checked":"" %> ><i></i>
                    Habilitar la opci&oacute;n de reparto propio.
                  </label>
                </div>
              </div>

              <label class="bold mb10">Tabla de Valores:</label>
              <p class="text-muted">Ingrese las diferentes zonas en donde realizará envios. Cada zona luego es seleccionable por el cliente.</p>

              <div class="row m-b clearfix">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Zona</label>
                    <input type="text" class="form-control" placeholder="Ej: ciudad, región, etc." id="valor_zona" />
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Costo Envio (0=Gratis)</label>
                    <input id="valor_costo_envio" value="0" type="number" class="form-control"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Monto desde</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="valor_monto_desde" />
                      <span class="input-group-btn">
                        <a id="valor_agregar" class="btn btn-info">Agregar +</a>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb30">
                <table id="valores_tabla" class="table m-b-none default footable">
                  <thead>
                  <tr>
                    <th>Zona</th>
                    <th>Costo Envio</th>
                    <th>Monto desde</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< valores.length;i++) { %>
                      <% var p = valores[i] %>
                      <tr data-id="<%= p.id %>">
                        <td><%= p.codigo_postal %></td>
                        <td><%= p.costo_envio %></td>
                        <td><%= p.monto_desde %></td>
                        <td><a href="javascript:void(0)" class="btn btn-white"><i class='fa fa-pencil cp editar_valor'></i></a></td>
                        <td><a href="javascript:void(0)" class="btn btn-white"><i class='fa fa-times eliminar_valor cp'></i></a></td>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

              <div class="form-group">
                <label class="control-label">Costo de envío por defecto</label>
                <input type="text" class="form-control" name="costo_envio_fijo" id="costo_envio_fijo" value="<%= costo_envio_fijo %>" />
              </div>

            </div>
          </div>
        </div>   

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Retiro en Sucursal</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Permite al cliente retirar el pedido por su sucursal.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (retiro_sucursal==1)?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" name="retiro_sucursal" class="checkbox" value="1" <%= (retiro_sucursal == 1)?"checked":"" %> ><i></i>
                    Habilitar la opci&oacute;n de retiro en sucursal.
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Coordinar envío</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Si el cliente selecciona está opción, el envío será coordinado de manera personal.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (convenir_envio==1)?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" name="convenir_envio" class="checkbox" value="1" <%= (convenir_envio == 1)?"checked":"" %> ><i></i>
                    Habilitar la opci&oacute;n para convenir el envio.
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Envíos Excepcionales</label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  En esta sección puede habilitar las áreas donde se entregarán
                  los artículos que fueron marcados como 'envíos excepcionales'.<br/>
                  Puede utilizar esta opción para entregar productos frágiles, refrigerados,
                  o que por algún motivo no utilizaría el servicio normal.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (!isEmpty(excepciones_fragiles))?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="form-group">
                  <label class="control-label">Lista de códigos postales habilitados. En cualquier otro caso, no se podrá realizar el envío.</label>
                  <input type="text" placeholder="Codigos postales (separados por coma)" name="excepciones_fragiles" class="form-control" value="<%= excepciones_fragiles %>"/>
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