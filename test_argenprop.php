<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
$headers = array(
  'cache-control' => 'no-cache',
  'content-type' => 'application/x-www-form-urlencoded'
);

$fields = array(
  'usr' => 'integrador@argenprop.com',
  'psd' => '123456',
  'aviso.SistemaOrigen.Id' => '10',
  'aviso.Vendedor.IdOrigen' => 'TestBasile_1',
  'aviso.EsWeb' => 'true',
  'tipoPropiedad' => '3',
  'aviso.Titulo' => 'Casa familiar de 4 ambientes c cochera y baño',
  'aviso.TipoOperacion' => '1',
  'aviso.DatosContacto.celular' => '44876066',
  'Aviso.DatosContacto.Telefono' => '1562540211',
  'propiedad.Direccion.Pais.Id' => '1',
  'propiedad.Direccion.Provincia.Id' => '1',
  'propiedad.Direccion.Partido.Id' => '63',
  'propiedad.Direccion.Ciudad.Id' => '1008',
  'propiedad.Direccion.Barrio.Id' => '4258',
  'propiedad.Direccion.Coordenadas.Latitud' => '-35.04914249999999',
  'propiedad.Direccion.Coordenadas.Longitud' => '-58.7595053',
  'propiedad.SuperficieCubierta' => '2000',
  'propiedad.SuperficieTerreno' => '240',
  'propiedad.Antiguedad' => '25',
  'propiedad.CantidadAmbientes' => '4',
  'propiedad.CantidadBanos' => '2',
  'propiedad.CantidadDormitorios' => '4',
  'propiedad.CantidadCocheras' => '1',
  'visibilidades[0].MontoOperacion' => '16400',
  'visibilidades[0].Moneda.Id' => '2',
  'propiedad.Ambientes.Jardin' => 'true',
  'propiedad.Instalaciones.InstalacionParrilla' => 'true',
  'propiedad.Ambientes.Sala' => 'true',
  'propiedad.Servicios.Telefono' => 'true',
  'propiedad.Ambientes.Terraza' => 'true',
  'aviso.Vendedor.Id' => '242566',
  'aviso.IdOrigen' => 'TestBasile_1',
  'propiedad.SuperficieTotal' => '2003'
);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, 'https://www.inmuebles.clarin.com/Publicaciones/PublicarIntranet?contentType=json');
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
$result = curl_exec($ch);
var_dump($result);
curl_close($ch);

exit();
*/

// CONSULTAR LOS AVISOS
$headers = array(
  'postman-token' => '2c14c5e5-0734-b923-f762-e73e13f37a2c',
  'cache-control' => 'no-cache',
  'content-type' => 'application/x-www-form-urlencoded'
);
$fields = array(
  'usr' => 'integrador@argenprop.com',
  'psd' => '123456',
  'sp.IdOrigen' => 'TestBasile_1',
);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, 'http://www.inmuebles.clarin.com/Avisos/FindByIdOrigen?contentType=json');
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
$result = curl_exec($ch);
var_dump($result);
curl_close($ch);
?>