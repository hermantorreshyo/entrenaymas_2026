<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <% if (control.check("compras_listado")>0) { %>
    <li class="<?php echo ($active=="compras_listado")?"active":""?>">
      <a href="<?php echo ($active=="compras_listado")?"javascript:void(0)":"app/#compras_listado" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Compras","en"=>"Purchases")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cuentas_corrientes_proveedores")>0) { %>
    <li class="<?php echo ($active=="ordenes_pago")?"active":""?>">
      <a href="<?php echo ($active=="ordenes_pago")?"javascript:void(0)":"app/#ordenes_pago" ?>"><i class="fa fa-file-o text-primary"></i> <?php echo lang(array("es"=>"Órdenes de Pago","en"=>"Payment Orders")); ?></a>
    </li>
  <% } %>
  <% if (control.check("gastos")>0) { %>
    <li class="<?php echo ($active=="gastos")?"active":""?>">
      <a href="<?php echo ($active=="gastos")?"javascript:void(0)":"app/#gastos" ?>"><i class="fa fa-money text-danger"></i> <?php echo lang(array("es"=>"Gastos","en"=>"Expenses")); ?></a>
    </li>
  <% } %>
  <% if (control.check("conceptos")>0) { %>
    <li class="<?php echo ($active=="conceptos")?"active":""?>">
      <a href="<?php echo ($active=="conceptos")?"javascript:void(0)":"app/#conceptos" ?>"><i class="fa fa-database text-warning"></i> <?php echo lang(array("es"=>"Conceptos","en"=>"Items")); ?></a>
    </li>
  <% } %>
  <% if (control.check("compras_resumen")>0) { %>
    <li class="<?php echo ($active=="compras_resumen")?"active":""?>">
      <a href="<?php echo ($active=="compras_resumen")?"javascript:void(0)":"app/#compras_resumen" ?>"><i class="fa fa-bar-chart-o text-success"></i> <?php echo lang(array("es"=>"Resumen","en"=>"Balance")); ?></a>
    </li>
  <% } %>
</ul>
