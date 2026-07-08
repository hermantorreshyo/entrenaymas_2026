<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" href="/resources/css/report.css"/>
<link rel="stylesheet" href="/resources/css/common.css"/>
</head>
<body>
<div class="a4">
    
    <div>
        <h1><?php echo $empresa->razon_social?></h1>
        <h3>CUIT: <?php echo $empresa->cuit; ?></h3>
        <p><?php echo $empresa->tipo_contribuyente; ?></p>
    </div>
    
    <div class="fr">
        <h3><?php echo $factura->comprobante; ?></h3>
        Fecha: <?php echo $factura->fecha; ?>
    </div>
    
    <div class="">
        <p>Cliente: <?php echo $factura->cliente->nombre; ?></p>
        <p>Direccion:
            <?php
            echo $factura->cliente->direccion;
            echo " - ".$factura->cliente->localidad;
            echo (!empty($factura->cliente->provincia)) ? " (".$factura->cliente->provincia.")":""; ?>
        </p>
        <p>
            CUIT: <?php echo $factura->cliente->cuit; ?>
            Tipo Contribuyente: <?php echo $factura->cliente->tipo_iva; ?>
        </p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Cod.</th>
                <th>Cant.</th>
                <th>Detalle</th>
                <th>Precio Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($factura->items as $i) { ?>
                <tr>
                    <td><?php echo (!empty($i->id_articulo))?$i->id_articulo:""; ?></td>
                    <td><?php echo (!empty($i->cantidad))?$i->cantidad:""; ?></td>
                    <td><?php echo (!empty($i->descripcion))?$i->descripcion:""; ?></td>
                    <td><?php echo (!empty($i->precio))?$i->precio:""; ?></td>
                    <td><?php echo (!empty($i->subtotal))?$i->subtotal:""; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <div class="">
        <div>Subtotal: <?php echo $factura->subtotal; ?></div>
        <div>IVA 10,5%: <?php echo $factura->iva_105; ?></div>
        <div>IVA 21,0%: <?php echo $factura->iva; ?></div>
        <div>Perc. IIBB: <?php echo $factura->percepcion_ib; ?></div>
        <div>TOTAL: <?php echo $factura->total; ?></div>
    </div>
    
    <div class="">
        C.A.E.: <?php echo $factura->cae; ?>
        Fecha Vto: <?php echo $factura->fecha_vto; ?>
    </div>
    
</div>
</body>
</html>