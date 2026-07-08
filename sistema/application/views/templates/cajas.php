<script type="text/template" id="cajas_panel_template">
  <% var modulo = control.get("cajas") %>
  <div class="bg-light lter b-b wrapper-md clearfix">
    <h1 class="m-n font-thin h3 pull-left"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <?php $active = "cajas"; include("caja/caja_menu.php"); ?>
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-12">
            <% if (control.check("cajas") > 1) { %>
              <div class="pull-right">
                <a class="btn btn-info btn-addon" href="app/#caja"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nueva Caja&nbsp;&nbsp;</a>
              </div>
              <div class="btn-group pull-right dropdown mr5">
                <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-cog"></i><span>Opciones</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" class="transferencia">Transferencia</a></li>
                  <li><a href="app/#cajas/1">Ver todas</a></li>
                </ul>
              </div>
            <% } %>
          </div>
        </div>
      </div>
      <div class="panel-body pt30">

        <ul class="nav nav-tabs nav-tabs-5 m-b" role="tablist" style="border:none">
          <li id="cambiar_tab_todos" class="<%= (window.cajas_tipo == "-1") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="-1" role="tab" data-toggle="tab"><?php echo lang(array("es"=>"Todas","en"=>"All")); ?></a>
          </li>
          <li id="cambiar_tab_efectivo" class="<%= (window.cajas_tipo == "0") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="0" role="tab" data-toggle="tab"><i class="fa fa-money text-success"></i> <?php echo lang(array("es"=>"Efectivo","en"=>"Cash")); ?></a>
          </li>
          <li id="cambiar_tab_banco" class="<%= (window.cajas_tipo == "1") ? "active":"" %>">
            <a href="javascript:void(0)" class="cambiar_tab" data-tipo="1" role="tab" data-toggle="tab"><i class="fa fa-bank text-info"></i> <?php echo lang(array("es"=>"Banco","en"=>"Bank")); ?></a>
          </li>
        </ul>        

        <div class="listado clearfix"></div>
      </div>
    </div>
  </div>
</script>


<script type="text/template" id="cajas_item">
  <div class="block panel padder-v pl15 pr15 item shadow" style="height: 160px">
    <div class="text-muted text-center text-md fs18 pt10">
      <span class="ver_movimientos text-info cp"><%= nombre %></span>
      <% if (permiso > 1) { %>
        <div class="btn-group dropdown pull-right">
          <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
          </button>     
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="editar" data-id="<%= id %>">Editar</a></li>
            <% if (activo == 1) { %>
              <li><a href="javascript:void(0)" class="ocultar" data-id="<%= id %>">Ocultar</a></li>
            <% } else { %>
              <li><a href="javascript:void(0)" class="ocultar" data-id="<%= id %>">Mostrar</a></li>
            <% } %>
            <li class="divider"></li>
            <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>
      <% } %>
    </div>
    <div class="font-thin h2 cp ver_movimientos text-center block m-t-md">$ <%= Number(saldo).format(2) %></div>
    <div class="text-right">
      <a class="text-info ver_movimientos text-md fs14 cp pt10 db">Ver detalle</a>
    </div>
  </div>
</script>

<script type="text/template" id="cajas_edit_panel_template">
<% var modulo = control.get("cajas") %>
<% console.log(modulo) %>
<div class="bg-light lter b-b wrapper-md clearfix">
  <h1 class="m-n font-thin h3 pull-left"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %>
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
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
                    <input type="text" name="nombre" class="form-control" id="cajas_nombre" value="<%= nombre %>" <%= (!edicion)?"disabled":"" %> />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo de Cuenta</label>
                    <select class="form-control" name="tipo" id="cajas_tipo">
                      <option <%= (tipo==0)?"selected":"" %> value="0">Efectivo</option>
                      <option <%= (tipo==1)?"selected":"" %> value="1">Cuenta Bancaria</option>
                      <option <%= (tipo==2)?"selected":"" %> value="2">MercadoPago</option>
                      <option <%= (tipo==3)?"selected":"" %> value="3">Paypal</option>
                      <option <%= (tipo==4)?"selected":"" %> value="4">TodoPago</option>
                    </select>
                  </div>
                </div>
              </div>
              <% if (control.check("almacenes")>0) { %>
                <div class="form-group">
                  <label class="control-label">Sucursal</label>
                  <select class="form-control" id="cajas_sucursales" name="id_sucursal">
                    <option value="0">-</option>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option <%= (id_sucursal == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  </select>
                </div>
              <% } %>
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

<script type="text/template" id="cajas_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
      <h4 class="h4 pull-left">Nueva caja</h4>
      <i class="pull-right fa fa-times text-muted cp cerrar"></i>
    </div>
    <div class="form-group">
      <input placeholder="Nombre" type="text" name="nombre" class="form-control tab" id="cajas_mini_nombre" value="<%= nombre %>"/>
    </div>
    <div class="form-group tar mb0">
      <button class="btn guardar tab btn-success">Guardar</button>
    </div>
  </div>
</div>
</script>