<div class="aside-wrap">
  <div class="navi-wrap">
	
    <div class="nav-bar-menu">
	  <a href="javascript:void(0)" class="btn navbar-btn">
		<i class="fa fa-fw fa-bars"></i>
	  </a>
	</div>
		 
    <div class="clearfix text-center" id="aside-user">
      <div class="dropdown wrapper">
        <a href="app/#inicio">
          <span class="thumb-lg w-auto-folded avatar m-t-sm">
            <?php if (isset($empresa) && !empty($empresa->path)) { ?>
                <img src="/sistema/<?php echo $empresa->path; ?>" class="img-full" alt="...">
            <?php } else { ?>
                <img src="resources/images/a0.jpg" class="img-full" alt="...">
            <?php } ?>
          </span>
        </a>
        <a href class="dropdown-toggle hidden-folded" data-toggle="dropdown">
          <span class="clear">
            <span class="block m-t-sm">
              <strong class="font-bold text-lt"><?php echo (isset($nombre_usuario)) ? $nombre_usuario : "Usuario" ?></strong> 
              <b class="caret"></b>
            </span>
          </span>
        </a>
        <!-- dropdown -->
        <ul class="dropdown-menu animated fadeInRight w hidden-folded">
            <li>
              <a href="app/#usuario/<?php echo $id_usuario; ?>">Perfil</a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="login/logout/">Salir</a>
            </li>
        </ul>
        <!-- / dropdown -->
      </div>
      <div class="line dk hidden-folded"></div>
    </div>

    <nav class="navi">
		<ul class="nav">
	        <?php
			function get_children($lista,$nivel) {
				foreach($lista as $t) {
	                if ($t->permiso > 0) {
						$href = ($t->tiene_pantalla == 1) ? "href='app/#$t->nombre'" : "";
	                    echo "<li class='".(($t->nombre=="inicio")?"active":"")."'>";
                        
                        // Si no tiene hijos y no tiene pantalla, no se muestra en el menu
                        if (sizeof($t->children) == 0 && $t->tiene_pantalla == 0) $class = "dn";
                        else $class = "";
                        
                        echo "<a $href class='$class'>";
                        if ($nivel == 0) {
                            if (!empty($t->clase)) {
                                echo '<i class="'.$t->clase.'"></i>';
                            } else {
                                echo '<i class="glyphicon glyphicon-envelope icon text-info-lter"></i>';    
                            }
                        }
                        if (sizeof($t->children)>0) {
                            echo '<span class="pull-right text-muted">';
                            echo '<i class="fa fa-fw fa-angle-right text"></i>';
                            echo '<i class="fa fa-fw fa-angle-down text-active"></i>';
                            echo '</span>';
                        }                        
                        echo "<span ".(($nivel==0)?"class='font-bold'":"").">";
                        echo html_entity_decode($t->title,ENT_QUOTES);
                        echo "</span>";
                        echo "</a>";
						if (sizeof($t->children)>0) {
							echo "<ul class='nav nav-sub'>";
							if ($nivel == 0) echo "<li class='nav-sub-title'><span>".html_entity_decode($t->title,ENT_QUOTES)."</span></li>";
							get_children($t->children,$nivel+1);
							echo "</ul>";
						} else {
						  if ($t->tiene_pantalla == 1 && $nivel == 0) {
							echo "<ul class='nav nav-sub'>";
							echo "<li class='nav-sub-title'><a href='app/#$t->nombre'>".html_entity_decode($t->title,ENT_QUOTES)."</a></li>";
							echo "</ul>";
						  }
						}
	                    echo "</li>";
	                }
				}
			}
			get_children($permisos_tree,0);
			?>
			<li class="line dk"></li>
		</ul>
    </nav>
	<?php if ($empresa->id != 0) { ?>
	  <!--<div style="padding:10px 24px 2px 24px;">
		<div class="text-center-folded">
		  <span class="pull-right pull-none-folded">60%</span>
		  <a href="app/#usuario/<?php echo $id_usuario; ?>">
			<i class="icon-user icon text-success-lter"></i>
			<span class="hidden-folded m-l-sm">Perfil</span>
		  </a>
		</div>
		<div class="progress-xxs m-t-sm dk progress ng-isolate-scope" value="60" type="info">
		  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
		</div>
	  </div>
	  <div class="line dk hidden-folded"></div>-->
	<?php } ?>
  </div>
</div>
