<script type="text/template" id="degenerator_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col">
      <div class="wrapper-md">
        <div class="row">
          <h3>EL NOMBRE DE LA TABLA DEBE SER IGUAL AL NOMBRE EN PLURAL ----> Y EN MINUSCULAS (SIN GUIONES, CON ESPACIO)!!!!<h3>
          <div class="col-md-4">
            <label>Singular</label>
            <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="singular" id="singular" name="singular">
          </div>
          <div class="col-md-4">
            <label>Plural</label>
            <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="plural" id="plural" name="plural">
          </div>
          <div class="col-md-4">
            <label>Carpeta</label>
            <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="carpeta" id="carpeta" name="carpeta">
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Tag Font Awesome</label>
              <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="Tag font awesome" id="Tag font awesome" name="tagfontawesome">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Tag Principal</label>
              <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="Tag Principal" id="Tag Principal" name="tagprincipal">
            </div>
          </div>
           <div class="col-md-4">
            <div class="form-group">
              <label>Tag nombre</label>
              <input type="text" class="form-control" required autocomplete="off"  title="Solo texto (Min 3 y Max 45 caracteres)" placeholder="Tag nombre" id="Tag nombre" name="tagnombre">
            </div>
          </div>

        </div>
        <textarea style="height:180px;" id="salida" class="form-control" placeholder="Salida"></textarea>
        <div class="row" style="margin-top:7px">
          <div class="col-md-6">
            <button type="submit" class="btn btn-success enviar" style="float: right">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</script>