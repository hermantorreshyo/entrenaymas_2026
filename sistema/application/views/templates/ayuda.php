<script type="text/template" id="rating_template">
  <div class="calification pull-left">
    <% for (var i=1;i<=stars; i++) { %>
      <i data-item="<%= i %>" class="fa fa-star star fs12 <%= (i <= value)?'active':'inactive' %>"></i>
    <% } %>
  </div>
</script>

<script type="text/template" id="ayuda_template">
<div class="panel panel-default mb0">
    <div class="panel-heading">
      <h1 class="m-n font-thin h3">Ayuda</h1>
    </div>
    <div class="panel-body">
        <div class="texto"></div>
    </div>
    <div class="panel-footer tar">
        <button class="btn btn-default cerrar">Aceptar</button>
    </div>
</div>
</script>

<script type="text/template" id="ayuda_form_template">
<div class="panel panel-default">
    <div class="panel-heading">Necesit&aacute;s ayuda?</div>
    <div class="panel-body">
        <div class="form-group">
            <div class="media">
                <span class="pull-left thumb-sm">
                    <img class="img-circle" src="/sistema/resources/images/soporte.png"/>
                </span>
                <div class="media-body">
                    <div class="pt10 gris">Comunicate con el soporte</div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <select class="form-control" id="ayuda_form_asunto">
                <option>Consultas Generales</option>
            </select>
        </div>
        <div class="form-group">
            <textarea id="ayuda_form_texto" class="form-control" style="height: 120px" placeholder="Escribe tu consulta"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-block btn-info enviar">Generar consulta</button>
        </div>
    </div>
</div>
</script>