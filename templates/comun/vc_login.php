<style type="text/css">
#varcreativeModalLogin {
  display: none;
}
.customGPlusSignIn {
  padding: 2px;
  background-color: #4285f4;
  color: white;
  text-align: center;
  height: 48px;
  box-shadow: 0px 0px 10px #ccc;
  cursor: pointer;
}
.customGPlusSignIn img {
  float: left;
  max-width: 45px;
}
.customGPlusSignIn span {
  color: white;
  font-size: 16px;
  font-weight: bold;
  margin-top: 9px;
  display: block;
}

.varcreative-link { color:#3a86f6 !important; text-decoration: none !important; cursor: pointer !important; }
.varcreative-form-modal .varcreative-form-group { 
  margin-bottom: 20px; 
  position: relative; 
  clear: both; 
}
.varcreative-form-modal .varcreative-input {
  box-shadow: none;
  background-image: none;
  background-color: white;
  border: none;
  border: 1px solid #b5b5b5;
  border-radius: 1px;
  height: 50px;
  line-height: 14px;
  font-size: 14px;
  color: #000;
  font-family: "LatoRegular",Arial;
  padding: 5px 10px;
  width: 100%;
}
#checkout_tarjeta.varcreative-input {
  padding-top: 17px;
}
#checkout_submit {
  padding: 8px;
  max-width: 200px;
  width: 100%;
  font-size: 16px;
  font-weight: bold;
}
.varcreative-form-modal .varcreative-label {
  position: absolute;
  pointer-events: none;
  transition: 0.2s ease all;
  padding: 5px;
  display: inline-block;
  top: -7px;
  left: 10px;
  font-size: 11px;
  opacity: 1;
  line-height: 13px;
  padding: 0px 6px;
  background-color: white;
}
.varcreative-checkout .varcreative-input:focus { outline: none; }
.varcreative-form-group .varcreative-input:focus { border-color: #3a86f6; }

.varcreative-form-modal .varcreative-input:disabled {
  background-color: #f1f1f1;
  color: #b1b1b1;
}

.modalLogin {
  background-color: white;
  box-shadow: 0px 0px 30px #7b7b7b;
}
.btn-facebook { 
  background-color: #4267b2;
  color: white;
  width: 302px;
  height: 50px;
  border-radius: 1px;
  border: none;
  -moz-border-radius: 1px;
  -webkit-box-shadow 0 2px 4px 0px rgba(0,0,0,.25);
  box-shadow: 0 2px 4px 0 rgba(0,0,0,.25);
  transition: background-color .218s,border-color .218s,box-shadow .218s;
  -webkit-user-select: none;
  -webkit-appearance: none;
  background-image: none;
  text-align: center;
  overflow: hidden;
  position: relative;
  white-space: nowrap;
  cursor: pointer;
  outline: none;
  font-size: 17px;
  line-height: 48px;
}
.btn-facebook i { 
  float: left;
  font-size: 30px;
  margin-top: 7px;
}
.btn-facebook:hover {
  text-decoration: none;
  outline: none;
  color: white;
  -webkit-box-shadow: 0 0 3px 3px rgba(66,133,244,.3);
  box-shadow: 0 0 3px 3px rgba(66,133,244,.3);
}

#varcreativeLoginForm, #varcreativeRegisterForm, #varcreativePasswordForm {
  max-width: 302px;
  margin: 0 auto;
}
@media (min-width: 768px) {
  .modal-dialog.modal-dialog-login { width: 380px; }
}

.varcreative-modal-header-logo { max-width: 200px; }
.varcreative-bt { border-top: 1px solid #e5e5e5; padding-top: 10px; padding-bottom: 10px }
.varcreative-politicas p { font-size: 12px; color: #828282; line-height: 16px; }
.varcreative-tengo-cuenta { font-size: 16px; line-height: 26px; color: #222; text-align: center; }
.varcreative-form-subtitulo { font-size: 16px; line-height: 26px; color: #222; text-align: center; margin-top: 5px; margin-bottom: 15px; }
.varcreative_close_modal { position: absolute; top: 0px; right: 0px; font-size: 20px; padding: 10px; color: #a5a5a5; cursor: pointer; }
</style>

<?php function varcreative_modal_login($config = array()) { ?>
  <div id="varcreativeModalLogin" class="modal varcreative-form-modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-login" role="document">
      <div class="modal-content">
        <div class="modal-body modalLogin">
          <?php if (isset($config["modal_header"])) { ?>
            <div class="modal-header mb20 tac">
              <a class="varcreative_close_modal" href="javascript:void(0)" rel="nofollow" onclick="varcreative_close_modal()"><i class="fa fa-times"></i></a>
              <a href="<?php echo mklink("/") ?>">
                <img src="<?php echo $config["modal_header"] ?>" class="varcreative-modal-header-logo" />
              </a>
            </div>
          <?php } ?>
          <?php varcreative_login($config) ?>
        </div>
      </div>
    </div>
  </div>  
<?php } ?>

<?php function varcreative_login($config = array()) { 
  
  $titulo = isset($config["titulo"]) ? $config["titulo"] : "";
  $titulo_login = isset($config["titulo_login"]) ? $config["titulo_login"] : $titulo;
  $titulo_registro = isset($config["titulo_registro"]) ? $config["titulo_registro"] : $titulo;
  $lang = isset($config["lang"]) ? $config["lang"] : "es";
  $login_google = isset($config["login_google"]) ? $config["login_google"] : 0;
  $login_facebook = isset($config["login_facebook"]) ? $config["login_facebook"] : 0;
  $usa_captcha = isset($config["usa_captcha"]) ? $config["usa_captcha"] : 0;
  $usa_password_2 = isset($config["usa_password_2"]) ? $config["usa_password_2"] : 0;

  $label_sign_google = ($lang == "es") ? "Entrar con Google" : "Signed in with Google";
  $label_sign_facebook = ($lang == "es") ? "Entrar con Facebook" : "Login with Facebook";
  $label_otro_email = ($lang == "es") ? "Utilizar otro email" : "Login with another email";
  $label_continuar = ($lang == "es") ? "Continuar" : "Continue";
  $label_sin_cuenta = ($lang == "es") ? "¿No tienes cuenta?" : "Do not have an account?";
  $label_con_cuenta = ($lang == "es") ? "¿Ya tienes cuenta?" : "Do have an account?";
  $label_repetir_password = ($lang == "es") ? "Repetir Password" : "Confirm password";
  $label_entrar = ($lang == "es") ? "Entrar" : "Sign In";
  $label_registrar = ($lang == "es") ? "Registrar" : "Sign Up";
  $label_recuperar_password = ($lang == "es") ? "Recuperar Password" : "Recover password";
  $label_cerrar = ($lang == "es") ? "Cerrar" : "Close";
  $error_email = ($lang == "es") ? "Por favor ingrese su email." : "Please enter your email.";
  $error_password = ($lang == "es") ? "Por favor ingrese su clave." : "Please enter your password.";
  $error_password_2 = ($lang == "es") ? "Error: las claves ingresadas son diferentes." : "Please confirm the password.";
  $error_validation = ($lang == "es") ? "Por favor confirme la validacion." : "Please check the validation.";
  $error_validacion_form = ($lang == "es") ? "Ocurrio un error al enviar el email de validacion." : "An error has ocurred when send the email confirmation.";
  $error_formulario = ($lang == "es") ? "Ocurrio un error al enviar el formulario." : "An error has ocurred when submit the form.";
  ?>
  <div class="varcreative-form" id="varcreativeLoginForm">
    <?php if (!empty($titulo)) { ?>
      <p class="varcreative-form-subtitulo"><?php echo $titulo ?></p>
    <?php } ?>
    <?php if ($login_google == 1) { ?>
      <div class="varcreative-form-group">
        <div id="my-signin2" class="customGPlusSignIn">
          <img src="/templates/comun/img/google.png" alt="Google"/>
          <span class="buttonText"><?php echo $label_sign_google ?></span>
        </div>
      </div>
    <?php } ?>
    <?php if ($login_facebook == 1) { ?>
      <div class="varcreative-form-group">
        <button id="facebook_login" onclick="varcreative_login_facebook()" class="btn btn-facebook"><i class="fa fa-facebook-official"></i> <?php echo $label_sign_facebook ?></button>
      </div>
    <?php } ?>
    <div class="varcreative-form-group">
      <input type="text" class="varcreative-input" required id="varcreative_login_email" />
      <span class="varcreative-label">Email</span>
    </div>
    <div class="varcreative-form-group oh">
      <img src="/templates/comun/img/ajax-loader.gif" id="varcreative_login_1_loading" class="fl" style="display: none">
      <button id="varcreative_login_submit" onclick="varcreative_consultar_email()" class="btn fr btn-primary"><?php echo $label_continuar ?></button>
    </div>
    <div class="varcreative-bt">
      <p class="varcreative-tengo-cuenta"><?php echo $label_sin_cuenta ?> <a class="varcreative-link" onclick="varcreative_mostrar_registro()" href="javascript:void(0)"><?php echo $label_registrar ?></a></p>
    </div>
  </div>

  <form class="varcreative-form" id="varcreativeRegisterForm" style="display: none" onsubmit="return varcreative_validar_registro()">
    <?php if (!empty($titulo_registro)) { ?>
      <p class="varcreative-form-subtitulo"><?php echo $titulo_registro ?></p>
    <?php } ?>
    <div class="varcreative-form-group">
      <input type="text" id="varcreative_registro_email" required autocomplete="off" class="varcreative-input">
      <span class="varcreative-label">Email</span>
    </div>
    <div class="varcreative-form-group">
      <input type="password" id="varcreative_registro_password" required autocomplete="off" class="varcreative-input">
      <span class="varcreative-label">Password</span>
    </div>
    <?php if ($usa_password_2 == 1) { ?>
      <div class="varcreative-form-group">
        <input type="password" id="varcreative_registro_password_2" required autocomplete="off" class="varcreative-input">
        <span class="varcreative-label"><?php echo $label_repetir_password ?></span>
      </div>
    <?php } ?>
    <?php if ($usa_captcha == 1) { ?>
      <div class="varcreative-form-group oh">
        <div class="g-recaptcha" data-callback="varcreative_registro_recaptcha" id="varcreative_registro_captcha" data-sitekey="6LeHSTQUAAAAAA5FV121v-M7rnhqdkXZIGmP9N8E"></div>
      </div>
    <?php } ?>
    <div class="varcreative-form-group oh">
      <img src="/templates/comun/img/ajax-loader.gif" id="varcreative_registro_loading" class="fl" style="display: none">
      <button id="varcreative_registro_submit" class="btn fr btn-primary"><?php echo $label_registrar ?></button>
    </div>
    <div class="varcreative-bt">
      <p class="varcreative-tengo-cuenta"><?php echo $label_con_cuenta ?> <a class="varcreative-link" onclick="varcreative_mostrar_login()" href="javascript:void(0)"><?php echo $label_entrar ?></a></p>
    </div>
  </form>

  <form class="varcreative-form" id="varcreativePasswordForm" style="display: none" onsubmit="return varcreative_validar_login()">
    <?php if (!empty($titulo_login)) { ?>
      <p class="varcreative-form-subtitulo"><?php echo $titulo_login ?></p>
    <?php } ?>
    <div class="varcreative-form-group">
      <input type="text" id="varcreative_login_password" required autocomplete="off" class="varcreative-input">
      <span class="varcreative-label">Password</span>
    </div>
    <div class="varcreative-form-group oh">
      <img src="/templates/comun/img/ajax-loader.gif" id="varcreative_login_loading" class="fl" style="display: none">
      <button id="varcreative_login_submit_2" class="btn fr btn-primary"><?php echo $label_entrar ?></button>
    </div>
    <div class="varcreative-bt">
      <p class="varcreative-tengo-cuenta">
        <a class="varcreative-link mr10" onclick="varcreative_recuperar_password()" href="javascript:void(0)"><?php echo $label_recuperar_password ?></a>
      </p>
      <p class="varcreative-tengo-cuenta">
        <a class="varcreative-link" onclick="varcreative_mostrar_login()" href="javascript:void(0)"><?php echo $label_otro_email ?></a>
      </p>
    </div>    
  </form>

  <div class="varcreative-form" id="varcreativeRegisterFinishForm" style="display: none">
    <p class="varcreative-form-subtitulo"></p>
    <div class="varcreative-form-group oh">
      <button onclick="varcreative_close_modal()" class="btn fr btn-primary"><?php echo $label_cerrar ?></button>
    </div>    
  </div>

  <?php /*
  <div class="varcreative-bt varcreative-politicas">
    <p>By creating an account I confirm that I have read and accepted the 
      <a href="https://millingandgrain.com/entrada/privacy-policy-18364/" target="_blank" class="varcreative-link">terms and conditions</a> 
      and the <a href="https://millingandgrain.com/entrada/privacy-policy-18364/" target="_blank" class="varcreative-link">privacy policy</a>.
    </p>
  </div>*/ ?>


<script type="text/javascript">

// Variable usada para saber si cuando se cierra el lightbox para recargar la pagina cuando se intenta cerrar
var recargar_al_cerrar = false;

function varcreative_open_login(cerrar) {
  // Si existen las cookies del usuario, no abrimos el login
  if (varcreative_check_cookies()) return;
  var conf = {}
  cerrar = (typeof cerrar != "undefined") ? cerrar : true;
  console.log(cerrar);
  if (!cerrar) {
    console.log("ANDA");
    conf.backdrop = 'static';
    conf.keyboard = true;
    $(".varcreative_close_modal").hide();
  }

  <?php if ($login_google == 1) { ?>
    // Renderizamos el boton de google
    varcreative_render_google_sigin();
  <?php } ?>

  // Abrimos el modal
  $("#varcreativeModalLogin").modal(conf);
}

function varcreative_recuperar_password() {
  var email = $("#varcreative_login_email").val();
  $.ajax({
    "url":"/sistema/clientes/function/recuperar_pass/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "email":email,
      "lang":"en",
    },
    "success":function(r) {
      if (r.error == 0) {
        // Mostramos en el panel que hemos enviado un email para la confirmacion del registro
        varcreative_mostrar_mensaje_final(r.mensaje);
      } else {
        alert("An error has ocurred when send the email.");
      }
    },
    "error":function() {
      alert("An error has ocurred when send the email.");
    }
  });

}

function varcreative_guardar_cookies(data) {
  if (typeof (data.id) != undefined) Cookies.set("id_cliente",data.id);
  if (typeof (data.nombre) != undefined) Cookies.set("nombre",data.nombre);
  if (typeof (data.codigo_postal) != undefined) Cookies.set("codigo_postal",data.codigo_postal);
  if (typeof (data.direccion) != undefined) Cookies.set("direccion",data.direccion);
  if (typeof (data.telefono) != undefined) Cookies.set("telefono",data.telefono);
  if (typeof (data.celular) != undefined) Cookies.set("celular",data.celular);
  if (typeof (data.lista) != undefined) Cookies.set("cliente_lista",data.lista);
  if (typeof (data.descuento) != undefined) Cookies.set("cliente_descuento",data.descuento);
  if (typeof (data.activo) != undefined) Cookies.set("activo",data.activo);
  if (typeof (data.email) != undefined) Cookies.set("email",data.email);
  if (typeof (data.path) != undefined) Cookies.set("path",data.path);
  if (typeof (data.tipo_registro) != undefined) Cookies.set("tipo_registro",data.tipo_registro);
}

// ===============================================
// FUNCIONES DE GOOGLE

// Funcion para mostrar el boton de login de Google
function varcreative_render_google_sigin() {
  console.log("varcreative_render_google_sigin");
  gapi.load('auth2', function() {
    auth2 = gapi.auth2.init({
      client_id: '713772376887-gmrtqi7fr102robbcfhop48v31p6srj3.apps.googleusercontent.com',
      cookiepolicy: 'single_host_origin',
    });
    /*
    gapi.signin2.render('my-signin2', {
      'scope': 'profile email',
      'width': 302,
      'height': 50,
      'longtitle': true,
      'theme': 'dark',
    });
    */
    var button = document.getElementById('my-signin2');
    auth2.attachClickHandler(button, {}, function(googleUser) {
      // Obtenemos la informacion del usuario
      var profile = googleUser.getBasicProfile();
      // Y lo mandamos a registrar
      varcreative_registrar({
        "nombre":profile.getName(),
        "email":profile.getEmail(),
        "password":profile.getId(),
        "path":profile.getImageUrl(),
        "activo":1,
        "tipo_registro":"G",
      });      
    }, function(error) {
      console.log(JSON.stringify(error, undefined, 2));
    });
  });
}

// Logout de google
function varcreative_google_logout() {
  gapi.load('auth2', function() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function() {
      location.reload();
    });
  });
}

// ===============================================
// FUNCIONES DE FACEBOOK

// Chequear estado
function checkLoginState() {
  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {   // Logged into your webpage and Facebook.
      console.log("FACEBOOK CONECTADO");
    } else {                                 // Not logged into your webpage or we are unable to tell.
      console.log(response);
    }
  });
}  

function varcreative_login_facebook() {
  console.log("varcreative_login_facebook")
  FB.login(function(response) {
    if (response.status === 'connected') {
      console.log("varcreative_login_facebook Facebook connected")
      FB.api('/me', { fields: 'name, email' },function(response) {
        varcreative_registrar({
          "email":response.email,
          "nombre":response.name,
          "path":"http://graph.facebook.com/"+response.id+"/picture?type=large&redirect=true&width=400&height=400",
          "activo":1,
          "tipo_registro":"F",
          "password":response.id,
        });
      });  
    } else {
      // The person is not logged into your webpage or we are unable to tell. 
      console.log("FB.Login FAIL");
    }
  }, {scope: 'public_profile,email'});  
}

// Logout de Facebook
function varcreative_facebook_logout() {
  FB.logout(function (response) {
    window.location.reload();
  });
} 

// ======================================

function varcreative_mostrar_registro() {
  $(".varcreative-form").hide();
  $("#varcreativeRegisterForm").show();
}
function varcreative_mostrar_password() {
  $(".varcreative-form").hide();  
  $("#varcreativePasswordForm").show();
}
function varcreative_mostrar_login() {
  $("#varcreative_login_submit").removeAttr("disabled");
  $(".varcreative-form").hide();  
  $("#varcreativeLoginForm").show();
}
function varcreative_mostrar_mensaje_final(mensaje) {
  $(".varcreative-form").hide();
  $("#varcreativeRegisterFinishForm .varcreative-form-subtitulo").html(mensaje)
  $("#varcreativeRegisterFinishForm").show();  
}

// TODO:
var llave_recaptcha = false;
function varcreative_registro_recaptcha() {
  llave_recaptcha = true;
}

function varcreative_validar_registro() {

  var email = $("#varcreative_registro_email").val();
  var password = $("#varcreative_registro_password").val();

  if (!validateEmail(email)) {
    alert("<?php echo $error_email ?>");
    $("#varcreative_registro_email").focus();
    return false;
  }
  if (isEmpty(password)) {
    alert("<?php echo $error_password ?>");
    $("#varcreative_registro_password").focus();
    return false;
  }

  <?php if ($usa_password_2) { ?>
    var password_2 = $("#varcreative_registro_password_2").val();
    if (isEmpty(password_2)) {
      alert("<?php echo $error_password ?>");
      $("#varcreative_registro_password_2").focus();
      return false;
    }
    if (password != password_2) {
      alert("<?php echo $error_password_2 ?>");
      $("#varcreative_registro_password_2").focus();
      return false;
    }
  <?php } ?>

  <?php if ($usa_captcha == 1) { ?>
    if (!llave_recaptcha) {
      alert("<?php echo $error_validation ?>");
      return false;
    }
  <?php } ?>

  return varcreative_registrar({
    "email":email,
    "password":password,
    "activo":0, // Con este parametro indicamos que se tiene que enviar un email de validacion
  });
}

// Enviamos al controlador los datos del cliente nuevo
function varcreative_registrar(data) {
  $("#varcreative_login_1_loading").show();
  $("#varcreative_registro_loading").show();
  $("#varcreative_registro_submit").attr("disabled","disabled");
  var tipo_registro = (typeof data.tipo_registro != undefined ? data.tipo_registro : "");
  var nombre = (typeof data.nombre != undefined ? data.nombre : "");
  var email = (typeof data.email != undefined ? data.email : "");
  var activo = (typeof data.activo != undefined ? data.activo : 1);
  var path = (typeof data.path != undefined ? data.path : "");
  var password = "";
  if (typeof data.password != undefined) {
    password = hex_md5(data.password);
  }
  $.ajax({
    "url":"/sistema/clientes/function/registrar/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "email":email,
      "nombre":nombre,
      "password":password,
      "path":path,
      "tipo":6, // Tipo Newsletter
      "activo":activo,
      "tipo_registro":tipo_registro,
      "lang":"en",
    },
    "success":function(r) {
      if (r.error == false) {
        if (isEmpty(tipo_registro)) {
          // Registro por email
          // Enviamos la confirmacion al email
          varcreative_enviar_confirmacion(r.id);
        } else {
          varcreative_guardar_cookies({
            "id":r.id,
            "nombre":nombre,
            "email":email,
            "tipo_registro":tipo_registro,
            "path":path,
          });
          if (!isEmpty(r.mensaje)) {
            recargar_al_cerrar = true;
            varcreative_mostrar_mensaje_final(r.mensaje);
          } else {
            // Sino directamente recargamos la pagina
            location.reload();
          }
        }
      } else {
        alert(r.mensaje);
      }
      $("#varcreative_registro_loading").hide();
      $("#varcreative_login_1_loading").hide();
      $("#varcreative_registro_submit").removeAttr("disabled");
    },
    "error":function() {
      alert("<?php echo $error_formulario ?>");
      $("#varcreative_registro_loading").hide();
      $("#varcreative_login_1_loading").hide();
      $("#varcreative_registro_submit").removeAttr("disabled");
    },    
  });
  return false;  
}

// Esta funcion envia un email de confirmacion al cliente
function varcreative_enviar_confirmacion(id_cliente) {
  $.ajax({
    "url":"/sistema/clientes/function/enviar_sms_verificacion/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "id_cliente":id_cliente,
      "url":location.href,
      "lang":"en",
    },
    "success":function(r) {
      if (r.error == 0) {
        // Mostramos en el panel que hemos enviado un email para la confirmacion del registro
        varcreative_mostrar_mensaje_final(r.mensaje);
      } else {
        alert("<?php echo $error_validacion_form ?>");
      }
    },
    "error":function() {
      alert("<?php echo $error_validacion_form ?>");
    }
  });
}

function varcreative_consultar_email() {
  var email = $("#varcreative_login_email").val();
  if (!validateEmail(email)) {
    alert("<?php echo $error_email ?>");
    $("#varcreative_login_email").focus();
    return false;
  }
  $("#varcreative_login_submit").attr("disabled","disabled");
  // Enviamos un AJAX para comprobar si el email existe en la base de datos
  $.ajax({
    "url":"/sistema/clientes/function/check_email/",
    "dataType":"json",
    "type":"post",
    "data":{
      "email":email,
      "id_empresa":ID_EMPRESA,
      "tipo":6,
    },
    "success":function(r) {
      if (r.error == 0) {
        // Si el email existe, pasamos al siguiente paso
        varcreative_mostrar_password();
      } else {
        // Si no existe, mostramos el mensaje
        alert(r.mensaje);
        $("#varcreative_login_submit").removeAttr("disabled");
      }
    },
    "error":function(r) {
      $("#varcreative_login_submit").removeAttr("disabled");
    }
  });
  return false;
}

function varcreative_validar_login() {
  var password = $("#varcreative_login_password").val();
  var email = $("#varcreative_login_email").val();
  if (isEmpty(password)) {
    alert("<?php echo $error_password ?>");
    $("#varcreative_login_password").focus();
    return false;
  }
  return varcreative_login({
    "email":email,
    "password":password,
  });
}

function varcreative_close_modal() {
  $("#varcreativeModalLogin").modal("hide");
  if (recargar_al_cerrar) location.reload();
}
</script>

<?php } ?>

<script type="text/javascript">

function varcreative_login(data) {
  $("#varcreative_login_loading").hide();
  $("#varcreative_login_submit_2").attr("disabled","disabled");
  var password = hex_md5(data.password);
  $.ajax({
    "url":"/sistema/login/check_cliente/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "email":data.email,
      "password":password,
      "lang":"en",
    },
    "success":function(r) {
      if (r.error == false) {
        // Las cookies ya son guardadas por el controlador
        location.reload();
      } else {
        alert(r.mensaje);
        $("#varcreative_login_loading").show();
        $("#varcreative_login_submit_2").removeAttr("disabled");
      }
    },
    "error":function(r) {
      $("#varcreative_login_loading").show();
      $("#varcreative_login_submit_2").removeAttr("disabled");
    }
  });
  return false;
}

function varcreative_logout() {
  Cookies.remove("id_cliente");
  Cookies.remove("nombre");
  Cookies.remove("codigo_postal");
  Cookies.remove("direccion");
  Cookies.remove("telefono");
  Cookies.remove("celular");
  Cookies.remove("cliente_lista");
  Cookies.remove("cliente_descuento");
  Cookies.remove("activo");
  Cookies.remove("email");
  Cookies.remove("path");
  var tipo_registro = Cookies.get("tipo_registro");
  Cookies.remove("tipo_registro");
  if (tipo_registro == "F") varcreative_facebook_logout();
  else if (tipo_registro == "G") varcreative_google_logout();
  location.reload();
}

// Funcion que verifica si existen las cookies del usuario
function varcreative_check_cookies() {
  return (typeof Cookies.get("id_cliente") != "undefined");
}
</script>