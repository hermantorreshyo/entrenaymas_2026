<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Factura_Electronica {
	
	private $token;
	private $sign;
	private $expiration_time;
	private $cuit;
	private $id_empresa;
	private $empresa = null;
	private $message;
	
	public function __construct() {
	}
	
	public function set_empresa($empresa) {
		$this->empresa = $empresa;
		$this->id_empresa = $empresa->id;
		$this->set_cuit($empresa->cuit);
	}
	public function set_cuit($cuit) {
		// El CUIT debe ser numerico y no se puede parsear a (int) por overflow
		// Entonces sumar cero es la mejor manera de castear
		$cuit = str_replace("-","",$cuit);
		$this->cuit = floatval($cuit) + 0;
	}
	
	
	public function get_token() {
		return $this->token;
	}
	public function get_sign() {
		return $this->sign;
	}
	public function get_expiration_time() {
		return $this->expiration_time;
	}
	
	public function get_auth() {
		return array(
			"Token"=>$this->token,
			"Sign"=>$this->sign,
			"Cuit"=>$this->cuit
		);
	}
	
	public function get_soap() {
		if ($this->empresa->config["facturacion_testing"] === 1) {
			$url = "uploads/testing.service.asmx.xml"; // $url = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL';
		} else {
			$url = "uploads/produccion.service.asmx.xml"; //else $url = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL';
		}
		return new SoapClient($url);
	}
	
	public function autorizar($service = "wsfe") {
		// Primero controlamos los datos guardados en la empresa
		if (empty($this->empresa)) return FALSE;
		$fecha = new DateTime($this->empresa->config["expiration_time"]);
		$ahora = new DateTime();
		if ($fecha > $ahora) {
			// Todavia no se vencieron los datos, se pueden usar estos
			$this->token = $this->empresa->config["token"];
			$this->sign = $this->empresa->config["sign"];
			$this->expiration_time = $this->empresa->config["expiration_time"];
			return TRUE;
		} else {
			// Hay que tomar nuevos parametros
			ini_set('soap.wsdl_cache_enabled',0);
			ini_set('soap.wsdl_cache_ttl',0);		
			
			// Nos movemos al directorio de la empresa
			//chdir("uploads/".$this->cuit);
			$base = str_replace("\\","/",getcwd())."/";
			$dir = "uploads/".$this->id_empresa."/certificados";
			
			$wsdl = "uploads/wsaa.wsdl";
			if ($this->empresa->config["facturacion_testing"] == 1) {
				$cert = "$dir/testing/matias.crt";
				$privatekey = "$dir/testing/matias.key";
			} else {
				$cert = "$dir/produccion/matias.crt";
				$privatekey = "$dir/produccion/matias.key";
			}
			$passphrase = "";
			if ($this->empresa->config["facturacion_testing"] == 1) $url = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
			else $url = "https://wsaa.afip.gov.ar/ws/services/LoginCms";
			
			$TRA = new SimpleXMLElement(
			  '<?xml version="1.0" encoding="UTF-8"?>' .
			  '<loginTicketRequest version="1.0">'.
			  '</loginTicketRequest>');
			$TRA->addChild('header');		
			//$TRA->header->addChild("source","C=ar, O=caporale victor eduardo, SERIALNUMBER=CUIT 20258176104, CN=caporale");
			//$TRA->header->addChild("destination","cn=wsaa,o=afip,c=ar,serialNumber=CUIT 33693450239");
			$TRA->header->addChild('uniqueId',date('U'));
			$fecha = new DateTime();
			$fecha->sub(date_interval_create_from_date_string('6 hours'));
			$TRA->header->addChild('generationTime',$fecha->format("Y-m-d")."T".$fecha->format("H:i:s")."-03:00");
			$fecha->add(date_interval_create_from_date_string('18 hours'));
			$TRA->header->addChild('expirationTime',$fecha->format("Y-m-d")."T".$fecha->format("H:i:s")."-03:00");
			$this->expiration_time = $fecha->format("Y-m-d H:i:s");
			$TRA->addChild('service',$service);
			$TRA->asXML($base."$dir/TRA.xml");
	
			//openssl_sign($TRA->asXML(),$cms,file_get_contents($privatekey));
			//$cms = base64_encode($cms);
			
			$STATUS=openssl_pkcs7_sign($base."$dir/TRA.xml", $base."$dir/TRA.tmp", "file://".realpath($cert),
			  array("file://".realpath($privatekey), $passphrase),
			  array(),
			  !PKCS7_DETACHED
			  );
			if (!$STATUS) {exit("ERROR generating PKCS#7 signature\n");}
			
			$inf=fopen("$dir/TRA.tmp", "r");
			$i=0;
			$CMS="";
			while (!feof($inf)){ 
				$buffer=fgets($inf);
				if ( $i++ >= 4 ) {$CMS.=$buffer;}
			}
			fclose($inf);
			$client=new SoapClient($wsdl, array(
				'location' => $url,
				'soap_version'   => SOAP_1_2,
				'trace'          => 1,
				'exceptions'     => false,
			)); 
			$results=$client->loginCms(array('in0'=>$CMS));
			if (is_soap_fault($results)) {
				if ($results->faultcode == "SOAP Fault: ns1:coe.alreadyAuthenticated") {
					// TODO: Ya fue autorizado con anterioridad, por lo tanto tenemos que
					// usar los mismos token y sign
					$this->message = $results->faultstring;
				} else {
					$this->message = $results->faultstring;
				}
				return FALSE;
			}
			
			$result = new SimpleXMLElement($results->loginCmsReturn);
			$this->token = $result->credentials->token;
			$this->sign = $result->credentials->sign;
			
			// Guardamos el token
			$sql = "UPDATE fact_configuracion SET ";
			$sql.= " token = '$this->token', ";
			$sql.= " sign = '$this->sign', ";
			$sql.= " expiration_time = '$this->expiration_time' ";
			$sql.= "WHERE id_empresa = $this->id_empresa ";
			$conx = get_conex();
			mysqli_query($conx,$sql);
			return TRUE;
		}
		
	}
	
	public function solicitar($factura) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    set_time_limit(0);
		
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>$this->message
			);
		}
		
		$dia = substr($factura->fecha,0,2);
		$mes = substr($factura->fecha,3,2);
		$anio = substr($factura->fecha,6);
		$fecha = $anio.$mes.$dia;
		
		$client = $this->get_soap();
		
		if ($factura->id_cliente == 0 || strtoupper($factura->cliente->nombre) == "CONSUMIDOR FINAL") {
			$doc_tipo = 99;
			$doc_nro = 0;
		} else {

      // Controlamos si el cliente tiene CUIT
      if (!isset($factura->cliente->cuit) || empty($factura->cliente->cuit)) {
        return array(
          "error"=>1,
          "mensaje"=>"El cliente no tiene cargado CUIT",
        );
      }

			$doc_tipo = $factura->cliente->id_tipo_documento + 0;
			$doc_nro = str_replace("-","",$factura->cliente->cuit) + 0;
		}

    if (!is_numeric($doc_nro)) {
      return array(
        "error"=>1,
        "mensaje"=>"El cuit [$doc_nro] no es valido.",
      );
    }

    $total_neto = 0;
    $total_iva = 0;
    if ($factura->id_tipo_comprobante != 11 && $factura->id_tipo_comprobante != 12 && $factura->id_tipo_comprobante != 13 && $factura->id_tipo_comprobante != 15) {
      foreach($factura->ivas as $iva) {
        $total_neto += ($iva->neto + 0);
        $total_iva += ($iva->iva + 0);
      }
    } else {
      // Para comprobantes tipo C, el NETO es igual al TOTAL y el IVA es 0
      $total_neto = $factura->total;
    }

		// Request generico
		$request = array(
			"Auth"=>$this->get_auth(),
			"FeCAEReq"=>array(
				"FeCabReq"=>array(
					"CantReg"=>1,
					"PtoVta"=>$factura->punto_venta,
					"CbteTipo"=>$factura->id_tipo_comprobante
				),
				"FeDetReq"=>array(
					"FECAEDetRequest"=>array(
						"Concepto"=>1,
						"DocTipo"=>$doc_tipo,
						"DocNro"=>$doc_nro,
						"CbteTipo"=>$factura->id_tipo_comprobante,
						"CbteDesde"=>$factura->numero,
						"CbteHasta"=>$factura->numero,
						"CbteFch"=>$fecha,
						"ImpTotConc"=>0.00,
						"ImpNeto"=>0.00,
						"ImpOpEx"=>0.00,
						"ImpIVA"=>0.00,
						"ImpTrib"=>0.00,
						"MonId"=>"PES",
						"MonCotiz"=>1.00,
					)
				)
			)			
		);

    /*
    if ($factura->id_empresa == 99) {
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["Concepto"] = 2;
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["FchServDesde"] = "20200801";
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["FchServHasta"] = "20200830";
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["FchVtoPago"] = "20200830";
    }
    */

    // Si es Factura MiPYME
    if ($factura->id_tipo_comprobante == 201 && !empty($factura->fecha_vto)) {

      // Obligatoriamente hay que informar la fecha de vencimiento
      $dia_vto = substr($factura->fecha_vto,0,2);
      $mes_vto = substr($factura->fecha_vto,3,2);
      $anio_vto = substr($factura->fecha_vto,6);
      $fecha_vto = $anio_vto.$mes_vto.$dia_vto;      
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["FchVtoPago"] = $fecha_vto;

      // Tambien es necesario informar el CBU del emisor
      // TODO: Hacer esto dinamico
      if ($factura->id_empresa == 46) {
        $opcional = array(
          "Id"=>"2101",
          "Valor"=>"0070100220000001831913",
        );
        $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["Opcionales"][] = $opcional;
      }
    }

    // Si es un NOTA DE CREDITO o DEBITO MiPyME,
    // hay que si o si relacionar con el comprobante que se esta anulando
    // TODO: Hacer esto dinamico despues
    if ($factura->id_empresa == 46 && $factura->id_tipo_comprobante == 203) {
      $asoc = new stdClass();
      $asoc->Cuit = "30709003883";
      $asoc->CbteFch = "20200801";
      $asoc->Tipo = 201;
      $asoc->PtoVta = 4;
      $asoc->Nro = 6;
      /*
      $asoc = array(
        "Cuit"=>,
        "CbteFch"=>"20200801",
        "Tipo"=>201,
        "PtoVta"=>4,
        "Nro"=>6,
      );*/
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["CbtesAsoc"][] = $asoc;
    }
			
		// Ponemos el NETO y el IVA
		$request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["ImpNeto"] = $total_neto;
		$request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["ImpIVA"] = $total_iva;
		$request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["ImpTotal"] = ($total_neto + $total_iva + $factura->percepcion_ib + 0);

    // Si tiene percepcion de IB
    if ($factura->percepcion_ib > 0) {
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["ImpTrib"] = $factura->percepcion_ib + 0;
      $tributos = array();

      /*
      [0] => stdClass Object
          (
              [Id] => 1
              [Desc] => Impuestos nacionales
              [FchDesde] => 20100917
              [FchHasta] => NULL
          )

      [1] => stdClass Object
          (
              [Id] => 2
              [Desc] => Impuestos provinciales
              [FchDesde] => 20100917
              [FchHasta] => NULL
          )

      [2] => stdClass Object
          (
              [Id] => 3
              [Desc] => Impuestos municipales
              [FchDesde] => 20100917
              [FchHasta] => NULL
          )

      [3] => stdClass Object
          (
              [Id] => 4
              [Desc] => Impuestos Internos
              [FchDesde] => 20100917
              [FchHasta] => NULL
          )

      [4] => stdClass Object
          (
              [Id] => 99
              [Desc] => Otro
              [FchDesde] => 20100917
              [FchHasta] => NULL
          )

      [5] => stdClass Object
          (
              [Id] => 5
              [Desc] => IIBB
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )

      [6] => stdClass Object
          (
              [Id] => 6
              [Desc] => Percepción de IVA
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )

      [7] => stdClass Object
          (
              [Id] => 7
              [Desc] => Percepción de IIBB
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )

      [8] => stdClass Object
          (
              [Id] => 8
              [Desc] => Percepciones por Impuestos Municipales
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )

      [9] => stdClass Object
          (
              [Id] => 9
              [Desc] => Otras Percepciones
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )

      [10] => stdClass Object
          (
              [Id] => 13
              [Desc] => Percepción de IVA a no Categorizado
              [FchDesde] => 20170829
              [FchHasta] => NULL
          )
      */
      $trib = array(
        "Id"=>7,
        "Desc"=>"Percepcion de IIBB",
        "BaseImp"=>($factura->neto * ($factura->porc_descuento / 100)) + 0,
        "Alic"=>$factura->porc_ib + 0,
        "Importe"=>$factura->percepcion_ib + 0,
      );
      $tributos[] = $trib;
      $request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["Tributos"] = $tributos;
    }
		
		// Para comprobantes "C", el IVA no debe informarse
		if ($factura->id_tipo_comprobante != 11 &&
			$factura->id_tipo_comprobante != 12 &&
			$factura->id_tipo_comprobante != 13 &&
			$factura->id_tipo_comprobante != 15) {
			
			// Array de IVA
			$ivas = array();
			foreach($factura->ivas as $iva) {
        if ($iva->neto == 0 && $iva->iva == 0 && $iva->id_alicuota_iva != 3) continue;
        if ($iva->id_alicuota_iva == 3 && $iva->neto == 0) continue;
        //if ($iva->neto != 0 && $iva->iva != 0) {
          $o = array(
            "Id"=>$iva->id_alicuota_iva + 0,
            "BaseImp"=>$iva->neto + 0,
            "Importe"=>$iva->iva + 0,
          );        
          $ivas[] = $o;          
        //}
			}
			$request["FeCAEReq"]["FeDetReq"]["FECAEDetRequest"]["Iva"] = $ivas;
		}

    // Log Request
    $id_empresa = $factura->id_empresa;
    $file = date("Ymd")."_fe.txt";
    $texto = "SOLICITUD FACTURA [$factura->id][$factura->id_punto_venta]: \n".print_r($request,true);
    if (!file_exists("logs/$id_empresa")) @mkdir("logs/$id_empresa");
    @file_put_contents("logs/$id_empresa/".$file, date("Y-m-d H:i:s").": ".$texto."\n", FILE_APPEND);

		$response = $client->FECAESolicitar($request);

    // Log Response
    $texto = "RESPUESTA FACTURA [$factura->id][$factura->id_punto_venta]: \n".print_r($response,true);
    if (!file_exists("logs/$id_empresa")) @mkdir("logs/$id_empresa");
    @file_put_contents("logs/$id_empresa/".$file, date("Y-m-d H:i:s").": ".$texto."\n", FILE_APPEND);

		return $response->FECAESolicitarResult;
	}

	// Consultamos por comprobante
	public function consultar($comprobante,$numero,$punto_venta) {
		
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>$this->message
			);
		}
		$client = $this->get_soap();
		$request = array(
			"Auth"=>$this->get_auth(),
			"FeCompConsReq"=>array(
				"CbteTipo"=>$comprobante,
				"CbteNro"=>$numero,
				"PtoVta"=>$punto_venta,
			),
		);
		$response = $client->FECompConsultar($request);
		return $response->FECompConsultarResult;
	}	
	
	// Devuelve el ultimo numero de comprobante utilizado
	public function get_ultimo_autorizado($punto_venta,$tipo_comprobante) {
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			$salida = new stdClass();
			$salida->error = 1;
			$salida->mensaje = $this->message;
			return $salida;
		}
		$client = $this->get_soap();
		$response = $client->FECompUltimoAutorizado(array(
			"Auth"=>$this->get_auth(),
			"PtoVta"=>intval($punto_venta),
			"CbteTipo"=>intval($tipo_comprobante)
		));
		return $response->FECompUltimoAutorizadoResult;
	}
	
	// Devuelve el estado de los servidores de la AFIP
	public function dummy() {
		$client = $this->get_soap();
		$response = $client->FEDummy();
		return $response->FEDummyResult;
	}	
	
	public function get_tipos_comprobante() {
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>"Error de autenticacion con la AFIP"
			);
		}
		$client = $this->get_soap();
		$response = $client->FEParamGetTiposCbte(array(
			"Auth"=>$this->get_auth(),
		));
		return $response->FEParamGetTiposCbteResult;
	}

  public function get_monedas() {
    if (!$this->autorizar()) {
      // Problema en la autorizacion
      return array(
        "error"=>1,
        "mensaje"=>"Error de autenticacion con la AFIP"
      );
    }
    $client = $this->get_soap();
    $response = $client->FEParamGetTiposMonedas(array(
      "Auth"=>$this->get_auth(),
    ));
    return $response->FEParamGetTiposMonedasResult;
  }

  public function get_puntos_venta() {
    if (!$this->autorizar()) {
      // Problema en la autorizacion
      return array(
        "error"=>1,
        "mensaje"=>"Error de autenticacion con la AFIP"
      );
    }
    $client = $this->get_soap();
    $response = $client->FEParamGetPtosVenta(array(
      "Auth"=>$this->get_auth(),
    ));
    print_r($response);
    return $response->FEParamGetPtosVentaResponse;
  }  
	
	public function get_tipos_iva() {
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>"Error de autenticacion con la AFIP"
			);
		}
		$client = $this->get_soap();
		$response = $client->FEParamGetTiposIva(array(
			"Auth"=>$this->get_auth(),
		));
		return $response->FEParamGetTiposIvaResult;
	}
	
	public function get_tipos_tributos() {
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>"Error de autenticacion con la AFIP"
			);
		}
		$client = $this->get_soap();
		$response = $client->FEParamGetTiposTributos(array(
			"Auth"=>$this->get_auth(),
		));
		return $response->FEParamGetTiposTributosResult;
	}
	
	public function get_tipos_monedas() {
		if (!$this->autorizar()) {
			// Problema en la autorizacion
			return array(
				"error"=>1,
				"mensaje"=>"Error de autenticacion con la AFIP"
			);
		}
		$client = $this->get_soap();
		$response = $client->FEParamGetTiposMonedas(array(
			"Auth"=>$this->get_auth(),
		));
		return $response->FEParamGetTiposMonedasResult;
	}	
	
}