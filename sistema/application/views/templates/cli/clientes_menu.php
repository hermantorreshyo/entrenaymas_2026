<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <% if (control.check("clientes")>0) { %>
    <li class="<?php echo ($active=="clientes")?"active":""?>">
      <a href="<?php echo ($active=="clientes")?"javascript:void(0)":"app/#clientes" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Listado","en"=>"List")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cuentas_corrientes_clientes")>0) { %>
    <li class="<?php echo ($active=="cuentas_corrientes_clientes")?"active":""?>">
      <a href="<?php echo ($active=="cuentas_corrientes_clientes")?"javascript:void(0)":"app/#cuentas_corrientes_clientes" ?>"><i class="fa fa-columns text-danger"></i> <?php echo lang(array("es"=>"Cuentas Corrientes","en"=>"Current Accounts")); ?></a>
    </li>
  <% } %>
  <% if (control.check("listado_saldos_clientes")>0) { %>
    <li class="<?php echo ($active=="listado_saldos_clientes")?"active":""?>">
      <a href="<?php echo ($active=="listado_saldos_clientes")?"javascript:void(0)":"app/#listado_saldos_clientes" ?>"><i class="fa fa-bar-chart-o text-success"></i> <?php echo lang(array("es"=>"Saldos","en"=>"Balance")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cuentas_corrientes_clientes")>0) { %>
    <li class="<?php echo ($active=="recibos_clientes")?"active":""?>">
      <a href="<?php echo ($active=="recibos_clientes")?"javascript:void(0)":"app/#recibos_clientes" ?>"><i class="fa fa-file-text-o text-info"></i> <?php echo lang(array("es"=>"Recibos","en"=>"Recibos")); ?></a>
    </li>
  <% } %>
</ul>
