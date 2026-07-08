<?php
include("../../params.php");
$conx = get_conex();

$data = json_decode(stripcslashes($_POST["datos"]));
$array = isset($data->array) ? $data->array : array();
$tabla = isset($data->table) ? $data->table : "";
$campo_orden = isset($data->order_column) ? $data->order_column : "orden";
$campo_id = isset($data->id_column) ? $data->id_column : "id";
$where = isset($data->where) ? $data->where : "";
// Recorremos el array y vamos actualizando sus elementos, en el orden que lo vamos recorriendo
$i=0;
foreach($array as $id) {
	$sql = "UPDATE $tabla SET $campo_orden = $i WHERE $campo_id = $id";
	if (!empty($where)) {
		$sql.= " AND $where";
	}
	$result = mysql_query($sql,$conx);
	$i++;
}

@mysql_close($conx);
echo json_encode(array("error"=>false));
?>