<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group">
            <label class="control-label">Nombre / Raz&oacute;n Social</label>
            <input type="text" <%= (!edicion)?"disabled":"" %> autocomplete="off" required name="nombre" id="clientes_nombre" value="<%= nombre %>" class="form-control"/>
          </div>

          <div class="form-group">
            <label class="control-label">Email </label>
            <input type="text" <%= (!edicion)?"disabled":"" %> name="email" autocomplete="off" class="form-control" id="clientes_email" value="<%= email %>"/>
          </div>

          <div class="form-group mb0 tar">
            <a class="expand-link" id="expand_principal">
              <?php echo lang(array(
                "es"=>"+ M&aacute;s opciones",
                "en"=>"+ More options",
              )); ?>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">

          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Imagen principal",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_width"]) ? $empresa->config["cliente_image_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_height"]) ? $empresa->config["cliente_image_height"] : 256),
            "quality"=>(isset($empresa->config["cliente_image_quality"]) ? $empresa->config["cliente_image_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
          )); ?>

          <div class="form-group">
            <label class="control-label">Etiquetas</label>
            <select multiple id="cliente_etiquetas" style="width: 100%">
              <% for (var i=0; i< etiquetas.length; i++) { %>
                <% var o = etiquetas[i] %>
                <option selected><%= o %></option>
              <% } %>
            </select>
          </div>

          <div class="form-group">
            <label class="control-label">Observaciones </label>
            <% if (edicion) { %>
              <textarea placeholder="Escriba aqui otros datos de contacto o notas de su cliente..." style="height:100px" class="form-control" name="observaciones" id="cliente_observaciones"><%= observaciones %></textarea>
            <% } else { %>
              <span><%= observaciones %></span>
            <% } %>
          </div>

          <div class="form-group">
            <% if (edicion) { %>
              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> ><i></i>
                  El cliente est&aacute; activo.
                </label>
              </div>
            <% } else { %>
              <span><%= ((activo==0) ? "Cliente inactivo" : "Cliente activo") %></span>
            <% } %>
          </div>

          <div class="row">
            <?php for($i=1;$i<=5;$i++) { ?>

              <?php if (isset($empresa->config["cliente_custom_".$i."_file"])) { ?>
                <div class="col-xs-12">
                  <?php single_file_upload(array(
                    "name"=>"custom_$i",
                    "label"=>$empresa->config["cliente_custom_".$i."_file"],
                    "url"=>"/sistema/clientes/function/save_file/",
                  )); ?>
                </div>
              <?php } else if (isset($empresa->config["cliente_custom_".$i."_label"])) { ?>
                <div class="<?php echo (isset($empresa->config['cliente_custom_'.$i.'_class'])) ? $empresa->config['cliente_custom_'.$i.'_class'] :'col-xs-12'?>">
                  <div class="form-group">
                    <label class="control-label"><?php echo $empresa->config["cliente_custom_".$i."_label"] ?></label>
                    <?php if(isset($empresa->config['cliente_custom_'.$i.'_values'])) { 
                      $values = explode("|",$empresa->config['cliente_custom_'.$i.'_values']); ?>
                      <select class="form-control" name="custom_<?php echo $i ?>">
                        <?php foreach($values as $value) { ?>
                          <option <%= (<?php echo "custom_".$i ?> == "<?php echo $value ?>")?"selected":""  %> value="<?php echo $value ?>"><?php echo $value ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <input type="text" name="custom_<?php echo $i ?>" id="articulo_custom_<?php echo $i ?>" value="<%= custom_<?php echo $i ?> %>" class="form-control"/>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>

        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">C&oacute;digo interno</label>
                <% if (edicion) { %>
                  <input type="text" name="codigo" id="clientes_codigo" value="<%= codigo %>" class="form-control"/>
                <% } else { %>
                  <span><%= codigo %></span>
                <% } %>
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Tipo de IVA </label>
                <% if (edicion) { %>
                  <select class="form-control" id="clientes_tipo_iva">
                    <option <%= (id_tipo_iva == 4) ? "selected":"" %> value="4">Consumidor Final</option>
                    <option <%= (id_tipo_iva == 2) ? "selected":"" %> value="2">Monotributo</option>
                    <option <%= (id_tipo_iva == 1) ? "selected":"" %> value="1">Responsable Inscripto</option>
                    <option <%= (id_tipo_iva == 3) ? "selected":"" %> value="3">Exento</option>
                  </select>    
                <% } else { %>
                  <span>
                    <%= (id_tipo_iva == 4) ? "Consumidor Final":"" %>
                    <%= (id_tipo_iva == 2) ? "Monotributo":"" %>
                    <%= (id_tipo_iva == 1) ? "Responsable Inscripto":"" %>
                    <%= (id_tipo_iva == 3) ? "Exento":"" %>
                  </span>
                <% } %>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Tipo de Documento</label>
                  <select <%= (edicion)?"":"disabled" %> class="form-control" id="clientes_tipo_documento">
                    <option <%= (id_tipo_documento == 96) ? "selected":"" %> value="96">DNI</option>
                    <option <%= (id_tipo_documento == 80) ? "selected":"" %> value="80">CUIT</option>
                    <option <%= (id_tipo_documento == 86) ? "selected":"" %> value="86">CUIL</option>
                    <option <%= (id_tipo_documento == 89) ? "selected":"" %> value="89">Libreta Enrolamiento</option>
                    <option <%= (id_tipo_documento == 90) ? "selected":"" %> value="90">Libreta Civica</option>
                    <option <%= (id_tipo_documento == 94) ? "selected":"" %> value="94">Pasaporte</option>
                    <option <%= (id_tipo_documento == 99) ? "selected":"" %> value="99">Sin identificacion</option>
                  </select>    
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Numero Doc/CUIT </label>
                <% if (edicion) { %>
                  <input type="text" name="cuit" class="form-control" id="clientes_cuit" value="<%= cuit %>"/>
                <% } else { %>
                  <span><%= cuit %></span>
                <% } %>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group mb0 clearfix">
            <label class="control-label">
              <?php echo lang(array(
                "es"=>"Informaci&oacute;n de contacto",
                "en"=>"Contact information",
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
                "es"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu cliente.",
                "en"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu cliente.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">

          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label class="control-label">Localidad</label>
                <% if (ID_EMPRESA == 1129) { %>
                  <input type="text" value="<%= cliente_localidad %>" name="localidad" id="clientes_localidad" class="form-control"/>
                <% } else { %>
                  <input type="text" value="<%= localidad %>" id="clientes_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
                <% } %>
              </div>  
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">C&oacute;digo Postal</label>
                <input type="text" name="codigo_postal" value="<%= codigo_postal %>" id="clientes_codigo_postal" class="form-control"/>
              </div>  
            </div>
          </div>
          <div class="form-group">
            <label class="control-label">Direccion </label>
            <% if (edicion) { %>
              <input type="text" name="direccion" class="form-control" id="clientes_direccion" value="<%= direccion %>"/>
            <% } else { %>
              <span><%= direccion %></span>
            <% } %>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Tel&eacute;fono </label>
                <div class="input-group">
                  <span class="input-group-btn">
                    <select class="form-control w120" name="fax" id="cliente_codigos_paises" <%= (!edicion)?"disabled":"" %>>
                      <option <%= (fax=="376")?"selected":"" %> value="376">AD (+376)</option>
                      <option <%= (fax=="971")?"selected":"" %> value="971">AE (+971)</option>
                      <option <%= (fax=="93")?"selected":"" %> value="93">AF (+93)</option>
                      <option <%= (fax=="1268")?"selected":"" %> value="1268">AG (+1268)</option>
                      <option <%= (fax=="1264")?"selected":"" %> value="1264">AI (+1 264)</option>
                      <option <%= (fax=="355")?"selected":"" %> value="355">AL (+355)</option>
                      <option <%= (fax=="374")?"selected":"" %> value="374">AM (+374)</option>
                      <option <%= (fax=="599")?"selected":"" %> value="599">AN (+599)</option>
                      <option <%= (fax=="244")?"selected":"" %> value="244">AO (+244)</option>
                      <option <%= (fax=="549")?"selected":"" %> value="549">AR (+54)</option>
                      <option <%= (fax=="1684")?"selected":"" %> value="1684">AS (+1 684)</option>
                      <option <%= (fax=="43")?"selected":"" %> value="43">AT (+43)</option>
                      <option <%= (fax=="61")?"selected":"" %> value="61">AU (+61)</option>
                      <option <%= (fax=="297")?"selected":"" %> value="297">AW (+297)</option>
                      <option <%= (fax=="994")?"selected":"" %> value="994">AZ (+994)</option>
                      <option <%= (fax=="387")?"selected":"" %> value="387">BA (+387)</option>
                      <option <%= (fax=="1246")?"selected":"" %> value="1246">BB (+1 246)</option>
                      <option <%= (fax=="880")?"selected":"" %> value="880">BD (+880)</option>
                      <option <%= (fax=="32")?"selected":"" %> value="32">BE (+32)</option>
                      <option <%= (fax=="226")?"selected":"" %> value="226">BF (+226)</option>
                      <option <%= (fax=="359")?"selected":"" %> value="359">BG (+359)</option>
                      <option <%= (fax=="973")?"selected":"" %> value="973">BH (+973)</option>
                      <option <%= (fax=="257")?"selected":"" %> value="257">BI (+257)</option>
                      <option <%= (fax=="229")?"selected":"" %> value="229">BJ (+229)</option>
                      <option <%= (fax=="590")?"selected":"" %> value="590">BL (+590)</option>
                      <option <%= (fax=="1441")?"selected":"" %> value="1441">BM (+1 441)</option>
                      <option <%= (fax=="673")?"selected":"" %> value="673">BN (+673)</option>
                      <option <%= (fax=="591")?"selected":"" %> value="591">BO (+591)</option>
                      <option <%= (fax=="55")?"selected":"" %> value="55">BR (+55)</option>
                      <option <%= (fax=="1242")?"selected":"" %> value="1242">BS (+1 242)</option>
                      <option <%= (fax=="975")?"selected":"" %> value="975">BT (+975)</option>
                      <option <%= (fax=="267")?"selected":"" %> value="267">BW (+267)</option>
                      <option <%= (fax=="375")?"selected":"" %> value="375">BY (+375)</option>
                      <option <%= (fax=="501")?"selected":"" %> value="501">BZ (+501)</option>
                      <option <%= (fax=="61")?"selected":"" %> value="61">CC (+61)</option>
                      <option <%= (fax=="243")?"selected":"" %> value="243">CD (+243)</option>
                      <option <%= (fax=="236")?"selected":"" %> value="236">CF (+236)</option>
                      <option <%= (fax=="242")?"selected":"" %> value="242">CG (+242)</option>
                      <option <%= (fax=="41")?"selected":"" %> value="41">CH (+41)</option>
                      <option <%= (fax=="225")?"selected":"" %> value="225">CI (+225)</option>
                      <option <%= (fax=="682")?"selected":"" %> value="682">CK (+682)</option>
                      <option <%= (fax=="56")?"selected":"" %> value="56">CL (+56)</option>
                      <option <%= (fax=="237")?"selected":"" %> value="237">CM (+237)</option>
                      <option <%= (fax=="86")?"selected":"" %> value="86">CN (+86)</option>
                      <option <%= (fax=="57")?"selected":"" %> value="57">CO (+57)</option>
                      <option <%= (fax=="506")?"selected":"" %> value="506">CR (+506)</option>
                      <option <%= (fax=="53")?"selected":"" %> value="53">CU (+53)</option>
                      <option <%= (fax=="238")?"selected":"" %> value="238">CV (+238)</option>
                      <option <%= (fax=="61")?"selected":"" %> value="61">CX (+61)</option>
                      <option <%= (fax=="537")?"selected":"" %> value="537">CY (+537)</option>
                      <option <%= (fax=="420")?"selected":"" %> value="420">CZ (+420)</option>
                      <option <%= (fax=="49")?"selected":"" %> value="49">DE (+49)</option>
                      <option <%= (fax=="253")?"selected":"" %> value="253">DJ (+253)</option>
                      <option <%= (fax=="45")?"selected":"" %> value="45">DK (+45)</option>
                      <option <%= (fax=="1767")?"selected":"" %> value="1767">DM (+1 767)</option>
                      <option <%= (fax=="1849")?"selected":"" %> value="1849">DO (+1 849)</option>
                      <option <%= (fax=="213")?"selected":"" %> value="213">DZ (+213)</option>
                      <option <%= (fax=="593")?"selected":"" %> value="593">EC (+593)</option>
                      <option <%= (fax=="372")?"selected":"" %> value="372">EE (+372)</option>
                      <option <%= (fax=="20")?"selected":"" %> value="20">EG (+20)</option>
                      <option <%= (fax=="291")?"selected":"" %> value="291">ER (+291)</option>
                      <option <%= (fax=="34")?"selected":"" %> value="34">ES (+34)</option>
                      <option <%= (fax=="251")?"selected":"" %> value="251">ET (+251)</option>
                      <option <%= (fax=="358")?"selected":"" %> value="358">FI (+358)</option>
                      <option <%= (fax=="679")?"selected":"" %> value="679">FJ (+679)</option>
                      <option <%= (fax=="500")?"selected":"" %> value="500">FK (+500)</option>
                      <option <%= (fax=="691")?"selected":"" %> value="691">FM (+691)</option>
                      <option <%= (fax=="298")?"selected":"" %> value="298">FO (+298)</option>
                      <option <%= (fax=="33")?"selected":"" %> value="33">FR (+33)</option>
                      <option <%= (fax=="241")?"selected":"" %> value="241">GA (+241)</option>
                      <option <%= (fax=="44")?"selected":"" %> value="44">GB (+44)</option>
                      <option <%= (fax=="1473")?"selected":"" %> value="1473">GD (+1 473)</option>
                      <option <%= (fax=="995")?"selected":"" %> value="995">GE (+995)</option>
                      <option <%= (fax=="594")?"selected":"" %> value="594">GF (+594)</option>
                      <option <%= (fax=="44")?"selected":"" %> value="44">GG (+44)</option>
                      <option <%= (fax=="233")?"selected":"" %> value="233">GH (+233)</option>
                      <option <%= (fax=="350")?"selected":"" %> value="350">GI (+350)</option>
                      <option <%= (fax=="299")?"selected":"" %> value="299">GL (+299)</option>
                      <option <%= (fax=="220")?"selected":"" %> value="220">GM (+220)</option>
                      <option <%= (fax=="224")?"selected":"" %> value="224">GN (+224)</option>
                      <option <%= (fax=="590")?"selected":"" %> value="590">GP (+590)</option>
                      <option <%= (fax=="240")?"selected":"" %> value="240">GQ (+240)</option>
                      <option <%= (fax=="30")?"selected":"" %> value="30">GR (+30)</option>
                      <option <%= (fax=="GS")?"selected":"" %> value="500">GS (+500)</option>
                      <option <%= (fax=="502")?"selected":"" %> value="502">GT (+502)</option>
                      <option <%= (fax=="1671")?"selected":"" %> value="1671">GU (+1 671)</option>
                      <option <%= (fax=="245")?"selected":"" %> value="245">GW (+245)</option>
                      <option <%= (fax=="592")?"selected":"" %> value="592">GY (+592)</option>
                      <option <%= (fax=="852")?"selected":"" %> value="852">HK (+852)</option>
                      <option <%= (fax=="504")?"selected":"" %> value="504">HN (+504)</option>
                      <option <%= (fax=="385")?"selected":"" %> value="385">HR (+385)</option>
                      <option <%= (fax=="509")?"selected":"" %> value="509">HT (+509)</option>
                      <option <%= (fax=="36")?"selected":"" %> value="36">HU (+36)</option>
                      <option <%= (fax=="62")?"selected":"" %> value="62">ID (+62)</option>
                      <option <%= (fax=="353")?"selected":"" %> value="353">IE (+353)</option>
                      <option <%= (fax=="972")?"selected":"" %> value="972">IL (+972)</option>
                      <option <%= (fax=="44")?"selected":"" %> value="44">IM (+44)</option>
                      <option <%= (fax=="91")?"selected":"" %> value="91">IN (+91)</option>
                      <option <%= (fax=="246")?"selected":"" %> value="246">IO (+246)</option>
                      <option <%= (fax=="964")?"selected":"" %> value="964">IQ (+964)</option>
                      <option <%= (fax=="98")?"selected":"" %> value="98">IR (+98)</option>
                      <option <%= (fax=="354")?"selected":"" %> value="354">IS (+354)</option>
                      <option <%= (fax=="39")?"selected":"" %> value="39">IT (+39)</option>
                      <option <%= (fax=="1876")?"selected":"" %> value="1876">JM (+1 876)</option>
                      <option <%= (fax=="962")?"selected":"" %> value="962">JO (+962)</option>
                      <option <%= (fax=="81")?"selected":"" %> value="81">JP (+81)</option>
                      <option <%= (fax=="254")?"selected":"" %> value="254">KE (+254)</option>
                      <option <%= (fax=="996")?"selected":"" %> value="996">KG (+996)</option>
                      <option <%= (fax=="855")?"selected":"" %> value="855">KH (+855)</option>
                      <option <%= (fax=="686")?"selected":"" %> value="686">KI (+686)</option>
                      <option <%= (fax=="269")?"selected":"" %> value="269">KM (+269)</option>
                      <option <%= (fax=="1869")?"selected":"" %> value="1869">KN (+1 869)</option>
                      <option <%= (fax=="850")?"selected":"" %> value="850">KP (+850)</option>
                      <option <%= (fax=="82")?"selected":"" %> value="82">KR (+82)</option>
                      <option <%= (fax=="965")?"selected":"" %> value="965">KW (+965)</option>
                      <option <%= (fax=="345")?"selected":"" %> value="345">KY (+ 345)</option>
                      <option <%= (fax=="77")?"selected":"" %> value="77">KZ (+77)</option>
                      <option <%= (fax=="856")?"selected":"" %> value="856">LA (+856)</option>
                      <option <%= (fax=="961")?"selected":"" %> value="961">LB (+961)</option>
                      <option <%= (fax=="1758")?"selected":"" %> value="1758">LC (+1 758)</option>
                      <option <%= (fax=="423")?"selected":"" %> value="423">LI (+423)</option>
                      <option <%= (fax=="94")?"selected":"" %> value="94">LK (+94)</option>
                      <option <%= (fax=="231")?"selected":"" %> value="231">LR (+231)</option>
                      <option <%= (fax=="266")?"selected":"" %> value="266">LS (+266)</option>
                      <option <%= (fax=="370")?"selected":"" %> value="370">LT (+370)</option>
                      <option <%= (fax=="352")?"selected":"" %> value="352">LU (+352)</option>
                      <option <%= (fax=="371")?"selected":"" %> value="371">LV (+371)</option>
                      <option <%= (fax=="218")?"selected":"" %> value="218">LY (+218)</option>
                      <option <%= (fax=="212")?"selected":"" %> value="212">MA (+212)</option>
                      <option <%= (fax=="377")?"selected":"" %> value="377">MC (+377)</option>
                      <option <%= (fax=="373")?"selected":"" %> value="373">MD (+373)</option>
                      <option <%= (fax=="382")?"selected":"" %> value="382">ME (+382)</option>
                      <option <%= (fax=="590")?"selected":"" %> value="590">MF (+590)</option>
                      <option <%= (fax=="261")?"selected":"" %> value="261">MG (+261)</option>
                      <option <%= (fax=="692")?"selected":"" %> value="692">MH (+692)</option>
                      <option <%= (fax=="389")?"selected":"" %> value="389">MK (+389)</option>
                      <option <%= (fax=="223")?"selected":"" %> value="223">ML (+223)</option>
                      <option <%= (fax=="95")?"selected":"" %> value="95">MM (+95)</option>
                      <option <%= (fax=="976")?"selected":"" %> value="976">MN (+976)</option>
                      <option <%= (fax=="853")?"selected":"" %> value="853">MO (+853)</option>
                      <option <%= (fax=="1670")?"selected":"" %> value="1670">MP (+1 670)</option>
                      <option <%= (fax=="596")?"selected":"" %> value="596">MQ (+596)</option>
                      <option <%= (fax=="222")?"selected":"" %> value="222">MR (+222)</option>
                      <option <%= (fax=="1664")?"selected":"" %> value="1664">MS (+1664)</option>
                      <option <%= (fax=="356")?"selected":"" %> value="356">MT (+356)</option>
                      <option <%= (fax=="230")?"selected":"" %> value="230">MU (+230)</option>
                      <option <%= (fax=="960")?"selected":"" %> value="960">MV (+960)</option>
                      <option <%= (fax=="265")?"selected":"" %> value="265">MW (+265)</option>
                      <option <%= (fax=="521")?"selected":"" %> value="521">MX (+52)</option>
                      <option <%= (fax=="60")?"selected":"" %> value="60">MY (+60)</option>
                      <option <%= (fax=="258")?"selected":"" %> value="258">MZ (+258)</option>
                      <option <%= (fax=="264")?"selected":"" %> value="264">NA (+264)</option>
                      <option <%= (fax=="687")?"selected":"" %> value="687">NC (+687)</option>
                      <option <%= (fax=="227")?"selected":"" %> value="227">NE (+227)</option>
                      <option <%= (fax=="672")?"selected":"" %> value="672">NF (+672)</option>
                      <option <%= (fax=="234")?"selected":"" %> value="234">NG (+234)</option>
                      <option <%= (fax=="505")?"selected":"" %> value="505">NI (+505)</option>
                      <option <%= (fax=="31")?"selected":"" %> value="31">NL (+31)</option>
                      <option <%= (fax=="47")?"selected":"" %> value="47">NO (+47)</option>
                      <option <%= (fax=="977")?"selected":"" %> value="977">NP (+977)</option>
                      <option <%= (fax=="674")?"selected":"" %> value="674">NR (+674)</option>
                      <option <%= (fax=="683")?"selected":"" %> value="683">NU (+683)</option>
                      <option <%= (fax=="64")?"selected":"" %> value="64">NZ (+64)</option>
                      <option <%= (fax=="968")?"selected":"" %> value="968">OM (+968)</option>
                      <option <%= (fax=="507")?"selected":"" %> value="507">PA (+507)</option>
                      <option <%= (fax=="51")?"selected":"" %> value="51">PE (+51)</option>
                      <option <%= (fax=="689")?"selected":"" %> value="689">PF (+689)</option>
                      <option <%= (fax=="675")?"selected":"" %> value="675">PG (+675)</option>
                      <option <%= (fax=="63")?"selected":"" %> value="63">PH (+63)</option>
                      <option <%= (fax=="92")?"selected":"" %> value="92">PK (+92)</option>
                      <option <%= (fax=="48")?"selected":"" %> value="48">PL (+48)</option>
                      <option <%= (fax=="508")?"selected":"" %> value="508">PM (+508)</option>
                      <option <%= (fax=="872")?"selected":"" %> value="872">PN (+872)</option>
                      <option <%= (fax=="1939")?"selected":"" %> value="1939">PR (+1 939)</option>
                      <option <%= (fax=="970")?"selected":"" %> value="970">PS (+970)</option>
                      <option <%= (fax=="351")?"selected":"" %> value="351">PT (+351)</option>
                      <option <%= (fax=="680")?"selected":"" %> value="680">PW (+680)</option>
                      <option <%= (fax=="595")?"selected":"" %> value="595">PY (+595)</option>
                      <option <%= (fax=="974")?"selected":"" %> value="974">QA (+974)</option>
                      <option <%= (fax=="262")?"selected":"" %> value="262">RE (+262)</option>
                      <option <%= (fax=="40")?"selected":"" %> value="40">RO (+40)</option>
                      <option <%= (fax=="381")?"selected":"" %> value="381">RS (+381)</option>
                      <option <%= (fax=="7")?"selected":"" %> value="7">RU (+7)</option>
                      <option <%= (fax=="250")?"selected":"" %> value="250">RW (+250)</option>
                      <option <%= (fax=="966")?"selected":"" %> value="966">SA (+966)</option>
                      <option <%= (fax=="677")?"selected":"" %> value="677">SB (+677)</option>
                      <option <%= (fax=="248")?"selected":"" %> value="248">SC (+248)</option>
                      <option <%= (fax=="249")?"selected":"" %> value="249">SD (+249)</option>
                      <option <%= (fax=="46")?"selected":"" %> value="46">SE (+46)</option>
                      <option <%= (fax=="65")?"selected":"" %> value="65">SG (+65)</option>
                      <option <%= (fax=="290")?"selected":"" %> value="290">SH (+290)</option>
                      <option <%= (fax=="386")?"selected":"" %> value="386">SI (+386)</option>
                      <option <%= (fax=="47")?"selected":"" %> value="47">SJ (+47)</option>
                      <option <%= (fax=="421")?"selected":"" %> value="421">SK (+421)</option>
                      <option <%= (fax=="232")?"selected":"" %> value="232">SL (+232)</option>
                      <option <%= (fax=="378")?"selected":"" %> value="378">SM (+378)</option>
                      <option <%= (fax=="221")?"selected":"" %> value="221">SN (+221)</option>
                      <option <%= (fax=="252")?"selected":"" %> value="252">SO (+252)</option>
                      <option <%= (fax=="597")?"selected":"" %> value="597">SR (+597)</option>
                      <option <%= (fax=="239")?"selected":"" %> value="239">ST (+239)</option>
                      <option <%= (fax=="503")?"selected":"" %> value="503">SV (+503)</option>
                      <option <%= (fax=="963")?"selected":"" %> value="963">SY (+963)</option>
                      <option <%= (fax=="268")?"selected":"" %> value="268">SZ (+268)</option>
                      <option <%= (fax=="1649")?"selected":"" %> value="1649">TC (+1 649)</option>
                      <option <%= (fax=="235")?"selected":"" %> value="235">TD (+235)</option>
                      <option <%= (fax=="228")?"selected":"" %> value="228">TG (+228)</option>
                      <option <%= (fax=="66")?"selected":"" %> value="66">TH (+66)</option>
                      <option <%= (fax=="992")?"selected":"" %> value="992">TJ (+992)</option>
                      <option <%= (fax=="690")?"selected":"" %> value="690">TK (+690)</option>
                      <option <%= (fax=="670")?"selected":"" %> value="670">TL (+670)</option>
                      <option <%= (fax=="993")?"selected":"" %> value="993">TM (+993)</option>
                      <option <%= (fax=="216")?"selected":"" %> value="216">TN (+216)</option>
                      <option <%= (fax=="676")?"selected":"" %> value="676">TO (+676)</option>
                      <option <%= (fax=="90")?"selected":"" %> value="90">TR (+90)</option>
                      <option <%= (fax=="1868")?"selected":"" %> value="1868">TT (+1 868)</option>
                      <option <%= (fax=="688")?"selected":"" %> value="688">TV (+688)</option>
                      <option <%= (fax=="886")?"selected":"" %> value="886">TW (+886)</option>
                      <option <%= (fax=="255")?"selected":"" %> value="255">TZ (+255)</option>
                      <option <%= (fax=="380")?"selected":"" %> value="380">UA (+380)</option>
                      <option <%= (fax=="256")?"selected":"" %> value="256">UG (+256)</option>
                      <option <%= (fax=="1")?"selected":"" %> value="1">US (+1)</option>
                      <option <%= (fax=="598")?"selected":"" %> value="598">UY (+598)</option>
                      <option <%= (fax=="998")?"selected":"" %> value="998">UZ (+998)</option>
                      <option <%= (fax=="379")?"selected":"" %> value="379">VA (+379)</option>
                      <option <%= (fax=="1784")?"selected":"" %> value="1784">VC (+1 784)</option>
                      <option <%= (fax=="58")?"selected":"" %> value="58">VE (+58)</option>
                      <option <%= (fax=="1284")?"selected":"" %> value="1284">VG (+1 284)</option>
                      <option <%= (fax=="1340")?"selected":"" %> value="1340">VI (+1 340)</option>
                      <option <%= (fax=="84")?"selected":"" %> value="84">VN (+84)</option>
                      <option <%= (fax=="678")?"selected":"" %> value="678">VU (+678)</option>
                      <option <%= (fax=="681")?"selected":"" %> value="681">WF (+681)</option>
                      <option <%= (fax=="685")?"selected":"" %> value="685">WS (+685)</option>
                      <option <%= (fax=="967")?"selected":"" %> value="967">YE (+967)</option>
                      <option <%= (fax=="262")?"selected":"" %> value="262">YT (+262)</option>
                      <option <%= (fax=="27")?"selected":"" %> value="27">ZA (+27)</option>
                      <option <%= (fax=="260")?"selected":"" %> value="260">ZM (+260)</option>
                      <option <%= (fax=="263")?"selected":"" %> value="263">ZW (+263)</option>
                    </select>                    
                  </span>
                  <input type="text" name="telefono" class="form-control" id="clientes_telefono" value="<%= telefono %>"  <%= (!edicion)?"disabled":"" %>/>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Tel&eacute;fono (2) </label>
                <input type="text" name="celular" class="form-control" id="clientes_celular" value="<%= celular %>" <%= (!edicion)?"disabled":"" %> />
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <% if (ID_EMPRESA == 220) { %>
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label">
                Datos de la cuenta
              </label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ M&aacute;s opciones",
                  "en"=>"+ More options",
                )); ?>
              </a>
              <div class="panel-description">
                Datos referidos a la cuenta del cliente en el sistema
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand">
          <div class="padder">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Cr&eacute;ditos</label>
                  <% if (edicion) { %>
                    <div class="input-group">
                      <input type="text" name="saldo_inicial" class="form-control" id="clientes_saldo_inicial" value="<%= saldo_inicial %>"/>
                      <span class="input-group-addon">$</span>
                    </div>
                  <% } else { %>
                    <span><%= saldo_inicial %></span>
                  <% } %>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Vencimiento</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="clientes_fecha_vencimiento" name="fecha_vencimiento" value="<%= fecha_vencimiento %>"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>        
                  </div>
                </div>
              </div>
            </div>

            <% if (control.check("vendedores") > 0) { %>
              <div class="form-group">
                <label class="control-label">Vendedor </label>
                <% if (edicion) { %>
                  <select class="form-control" id="clientes_vendedores">
                  <option value="0">-</option>
                  <% for(var i=0;i < vendedores.length;i++) { %>
                    <% var o = vendedores[i]; %>
                    <option value="<%= o.id %>" <%= (o.id==id_vendedor)?"selected":"" %>><%= o.nombre %></option>
                  <% } %>                   
                  </select>
                <% } %>
              </div>
            <% } %> 
          </div>
        </div>
      </div>
    <% } else { %>
      <div class="panel panel-default <%= (ID_PROYECTO == 19)?"dn":"" %>">
        <div class="panel-body">
          <div class="padder">
            <div class="form-group mb0 clearfix">
              <label class="control-label">
                <?php echo lang(array(
                  "es"=>"Datos comerciales",
                  "en"=>"Datos comerciales",
                )); ?>
              </label>
              <a class="expand-link fr">
                <?php echo lang(array(
                  "es"=>"+ M&aacute;s opciones",
                  "en"=>"+ More options",
                )); ?>
              </a>
              <div class="panel-description">
                <?php echo lang(array(
                  "es"=>"Cuenta corriente, descuentos, listas de precios, etc.",
                  "en"=>"Cuenta corriente, descuentos, listas de precios, etc.",
                )); ?>                  
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body expand">
          <div class="padder">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Utilizar lista de precios </label>
                  <select <%= (control.check("clientes")>2)?"":"disabled" %> class="form-control" id="clientes_lista">
                    <option <%= (lista == 0) ? "selected":"" %> value="0">Lista 1</option>
                    <option <%= (lista == 1) ? "selected":"" %> value="1">Lista 2</option>
                    <option <%= (lista == 2) ? "selected":"" %> value="2">Lista 3</option>
                    <option <%= (lista == 3) ? "selected":"" %> value="3">Lista 4</option>
                    <option <%= (lista == 4) ? "selected":"" %> value="4">Lista 5</option>
                    <option <%= (lista == 5) ? "selected":"" %> value="5">Lista 6</option>
                  </select>    
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Aplicar Descuento</label>
                  <div class="input-group">
                    <input <%= (control.check("clientes")>2)?"":"disabled" %> type="text" name="descuento" class="form-control" id="clientes_descuento" value="<%= descuento %>"/>
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Forma de Pago </label>
                  <select <%= (control.check("clientes")>1)?"":"disabled" %> class="form-control" id="clientes_forma_pago">
                    <option <%= (forma_pago == "C") ? "selected":"" %> value="C">Cuenta Corriente</option>
                    <option <%= (forma_pago == "E") ? "selected":"" %> value="E">Efectivo</option>
                  </select>    
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Fecha Alta</label>
                  <div class="input-group">
                    <input <%= (control.check("clientes")>2)?"":"disabled" %> type="text" class="form-control" id="clientes_fecha_inicial" name="fecha_inicial" value="<%= fecha_inicial %>"/>
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>        
                  </div>
                </div>
              </div>
            </div>

            <% if (control.check("vendedores") > 0) { %>
              <div class="form-group">
                <label class="control-label">Vendedor </label>
                <select <%= (control.check("clientes")>2)?"":"disabled" %> class="form-control" id="clientes_vendedores">
                  <option value="0">-</option>
                  <% for(var i=0;i < vendedores.length;i++) { %>
                    <% var o = vendedores[i]; %>
                    <option value="<%= o.id %>" <%= (o.id==id_vendedor)?"selected":"" %>><%= o.nombre %></option>
                  <% } %>                   
                </select>
              </div>
            <% } %>

            <% if (control.check("planes") > 0) { %>
              <div class="form-group">
                <label class="control-label">Plan </label>
                <select <%= (control.check("clientes")>2)?"":"disabled" %> class="form-control" id="clientes_planes">
                  <option value="0">-</option>
                  <% for(var i=0;i < planes.length;i++) { %>
                    <% var o = planes[i]; %>
                    <option value="<%= o.id %>" <%= (o.id==id_plan)?"selected":"" %>><%= o.nombre %></option>
                  <% } %>                   
                </select>
              </div>
            <% } %>   

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Saldo Inicial</label>
                  <div class="input-group">
                    <input <%= (control.check("clientes")>2)?"":"disabled" %> type="text" name="saldo_inicial" class="form-control" id="clientes_saldo_inicial" value="<%= saldo_inicial %>"/>
                    <span class="input-group-addon">$</span>
                  </div>
                </div>
              </div>
              <% if (ESTADO == 1) { %>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Saldo Inicial B</label>
                    <div class="input-group">
                      <input <%= (control.check("clientes")>2)?"":"disabled" %> type="text" name="saldo_inicial_2" class="form-control" id="clientes_saldo_inicial_2" value="<%= saldo_inicial_2 %>"/>
                      <span class="input-group-addon">$</span>
                    </div>
                  </div>
                </div>
              <% } %>
            </div>
          
            <div class="form-group">
              <div class="form-inline">
                <div class="checkbox" style="margin-left:6px">
                  <label class="i-checks">
                    <input <%= (control.check("clientes")>2)?"":"disabled" %> type="checkbox" name="percibe_ib" class="checkbox" value="1" <%= (percibe_ib == 1)?"checked":"" %>><i></i>
                    Percibe ingresos brutos?
                  </label>
                </div>

                <div class="input-group w-sm m-l">
                  <input <%= (control.check("clientes")>2)?"":"disabled" %> type="text" name="percepcion_ib" class="form-control" id="clientes_percepcion_ib" value="<%= percepcion_ib %>"/>
                  <span class="input-group-addon">%</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    <% } %>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group mb0 clearfix">
            <label class="control-label">
              <?php echo lang(array(
                "es"=>"Contrase&ntilde;a",
                "en"=>"Password",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"Cambiar contrase&ntilde;a",
                "en"=>"Change password",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Clave utilizada para ingresar al sistema.",
                "en"=>"Agregar variantes a productos como talle, color, etc.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">
          <div class="form-group">
            <label class="control-label">Contrase&ntilde;a</label>
            <input type="password" autocomplete="new-password" class="form-control" id="clientes_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
          </div>
          <div class="form-group">
            <label class="control-label">Repetir contrase&ntilde;a</label>
            <input type="password" autocomplete="new-password" class="form-control" id="clientes_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
          </div>
         </div>
      </div>
    </div>

  </div>
</div>