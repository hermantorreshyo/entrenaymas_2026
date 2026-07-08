<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />

<title><?php echo $empresa->razon_social; ?></title>

<link rel="stylesheet" href="<?php echo $folder ?>/reset.css" media="all" />
<link rel="stylesheet" href="<?php echo $folder ?>/style.css" media="all" />
<link rel="stylesheet" href="<?php echo $folder ?>/print.css" media="print" />

<!-- give life to HTML5 objects in IE -->
<!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<!-- js HTML class -->
<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<!-- begin markup -->



<div id="invoice" class="new"><!-- INVOICE -->

	<header id="header"><!-- HEADER -->
		<div style="float: left; font-size: 26px; width: auto" class="this-is">
			<?php echo $empresa->razon_social ?>
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

        <div class="invoice-to">
			<div class="invoice-to-title"><?php echo $factura->cliente->nombre; ?></div>
            <div class="to-org">
				<?php
				echo $factura->cliente->direccion;
				echo (isset($factura->cliente->localidad) && !empty($factura->cliente->localidad)) ? " - ".$factura->cliente->localidad : "";
				echo (!empty($factura->cliente->provincia)) ? " (".$factura->cliente->provincia.")":"";
				?>
			</div>
			<div class="to-org">
				<?php
				echo (isset($factura->cliente->tipo_iva) && !empty($factura->cliente->tipo_iva)) ? $factura->cliente->tipo_iva : "";
				echo (isset($factura->cliente->cuit) && !empty($factura->cliente->cuit)) ? " - CUIT: ".$factura->cliente->cuit : "";
				?>
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
								<h1><?php echo (!empty($i->descripcion))?$i->descripcion:""; ?></h1>
								<?php echo ((isset($i->variante) && !empty($i->variante))?"<br/><span>".($i->variante)."</span>":""); ?>
							</th>							
							<td><?php echo (!empty($i->cantidad))?$i->cantidad:"0"; ?></td>
							<?php if ($factura->id_tipo_comprobante <= 4) { ?>
								<td><?php echo (!empty($i->neto))?"$ ".$i->neto:"$ 0.00"; ?></td>
								<td><?php echo (!empty($i->total_sin_iva))?"$ ".$i->total_sin_iva:"$ 0.00"; ?></td>
							<?php } else { ?>
								<td><?php echo (!empty($i->precio))?"$ ".$i->precio:"$ 0.00"; ?></td>
								<td><?php echo (!empty($i->total_con_iva))?"$ ".$i->total_con_iva:"$ 0.00"; ?></td>
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
						<?php if ($factura->porc_descuento != 0) { ?>
							<tr>
								<th>Descuento 0%</th>						
								<td> </td>
							</tr>
						<?php } ?>
                        <tr>
                            <th class="col-1">Total:</th>						
                            <td class="col-2"><?php echo "$ ".$factura->total; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
		</div>
	</section>
</div><!-- INVOICE -->
<?php if($empresa->facturacion_abrir_dialogo_imprimir == 1) { ?>
	<script type='text/javascript'>
	window.print();
	setTimeout(function(){
		window.close();
	},3000);
	</script>
<?php } ?>
</body>
</html>