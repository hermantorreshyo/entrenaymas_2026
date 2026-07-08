<?php
function cookies($config = array()) { 
  global $_COOKIE;
  // Si ya existe la cookie, no debemos mostrar el cartel
  if (isset($_COOKIE["vc_cookie"])) return;
  $texto = isset($config["texto"]) ? $config["texto"] : "El acceso a este sitio web permite la utilización de cookies nuestras y de terceros para ofrecerle una mejor calidad de nuestros servicios. Además, cualquier dato personal que nos proporcione podrá ser guardado y tratado en nuestras bases de datos.";
  $acepto_texto = isset($config["acepto_texto"]) ? $config["acepto_texto"] : "Acepto";
  $no_acepto_texto = isset($config["no_acepto_texto"]) ? $config["no_acepto_texto"] : "No Acepto";
  $link_politicas = isset($config["link_politicas"]) ? $config["link_politicas"] : "";
  $mas_info_texto = isset($config["mas_info_texto"]) ? $config["mas_info_texto"] : "Más información";
?>

<style type="text/css">
.vc-cookies-cont { 
  z-index: 999999;
  left: 0; right: 0; bottom: 0; 
  padding: 1em 1.8em; width: 100%; 
  color: black; 
  background-color: #f7f1f1c7; 
  display: flex;
  flex-wrap: nowrap;
  flex-direction: row;
  position: fixed;
  overflow: hidden;
  box-sizing: border-box;
  font-family: Helvetica,Calibri,Arial,sans-serif;
  font-size: 16px;
  line-height: 1.5em;
  -ms-flex-wrap: nowrap;
  flex-wrap: nowrap;
  opacity: 1;
  transition: opacity 1s ease;
}
.vc-cookies-link { color: white !important; text-decoration: underline !important; }
.vc-cookies-message { flex: 2; }
.vc-compliance {
  flex: 1;
  display: flex;
  align-items: center;
  align-content: space-between;
}
.vc-cookies-btn { text-align: center; flex: 1; color: white !important; background-color: #569e81; padding: 10px 15px; color: white; text-decoration: none; margin: 10px }
.vc-cookies-btn:hover, .vc-cookies-btn:focus { color: white !important; text-decoration: none; cursor:pointer; }

@media screen and (max-width: 500px) and (orientation: portrait), screen and (max-width: 736px) and (orientation: landscape) {
  .vc-cookies-cont { flex-direction: column; }
  .vc-compliance { margin-left: -10px; margin-right: -10px; }
}
</style>


<div role="dialog" aria-live="polite" aria-label="cookieconsent" aria-describedby="cookieconsent:desc" class="vc-cookies-cont">
  <!--googleoff: all-->
  <span class="vc-cookies-message"><?php echo $texto ?>
    <a aria-label="learn more about cookies" role="button" tabindex="0" class="vc-cookies-link" href="<?php echo $link_politicas ?>" target="_blank"><?php echo $mas_info_texto ?></a>
  </span>
  <div class="vc-compliance">
    <a aria-label="dismiss cookie message" onclick="vc_aceptar_cookies()" role="button" tabindex="0" class="vc-cookies-btn vc-dismiss"><?php echo $no_acepto_texto ?></a>
    <a aria-label="allow cookies" onclick="vc_no_aceptar_cookies()" role="button" tabindex="0" class="vc-cookies-btn vc-allow"><?php echo $acepto_texto ?></a>
  </div>
  <!--googleon: all-->
</div>


<script>
function setCookie(name,value,days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
function eraseCookie(name) {   
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function vc_aceptar_cookies() {
  setCookie('vc_cookie','1',365);
  $(".vc-cookies-cont").hide();
}
function vc_no_aceptar_cookies() {
  setCookie('vc_cookie','0',365);
  $(".vc-cookies-cont").hide();
}
</script>
<?php } ?>