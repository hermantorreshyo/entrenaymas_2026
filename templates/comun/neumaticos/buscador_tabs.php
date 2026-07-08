<?php 
// Tomamos la configuracion de marcas y modelos de VULCA
$id_empresa_referencia = 120; ?>
<section class="buscador_neumaticos_tabs">
  <div class="container">
    <div class="tabs">
      <ul class="tab-button">
        <li><a class="b-t-main-h" href="#for-rent">por medida</a></li>
        <?php if ($empresa->id != 433) { ?>
          <li><a class="b-t-main-h" href="#for-sale">por vehiculo</a></li>
        <?php } ?>
      </ul>
      <form method="get" action="<?php echo mklink("productos/"); ?>" class="tab-content" id="for-rent">
        <div class="col-sm-9">
          <div class="row">
            <div class="col-sm-4 buscador_neumaticos_select_cont">
              <select name="custom_7">
                <option value="0">Seleccione Ancho</option>
                <?php $sql = "SELECT DISTINCT(custom_7) FROM articulos ";
                $sql.= "WHERE id_empresa = $empresa->id ";
                $sql.= "ORDER BY custom_7 ASC ";
                $q = mysqli_query($conx,$sql);
                while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                  <?php if (empty($r->custom_7)) continue; ?>
                  <option <?php echo (isset($custom_7) && $custom_7 == $r->custom_7)?"selected":"" ?> value="<?php echo $r->custom_7 ?>"><?php echo str_replace(".00", "", $r->custom_7) ?></option>
                <?php } ?>
              </select>
              <div class="block">
                <span>ejemplo: 205</span>
                <div class="info-msg">
                  <a href="javascript:void()"><i class="fa fa-info"></i></a>
                  <div class="msg-toltip"><img src="/templates/comun/neumaticos/images/ancho.jpg" alt="info" /></div>
                </div>
              </div>
            </div>
            <div class="col-sm-4 buscador_neumaticos_select_cont">
              <select name="custom_8">
                <option value="0">Seleccione Perfil</option>
                <?php $sql = "SELECT DISTINCT(custom_8) FROM articulos ";
                $sql.= "WHERE id_empresa = $empresa->id ";
                $sql.= "ORDER BY custom_8 ASC ";
                $q = mysqli_query($conx,$sql);
                while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                  <?php if (empty($r->custom_8)) continue; ?>
                  <option <?php echo (isset($custom_8) && $custom_8 == $r->custom_8)?"selected":"" ?> value="<?php echo $r->custom_8 ?>"><?php echo str_replace(".00", "", $r->custom_8) ?></option>
                <?php } ?>
              </select>
              <div class="block">
                <span>ejemplo: 55</span>
                <div class="info-msg">
                  <a href="javascript:void()"><i class="fa fa-info"></i></a>
                  <div class="msg-toltip"><img src="/templates/comun/neumaticos/images/perfil.jpg" alt="info" /></div>
                </div>
              </div>
            </div>
            <div class="col-sm-4 buscador_neumaticos_select_cont">
              <select name="custom_9">
                <option value="0">Seleccione Llanta</option>
                <?php $sql = "SELECT DISTINCT(custom_9) FROM articulos ";
                $sql.= "WHERE id_empresa = $empresa->id ";
                $sql.= "ORDER BY custom_9 ASC ";
                $q = mysqli_query($conx,$sql);
                while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                  <?php if (empty($r->custom_9)) continue; ?>
                  <option <?php echo (isset($custom_9) && $custom_9 == $r->custom_9)?"selected":"" ?> value="<?php echo $r->custom_9 ?>"><?php echo str_replace(".00", "", $r->custom_9) ?></option>
                <?php } ?>
              </select>
              <div class="block">
                <span>ejemplo: 16</span>
                <div class="info-msg">
                  <a href="javascript:void()"><i class="fa fa-info"></i></a>
                  <div class="msg-toltip"><img src="/templates/comun/neumaticos/images/aro.jpg" alt="info" /></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <input type="submit" value="buscar" class="btn bg-main btn-block" />
        </div>
      </form>
      <?php if ($empresa->id != 433) { ?>
        <form onsubmit="return buscar_por_marca()" method="get" action="<?php echo mklink("productos/"); ?>" class="tab-content" id="for-sale">
          <input type="hidden" name="custom_7" id="buscar_por_marca_ancho" value="0">
          <input type="hidden" name="custom_8" id="buscar_por_marca_perfil" value="0">
          <input type="hidden" name="custom_9" id="buscar_por_marca_rodado" value="0">
          <div class="col-sm-9">
            <div class="row">
              <div class="col-sm-3 buscador_neumaticos_select_cont">
                <select id="buscador_marca" onchange="actualizar_select(this)">
                  <option value="0">Marca</option>
                  <?php 
                  $sql = "SELECT DISTINCT marca FROM veh_autos ";
                  $sql.= "WHERE id_empresa = $id_empresa_referencia ";
                  $sql.= "ORDER BY marca ASC ";
                  $q = mysqli_query($conx,$sql);
                  while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                    <option value="<?php echo $r->marca ?>"><?php echo $r->marca ?></option>
                  <?php } ?>
                </select>
                <div class="block">
                  <span>ejemplo: Ford</span>
                </div>
              </div>
              <div class="col-sm-3 buscador_neumaticos_select_cont">
                <select id="buscador_modelo" onchange="actualizar_select(this)">
                  <option value="0">Modelo</option>
                </select>
                <div class="block">
                  <span>ejemplo: Fiesta</span>
                </div>
              </div>
              <div class="col-sm-3 buscador_neumaticos_select_cont">
                <select id="buscador_anio" onchange="actualizar_select(this)">
                  <option value="0">A&ntilde;o</option>
                </select>
                <div class="block">
                  <span>ejemplo: 2014</span>
                </div>
              </div>
              <div class="col-sm-3 buscador_neumaticos_select_cont">
                <select id="buscador_version" onchange="actualizar_select(this)">
                  <option value="0">Versi&oacute;n</option>
                </select>
                <div class="block">
                  <span>ejemplo: 1.6 Titanium</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <input type="submit" value="buscar" class="btn bg-main btn-block" />
          </div> 
        </form>
      <?php } ?>
    </div>
  </div>
</section>
<?php include("buscador_js.php"); ?>