<div class="panel panel-default">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Variantes",
            "en"=>"Variants",
          )); ?>
        </label>
        <a class="expand-link fr">
          <?php echo lang(array(
            "es"=>"+ Ver opciones",
            "en"=>"+ View options",
          )); ?>
        </a>
        <div class="panel-description">
          <?php echo lang(array(
            "es"=>"Agregar variantes a productos como talle, color, etc.",
            "en"=>"Add size, color, and other variants for this product",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand" style="<%= (variantes.length>0)?'display:block':'' %>">
    <div class="padder">
      <div class="w100p dt">
        <div class="dtr">
          <div class="dtc w20p">
            <div class="form-group">
              <label class="control-label bold mb0"><?php echo lang(array("es"=>"Propiedad","en"=>"Property")); ?></label>  
            </div>
          </div>
          <div class="dtc">
            <div class="form-group">
              <label class="control-label bold mb0"><?php echo lang(array("es"=>"Variantes","en"=>"Variant")); ?></label>  
            </div>
          </div>
          <div class="dtc"></div>
        </div>
      </div>
      <div class="w100p dt" id="articulo_propiedades"></div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <button class="btn btn-white btn-block nueva_propiedad">Agregar Propiedad</button>
          </div>
        </div>
      </div>
      <div class="form-group" id="articulo_variantes_tabla_cont" style="display: none;">
        <div class="b-a table-responsive">
          <table id="articulo_variantes_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th><?php echo lang(array("es"=>"Variante","en"=>"Variant")); ?></th>
                <th><?php echo lang(array("es"=>"Imagen","en"=>"Image")); ?></th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>