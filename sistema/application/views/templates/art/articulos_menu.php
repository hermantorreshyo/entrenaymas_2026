<ul class="nav nav-tabs nav-tabs-2 <%= (ID_EMPRESA == 1284)?"dn":"" %>" role="tablist">
  <% if (control.check("articulos")>0) { %>
    <li class="<?php echo ($active=="articulos")?"active":""?>">
      <a href="<?php echo ($active=="articulos")?"javascript:void(0)":"app/#articulos" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Listado","en"=>"List")); ?></a>
    </li>
  <% } %>
  <% if (control.check("stock")>0) { %>
    <li class="<?php echo ($active=="stock")?"active":""?>">
      <a href="<?php echo ($active=="stock")?"javascript:void(0)":"app/#stock" ?>"><i class="fa fa-database text-danger"></i> Stock</a>
    </li>
  <% } %>
  <% if (control.check("stock_por_sucursal") > 0) { %>
    <li class="<?php echo ($active=="stock_por_sucursal")?"active":""?>">
      <a href="<?php echo ($active=="stock_por_sucursal")?"javascript:void(0)":"app/#stock_por_sucursal" ?>"><i class="fa fa-database text-danger"></i> Stock por Sucursal</a>
    </li>
  <% } %>
  <% if (control.check("actualizacion_precios")>0) { %>
    <li class="<?php echo ($active=="actualizacion_precios")?"active":""?>">
      <a href="<?php echo ($active=="actualizacion_precios")?"javascript:void(0)":"app/#actualizacion_precios" ?>"><i class="fa fa-dollar text-success"></i> <?php echo lang(array("es"=>"Actualizaci&oacute;n Precios","en"=>"Price Update")); ?></a>
    </li>
  <% } %>
</ul>
