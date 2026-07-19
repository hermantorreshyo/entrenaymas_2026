<?php
function imprimir_tabla($coleccion,$i,$hasta) { ?> 
    <table class="tabla fl w48p" <?php if ($i==0) { ?> style="border-right: solid 1px #CCC; margin-right: 10px" <?php } ?>>
        <thead>
            <tr>
                <th style="width:50px">Codigo</th>
                <th>Descripcion</th>
                <th style="width:40px" class="pr10 tar">Unid.</th>
                <th style="width:40px" class="pr10 tar">UxB</th>
                <th style="width:40px" class="pr10 tar">Bultos</th>
            </tr>
        </thead>
        <tbody>            
        <?php for ($i;$i<$hasta;$i++) { ?>
            <?php $articulo = $coleccion[$i]; ?>
            <tr>
                <td><?php echo $articulo->id; ?></td>
                <td><?php echo mb_convert_encoding($articulo->descripcion, 'ISO-8859-1', 'UTF-8') ?></td>
                <td class="pr10 tar"><?php echo $articulo->cantidad; ?></td>
                <td class="pr10 tar"><?php echo round($articulo->uxb,0); ?></td>
                <td class="pr10 tar"><?php echo round($articulo->cantidad / $articulo->uxb,2); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>    
<?php } ?>
<!DOCTYPE>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/report.css" />
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css" />
<style type="text/css">
.tabla tr td {
    padding-bottom: 3px;
    padding-top: 3px;
}
</style>
</head>
<body>
<div class="page">
  <div class="p2mm">    
    <div class="header oh">
        <div class="subtitulo fl">
            VENTAS
            DE <?php echo $proveedor->nombre ?>
        </div>
        <div class="fr">
            <span>Fecha: <span class="bold"><?php echo date("d/m/Y") ?></span></span><br/>
        </div>
    </div>
    
    <?php foreach($resultados as $clave => $row) { ?>
        
        <div class="subtitulo">
            <?php echo mb_convert_encoding($clave, 'ISO-8859-1', 'UTF-8'); ?>
        </div>
        
        <?php foreach($row as $c => $r) { ?>
            <div class="subtitulo2">
                <?php echo mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'); ?>
            </div>
            <div class="oh">    
            <?php
            $total_filas = sizeof($r);
            $mitad = ceil($total_filas/2);
            echo imprimir_tabla($r,0,$mitad);
            if ($mitad != $total_filas) echo imprimir_tabla($r,$mitad,$total_filas);
            ?>
            </div>
        <?php } ?>
        
        <div class="bbs mt5 mb5 cb"></div>
        
    <?php } ?>
    
    <?php
    /*
    $total_filas = sizeof($articulos);
    $mitad = ceil($total_filas/2);
    echo imprimir_tabla($articulos,0,$mitad);
    if ($mitad != $total_filas) echo imprimir_tabla($articulos,$mitad,$total_filas);
    */
    ?>
    
  </div>
</div>
</body>
</html>