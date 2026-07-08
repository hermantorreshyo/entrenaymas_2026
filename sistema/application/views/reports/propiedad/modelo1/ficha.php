<!DOCTYPE>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/fonts/lato/lato.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/font-awesome.min.css" />
<style type="text/css">
body { font-family: "LatoRegular"; font-size: 14px; color: #818181; }
.pt10 { padding-top: 10px; }
.bold { font-weight: bold; font-family: "LatoBold"; }
.gris { background-color: #f5f5f6; }
.blanco { background-color: white; }
table { width: 100%; }
table tr { vertical-align: top; margin-bottom: 10px; }
table tr td:last-child { padding-left: 10px; }
.info_propiedad { padding: 0px; margin: 0px; list-style: none; font-size: 13px; }
.info_propiedad li { padding: 10px 0px 10px 25px; line-height: 23px; }
.info_propiedad li:nth-child(even) { background-color: #ffffff; }
.info_propiedad li span.fr { width: 50px; }
.precio { display: block; font-size: 14px; text-align: center; color: white; background-color: <?php echo $empresa->color_principal ?>; padding-top: 24px; padding-bottom: 24px; }
.precio_numero { display: block; font-size:35px; margin-top: 5px; }
.texto { color: #797878; font-size: 14px; padding: 20px 30px; line-height: 22px; }
.empresa_nombre { margin-bottom: 10px; margin-top: 20px; text-transform: uppercase; color: #424242; font-weight: bold; font-family: "LatoBold"; font-size: 18px; }
.empresa_direccion { margin-bottom: 10px; font-size: 16px; }
.empresa_telefono { margin-bottom: 10px; color: #424242; font-weight: bold; font-family: "LatoBold"; font-size: 16px; line-height: 24px; }
.empresa_email a { color: <?php echo $empresa->color_principal ?>; text-decoration: none; }
.footer { margin-top: 30px; border-top: solid 1px #e8e8e8; padding: 25px; overflow: hidden; }
.footer a { text-transform: uppercase; color: #4f4f4f; font-size: 15px; margin-top: 10px; }
.caracteristicas { list-style: none; }
.caracteristicas li { text-transform: uppercase; padding: 10px 10px; }
.caracteristicas li img { margin-right: 5px; }
.caracteristicas li:nth-child(even) { background-color: #f5f5f6; }
i.bullet { background-color: <?php echo $empresa->color_principal ?>; display: inline-block; width: 11px; height: 11px; overflow: hidden; border-radius: 300px; -moz-border-radius: 300px; -webkit-border-radius: 300px; margin-right: 5px; }
</style>
<title><?php echo utf8_encode($propiedad->nombre) ?></title>  
</head>
<body>
<?php echo $header; ?>
<div id="printable">
  <div class="a4">
    <div class="inner">
      <?php ?>
      <div class="cb tar mb20">
        <?php if (!empty($empresa->logo)) { ?>
          <img style="max-width: 300px; max-height: 150px;" src="/sistema/<?php echo $empresa->logo ?>"/>
        <?php } else if (!empty($empresa->logo_1)) { ?>
          <img style="max-width: 300px; max-height: 150px;" src="/sistema/<?php echo $empresa->logo_1 ?>"/>
        <?php } else { ?>
          <h1><?php echo strtoupper($empresa->nombre) ?></h1>
        <?php } ?>
      </div>
      <?php if (!empty($propiedad->codigo)) { ?>
        <div class="cb tal" style="padding: 15px 25px; color: #222">
          <span class="bold">C&Oacute;DIGO: </span>
          <?php echo $propiedad->codigo; ?>
        </div>
      <?php } ?>
      <table>
        <tr>
          <td class="gris" style="width: 40%; color: #222">
            <ul class="info_propiedad">
              <li style="font-size: 16px">
                <?php echo $propiedad->calle.(($propiedad->publica_altura == 1)?" ".$propiedad->altura:""); ?><br/>
                <span class="bold"><?php echo $propiedad->localidad; ?></span>
              </li>
              <li>
                METROS 2
                <span class="fr"><?php echo $propiedad->superficie_total ?></span>
              </li>
              <li>
                HABITACIONES
                <span class="fr"><?php echo $propiedad->dormitorios ?></span>
              </li>
              <li>
                BA&Ntilde;OS
                <span class="fr"><?php echo $propiedad->banios ?></span>
              </li>
              <li>
                COCHERAS
                <span class="fr"><?php echo $propiedad->cocheras ?></span>
              </li>
            </ul>            
          </td>
          <td style="width: 60%">
            <?php if (strpos($propiedad->path, "http") === FALSE) { ?>
              <img style="width: 100%" src="/sistema/<?php echo $propiedad->path ?>"/>
            <?php } else if (!empty($propiedad->path)) { ?>
              <img style="width: 100%" src="<?php echo $propiedad->path ?>"/>
            <?php } ?>
          </td>
        </tr>
        <tr>
          <td class="pt10" style="width: 40%">
            <?php if (sizeof($propiedad->images)>0) { ?>
              <?php $imagen = $propiedad->images[0]; ?>
              <?php if (strpos($imagen, "http") === FALSE) { ?>
                <img style="width: 100%" src="/sistema/<?php echo $imagen ?>"/>
              <?php } else if (!empty($imagen)) { ?>
                <img style="width: 100%" src="<?php echo $imagen ?>"/>
              <?php } ?>
            <?php } ?>
            <div class="">
              <div class="empresa_nombre">
                <?php echo $empresa->razon_social; ?>
              </div>
              <div class="empresa_direccion">
                <?php echo $empresa->direccion; ?>
                <?php echo (!empty($empresa->ciudad)) ? " - ".$empresa->ciudad : ""; ?>
              </div>
              <div class="empresa_telefono">
                <?php echo $empresa->telefono; ?><br/>
                <?php 
                // LANGONE TIENE ICONO DE WHATSAPP
                if ($empresa->id == 161) { ?>
                  <i style="font-size: 18px; color: #1bc035" class="fa fa-whatsapp"></i>
                <?php } ?>
                <?php echo $empresa->telefono_2; ?>
              </div>
              <div class="empresa_email">
                <a href="mailto:<?php echo $empresa->email ?>"><?php echo $empresa->email ?></a>
              </div>
            </div>
          </td>
          <td class="pt10" style="width: 60%">
            <div class="precio">
              PRECIO<br/>
              <span class="precio_numero">
                <?php if ($propiedad->precio_final == 0 || $propiedad->publica_precio == 0) { ?>
                  A CONSULTAR
                <?php } else { ?>
                  <?php echo $propiedad->moneda." ".number_format($propiedad->precio_final,0); ?>
                <?php } ?>
              </span>
            </div>
            <?php if (!empty($propiedad->breve) || !empty($propiedad->texto)) { ?>
              <div class="pt10">
                <div class="gris">
                  <div class="texto">
                    <?php
                    $maximo_texto = 300;
                    if (!empty($propiedad->breve)) {
                      echo $propiedad->breve;
                    } else if (strlen($propiedad->texto)>$maximo_texto) {
                      echo substr(strip_tags($propiedad->texto),0,$maximo_texto)."...";
                    } else {
                      echo $propiedad->texto;
                    }?>
                  </div>
                </div>
              </div>
            <?php } ?>
            <?php $caracteristicas = explode(";;;",$propiedad->caracteristicas);
            if (!empty($caracteristicas) && !empty($caracteristicas[0])) { ?>
              <div class="pt10">
                <div class="empresa_nombre">
                  CARACTER&Iacute;STICAS
                </div>
                <ul class="caracteristicas">
                  <?php foreach($caracteristicas as $c) { ?>
                    <li><i class="bullet"></i> <?php echo utf8_encode($c) ?></li>
                  <?php } ?>
                </ul>
              </div>
            <?php } ?>
          </td>          
        </tr>
      </table>
      <div class="clear footer">
        <?php if (!empty($empresa->logo_1)) { ?>
          <img class="fl" style="max-width: 200px" src="/sistema/<?php echo $empresa->logo_1 ?>"/>
        <?php } ?>
        <?php if (sizeof($empresa->dominios)>0) {
          $dominio = $empresa->dominios[0]; ?>
          <a class="fr" href="<?php echo $dominio ?>" target="_blank"><?php echo $dominio ?></a>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>