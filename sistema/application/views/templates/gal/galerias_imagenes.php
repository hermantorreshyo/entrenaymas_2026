<script type="text/template" id="galeria_template">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="active">
        <a href="javascript:void(0)" class="buscar_productos" role="tab" data-toggle="tab">
          <i class="fa text-warning fa-calendar m-r-xs"></i>
          Productos
        </a>
      </li>
      <li>
        <a href="javascript:void(0)" class="buscar_productos" role="tab" data-toggle="tab">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Noticias
        </a>
      </li>
      <li>
        <a href="javascript:void(0)" class="buscar_productos" role="tab" data-toggle="tab">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Propiedades
        </a>
      </li>
      <li>
        <a href="javascript:void(0)" class="buscar_productos" role="tab" data-toggle="tab">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Biblioteca
        </a>
      </li>
      <li>
        <a href="javascript:void(0)" class="cerrar">
          <i class="fa text-danger fa-share m-r-xs"></i>
          Volver atras
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane panel-body active">
        <div class="input-group">
          <input type="text" class="form-control no-model" placeholder="Buscar..." id="galeria_buscador">
          <span class="input-group-btn">
            <button class="btn btn-default"><i class="fa fa-search"></i></button>
          </span>
        </div>
        <div style="overflow: auto; min-height: 300px;" id="galeria_container"></div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="galeria_item_template">
  <img style="width: 100%" src="/sistema/<%= path %>" />
  <a class="link aceptar"><%= nombre %></a>
</script>
