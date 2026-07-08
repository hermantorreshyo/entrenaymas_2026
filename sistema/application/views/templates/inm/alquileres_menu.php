<ul class="nav nav-tabs nav-tabs-2" role="tablist">
  <li class="<%= (active=="alquileres")?"active":""%>">
    <a href="<%= (active=="alquileres")?"javascript:void(0)":"app/#alquileres" %>"><i class="fa fa-key text-warning"></i> Alquileres</a>
  </li>
  <li class="<%= (active=="recibos_alquileres_adeudados")?"active":""%>">
    <a href="<%= (active=="recibos_alquileres_adeudados")?"javascript:void(0)":"app/#recibos_alquileres/0" %>"><i class="fa fa-dollar text-danger"></i> Por cobrar</a>
  </li>
  <li class="<%= (active=="recibos_alquileres_pagados")?"active":""%>">
    <a href="<%= (active=="recibos_alquileres_pagados")?"javascript:void(0)":"app/#recibos_alquileres/1" %>"><i class="fa fa-check text-success"></i> Pagados</a>
  </li>
</ul>
