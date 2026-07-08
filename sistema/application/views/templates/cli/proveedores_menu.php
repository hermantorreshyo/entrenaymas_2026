<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <% if (control.check("proveedores")>0) { %>
    <li class="<?php echo ($active=="proveedores")?"active":""?>">
      <a href="<?php echo ($active=="proveedores")?"javascript:void(0)":"app/#proveedores" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Listado","en"=>"List")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cuentas_corrientes_proveedores")>0) { %>
    <li class="<?php echo ($active=="cuentas_corrientes_proveedores")?"active":""?>">
      <a href="<?php echo ($active=="cuentas_corrientes_proveedores")?"javascript:void(0)":"app/#cuentas_corrientes_proveedores" ?>"><i class="fa fa-columns text-danger"></i> <?php echo lang(array("es"=>"Cuentas Corrientes","en"=>"Current Accounts")); ?></a>
    </li>
  <% } %>
  <% if (control.check("listado_saldos_proveedores")>0) { %>
    <li class="<?php echo ($active=="listado_saldos_proveedores")?"active":""?>">
      <a href="<?php echo ($active=="listado_saldos_proveedores")?"javascript:void(0)":"app/#listado_saldos_proveedores" ?>"><i class="fa fa-bar-chart-o text-success"></i> <?php echo lang(array("es"=>"Saldos","en"=>"Balance")); ?></a>
    </li>
  <% } %>
  <% if (control.check("deuda_proveedores")>0) { %>
    <li class="<?php echo ($active=="deuda_proveedores")?"active":""?>">
      <a href="<?php echo ($active=="deuda_proveedores")?"javascript:void(0)":"app/#deuda_proveedores" ?>"><i class="fa fa-bar-chart-o text-danger"></i> <?php echo lang(array("es"=>"Deuda","en"=>"Debt")); ?></a>
    </li>
  <% } %>
</ul>
