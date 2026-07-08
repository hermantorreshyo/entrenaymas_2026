<tr>
  <td>
    <?php if(!empty($empresa->logo_1)) { ?>
      <img style="max-height: 130px;" src="/sistema/<?php echo $empresa->logo_1 ?>"/>
    <?php } ?>      
  </td>
  <td>
    <div class="encabezado_info">
      <?php if (!empty($empresa->direccion)) { ?>
        <p><b>DIRECCIÓN:</b> <?php echo $empresa->direccion ?></p>
      <?php } ?>
      <?php if (!empty($empresa->ciudad)) { ?>
        <p>
          <?php echo $empresa->ciudad ?>
          <?php echo (!empty($empresa->codigo_postal)) ? " | CP: ".$empresa->codigo_postal : "" ?>
        </p>
      <?php } ?>
      <?php if (!empty($empresa->telefono)) { ?>
        <p><b>TELÉFONO:</b> <?php echo $empresa->telefono ?></p>
      <?php } ?>
      <?php if (!empty($empresa->email)) { ?>
        <p><b>EMAIL:</b> <?php echo $empresa->email ?></p>
      <?php } ?>
    </div>      
  </td>
</tr>