<script type="text/template" id="select_option">  
<style type="text/css">
#select_option_paso_2 .input-group-prepend { display: none }
#select_option_paso_2 .input-group { display:block; width: 100%; }
#select_option_paso_2 .input-group .form-control { display: block; float: none; }
</style>
  <div class="modal-header">
    <b><%= titulo %></b>
    <i class="pull-right cerrar fs16 fa fa-times cp"></i>
  </div>

  <div class="modal-body">
    <div id="select_option_paso_1" class="">
      <div class="d-flex justify-evenly">
        <% for (var i = 0; i < opciones.length; i++) { %>
          <% var o = opciones[i] %>
          <div class="option" data-value="<%= o.value %>">
            <span class="title"><%= o.title %></span>
            <span class="text"><%= o.text %></span>
          </div>
        <% } %>
      </div>
      <% if (cupon_descuento == 1) { %>
        <div>
          <div class="form-group mt20">
            <label>¿Tienes un cupon de descuento? Ingresalo aquí</label>
            <input placeholder="Ingrese un cupon de descuento" class="cupon_descuento form-control" value="" type="text">
            <span class="dn texto-cupon"></span>
          </div>
        </div>
      <% } %>
      <div class="tar">
        <button disabled class="btn btn-info seleccionar">Continuar</button>
      </div>
    </div>
  </div>
</script>