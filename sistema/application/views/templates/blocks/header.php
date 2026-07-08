      <!-- navbar header -->
      <div class="navbar-header bg-black">

        <!-- brand -->
        <a href="app/#inicio" class="navbar-brand text-lt">
          <span class="hidden-folded m-l-xs"><?php echo (isset($empresa)) ? $empresa->nombre : ""; ?></span>
          <a href="javascript:void(0)" class="btn navbar-btn pull-right">
            <i class="fa fa-fw fa-bars"></i>
          </a>
        </a>
        <!-- / brand -->
      </div>
      <!-- / navbar header -->

      <!-- navbar collapse -->
      <div class="collapse navbar-collapse box-shadow bg-white-only">
		
        <div class="nav navbar-nav m-l-sm m-t-sm">
		  <div class="btn-group dropdown">
			<button class="btn btn-sm btn-success dropdown-toggle btn-addon" data-toggle="dropdown">
			  <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
			  <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			  <li><a href="app/#facturacion">Factura</a></li>
			  <li><a href="app/#articulo">Articulo</a></li>
			  <li><a href="app/#cliente">Cliente</a></li>
			  <li><a href="javascript:void(0)" onclick="workspace.nuevo_email()">Email</a></li>
			</ul>
		  </div>
		  
		  
          <!--<a href="" class="btn no-shadow navbar-btn active" ui-toggle-class="show" target="#aside-user">
            <i class="icon-user fa-fw"></i>
          </a>-->
        </div>
        
        <!-- nabar right -->
        <ul class="nav navbar-nav navbar-right">
          
		<?php if (isset($volver_superadmin) && $volver_superadmin == 1) { ?>
            <li onclick="volver_superadmin()">
              <a class="active"><i class="fa fa-user"></i></a>
            </li>
<script type="text/javascript">
function volver_superadmin() {
  $.ajax({
    "url":"login/cambiar_empresa/",
    "dataType":"json",
    "success":function(r) {
       if (r.error == false) window.location = "app/";
    }
  });
}
</script>
		<?php } ?>          
            <li id="fullscreen" class="hidden-xs">
              <a ui-fullscreen="" class="active"><i class="fa fa-expand"></i></a>
            </li>
            <li class="dropdown">
              <a href class="dropdown-toggle clear" data-toggle="dropdown">
                <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                  <?php if (isset($empresa) && !empty($empresa->path)) { ?>
                      <img src="/sistema/<?php echo $empresa->path; ?>" alt="...">
                  <?php } else { ?>
                      <img src="resources/images/a0.jpg" alt="...">
                  <?php } ?>
                  <i class="<?php echo ($estado==0) ? "on":"busy" ?> md b-white bottom"></i>
                </span>
                <span class="hidden-sm hidden-md"><?php //echo (isset($login_user)) ? $login_user->nombre." ".$login_user->apellido : "Usuario" ?></span> <b class="caret"></b>
              </a>
              <!-- dropdown -->
              <ul class="dropdown-menu animated fadeInRight w">
                <li>
                  <a href="app/#usuario/<?php echo $id_usuario; ?>">Perfil</a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="login/logout/">Salir</a>
                </li>
              </ul>
              <!-- / dropdown -->
            </li>
        </ul>
        <!-- / navbar right -->

      </div>
      <!-- / navbar collapse -->
