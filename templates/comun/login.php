<?php
include_once("carrito_css.php");
?>
<div class="varcreative-checkout">
  <div class="varcreative-container">

    <div class="varcreative-panel checkout_registro panel_activo">
      <form onsubmit="return enviar_login()">
        <a href="javascript:void(0)" class="varcreative-panel-heading">Ingresa tus datos</a>
        <div class="varcreative-panel-body">

          <div class="varcreative-form-group">
            <input type="text" class="varcreative-input" required id="login_email" name="email" />
            <span class="varcreative-label">Email</span>
          </div>
          <div class="varcreative-form-group">
            <input type="password" class="varcreative-input" required id="login_ps" name="password" />
            <span class="varcreative-label">Contraseña</span>
          </div>
        </div>
        <div class="varcreative-panel-footer">
          <input id="login_submit" type="submit" class="varcreative-btn" value="Entrar" />
        </div>
      </form>
    </div>

  </div>
</div>