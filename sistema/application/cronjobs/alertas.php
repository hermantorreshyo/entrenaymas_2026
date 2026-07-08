<?php
set_time_limit(0);
ini_set('auto_detect_line_endings', 1);
ini_set('max_execution_time', '0');
ob_end_clean();
gc_enable();
include("../../params.php");

function sse_message( $evtname='chat', $data=null, $retry=1000 ){
    if( !is_null( $data ) ){
        echo "event:".$evtname."\r\n";
        echo "retry:".$retry."\r\n";
        echo "data:" . json_encode( $data, JSON_FORCE_OBJECT|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS );
        echo "\r\n\r\n";    
    }
}

$id_empresa = 113;
$sleep=10;
$c=1;		
$ultimo_id = 0;

header("Content-Type: text/event-stream\n\n");
header('Cache-Control: no-cache');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Expose-Headers: X-Events');      	

while (TRUE) {
    if( connection_status() != CONNECTION_NORMAL or connection_aborted() ) {
        break;
    }
	$sql = "SELECT A.*, ";
	$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, ";
	$sql.= " IF(U.nombre IS NULL,'',U.nombre) AS nombre, ";
	$sql.= " IF(U.direccion IS NULL,'',U.direccion) AS direccion ";
	$sql.= "FROM app_alertas A ";
	$sql.= " LEFT JOIN web_users U ON (A.id_usuario = U.id) ";
	$sql.= "WHERE A.id_empresa = $id_empresa ";
	$sql.= "AND A.id > $ultimo_id ";
	$sql.= "ORDER BY A.id DESC ";
	$q = mysqli_query($conx,$sql);
	if (mysqli_num_rows($q)>0) {
		while(($row=mysqli_fetch_object($q))!==NULL) {
			sse_message( 'chat', $row );
			$ultimo_id = $row->id;
		}
	}
	mysqli_free_result($q);
	mysqli_close($conx);
    if( @ob_get_level() > 0 ) for( $i=0; $i < @ob_get_level(); $i++ ) @ob_flush();
    @flush();

    sleep( $sleep );
    $c++;

    if( $c % 1000 == 0 ){/* I used this whilst streaming twitter data to try to reduce memory leaks */
        gc_collect_cycles();
        $c=1;   
    }
}

if( @ob_get_level() > 0 ) {
    for( $i=0; $i < @ob_get_level(); $i++ ) @ob_flush();
    @ob_end_clean();
}
?>