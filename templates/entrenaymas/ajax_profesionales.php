<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php");
include("includes/search_params.php");
$nombre_pagina = "profesionales";
$link_general = "profesionales/";
foreach($profesionales as $r) { 
  item($r);
 } ?>