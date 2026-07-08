<script type="text/template" id="image_gallery_template">
<div class="wrapper-md bg-light" style="height:500px">
  <div class="form-inline">
    <input class="form-control w200" placeholder="Buscar..." />
    <select class="form-control w200">
      <option>Categoria</option>
    </select>
    <select class="form-control w200">
      <option>Ordernar por</option>
      <option>M&aacute;s utilizadas</option>
      <option>M&aacute;s nuevas</option>
    </select>
  </div>
  <div id="image_gallery_list" class="row"></div>
  <button class="buscar btn btn-default">Buscar</button>
</div>
</script>

<script type="text/template" id="image_gallery_item_template">
<a class="thumb thumb-wrapper">
  <img class="seleccionar img-full" data-id="path" src="<%= path %>">
</a>
</script>