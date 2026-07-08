<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <% if (control.check("cajas")>0) { %>
    <li class="<?php echo ($active=="cajas")?"active":""?>">
      <a href="<?php echo ($active=="cajas")?"javascript:void(0)":"app/#cajas" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Cajas","en"=>"Cajas")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cajas_diarias")>0) { %>
    <li class="<?php echo ($active=="cajas_diarias")?"active":""?>">
      <a href="<?php echo ($active=="cajas_diarias")?"javascript:void(0)":"app/#cajas_diarias" ?>"><i class="fa fa-list text-info"></i> <?php echo lang(array("es"=>"Arqueos de Cajas","en"=>"List")); ?></a>
    </li>
  <% } %>
  <% if (control.check("cheques")>0) { %>
    <li class="<?php echo ($active=="cheques")?"active":""?>">
      <a href="<?php echo ($active=="cheques")?"javascript:void(0)":"app/#cheques" ?>"><i class="fa fa-money text-success"></i> <?php echo lang(array("es"=>"Cartera de Cheques","en"=>"Balance")); ?></a>
    </li>
  <% } %>
</ul>
