<style type="text/css">
.ajax-loading { display: none; }
</style>
<?php  
$metodo_envio = $web_model->get_metodo_envio();
$sucursales = $web_model->get_sucursales();
if (!isset($carrito->id_localidad)) $carrito->id_localidad = 0;
if (!isset($carrito->id_provincia)) $carrito->id_provincia = 0;
if (!isset($carrito->id_sucursal)) $carrito->id_sucursal = 0;
if ($metodo_envio !== FALSE && !empty($metodo_envio->forma_envio)) { ?>
<div class="varcreative-panel varcreative-panel-full checkout_registro panel_activo">
  <a href="javascript:void(0)" class="varcreative-panel-heading">Calcular costo de env&iacute;o</a>
  <?php 
  // Si el metodo de envio es OCA, MERCADOENVIO o ANDREANI,
  // la consulta se hace sobre el CODIGO POSTAL
  if ($metodo_envio->forma_envio == "OCA" || $metodo_envio->forma_envio == "ANDREANI" || $metodo_envio->forma_envio == "MERCADOENVIOS" || $metodo_envio->forma_envio == "REPARTO") { ?>
    <form onsubmit="return consultar_costo_envio_mercadoenvio()" class="varcreative-panel-body">
      <?php if(sizeof($sucursales)>0) { ?>
        <div class="varcreative-form-group">
          <label class="mb10 cb db">Desde la sucursal</label>
          <select class="varcreative-input cb sucursal_select">
            <?php foreach($sucursales as $r) { ?>
              <option data-longitud="<?php echo $r->longitud ?>" data-latitud="<?php echo $r->latitud ?>" value="<?php echo $r->id ?>"><?php echo $r->nombre ?></option>
            <?php } ?>
          </select>
        </div>
      <?php } ?>
      <div class="input-group">
        <div class="varcreative-form-group">
          <input type="text" required value="<?php echo isset($carrito->codigo_postal) ? $carrito->codigo_postal : "" ?>" id="costo_envio_codigo_postal" class="varcreative-input">
          <span class="varcreative-label">C&oacute;digo postal de entrega</span>
        </div>
        <div class="input-group-btn">
          <button class="varcreative-btn varcreative-btn-2 calcular_btn">Calcular</button>
        </div>
      </div>
      <div class="oh">
        <a class="varcreative-link" onclick="javascript:window.open('http://www.andreani.com/buscador-de-codigos-postales-resultado','_blank')">No conozco mi c&oacute;digo postal</a>
        <div class="fr ajax-loading">
          <img src="/templates/comun/img/ajax-loader.gif" class="mr5" alt="Loading" />
          Por favor espere...
        </div>
      </div>
    </form>
  <?php } else if ($metodo_envio->forma_envio == "CORREO_ARGENTINO") { ?>
    <form onsubmit="return consultar_costo_envio_correo_argentino()" class="varcreative-panel-body">
      <div class="">
        <label class="mb10 cb db bold">Tipo de env&iacute;o</label>
        <div class="row">
          <div class="varcreative-col6">
            <div class="radio">
              <label class="i-checks">
                <input type="radio" value="1" <?php echo (isset($carrito->tipo_servicio) && ($carrito->tipo_servicio == 1 || $carrito->tipo_servicio == 0))?"checked":"" ?> name="tipo_servicio_correo_argentino">
                <i></i> Entrega a domicilio
              </label>
            </div>
          </div>
          <div class="varcreative-col6">
            <div class="radio">
              <label class="i-checks">
                <input type="radio" value="2" <?php echo (isset($carrito->tipo_servicio) && $carrito->tipo_servicio == 2)?"checked":"" ?> name="tipo_servicio_correo_argentino">
                <i></i> Retiro en correo
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="varcreative-form-group">
        <select class="varcreative-input provincia_select" onchange="cambiar_provincia(this.value)">
          <option value="0">Provincia</option>
          <?php $q = mysqli_query($conx,"SELECT * FROM com_provincias WHERE id_pais = 1 ORDER BY nombre ASC");
          while(($r=mysqli_fetch_object($q))!==NULL) { ?>
          <option <?php echo ($carrito->id_provincia == $r->id)?"selected":"" ?> value="<?php echo $r->id; ?>"><?php echo $r->nombre ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form p0 cb form-group input-group">
        <select data-selected="<?php echo $carrito->id_localidad ?>" class="varcreative-input localidad_select" name="id_localidad" id="registro_localidad">
          <option value="0">Localidad</option>
        </select>
        <div class="input-group-btn">
          <button class="varcreative-btn varcreative-btn-2 calcular_btn">Calcular</button>
        </div>
      </div>
    </form>
  <?php } else if ($metodo_envio->forma_envio == "CUSTOM") { ?>
    <form 
      <?php if (isset($consulta_costo_envio)) { 
        // La consulta se realiza con AJAX ?>
        onsubmit="return consultar_costo_envio_personalizado(this)" 
      <?php } else { 
        // La consulta se realiza renderizando de nuevo la pagina del carrito ?>
        onsubmit="return calcular_costo_envio_personalizado_<?php echo $carrito->numero ?>(this)" 
      <?php } ?>
      class="varcreative-panel-body">
      <div class="row oh">
        <?php if(sizeof($sucursales)>0) { ?>
        <div class="col-md-4">
          <div class="form p0">
            <label class="mb10 cb db">Desde la sucursal</label>
            <?php foreach($sucursales as $r) { ?>
            <div class="form-group">
              <div class="radio">
                <label class="i-checks">
                  <input data-nombre="<?php echo $r->nombre ?>" <?php echo ($carrito->id_sucursal == $r->id)?"checked":"" ?> data-latitud="<?php echo $r->latitud ?>" data-longitud="<?php echo $r->longitud ?>" type="radio" class="sucursal_select" value="<?php echo $r->id ?>" name="sucursal">
                  <i></i>
                  <?php echo $r->nombre ?>
                </label>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-md-8">
          <div>
            <?php } else { ?>
            <div class="col-md-12">
              <div>
                <?php } ?>
                <div class="form p0 cb form-group">
                  <select class="select provincia_select" onchange="cambiar_provincia(this.value)">
                    <option value="0">Provincia</option>
                    <?php $q = mysqli_query($conx,"SELECT * FROM com_provincias WHERE id_pais = 1 ORDER BY nombre ASC");
                    while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                    <option <?php echo ($carrito->id_provincia == $r->id)?"selected":"" ?> value="<?php echo $r->id; ?>"><?php echo $r->nombre ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form p0 cb form-group input-group">
                <select data-selected="<?php echo $carrito->id_localidad ?>" class="select localidad_select" name="id_localidad" id="registro_localidad">
                  <option value="0">Localidad</option>
                </select>
                <div class="input-group-btn">
                  <button class="varcreative-btn varcreative-btn-2 calcular_btn">Calcular</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLtJGodU9yTjGDl0a0oB6h1zjR8qfXmSM&signed_in=true&libraries=places" async defer></script>
    <?php } ?>
  </div>
<?php } else { 
  // No tiene configurado metodo de envio
  // Ponemos un blanco para que haga igual el espacio y el resto quede alineado a la derecha
  ?>
  &nbsp;
<?php } ?>
<script type="text/javascript">
<?php if ($carrito->id_provincia != 0 && $carrito->id_localidad != 0) { ?>
  cambiar_provincia(<?php echo $carrito->id_provincia ?>);
<?php } ?>
</script>