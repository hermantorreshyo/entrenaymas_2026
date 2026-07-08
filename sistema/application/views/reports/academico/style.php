body { background-color: white; font-family: "Lato-Regular",Arial; font-size: 16px; color: <?php echo $c1; ?>; }
.subtitulo { padding-bottom: 15px; margin-bottom: 15px; font-size: 16px; text-transform: uppercase; border-bottom: solid 3px <?php echo $c1 ?>; font-weight: bold }
.info .subtitulo { border-bottom-color: #e6e6e6; }
.a4 {
  width: 90%; 
  padding: 15px 30px;
  margin: 0 auto;
  margin-bottom: 30px;
}
.encabezado { width: 100%; }
@media print {
  body {-webkit-print-color-adjust: exact; background-color: white; }
  .a4 { width: 100%; }
}

.tabla { margin-top: 20px; color: #999; }
.tabla table { padding-bottom: 15px; border-collapse: collapse; width: 100%; font-size: 14px; }
.tabla table thead th { background-color:<?php echo $c1; ?>; color: white; font-size: 14px; padding: 7px;}
.tabla table tr td { font-size: 16px; padding: 8px 0px; border-bottom: solid 1px #e6e6e6; }

h1 { font-weight: normal; text-align: right; padding-top: 15px; padding-bottom: 15px; margin-top: 0px; text-transform: uppercase; color: <?php echo $c1; ?>; font-family: "Lato-Bold"; font-weigth: bold; font-size: 42px; width: 100%; }
.encabezado_info { color: #333; text-align: right; padding-top: 30px; padding-bottom: 20px; }
.encabezado_info p { margin-bottom: 5px; font-size: 14px; }

.informacion { color: #333; }
.informacion p { margin-bottom: 5px; font-size: 15px; }