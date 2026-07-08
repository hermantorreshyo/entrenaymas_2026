<div class="panel panel-default">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Inventario",
            "en"=>"Stock",
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
            "es"=>"Mantenga el stock actualizado de sus art&iacute;culos desde aqu&iacute;.",
            "en"=>"Agregar variantes a productos como talle, color, etc.",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand">
    <div class="padder">
      <div class="form-group">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"C&oacute;digo interno",
            "en"=>"SKU",
          )); ?>
        </label>
        <input type="text" name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control"/>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input type="checkbox" id="articulo_usa_stock" name="usa_stock" value="1" <%= (usa_stock == 1)?"checked":"" %> ><i></i>
            Gestionar el stock de este producto
          </label>
        </div>          
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">
              <?php echo lang(array(
                "es"=>"Stock actual",
                "en"=>"Stock",
              )); ?>
            </label>
            <input type="text" name="stock" id="articulo_stock" value="<%= stock %>" class="form-control"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Cant. M&aacute;x. por pedido</label>
            <input type="text" name="maximo_disponible" id="articulo_maximo_disponible" value="<%= maximo_disponible %>" class="form-control"/>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>