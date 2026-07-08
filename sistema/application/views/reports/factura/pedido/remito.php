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
<title>Nota de Pedido</title>
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
								<img style="max-width: 280px;" src="/sistema/<?php echo $empresa->logo ?>"/>
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
								<?php echo $factura->cliente->nombre; ?>
							</div>
							<div class="to-org">
								<?php
	              if (isset($factura->direccion) && !empty($factura->direccion)) {
	                echo "Direcci&oacute;n: ".$factura->direccion."<br/>";
	              } else if (isset($factura->cliente->direccion) && !empty($factura->cliente->direccion)) {
	                echo "Direcci&oacute;n: ".$factura->cliente->direccion."<br/>";
	              }
	              if (isset($factura->localidad) && !empty($factura->localidad)) {
	                echo "Localidad: ".$factura->localidad."<br/>";
	              } else if (isset($factura->cliente->localidad) && !empty($factura->cliente->localidad)) {
	                echo "Localidad: ".$factura->cliente->localidad."<br/>";
	              }
	              if (isset($factura->cliente->cuit) && !empty($factura->cliente->cuit)) {
	                echo "DNI/CUIT: ".$factura->cliente->cuit."<br/>";
	              }
	              if (isset($factura->cliente->email) && !empty($factura->cliente->email)) {
	                echo "Email: ".$factura->cliente->email."<br/>";
	              }
	              if (isset($factura->cliente->telefono) && !empty($factura->cliente->telefono)) {
	                echo "Tel&eacute;fono: ".$factura->cliente->telefono."<br/>";
	              }
								?>
							</div>
							<!--
							<div class="to-org">
								<?php echo $factura->cliente->tipo_iva; ?>
								- CUIT: <?php echo $factura->cliente->cuit; ?>
							</div>
							-->
						</div><!-- INVOICE TO -->
				
						<div class="invoice-meta">
							<?php if (!empty($factura->numero)) { ?>
								<div class="meta-uno">Numero:</div>
								<div class="meta-duo"><?php echo $factura->numero; ?></div>
							<?php } ?>
							<div class="meta-uno">Fecha:</div>
							<div class="meta-duo"><?php echo $factura->fecha; ?></div>
							<div class="meta-uno">Estado:</div>
							<div class="meta-duo"><?php echo $factura->estado; ?></div>
						</div>
				
					</section><!-- TO SECTION -->
				
					<section class="invoice-financials"><!-- FINANCIALS SECTION -->
				
						<div class="invoice-items"><!-- INVOICE ITEMS -->
							<table>
								<thead>
									<tr>
										<th class="col-1">Descripcion</th>
	                  <th class="col-2">C&oacute;digo</th>
										<th class="col-2">Cantidad</th>
										<th class="col-3">Precio Unit</th>
										<th class="col-4">Total</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($factura->items as $i) { ?>
										<tr>
											<td>
												<h1>
													<?php echo (!empty($i->nombre))? utf8_decode($i->nombre):""; ?>
													<?php echo ((isset($i->variante) && !empty($i->variante))?"<br/><span class='fwn'>".($i->variante)."</span>":""); ?>
													<?php echo (!empty($i->descripcion)?"<br/><span class='fwn'>". utf8_decode($i->descripcion)."</span>":""); ?>
												</h1>
											</td>
	                    <td><?php echo (!empty($i->codigo))? ($i->codigo):""; ?></td>
											<td><?php echo (!empty($i->cantidad))?number_format($i->cantidad,2):"0"; ?></td>
											<td><?php echo (!empty($i->precio))?"$ ".number_format($i->precio,2):"$ 0.00"; ?></td>
											<td><?php echo "$ ".number_format($i->cantidad * $i->precio,2); ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div><!-- INVOICE ITEMS -->
						
						<div class="lower-block"><!-- TERMS&PAYMENT INFO -->
							
							<?php if (!empty($factura->numero_envio)) { ?>
								<div style="float: left; text-align: left; margin-left: 15px; ">
									<div class="">Cod. env&iacute;o Andreani:</div>
									<div class="bold"><?php echo $factura->numero_envio ?></div>
								</div>
							<?php } ?>
						
							<div class="invoice-totals"><!-- TOTALS -->
								<table>
									<tbody>
	                  <tr>
	                    <td>Subtotal</td>
	                    <td><?php echo "$ ".number_format($factura->subtotal,2); ?></td>
	                  </tr>
										<?php if ($factura->porc_descuento != 0) { ?>
											<tr>
												<td>Descuento <?php echo number_format($factura->porc_descuento,0); ?>%</td>
												<td><?php echo "$ ".number_format($factura->descuento,2); ?></td>
											</tr>
										<?php } ?>
	                  <?php if ($factura->id_tipo_comprobante != 999 && $factura->iva != 0) { ?>
	                    <tr>
	                      <td>IVA</td>
	                      <td><?php echo "$ ".number_format($factura->iva,2); ?></td>
	                    </tr>
	                  <?php } ?>
										<tr>
											<th class="col-1">Total:</th>						
											<td class="col-2"><?php echo "$ ".number_format($factura->total,2); ?></td>
										</tr>
										<?php if ($factura->costo_envio > 0) { ?>
											<tr>
												<td style="padding-top: 15px !important">Costo de Envio</td>
												<td style="padding-top: 15px !important"><?php echo "$ ".number_format($factura->costo_envio,2); ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
						
						<?php if (!empty($factura->observaciones)) { ?>
							<div class="observaciones">
								<strong>Observaciones: </strong><br/>
								<?php echo $factura->observaciones ?>
								<?php if ($empresa->id == 120 && $factura->id_tipo_estado == 8) { ?>
									<br/>El pago en sucursal deber&aacute; realizarse en efectivo &uacute;nicamente.
								<?php } ?>
							</div>
						<?php } ?>
						
					</section><!-- FINANCIALS SECTION -->
				</div><!-- INVOICE -->
			</div>
		</div>
	<?php } ?>
</body>
</html>