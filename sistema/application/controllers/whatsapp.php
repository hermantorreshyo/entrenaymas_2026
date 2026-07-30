<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp extends CI_Controller {

  function __construct() {
    parent::__construct();
  }

  function count() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = $this->input->get("id_empresa");
    if (!is_numeric($id_empresa)) { echo json_encode(array()); return; }
    $id_usuario = $this->input->get("id_usuario");
    if (!is_numeric($id_usuario)) { echo json_encode(array()); return; }
    
    $pagina = ($this->input->get("pagina") !== FALSE) ? $this->input->get("pagina") : "";
    $pagina = str_replace("www.", "", $pagina);
    $pagina = str_replace("http://", "", $pagina);
    $pagina = str_replace("https://", "", $pagina);

    $stamp = time();
    $sql = "INSERT INTO whatsapp_clicks (id_empresa,id_usuario,stamp,pagina) VALUES ('$id_empresa','$id_usuario','$stamp','$pagina') ";
    $this->db->query($sql);
    echo json_encode(array());
  }

  function set() {
    @session_start();
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    header('Access-Control-Allow-Origin: *');
    $abierto = $this->input->get("abierto");
    $_SESSION["clienapp_abierto"] = $abierto;
    echo json_encode(array("error"=>0));
  }

  /*
  // INSTALACION
<script type="text/javascript" src="https://www.varcreative.com/sistema/resources/js/loader.js"></script>
<script type="text/javascript">loadScript("https://www.varcreative.com/sistema/whatsapp/get/"+window.location.hostname+"/");</script>
  */
  function get($dominio = 0) {
    @session_start();
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    header('Access-Control-Allow-Origin: *');

    $dominio = str_replace("www.", "", $dominio);
    $this->load->model("Empresa_Model");
    $id_empresa = $this->Empresa_Model->get_id_empresa_by_dominio($dominio);
    if ($id_empresa == 0) {
      // No existe la empresa seleccionada, no devolvemos nada
      exit();
    }

    $empresa = $this->Empresa_Model->get($id_empresa);
    if ($empresa->estado_cuenta == 2) {
      // La cuenta esta vencida, no debemos mostrar el CHAT
      exit();
    }
    $conf = array();

    // Obtenemos los usuarios de esa empresa
    $this->load->model("Usuario_Model");
    $conf["usuarios"] = array();

    $cc = array(
      "activo"=>1,
      "id_empresa"=>$id_empresa,
      "offset"=>9999,
      "order"=>"RAND() ASC ",
      "buscar_horarios"=>1,
      "aparece_web"=>1,
    );
    $usuarios = $this->Usuario_Model->buscar($cc);
    $total_disponibles = 0;
    $i=0;
    foreach($usuarios as $user) {
      $u = new stdClass();
      // TODO: Control de dias y horarios
      $u->id = $user->id;
      $u->nombre_usuario = $user->nombre;
      $u->email = (empty($user->email) ? $empresa->email : $user->email);
      $u->bcc = (isset($empresa->config["bcc_email"]) ? $empresa->config["bcc_email"] : "");
      $u->path = (empty($user->path)) ? "https://www.varcreative.com/sistema/resources/images/a0.jpg" : "https://www.varcreative.com/sistema/".$user->path;
      $u->cargo = $user->cargo;
      $u->celular = preg_replace("/[^0-9]/", "", $user->celular);
      if (sizeof($user->horarios) == 0) {
        $u->disponible = 1;
      } else {
        $u->disponible = 0;
        $dia_semana = date("N");
        $hora_actual = date("H:i:s");
        foreach($user->horarios as $horario) {
          if ($horario->dia == $dia_semana && $horario->desde <= $hora_actual && $hora_actual <= $horario->hasta) {
            $u->disponible = 1; break;
          }
        }
      }
      if (!($u->disponible == 0 && $user->ocultar_notificaciones == 1)) {
        if ($u->disponible == 1) $total_disponibles++;
        $conf["usuarios"][] = $u;
      }
      $i++;
    }

    $useragent=$_SERVER['HTTP_USER_AGENT'];
    $conf["movil"] = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) ? 1 : 0;

    $conf["empresa"] = $empresa;
    if ($empresa->config["clienapp_mantener_cerrado"] == 0) {
      $conf["abierto"] = (isset($_SESSION["clienapp_abierto"])) ? $_SESSION["clienapp_abierto"] : $empresa->config["clienapp_abierto"];
      $conf["sonido"] = ($conf["abierto"] == 1) ? $empresa->config["clienapp_sonido"] : 0;
    } else {
      $conf["abierto"] = $empresa->config["clienapp_abierto"];
      $conf["sonido"] = $empresa->config["clienapp_sonido"];
    }
    $conf["formulario"] = $empresa->config["clienapp_formulario"];
    $conf["posicion"] = $empresa->config["clienapp_posicion"];
    $conf["mostrar_email"] = $empresa->config["clienapp_mostrar_email"];
    $conf["prefijo"] = $empresa->config["clienapp_prefijo"];
    $conf["largo_telefono"] = $empresa->config["clienapp_largo_telefono"];
    $conf["total_disponibles"] = $total_disponibles;
    $tpl_base = $this->load->view("whatsapp/chat_base",null,true);
    $tpl_user = $this->load->view("whatsapp/chat_user",null,true);
    $this->load->view("whatsapp/chat",array(
      "id_empresa"=>$id_empresa,
      "config"=>$conf,
      "tpl_base"=>$tpl_base,
      "tpl_user"=>$tpl_user,
    ));
  }
}