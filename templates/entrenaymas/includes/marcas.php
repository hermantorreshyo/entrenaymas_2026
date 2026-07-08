<?php $marcas = $articulo_model->get_marcas(); 
if (sizeof($marcas)>0) { ?>
  <div class="container">
    <div class="colaboradores">
      <h3 class="colaboradores-title">Colaboradores</h3>
      <div class="owl-carousel owl-theme" data-items="4">
        <?php foreach($marcas as $m) { ?>
          <div class="item">
            <a href="<?php echo $m->link ?>">
              <img src="<?php echo $m->path ?>" />
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>