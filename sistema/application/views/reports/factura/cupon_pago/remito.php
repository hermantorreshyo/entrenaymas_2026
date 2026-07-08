<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-js">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<title>Cupon de Pago</title>
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
</style>

<!-- give life to HTML5 objects in IE -->
<!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<!-- js HTML class -->
<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
	<?php foreach($facturas as $factura) { ?>
		<div class="a4">
			<div class="inner">
				<div id="invoice" class="new"><!-- INVOICE -->
					<header id="header"><!-- HEADER -->
						<div style="float: left; font-size: 36px; width: auto" class="this-is">
							<?php if (!empty($empresa->logo)) { ?>
								<img style="max-width: 280px; max-height: 120px" src="/sistema/<?php echo $empresa->logo ?>"/>
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
				
					<section id="info-to"><!-- TO SECTION -->
						<!--
						<div class="this-is" style="font-size:32px; float: none !important; margin-left: 15px !important; margin-bottom: 20px !important; clear: both !important; ">PEDIDO</div>
				-->
						<div class="invoice-to">
							<div class="invoice-to-title">
								<?php echo $factura->cliente; ?>
							</div>
							<div class="to-org">
								<?php
	              if (isset($factura->cuit) && !empty($factura->cuit)) {
	                echo "DNI/CUIT: ".$factura->cuit."<br/>";
	              }
	              if (isset($factura->email) && !empty($factura->email)) {
	                echo "Email: ".$factura->email."<br/>";
	              }
	              if (isset($factura->cliente_telefono) && !empty($factura->cliente_telefono)) {
	                echo "Tel&eacute;fono: ".$factura->cliente_telefono."<br/>";
	              }
								?>
							</div>
						</div>
				
						<div class="invoice-meta">
							<?php if (!empty($factura->numero)) { ?>
								<div class="meta-uno">Numero:</div>
								<div class="meta-duo"><?php echo $factura->numero; ?></div>
							<?php } ?>
							<div class="meta-uno">Fecha:</div>
							<div class="meta-duo"><?php echo $factura->fecha; ?></div>
						</div>
				
					</section><!-- TO SECTION -->
				
					<section class="invoice-financials"><!-- FINANCIALS SECTION -->
				
						<div class="invoice-items"><!-- INVOICE ITEMS -->
							<table>
								<thead>
									<tr>
										<th class="col-1">Descripcion</th>
										<th class="col-4">Periodo</th>
										<th class="col-4">Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="tal pl15">Alquiler por: <?php echo $factura->propiedad ?></td>
										<td><?php echo $factura->corresponde_a ?></td>
										<td><?php echo "$ ".number_format($factura->monto,0) ?></td>
									</tr>
									<?php 
									$total = $factura->monto;
									foreach($factura->items as $item) { ?>
										<tr>
											<td class="tal pl15"><?php echo $item->nombre ?></td>
											<td><?php echo $factura->corresponde_a ?></td>
											<td><?php echo "$ ".number_format($item->monto,0) ?></td>
										</tr>
									<?php $total += $item->monto; } ?>
								</tbody>
							</table>
						</div>
						
						<div class="lower-block">
							<div class="invoice-totals">
								<table>
									<tbody>
										<tr>
											<th class="col-1">Total:</th>						
											<td class="col-2"><?php echo "$ ".number_format($total,2); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						
						<?php if (!empty($factura->observaciones)) { ?>
							<div class="observaciones">
								<?php echo $factura->observaciones ?>
							</div>
						<?php } ?>

            <?php if (isset($mp) && $mp !== FALSE && $preference_data !== FALSE) { 
              $preference = $mp->create_preference($preference_data); ?>
								<div class="oh">
                	<a 
                    style="background-color: #009ee3;
                    float: left;
    border: none;
    text-decoration: none;
    color: white;
    margin: 10px 0px;
    padding: 8px 20px;
    font-size: 16px;
    line-height: 34px;
    border-radius: 4px;
    text-shadow: 1px 1px 1px #969696;
    cursor: pointer;"
                    href="<?php echo $preference["response"]["init_point"]; ?>" 
                    mp-mode="modal" 
                    name="MP-Checkout" 
                    class="pago-mercadopago">Pagar con MercadoPago</a>
                </div>
              <?php } ?>
              
						
					</section>
				</div>
			</div>
		</div>
	<?php } ?>
</body>
</html>