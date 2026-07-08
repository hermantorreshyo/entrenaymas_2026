<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <li class="<?php echo ($active=="cursos")?"active":""?>">
    <a href="<?php echo ($active=="cursos")?"javascript:void(0)":"app/#cursos" ?>"><i class="<%= modulo.clase %> text-info"></i> <%= modulo.title %></a>
  </li>
  <li class="<?php echo ($active=="cursos_categorias")?"active":""?>">
    <a href="<?php echo ($active=="cursos_categorias")?"javascript:void(0)":"app/#cursos_categorias" ?>"><i class="fa fa-tags text-danger"></i> Categorias</a>
  </li>
  <li class="<?php echo ($active=="cursos_autores")?"active":""?>">
    <a href="<?php echo ($active=="cursos_autores")?"javascript:void(0)":"app/#cursos_autores" ?>"><i class="fa fa-users text-success"></i> Autores</a>
  </li>
</ul>
