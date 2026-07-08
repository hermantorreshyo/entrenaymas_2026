<script type="text/template" id="proveedores_panel_template">
<% if (seleccionar) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Contactos / <b>Proveedores</b></h1>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-xs-12 sm-m-b">
          <div class="input-group">
            <input type="text" id="proveedores_buscar" value="<%= window.proveedores_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="proveedores_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;"></th>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
<% } else { %>
  <?php /*
  <div class="seccion_vacia" style="display:none">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n proveedor</h1>
    <h3 class="h3">Para crear tu primer proveedor, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#proveedor"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#proveedor">
      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
  */ ?>
  <div>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Proveedores</h1>
    </div>
    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">

        <?php $active = "proveedores"; include("cli/proveedores_menu.php"); ?>
      
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-8 sm-m-b">
              <div class="input-group">
                <input type="text" id="proveedores_buscar" value="<%= window.proveedores_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                </span>

                <span class="input-group-btn">
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void(0)" class="exportar_excel">Excel</a></li>
                      <li><a href="javascript:void(0)" class="exportar_csv">Archivo Texto</a></li>
                    </ul>
                  </div>
                </span>

                <span class="input-group-btn">
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-download"></i><span><?php echo lang(array("es"=>"Importar","en"=>"Import")); ?></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void(0)" class="importar_excel">Excel</a></li>
                      <li><a href="javascript:void(0)" class="importar_csv">Archivo Texto</a></li>
                      <% if (typeof RETIENE_IB != "undefined" && RETIENE_IB == 1) { %>
                        <li class="divider"></li>
                        <li><a href="proveedores/function/actualizar_padron/" target="_blank">Actualizar padron</a></li>
                      <% } %>
                    </ul>
                  </div>
                </span>

              </div>
            </div>
            <div class="col-md-4 text-right">
              <% if (control.check("proveedores")>1) { %> 
                <a class="btn btn-info btn-addon ml5" href="app/#proveedor">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo Proveedor&nbsp;&nbsp;</span>
                </a>
              <% } %>
            </div>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="<%= (window.proveedores_tipo_proveedor > 1) ? "display:block" : "display:none" %>">
          <div class="wrapper oh">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select <%= (PERFIL == 395)?"disabled":"" %> class="w100p form-control no-model" id="proveedores_tipo_proveedor">
                    <option <%= (window.proveedores_tipo_proveedor == 0)?"selected":"" %> value="0">Tipo de Proveedor</option>
                    <option <%= (window.proveedores_tipo_proveedor == 1)?"selected":"" %> value="1">Mercaderia</option>
                    <option <%= (window.proveedores_tipo_proveedor == 2)?"selected":"" %> value="2">Alquiler</option>
                    <option <%= (window.proveedores_tipo_proveedor == 3)?"selected":"" %> value="3">Profesional</option>
                    <option <%= (window.proveedores_tipo_proveedor == 4)?"selected":"" %> value="4">Otros</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="proveedores_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th style="width:20px;"></th>
                  <th class="w50 tac hidden-xs"></th>
  								<th class="sorting" data-sort-by="nombre">Nombre</th>
  								<th class="col-xxs-0 sorting" data-sort-by="telefono">Tel&eacute;fono</th>
  								<th class="col-xxs-0 sorting" data-sort-by="email">Email</th>
                  <th class="col-xxs-0 w70"></th>
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
  </div>
<% } %>
</script>

<script type="text/template" id="proveedores_item">
  <% var clase = (activo==1)?"":"text-muted"; %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
    <td class='data'><span class="<%= (activo==1)?'text-info':'text-muted' %>"><%= nombre %></span></td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
    <td class="<%= clase %> data hidden-xs">
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
      </span>
    </td>
    <td class='data'>
      <span class="<%= (activo==1)?'text-info':'text-muted' %>"><%= nombre %></span>
      <% if (!isEmpty(codigo)) { %><br/><span>Cod: <%= codigo %></span><% } %>
      <% if (tipo == 10) { %>
        <br/><span class="label bg-success">Clase A</span>
      <% } else if (tipo == 5) { %>
        <br/><span class="label bg-warning">Clase B</span>
      <% } else if (tipo == 3) { %>
        <br/><span class="label bg-light dk">Clase C</span>
      <% } %>
    </td>
    <td class="data col-xxs-0"><span><%= (isEmpty(telefono))?"—":telefono %></span></td>
    <td class="data col-xxs-0"><span><%= (isEmpty(email))?"—":email %></span></td>
    <td class="p5"><a class="btn btn-success btn-xs" href="app/#cuentas_corrientes_proveedores/<%= id %>">Cuenta Cte.</a></td>
    <% if (permiso > 1) { %>
      <td class="p5 <%= clase %> td_acciones">
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown ml10">
          <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
          </button>    
          <ul class="dropdown-menu pull-right">
            <% if (control.check("ingresos_proveedores") > 0) { %>
              <li><a href="app/#nuevo_ingreso_proveedor/<%= id %>">Cargar ingreso</a></li>
            <% } %>
            <% if (MEGASHOP != 1) { %>
              <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
            <% } %>
          </ul>
        </div>  
      </td>
    <% } %>
  <% } %>  
</script>

<script type="text/template" id="proveedores_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Contactos / Proveedores
  / <b><%= (id == undefined)?"Nuevo":nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>                    
                    <input type="text" <%= (!edicion)?"disabled":"" %> required name="nombre" autocomplete="off" id="proveedores_nombre" value="<%= nombre %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Raz&oacute;n Social</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="razon_social" autocomplete="off" id="proveedores_razon_social" value="<%= razon_social %>" class="form-control"/>
                  </div>
                </div>
              </div>              
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> required name="codigo" id="proveedores_codigo" value="<%= codigo %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Tipo IVA </label>
                    <select <%= (!edicion)?"disabled":"" %> class="form-control" id="proveedores_tipo_iva">
                      <option <%= (id_tipo_iva == 4) ? "selected":"" %> value="4">Consumidor Final</option>
                      <option <%= (id_tipo_iva == 2) ? "selected":"" %> value="2">Monotributo</option>
                      <option <%= (id_tipo_iva == 1) ? "selected":"" %> value="1">Responsable Inscripto</option>
                      <option <%= (id_tipo_iva == 3) ? "selected":"" %> value="3">Exento</option>
                    </select>    
                  </div>  
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">CUIT / DNI </label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="cuit" class="form-control" id="proveedores_cuit" value="<%= cuit %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Nro. Ing. Brutos</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="convenio_multilateral" class="form-control" id="proveedores_convenio_multilateral" value="<%= convenio_multilateral %>"/>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Telefono </label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="telefono" class="form-control" id="proveedores_telefono" value="<%= telefono %>"/>
                  </div>  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">FAX </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="fax" class="form-control" id="proveedores_fax" value="<%= fax %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Email </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="email" class="form-control" id="proveedores_email" value="<%= email %>"/>
                  </div>
                </div>
              </div>
          
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Direccion </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="direccion" class="form-control" id="proveedores_direccion" value="<%= direccion %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Localidad</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> value="<%= localidad %>" id="proveedores_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                  </div>  
                </div>
              </div> 

              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input <%= (!edicion)?"disabled":"" %> type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> ><i></i>
                    El proveedor est&aacute; activo.
                  </label>
                </div>
              </div>

              <div class="form-group mb0 tar">
                <a class="expand-link" id="expand_principal">
                  <?php echo lang(array(
                    "es"=>"+ M&aacute;s opciones",
                    "en"=>"+ More options",
                  )); ?>
                </a>
              </div>

            </div>
          </div>

          <div class="panel-body expand">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Nombre de contacto </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="contacto" class="form-control" id="proveedores_contacto" value="<%= contacto %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Telefono de contacto </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="contacto_telefono" class="form-control" id="proveedores_contacto_telefono" value="<%= contacto_telefono %>"/>
                  </div>  
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Direccion de contacto</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="contacto_direccion" class="form-control" id="proveedores_contacto_direccion" value="<%= contacto_direccion %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email de contacto</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="contacto_email" class="form-control" id="proveedores_contacto_email" value="<%= contacto_email %>"/>
                  </div>  
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Horario de atencion</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="horario" class="form-control" id="proveedores_horario" value="<%= horario %>"/>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Rubro del proveedor</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="rubro_comercial" class="form-control" id="proveedores_rubro_comercial" value="<%= rubro_comercial %>"/>
                  </div>  
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Sitio Web</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="web" class="form-control" id="proveedores_web" value="<%= web %>"/>
              </div>  

              <div class="form-group">
                <label class="control-label">Observaciones </label>
                <textarea <%= (!edicion)?"disabled":"" %> placeholder="Escriba aqui otros datos de contacto o notas de su proveedor..." style="height:100px" class="form-control" name="observaciones" id="proveedor_observaciones"><%= observaciones %></textarea>
              </div>

            </div>
          </div>

        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Tipo de proveedor</label>
                    <select <%= (!edicion)?"disabled":"" %> name="tipo_proveedor" class="form-control" id="proveedores_tipo_proveedor">
                      <option <%= (tipo_proveedor==1) ? "selected": "" %> value="1">Mercaderia</option>
                      <option <%= (tipo_proveedor==2) ? "selected": "" %> value="2">Alquiler</option>
                      <option <%= (tipo_proveedor==3) ? "selected": "" %> value="3">Profesional</option>
                      <option <%= (tipo_proveedor==4) ? "selected": "" %> value="4">Otros</option>
                      <% if (MEGASHOP == 1) { %>
                        <option <%= (tipo_proveedor==5) ? "selected": "" %> value="5">Heredado</option>
                      <% } %>
                    </select>
                  </div>
                </div>                
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Saldo Inicial</label>
                    <div class="input-group">
                      <input type="text" name="saldo_inicial" class="form-control" id="proveedores_saldo_inicial" value="<%= saldo_inicial %>"/>
                      <span class="input-group-addon">$</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Retenci&oacute;n de Ing. Brutos</label>
                    <div class="input-group">
                      <input <%= (!edicion)?"disabled":"" %> type="text" name="porc_ret_ib" class="form-control" id="proveedores_porc_ret_ib" value="<%= porc_ret_ib %>"/>
                      <span class="input-group-addon">%</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Retenci&oacute;n de ganancias</label>
                    <div class="checkbox" style="margin-left:6px">
                      <label class="i-checks">
                        <input <%= (!edicion)?"disabled":"" %> type="checkbox" name="aplica_ret_ganancias" class="checkbox" value="1" <%= (aplica_ret_ganancias == 1)?"checked":"" %>><i></i>
                        Aplicar retenci&oacute;n
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Forma de Pago </label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="forma_pago" class="form-control" id="proveedores_forma_pago" value="<%= forma_pago %>"/>
                  </div>
                </div>                
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Plazo de Pago</label>
                    <select <%= (!edicion)?"disabled":"" %> name="dias_pago" class="form-control" id="proveedores_dias_pago">
                      <option <%= (dias_pago==0) ? "selected": "" %> value="0">Pago inmediato</option>
                      <option <%= (dias_pago==30) ? "selected": "" %> value="30">30 días</option>
                      <option <%= (dias_pago==60) ? "selected": "" %> value="60">60 días</option>
                      <option <%= (dias_pago==90) ? "selected": "" %> value="90">90 días</option>
                      <option <%= (dias_pago==120) ? "selected": "" %> value="120">120 días</option>
                    </select>
                  </div>
                </div>                
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tipo de proveedor</label>
                    <select <%= (!edicion)?"disabled":"" %> name="tipo_proveedor" class="form-control" id="proveedores_tipo_proveedor">
                      <option <%= (tipo_proveedor==1) ? "selected": "" %> value="1">Mercaderia</option>
                      <option <%= (tipo_proveedor==2) ? "selected": "" %> value="2">Alquiler</option>
                      <option <%= (tipo_proveedor==3) ? "selected": "" %> value="3">Profesional</option>
                      <option <%= (tipo_proveedor==4) ? "selected": "" %> value="4">Otros</option>
                      <% if (MEGASHOP == 1) { %>
                        <option <%= (tipo_proveedor==5) ? "selected": "" %> value="5">Heredado</option>
                      <% } %>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Clase</label>
                    <select class="form-control" id="proveedor_tipo" name="tipo" <%= (!edicion)?"disabled":"" %>>
                      <option <%= (tipo=="")?"selected":"" %> value="">-</option>
                      <option <%= (tipo=="10")?"selected":"" %> value="10">Clase A</option>
                      <option <%= (tipo=="5")?"selected":"" %> value="5">Clase B</option>
                      <option <%= (tipo=="3")?"selected":"" %> value="3">Clase C</option>
                    </select>
                  </div> 
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Frecuencia</label>
                    <select <%= (!edicion)?"disabled":"" %> name="frecuencia" class="form-control" id="proveedores_frecuencia">
                      <option <%= (frecuencia=="0") ? "selected": "" %> value="0">No definida</option>
                      <option <%= (frecuencia=="7") ? "selected": "" %> value="7">Semanal</option>
                      <option <%= (frecuencia=="14") ? "selected": "" %> value="14">Quincenal</option>
                      <option <%= (frecuencia=="30") ? "selected": "" %> value="30">Mensual</option>
                      <option <%= (frecuencia=="30") ? "selected": "" %> value="30">Bimestral</option>
                      <option <%= (frecuencia=="90") ? "selected": "" %> value="90">Trimestral</option>
                      <option <%= (frecuencia=="120") ? "selected": "" %> value="120">Cuatrimestral</option>
                      <option <%= (frecuencia=="180") ? "selected": "" %> value="180">Semestral</option>
                      <option <%= (frecuencia=="365") ? "selected": "" %> value="365">Anual</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Relacionados</label>
                <select <%= (!edicion)?"disabled":"" %> id="proveedores_relacionados" class="w100p"></select>
              </div>

            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <% if (edicion) { %>
                <div class="row m-b clearfix">
                  <div class="form-group col-sm-6">
                    <label class="control-label">Banco </label>
                    <select <%= (!edicion)?"disabled":"" %> class="form-control no-model" id="proveedores_cuentas_bancos">
                      <option value="0">Seleccione</option>
                      <% for(var i=0;i<bancos.length;i++) { %>
                        <% var banco = bancos[i] %>
                        <option value="<%= banco.id %>"><%= banco.nombre %></option>
                      <% } %>
                    </select>    
                  </div>
                  <div class="form-group col-sm-3">
                    <label class="control-label">C.B.U.</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" class="form-control no-model" id="proveedores_cuentas_cbu"/>
                  </div>
                  <div class="form-group col-sm-3">
                    <label class="control-label">Nro. Cuenta</label>
                    <div class="input-group">
                      <input <%= (!edicion)?"disabled":"" %> type="text" class="form-control no-model" id="proveedores_cuentas_cuenta_bancaria"/>
                      <span class="input-group-btn">
                        <a id="proveedores_cuentas_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                      </span>
                    </div>
                  </div>
                </div>
              <% } %>
              <div class="">
                <table id="proveedores_cuentas_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Banco</th>
                      <th>CBU</th>
                      <th>Cuenta</th>
                      <% if (edicion) { %>
                        <th class="w25"></th>
                        <th class="w25"></th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< cuentas_bancarias.length;i++) { %>
                      <% var p = cuentas_bancarias[i] %>
                      <tr data-id="<%= p.id_banco %>">
                        <td><%= p.banco %></td>
                        <td><%= p.cbu %></td>
                        <td><%= p.cuenta %></td>
                        <% if (edicion) { %>
                          <td><i class='fa fa-pencil cp editar_cuenta'></i></td>
                          <td><i class='fa fa-times eliminar_cuenta text-danger cp'></i></td>
                        <% } %>
                      </tr>
                    <% } %>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
    <% if (control.check("proveedores")>1) { %> 
      <div class="row">
        <div class="col-md-10 col-md-offset-1 text-right">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    <% } %>
  </div>
</div>  
</script>

<script type="text/template" id="proveedores_edit_mini_panel_template">
<div class="panel pb0 mb0">
	<div class="panel-body">
	  <div class="form-group">
		  <input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="proveedores_mini_nombre"/>
	  </div>
	  <div class="form-group">
		  <input type="text" autocomplete="off" placeholder="Direccion" name="direccion" class="tab form-control" id="proveedores_mini_direccion"/>
	  </div>
	  <div class="form-group">
  		<select class="tab form-control tab" name="id_tipo_iva" id="proveedores_mini_tipo_iva">
  			<option <%= (id_tipo_iva == 1) ? "selected":"" %> value="1">Responsable Inscripto</option>
  			<option <%= (id_tipo_iva == 2) ? "selected":"" %> value="2">Monotributo</option>
  			<option <%= (id_tipo_iva == 3) ? "selected":"" %> value="3">Exento</option>
  			<option <%= (id_tipo_iva == 4) ? "selected":"" %> value="4">Consumidor Final</option>
  		</select>    
	  </div>
	  <div class="form-group">
		  <input type="text" autocomplete="off" placeholder="Nro. Doc / CUIT" name="cuit" class="tab form-control" id="proveedores_mini_cuit"/>
	  </div>
	  <div class="form-group">
		  <button class="btn guardar btn-success tab btn-block">Guardar</button>
	  </div>
	</div>
</div>
</script>