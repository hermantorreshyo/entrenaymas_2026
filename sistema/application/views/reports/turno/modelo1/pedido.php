<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-js">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<title>Turno Web</title>
<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/reset.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/style.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/print.css" media="print" />
<style type="text/css">
<?php $c1 = $empresa->config["color_principal"]; ?>
<?php $c2 = (empty($empresa->config["color_secundario"])) ? "rgb(143, 144, 146)" : $empresa->config["color_secundario"]; ?>
#header { padding-bottom: 0px; }
#invoice { padding: 0px !important; margin: 0px !important; border: none !important; -webkit-box-shadow: none !important; box-shadow: none !important; }
.invoice-to { width: 305px; }
td h1, .invoice-items thead th.col-1 { text-align: left; padding-left: 15px; }
.invoice-items { margin-top: 20px; }
.invoice-totals tr td { padding-top: 0px !important; }
.invoice-totals tbody .col-1, .invoice-totals tbody .col-2 { padding: 8px 12px !important; }
.invoice-meta { float: right !important; }
.this-is { margin-left: 0px !important; color: <?php echo $c1 ?>; }
.this-is-line { border-top-color: <?php echo $c1 ?>; }
.invoice-items thead th.col-4, .invoice-totals tbody td.col-2 { background: <?php echo $c1 ?>; }
.observaciones { padding-left: 15px; text-align: left; }
.observaciones strong { margin-bottom: 10px; }
.invoice-items thead th{ background: <?php echo $c2 ?>; }
.invoice-totals tbody th.col-1 { background: <?php echo $c2 ?>; }
.invoice-to-title { padding-left: 0px; }
b { font-weight: bold !important; display: inline-block; width: 100px; }
</style>

<!-- give life to HTML5 objects in IE -->
<!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<!-- js HTML class -->
<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
	<div class="a4">
		<div class="inner">
			<div id="invoice" class="new"><!-- INVOICE -->
				<header id="header"><!-- HEADER -->
					<div style="float: left; font-size: 36px; width: auto" class="this-is">
						<?php if (!empty($empresa->logo)) { ?>
							<img style="max-height: 100px; max-width: 600px;" src="/sistema/<?php echo $empresa->logo ?>"/>
						<?php } else { ?>
							<?php echo $empresa->razon_social ?>
						<?php } ?>
					</div>
					<div class="invoice-from" style="width: auto; margin-top: 10px; text-align: right; float: right;"><!-- HEADER FROM -->
						<div class="org">
							<?php echo $empresa->direccion.((!empty($empresa->localidad))?" - ".$empresa->localidad:""); ?>
						</div>
						<div class="org">
						<?php
							echo (!empty($empresa->telefono))?"TEL: ".$empresa->telefono."<br/>":"";
							echo (!empty($empresa->email))?$empresa->email:"";
						?>
						</div>
					</div><!-- HEADER FROM -->
			
				</header><!-- HEADER -->
				<!-- e: invoice header -->
			  
				<div class="this-is-line" style="padding: 0px; height: 25px;"></div>
			
				<section id="info-to">
					<div class="invoice-to">
						<div class="invoice-to-title">
							<?php echo ucwords(strtolower($pedido->cliente)); ?>
						</div>
            <?php if (!empty($cliente->telefono)) { ?>
              <div style="text-align: left;">
                <b>Tel&eacute;fono:</b> <?php echo $cliente->telefono ?>
              </div>
            <?php } ?>
            <?php if (!empty($cliente->email)) { ?>
              <div style="text-align: left;">
                <b>Email:</b> <?php echo $cliente->email ?>
              </div>
            <?php } ?>
					</div>
				</section><!-- TO SECTION -->
        <section class="invoice-financials"><!-- FINANCIALS SECTION -->
          <div class="invoice-items"><!-- INVOICE ITEMS -->
            <table>
              <thead>
                <tr>
                  <th class="col-1">Servicio</th>
                  <th class="col-2">Fecha</th>
                  <th class="col-2">Hora</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <h1>
                      <?php echo ucwords(strtolower($pedido->servicio)) ?>
                    </h1>
                  </td>
                  <td><?php echo fecha_es($pedido->fecha) ?></td>
                  <td><?php echo substr($pedido->hora,0,5) ?></td>
                </tr>
              </tbody>
            </table>
          </div><!-- INVOICE ITEMS -->
        </section>
			</div>
		</div>
	</div>
</body>
</html>