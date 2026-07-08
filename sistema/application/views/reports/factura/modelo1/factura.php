<?php
$codigo_comprobante = str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-js">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />

<title><?php echo $comprobante." ".$factura->comprobante ?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/reset.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/style.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $folder ?>/print.css" media="print" />

<!-- give life to HTML5 objects in IE -->
<!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<!-- js HTML class -->
<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<?php echo $header; ?>
<div id="printable">
<?php
$copias = $empresa->config["facturacion_cantidad_copias"];
for($copia=0;$copia < $copias; $copia++) { ?>
	<div class="a4">
		<div class="inner">
			<div id="invoice" class="new"><!-- INVOICE -->
				<header id="header"><!-- HEADER -->
					<div style="float: left; font-size: 26px; width: auto" class="this-is">
						<?php echo $empresa->razon_social ?>
					</div>
					<div style="float: left;width: auto; text-align: center; margin-left: 40px;">
						<div class="this-is" style="float: none; width: auto;">
							<?php echo $tipo_comprobante->letra ?>
						</div>
						<div style="">
						Cod: 0<?php echo $tipo_comprobante->id; ?>
						</div>
					</div>
					<div class="invoice-from" style="width: auto;"><!-- HEADER FROM -->
						<div class="org"><?php echo $empresa->tipo_contribuyente." - CUIT: ".$empresa->cuit; ?></div>
						<div class="org"><?php echo $empresa->direccion." - ".$empresa->localidad; ?></div>
						<div class="org"><?php
						echo (!empty($empresa->telefono))?"TEL: ".$empresa->telefono." - ":"";
						echo (!empty($empresa->email))?"Email: ".$empresa->email:""; ?>
						</div>
					</div><!-- HEADER FROM -->
			
				</header><!-- HEADER -->
				<!-- e: invoice header -->
			  
				<div class="this-is-line" style="padding: 0px; height: 15px;"></div>
			
				<section id="info-to"><!-- TO SECTION -->
			
					<div>
						<div class="invoice-to-title" style="float: left">
							<?php echo $factura->cliente->nombre; ?>
						</div>
						<div class="this-is" style="float: right">FACTURA</div>
					</div>
					<div class="invoice-to">
						<div class="to-org">
							<?php
							echo $factura->cliente->direccion;
							echo " - ".$factura->cliente->localidad;
							echo (!empty($factura->cliente->provincia)) ? " (".$factura->cliente->provincia.")":"";
							?>
						</div>
						<div class="to-org">
							<?php echo $factura->cliente->tipo_iva; ?>
							- CUIT: <?php echo $factura->cliente->cuit; ?>
						</div>
					</div><!-- INVOICE TO -->
			
					<div class="invoice-meta">
						<div class="meta-uno invoice-number">Numero:</div>
						<div class="meta-duo"><?php echo end(explode(" ",$factura->comprobante)); ?></div>
						<div class="meta-uno invoice-date">Fecha:</div>
						<div class="meta-duo"><?php echo $factura->fecha; ?></div>
					</div>
			
				</section><!-- TO SECTION -->
			
				<section class="invoice-financials"><!-- FINANCIALS SECTION -->
			
					<div class="invoice-items"><!-- INVOICE ITEMS -->
						<table>
							<thead>
								<tr>
									<th class="col-1">Descripcion</th>
									<th class="col-2">Cantidad</th>
									<th class="col-3">Precio Unit</th>
									<th class="col-4">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($factura->items as $i) { ?>
									<tr>
										<th>
											<h1>
												<?php echo (!empty($i->nombre))?$i->nombre:""; ?>
												<?php echo ((isset($i->variante) && !empty($i->variante))?"<br/><span>".($i->variante)."</span>":""); ?>
												<?php echo (!empty($i->descripcion))?"<br/>".$i->descripcion:""; ?>
											</h1>
										</th>							
										<td><?php echo (!empty($i->cantidad))?number_format($i->cantidad,2):"0"; ?></td>
										<?php if ($factura->id_tipo_comprobante <= 4) { ?>
											<td><?php echo (!empty($i->neto))?"$ ".number_format($i->neto,2):"$ 0.00"; ?></td>
											<td><?php echo (!empty($i->total_sin_iva))?"$ ".number_format($i->total_sin_iva,2):"$ 0.00"; ?></td>
										<?php } else { ?>
											<td><?php echo (!empty($i->precio))?"$ ".number_format($i->precio,2):"$ 0.00"; ?></td>
											<td><?php echo (!empty($i->total_con_iva))?"$ ".number_format($i->total_con_iva,2):"$ 0.00"; ?></td>
										<?php } ?>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div><!-- INVOICE ITEMS -->
					
					<div class="lower-block"><!-- TERMS&PAYMENT INFO -->
						<div class="invoice-totals"><!-- TOTALS -->
							<table>
								<tbody>
									<tr>
										<th>Subtotal</th>						
										<td><?php echo "$ ".number_format($factura->neto,2); ?></td>
									</tr>
									<tr>
										<th>IVA 21%</th>						
										<td><?php echo "$ ".number_format($factura->iva,2); ?></td>
									</tr>
									<?php if ($factura->porc_descuento != 0) { ?>
										<tr>
											<th>Descuento 0%</th>						
											<td> </td>
										</tr>
									<?php } ?>
									<tr>
										<th class="col-1">Total:</th>						
										<td class="col-2"><?php echo "$ ".number_format($factura->total,2); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php if (!empty($factura->cae)) { ?>
							<div style="float: left; text-align: left; font-weight: bold;">
								<br/>
								<br/>
								<br/>
								CAE: <?php echo $factura->cae; ?><br/>
								Fecha Vencimiento: <?php echo ($factura->fecha_vto); ?><br/>
							</div>
						<?php } ?>
					</div><!-- TERMS&PAYMENT INFO -->
					<div class="barcode">
						<div><img src="/sistema/application/helpers/barcode.php?text=<?php echo $barcode; ?>" /></div>
						<div><?php echo $barcode ?></div>
					</div>					
				</section><!-- FINANCIALS SECTION -->
			</div><!-- INVOICE -->
		</div>
	</div>
<?php } ?>
</body>
</html>