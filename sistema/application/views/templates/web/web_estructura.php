<style type="text/css">
.form-container {
  position: relative;
  height: 100% !important;
  overflow: hidden !important;
  margin: 0px 0px 10px 0px;
  padding: 15px 10px;
  min-height: 34px;
  border: 3px dashed #999;
  border-radius: 5px;
  background-color: #efefef;
}
.eliminar_container { cursor: pointer; position: absolute; top: 0px; right: 0px; display: none }
.form-container:hover > .eliminar_container { display: block; }

.editar_container { cursor: pointer; position: absolute; top: 20px; right: 0px; display: none }
.form-container:hover > .editar_container { display: block; }

.form-container.sortable {
  background-color: white !important;
}
.form-container:hover {
    border: 3px dashed black !important;
}
.form-container:empty {
    border: 3px dashed #999;
    border-radius: 5px;
    vertical-align: middle;
    text-align: center;
    color: #999;
    font-size: 20px;
    background-color: #c2c2c2;
}
.main-form-container {
  width: 100%;
  border: 3px dashed;
  border-color: blue;
  min-height: 500px;
  padding: 10px;
  border-radius: 5px;
}
.droppable-hovered {
    border-color: lightgreen !important;
}
.sortable-placeholder {
    margin:10px;
    padding: 10px;
    min-height: 34px;
    border: 3px dashed red;
    border-radius: 5px;
}
.sortable {
    overflow: visible;
}
</style>
<script type="text/template" id="web_estructura_edit_panel_template">
<div style="width: 256px; float: left; height: 100%; overflow: auto; ">
  <div class="header-accordion bg-light lter font-thin">
    <?php echo lang(array(
      "es"=>"Contenedores",
    )); ?>
  </div>
  <div class="info-accordion">
    <p class="subtitle-accordion">
    <?php echo lang(array(
      "es"=>"Elementos que encierran otros componentes",
    )); ?>
    </p>
    <div class="draggable_element" data-component="container_1">1 Columna</div>
    <div class="draggable_element" data-component="container_2">2 Columnas</div>
    <div class="draggable_element" data-component="container_3">3 Columnas</div>
    <div class="draggable_element" data-component="container_4">4 Columnas</div>
    <div class="draggable_element" data-component="container_6">6 Columnas</div>
  </div>

  <div class="header-accordion bg-light lter font-thin">
    <?php echo lang(array(
      "es"=>"Componentes",
    )); ?>
  </div>
  <div class="info-accordion">
    <p class="subtitle-accordion">
    <?php echo lang(array(
      "es"=>"Arrastre los componentes que desea",
    )); ?>
    </p>
    <div class="draggable_element" data-component="entradas">Bloque de Entradas</div>
    <div class="draggable_element" data-component="publicidad">Publicidad</div>
  </div>

  <div class="form-group oh cb mt15">
    <button class="btn btn-success guardar btn-block">
    <?php echo lang(array(
      "es"=>"Guardar",
      "en"=>"Save changes",
    )); ?>
    </button>
  </div>          

</div>
<div style="margin-left: 256px; height: 100%; padding-bottom: 30px; overflow: auto;">
  <div style="width: 95%; height: 100%; border:none; padding: 0px; margin: 5px; ">
    
<div class="main-form-container sortable"></div>

  </div>
</div>
</script>

<script type="text/template" id="web_estructura_config_entradas_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-heading">Configuraci&oacute;n de Bloque de Entradas</div>
    <div class="panel-body">
      <div class="form-group">
        <label class="control-label">Diseño</label>
        <div class="radio">
          <label class="i-checks">
            <input type="radio" name="estilo" class="radio" value="0" <%= (estilo==0)?'checked=""':'' %>>
            <i></i>
            Dos columnas
          </label>
        </div>
        <div class="radio">
          <label class="i-checks">
            <input type="radio" name="estilo" class="radio" value="1" <%= (estilo==1)?'checked=""':'' %>>
            <i></i>
            Dos columnas 2
          </label>
        </div>
        <div class="radio">
          <label class="i-checks">
            <input type="radio" name="estilo" class="radio" value="2" <%= (estilo==2)?'checked=""':'' %>>
            <i></i>
            Dos columnas 3
          </label>
        </div>
      </div>      
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label class="control-label">Categoria</label>
            <select class="form-control tab" name="id_categoria" id="web_estructura_config_entradas_categorias"></select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Cantidad</label>
            <input type="text" class="form-control" value="<%= offset %>" name="offset" id="web_estructura_config_entradas_offset" />
          </div>
        </div>
      </div>
      <div class="oh">
        <button class="btn cerrar tab btn-default fl">Cerrar</button>
        <button class="btn guardar tab btn-success fr">Guardar</button>
      </div>
    </div>
  </div>
</script>
