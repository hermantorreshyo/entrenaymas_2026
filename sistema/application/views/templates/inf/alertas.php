<script type="text/template" id="alertas_mapa_template">
<div class="row">
	<div class="col-md-3">
		<div id="alertas_lista" class="list-group list-group-lg list-group-sp"></div>
	</div>
	<div class="col-md-9">
		<div style="height:600px" id="mapa"></div>
	</div>
</div>
</script>

<script type="text/template" id="alertas_item_template">
<div class="list-group-item clearfix">
  <span class="pull-left thumb-sm avatar m-r">
    <img src="img/a4.jpg" alt="...">
  </span>
  <span class="clear">
    <span><%= nombre %></span>
    <small class="text-muted clear text-ellipsis"><%= direccion %></small>
  </span>
</div>
</script>