<div class="panel panel-default">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Ficha Técnica",
            "en"=>"Ficha Técnica",
          )); ?>
        </label>
        <div class="panel-description">
          <?php echo lang(array(
            "es"=>"Completa lo más posible la información específica de tu producto para mejorar tu posicionamiento y resultados de búsqueda.",
            "en"=>"Completa lo más posible la información específica de tu producto para mejorar tu posicionamiento y resultados de búsqueda.",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand expanded">
    <div class="padder">

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Ancho</label>
            <input type="text" name="custom_7" id="articulo_custom_7" value="<%= custom_7 %>" class="form-control"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Perfil</label>
            <input type="text" name="custom_8" id="articulo_custom_8" value="<%= custom_8 %>" class="form-control"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Rodado</label>
            <input type="text" name="custom_9" id="articulo_custom_9" value="<%= custom_9 %>" class="form-control"/>
          </div>
        </div>
      </div>
      <?php /*
      <div class="row">

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Línea
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <input type="text" data-id_atributo="LINE" class="form-control atributo_meli"/>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Modelo
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <input type="text" data-id_atributo="MODEL" class="form-control atributo_meli"/>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Origen
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <input type="text" data-id_atributo="ORIGIN" class="form-control atributo_meli"/>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Unidades por paquete
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <select data-id_atributo="UNITS_PER_PACKAGE" class="form-control atributo_meli">
              <option value="2726554">1</option>
              <option value="2726555">2</option>
              <option value="2726556">4</option>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Es Run Flat
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <select data-id_atributo="IS_RUN_FLAT" class="form-control atributo_meli">
              <option value="242084">No</option>
              <option value="242085">Si</option>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Índice de carga
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <input type="text" data-id_atributo="LOAD_INDEX" class="form-control atributo_meli"/>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Índice de velocidad
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <select data-id_atributo="SPEED_INDEX" class="form-control atributo_meli">
              <option value="">-</option>
              <option value="362211">R 170 km/H</option>
              <option value="362197">A8 40 km/H</option>
              <option value="75312">V</option>
              <option value="362214">U 200 km/H</option>
              <option value="362204">J 100 km/H</option>
              <option value="76057">ZR</option>
              <option value="76167">TL</option>
              <option value="362210">Q 160 km/H</option>
              <option value="362209">P 150 km/H</option>
              <option value="82777">N</option>
              <option value="362202">F 80 km/H</option>
              <option value="362199">C 60 km/H</option>
              <option value="362217">W 270 km/H</option>
              <option value="362195">A6 30 km/H</option>
              <option value="362198">B 50 km/H</option>
              <option value="76171">TL G1</option>
              <option value="75324">Z</option>
              <option value="362218">Y 300 km/H</option>
              <option value="76749">GV</option>
              <option value="362201">E 70 km/H</option>
              <option value="362190">A1 5 km/H</option>
              <option value="362208">N 140 km/H</option>
              <option value="362193">A4 20 km/H</option>
              <option value="75981">P</option>
              <option value="75518">R</option>
              <option value="362207">M 130 km/H</option>
              <option value="362194">A5 25 km/H</option>
              <option value="362196">A7 35 km/H</option>
              <option value="362192">A3 15 km/H</option>
              <option value="75322">Y</option>
              <option value="362191">A2 10 km/H</option>
              <option value="362206">L 120 km/H</option>
              <option value="362200">D 65 km/H</option>
              <option value="362212">S 180 km/H</option>
              <option value="362205">K 110 km/H</option>
              <option value="362203">G 90 km/H</option>
              <option value="75320">W</option>
              <option value="76170">TL MI</option>
              <option value="75310">T</option>
              <option value="362215">H 210 km/H</option>
              <option value="75314">H</option>
              <option value="75824">Q</option>
              <option value="75337">S</option>
              <option value="362216">V 240 km/H</option>
              <option value="362213">T 190 km/H</option>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label w100p">
              Índice de desgaste
              <span data-toggle="tooltip" title="Hace click en N/A si esta especificacion no aplica a tu producto" class="fr no_aplica fs14 text-info cp">N/A</span>
            </label>
            <input type="text" data-id_atributo="TREADWEAR" data-type="number" class="form-control atributo_meli"/>
          </div>
        </div>

      </div>
      */ ?>

    </div>
  </div>
</div>