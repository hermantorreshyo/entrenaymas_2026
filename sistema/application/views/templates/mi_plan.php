<script type="text/template" id="mi_plan_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-rocket icono_principal"></i>Mi plan</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="row">
    <% for (var i = 0; i < planes.length; i++) { %>
      <div class="col-md-6">
        <% var plan = planes[i]; %>
        <div class="plan <%= (i == 0) ? 'mla' : '' %> <%= (PERFIL == plan.id) ? 'selected' : '' %>" data-id="<%= plan.id %>">
          <div class="header">
            <span><%= plan.nombre %></span>
            <% if (plan.valor_anual !== undefined) { %>
              <p>
                <%= plan.valor %> <span class="fs-22 text-black">€</span> <span class=""> Mensual</span>
                <span class="fs22 dib ml10 mr10"> / </span>
                <%= plan.valor_anual %> <span class="fs-22 text-black">€</span> <span class=""> Anual </span>
              </p>
            <% } else { %>
              <p><%= plan.valor %> <span class="fs-22 text-black">€</span></p>
            <% } %>
          </div>
          <div class="body">
            <% for (clase in plan.items) { %>
              <p><i class="<%= clase %>"></i> - <span><%= plan.items[clase] %></span></p>
            <% } %>
          </div>
          <div class="footer">
            <% if (PERFIL != plan.id) { %>
              <button class="cambiar_plan">Cambiar a <%= plan.nombre %></button>
            <% } else { %>
              <button class="not">Plan seleccionado</button>
            <% } %>
          </div>
        </div>
      </div>
    <% } %>
  </div>

  <% if (PERFIL == 1357) { %>

    <% if (DESTACADO == 0) { %>
      <div class="tac">
        <div class="util-container">
          Si no deseas seleccionar el perfil Premium, tienes la opcion de verificar tu perfil por 3.90€<br>
          <button class="verificar_perfil mt10">Verificar Perfil</button>
        </div>
      </div>
    <% } else { %>
      <div class="tac">
        <div class="util-container selected">
          ¡Felicitaciones! tu perfil ya se encuentra verificado.<br>
          <button class="not mt10">Perfil Verificado</button>
        </div>
      </div>
    <% } %>

  <% } %>

</div>
</script>

<script type="text/template" id="verificar_perfil_view">
  <style type="text/css">
  #verificar_perfil_cont .input-group-prepend { display: none }
  #verificar_perfil_cont .input-group { display:block; width: 100%; }
  #verificar_perfil_cont .input-group .form-control { display: block; float: none; }
  </style> 
  <div class="modal-header">
    <b>Verificar Perfil</b>
    <i class="pull-right cerrar fs16 fa fa-times cp"></i>
  </div>
  <div class="modal-body">
    <div id="verificar_perfil_cont"></div>
  </div>
</script>

<script type="text/template" id="muchas_gracias_view">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-rocket icono_principal"></i>¡Su plan ha sido cambiado!</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="tac">
    <h1>¡Hola <%= NOMBRE_USUARIO %>!</h1>
    <h2>Su pago ha sido procesado correctamente.</h2>
  </div>
</div>
</script>

<script type="text/template" id="pago_rechazado_view">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-rocket icono_principal"></i>Pago Rechazado</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="tac">
    <h1>¡Hola <%= NOMBRE_USUARIO %>!</h1>
    <h2>Se ha rechazado su pago. <br/>Por favor corrobore si los datos ingresados son correctos.</h2>
  </div>
</div>
</script>
