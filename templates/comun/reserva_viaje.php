<?php $lang = isset($lang) ? $lang : "es"; ?>
<div class="reserve">
  <div class="main style2">
    <div class="container">
      <div class="primary style2">
        <div class="steps">
          <ul>
            <li class="<?php echo ($step==1) ? 'active':'' ?>">
              <i class="fa fa-user" aria-hidden="true"></i>
              <div class="block">
                <span>1</span>
              </div>
              <p class="tac">
                <?php if ($step==2) { ?><a href="<?php echo mklink("web/reserva/?step=1&id=$id&fecha=$fecha_reserva") ?>"><?php } ?>
                  <?php echo ($lang == "es")?"Informaci&oacute;n":"" ?>
                  <?php echo ($lang == "en")?"Information":"" ?>
                  <?php echo ($lang == "pt")?"Informação":"" ?>
                <?php if ($step==2) { ?></a><?php } ?>
              </p>
            </li>
            <li class="<?php echo ($step==2) ? 'active':'' ?>">
              <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
              <div class="block">
                <span>2</span>
              </div>
              <p class="tac">
                <?php echo ($lang == "es")?"Pago":"" ?>
                <?php echo ($lang == "en")?"Payment":"" ?>
                <?php echo ($lang == "pt")?"Pagamento":"" ?>
              </p>
            </li>
            <li class="<?php echo ($step==3) ? 'active':'' ?>">
              <i class="fa fa-check" aria-hidden="true"></i>
              <div class="block">
                <span>3</span>
              </div>
              <p class="tac">
                <?php echo ($lang == "es")?"Confirmaci&oacute;n":"" ?>
                <?php echo ($lang == "en")?"Confirmation":"" ?>
                <?php echo ($lang == "pt")?"Confirmação":"" ?>
              </p>
            </li>
          </ul>
        </div>
        <div class="step_1" style="<?php echo ($step==1)?'display:block':'display:none'; ?>">
          <div class="title">
            <?php echo ($lang == "es")?"Informaci&oacute;n":"" ?>
            <?php echo ($lang == "en")?"Information":"" ?>
            <?php echo ($lang == "pt")?"Informação":"" ?>
          </div>
          <div class="item-detail style2">
            <div class="row">
              <div class="col-md-5">
                <label>
                  <?php echo ($lang == "es")?"Elegir Fecha":"" ?>
                  <?php echo ($lang == "en")?"Choose date":"" ?>
                  <?php echo ($lang == "pt")?"Escolher a data":"" ?>
                </label>
                <div class="view-inputs style2">
                  <input id="reserva_fecha" onchange="modificar_fecha_reserva()" type="text" placeholder="Fecha de excursión" class="" />
                </div>
              </div>
              <div class="col-md-3">
                <label>
                  <?php echo ($lang == "es")?"Adultos":"" ?>
                  <?php echo ($lang == "en")?"Adults":"" ?>
                  <?php echo ($lang == "pt")?"Adultos":"" ?>
                </label>
                <div class="quantity">
                  <input id="cantidad_adultos" onchange="modificar_pasajeros()" min="1" max="100" step="1" value="<?php echo sizeof($pedido->adultos) ?>" type="number">
                </div>
              </div>
              <div class="col-md-3">
                <label>
                  <?php echo ($lang == "es")?"Niños":"" ?>
                  <?php echo ($lang == "en")?"Children":"" ?>
                  <?php echo ($lang == "pt")?"Menores":"" ?>
                </label>
                <div class="quantity">
                  <input id="cantidad_menores" onchange="modificar_pasajeros()" min="0" max="100" step="1" value="<?php echo sizeof($pedido->menores) ?>" type="number">
                </div>
              </div>
            </div>
          </div>
          <div class="sub-title">
            <?php echo ($lang == "es")?"datos del/los pasajero/s":"" ?>
            <?php echo ($lang == "en")?"Passenger information":"" ?>
            <?php echo ($lang == "pt")?"dados de passageiros":"" ?>
          </div>
          <?php if (sizeof($pedido->adultos)>0) { 
            $adulto = $pedido->adultos[0]; ?>
            <div class="heading2">
              <i class="fa fa-check-circle-o" aria-hidden="true"></i> 
                <?php echo ($lang == "es")?"ADULTO":"" ?>
                <?php echo ($lang == "en")?"ADULT":"" ?>
                <?php echo ($lang == "pt")?"ADULTO":"" ?>
                1
            </div>
            <div class="forms style2 form_pasajeros form_adultos">
              <div class="row">
                <input type="hidden" value="<?php echo $adulto->precio ?>" class="precio"/>
                <input type="hidden" value="<?php echo $adulto->moneda ?>" class="moneda"/>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->nombre ?>" class="nombre" placeholder="<?php echo ($lang == "es")?"Nombre completo":"" ?><?php echo ($lang == "en")?"Name":"" ?><?php echo ($lang == "pt")?"Nome":"" ?>" />
                  <i class="fa fa-user" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->dni ?>" class="dni" placeholder="<?php echo ($lang == "es")?"Pasaporte / DNI":"" ?><?php echo ($lang == "en")?"Passport":"" ?><?php echo ($lang == "pt")?"Passaporte":"" ?>" />
                  <i class="fa fa-address-card" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <label>
                    <?php echo ($lang == "es")?"Fecha de nacimiento":"" ?>
                    <?php echo ($lang == "en")?"Birthdate":"" ?>
                    <?php echo ($lang == "pt")?"Data de nascimento":"" ?>
                  </label>
                  <div class="view-inputs">
                    <input onchange="recalcular_totales()" type="text" value="<?php echo $adulto->fecha_nac ?>" placeholder="Fecha de nacimiento" class="fecha_nac" />
                 </div>
                </div>
                <div class="col-md-6">
                  <select class="nacionalidad">
                    <option value="">
                      <?php echo ($lang == "es")?"Nacionalidad":"" ?>
                      <?php echo ($lang == "en")?"Nationality":"" ?>
                      <?php echo ($lang == "pt")?"Nacionalidade":"" ?>
                    </option>
                    <?php 
                    $sql_nac = "SELECT * ";
                    if ($lang == "en") $sql_nac.= ", nombre_en AS nombre ";
                    else if ($lang == "pt") $sql_nac.= ", nombre_pt AS nombre ";    
                    $sql_nac .= "FROM custom_nacionalidades ORDER BY orden ASC";
                    $q_nac = mysqli_query($conx,$sql_nac);
                    while(($nac=mysqli_fetch_object($q_nac))!==NULL) { ?>
                      <option <?php echo (utf8_encode($nac->nombre)==$adulto->nacionalidad)?"selected":"" ?> value="<?php echo utf8_encode($nac->nombre) ?>"><?php echo utf8_encode($nac->nombre) ?></option>
                    <?php } ?>
                  </select>
                  <i class="fa fa-globe" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->email ?>" class="email" placeholder="<?php echo ($lang == "es")?"Dirección de email":"" ?><?php echo ($lang == "en")?"Email":"" ?><?php echo ($lang == "pt")?"Email":"" ?>" />
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->email_2 ?>" class="email_2" placeholder="<?php echo ($lang == "es")?"Confirmar dirección de email":"" ?><?php echo ($lang == "en")?"Confirm email address":"" ?><?php echo ($lang == "pt")?"Confirmar email":"" ?>" />
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->telefono ?>" class="telefono" placeholder="<?php echo ($lang == "es")?"Teléfono de contacto":"" ?><?php echo ($lang == "en")?"Contact phone":"" ?><?php echo ($lang == "pt")?"Telefone de contato":"" ?>" />
                  <i class="fa fa-phone" aria-hidden="true"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" value="<?php echo $adulto->celular ?>" class="celular" placeholder="<?php echo ($lang == "es")?"Celular de viaje":"" ?><?php echo ($lang == "en")?"Travel mobile phone":"" ?><?php echo ($lang == "pt")?"Celular viagem":"" ?>" />
                  <i class="fa fa-mobile" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          <?php } ?>
          <div id="pasajeros_adultos">
            <?php for($i=1;$i<sizeof($pedido->adultos);$i++) { 
              $adulto = $pedido->adultos[$i]; ?>
              <div class="form_adultos form_pasajeros">
                <div class="heading2"><i class="fa fa-check-circle-o" aria-hidden="true"></i> 
                  <?php echo ($lang == "es")?"ADULTO":"" ?>
                  <?php echo ($lang == "en")?"ADULT":"" ?>
                  <?php echo ($lang == "pt")?"ADULTO":"" ?>
                  <?php echo ($i+1) ?>
                </div>
                <div class="forms style2">
                  <input type="hidden" value="<?php echo $adulto->precio ?>" class="precio"/>
                  <input type="hidden" value="<?php echo $adulto->moneda ?>" class="moneda"/>
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" value="<?php echo $adulto->nombre ?>" class="nombre" placeholder="<?php echo ($lang == "es")?"Nombre completo":"" ?><?php echo ($lang == "en")?"Name":"" ?><?php echo ($lang == "pt")?"Nome":"" ?>" />
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6">
                      <input type="text" class="dni" value="<?php echo $adulto->dni ?>" placeholder="<?php echo ($lang == "es")?"Pasaporte / DNI":"" ?><?php echo ($lang == "en")?"Passport":"" ?><?php echo ($lang == "pt")?"Passaporte":"" ?>" />
                      <i class="fa fa-address-card" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6">
                      <label>
                        <?php echo ($lang == "es")?"Fecha de nacimiento":"" ?>
                        <?php echo ($lang == "en")?"Birthdate":"" ?>
                        <?php echo ($lang == "pt")?"Data de nascimento":"" ?>
                      </label>
                      <div class="view-inputs">
                        <input onchange="recalcular_totales()" type="text" value="<?php echo $adulto->fecha_nac ?>" placeholder="<?php echo ($lang == "es")?"Fecha de nacimiento":"" ?><?php echo ($lang == "en")?"Birthdate":"" ?><?php echo ($lang == "pt")?"Data de nascimento":"" ?>" class="fecha_nac" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select class="nacionalidad">
                        <option value="">
                          <?php echo ($lang == "es")?"Nacionalidad":"" ?>
                          <?php echo ($lang == "en")?"Nationality":"" ?>
                          <?php echo ($lang == "pt")?"Nacionalidade":"" ?>
                        </option>
                        <?php $q_nac = mysqli_query($conx,"SELECT * FROM custom_nacionalidades WHERE id_empresa = $empresa->id ORDER BY orden ASC");
                        while(($nac=mysqli_fetch_object($q_nac))!==NULL) { ?>
                          <option <?php echo (utf8_encode($nac->nombre)==$adulto->nacionalidad)?"selected":"" ?> value="<?php echo utf8_encode($nac->nombre) ?>"><?php echo utf8_encode($nac->nombre) ?></option>
                        <?php } ?>
                      </select>
                      <i class="fa fa-globe" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <div id="pasajeros_menores">
            <?php for($i=0;$i<sizeof($pedido->menores);$i++) { 
              $adulto = $pedido->menores[$i]; ?>
              <div class="form_menores form_pasajeros">
                <div class="heading2"><i class="fa fa-check-circle-o" aria-hidden="true"></i> 
                  <?php echo ($lang == "es")?"NI&Ntilde;O":"" ?>
                  <?php echo ($lang == "en")?"CHILD":"" ?>
                  <?php echo ($lang == "pt")?"MENOR":"" ?>
                  <?php echo ($i+1) ?>
                </div>
                <div class="forms style2">
                  <input type="hidden" value="<?php echo $adulto->precio ?>" class="precio"/>
                  <input type="hidden" value="<?php echo $adulto->moneda ?>" class="moneda"/>
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" value="<?php echo $adulto->nombre ?>" class="nombre" placeholder="<?php echo ($lang == "es")?"Nombre completo":"" ?><?php echo ($lang == "en")?"Name":"" ?><?php echo ($lang == "pt")?"Nome":"" ?>" />
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6">
                      <input type="text" class="dni" value="<?php echo $adulto->dni ?>" placeholder="<?php echo ($lang == "es")?"Pasaporte / DNI":"" ?><?php echo ($lang == "en")?"Passport":"" ?><?php echo ($lang == "pt")?"Passaporte":"" ?>" />
                      <i class="fa fa-address-card" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6">
                      <label>
                        <?php echo ($lang == "es")?"Fecha de nacimiento":"" ?>
                        <?php echo ($lang == "en")?"Birthdate":"" ?>
                        <?php echo ($lang == "pt")?"Data de nascimento":"" ?>
                      </label>
                      <div class="view-inputs">
                        <input onchange="recalcular_totales()" type="text" value="<?php echo $adulto->fecha_nac ?>" placeholder="<?php echo ($lang == "es")?"Fecha de nacimiento":"" ?><?php echo ($lang == "en")?"Birthdate":"" ?><?php echo ($lang == "pt")?"Data de nascimento":"" ?>" class="fecha_nac" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select class="nacionalidad">
                        <option value="">
                          <?php echo ($lang == "es")?"Nacionalidad":"" ?>
                          <?php echo ($lang == "en")?"Nationality":"" ?>
                          <?php echo ($lang == "pt")?"Nacionalidade":"" ?>
                        </option>
                        <?php $q_nac = mysqli_query($conx,"SELECT * FROM custom_nacionalidades WHERE id_empresa = $empresa->id ORDER BY orden ASC");
                        while(($nac=mysqli_fetch_object($q_nac))!==NULL) { ?>
                          <option <?php echo (utf8_encode($nac->nombre)==$adulto->nacionalidad)?"selected":"" ?> value="<?php echo utf8_encode($nac->nombre) ?>"><?php echo utf8_encode($nac->nombre) ?></option>
                        <?php } ?>
                      </select>
                      <i class="fa fa-globe" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <?php 
          // Si el cliente tiene cargado hoteles
          $sql = "SELECT * FROM hot_hoteles WHERE id_empresa = $empresa->id ";
          $q = mysqli_query($conx,$sql);
          if (mysqli_num_rows($q)>0) { ?>
            <div class="sub-title">
              <?php echo ($lang == "es")?"información de viaje":"" ?>
              <?php echo ($lang == "en")?"travel information":"" ?>
              <?php echo ($lang == "pt")?"detalhes da viagem":"" ?>
            </div>
            <div class="forms style2">
              <div class="row">
                <div class="col-md-6">
                  <select id="pedido_hoteles">
                    <option value="">
                      <?php echo ($lang == "es")?"Hotel de destino":"" ?>
                      <?php echo ($lang == "en")?"Destination hotel":"" ?>
                      <?php echo ($lang == "pt")?"Hotel de destino":"" ?>
                    </option>
                    <?php while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                      <option <?php echo ($r->nombre==$pedido->hotel)?"selected":"" ?> value="<?php echo utf8_encode($r->nombre) ?>"><?php echo utf8_encode($r->nombre) ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label>
                    <?php echo ($lang == "es")?"Fecha de llegada":"" ?>
                    <?php echo ($lang == "en")?"Arrival date":"" ?>
                    <?php echo ($lang == "pt")?"Data de chegada":"" ?>
                  </label>
                  <div class="view-inputs">
                    <input id="pedido_fecha_llegada_hotel" value="<?php echo $pedido->fecha_llegada_hotel ?>" type="text" class="" />
                  </div>
                </div>
                <div class="col-md-12">
                  <textarea id="pedido_hotel_observaciones" value="<?php echo $pedido->hotel_observaciones ?>" placeholder="<?php echo ($lang == "es")?"Comentarios":"" ?><?php echo ($lang == "en")?"Comments":"" ?><?php echo ($lang == "pt")?"Comentários":"" ?>" style="margin-top:0;"></textarea>
                </div>
              </div>
            </div>
          <?php } ?>
          <?php if(sizeof($viaje->opcionales)>0) { ?>
            <div class="payment-methods">
              <div class="heading2"><i class="fa fa-plus-circle" aria-hidden="true"></i> 
                <?php echo ($lang == "es")?"Opcionales":"" ?>
                <?php echo ($lang == "en")?"Optional":"" ?>
                <?php echo ($lang == "pt")?"Opcional":"" ?>
              </div>
              <?php foreach($viaje->opcionales as $opcional) { ?>
                <div class="block">
                  <?php
                  // Controlamos si el opcional fue seleccionado o no
                  $checked = 0;
                  foreach($pedido->opcionales as $opc) {
                    foreach($opc as $op) {
                      if (isset($op->id_opcional) && $op->id_opcional == $opcional->id) { $checked = 1; break; }
                    }
                  } ?>
                  <input onchange="recalcular_totales()" <?php echo ($checked==1)?"checked":"" ?> class="opcionales_checkbox" id="opcional_<?php echo $opcional->id ?>" value="<?php echo $opcional->id ?>" name="opcional_<?php echo $opcional->id ?>" type="checkbox">
                  <label for="opcional_<?php echo $opcional->id ?>">
                    <span></span>
                    <div class="pull-left" style="max-width: 500px">
                      <h4><?php echo $opcional->nombre ?></h4>

                      <?php $texto = strip_tags(html_entity_decode($opcional->texto,ENT_QUOTES));
                      if (strlen($texto)>120) { ?>
                        <div class="texto_cont">
                          <div class="texto_breve">
                            <?php echo substr($texto, 0, 120)."..."; ?>
                            <a class="external-link" onclick="expandir_texto(this)" href="javascript:void(0)">Ver m&aacute;s</a>
                          </div>
                          <div class="texto_completo" style="display: none;">
                            <?php echo $texto ?>
                            <a class="external-link" onclick="colapsar_texto(this)" href="javascript:void(0)">Ver menos</a>
                          </div>
                        </div>
                      <?php } else echo $texto; ?>
                    </div>
                    <div class="pull-right">
                      <?php if ($opcional->precio == 0) { ?>
                        <h5 style="font-size: 22px;">
                          <?php /*echo ($lang == "es")?"SIN CARGO":"" ?>
                          <?php echo ($lang == "en")?"NO FEE":"" ?>
                          <?php echo ($lang == "pt")?"SEM CARGO":""*/ ?>
                        </h5>
                      <?php } else { ?>
                        <h5><?php echo $opcional->moneda." ".$opcional->precio ?></h5>
                        <p>
                          <?php echo ($lang == "es")?"Por persona":"" ?>
                          <?php echo ($lang == "en")?"Per person":"" ?>
                          <?php echo ($lang == "pt")?"Por pessoa":"" ?>
                        </p>
                      <?php } ?>
                    </div>
                  </label>
                  <div class="flight-details">
                    <div class="forms">
                      <div class="row">
                        <div class="col-md-6">
                          <label>Fecha de nacimiento</label>
                          <div class="view-inputs">
                            <input placeholder="DD" maxlength="2" size="2" class="ddmmyy" type="text">
                            <input placeholder="MM" maxlength="2" size="2" class="ddmmyy" type="text">
                            <input placeholder="YY" maxlength="4" size="4" class="ddmmyy" type="text">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label>
                            <?php echo ($lang == "es")?"Horario de llegada":"" ?>
                            <?php echo ($lang == "en")?"Arrival schedule":"" ?>
                            <?php echo ($lang == "pt")?"Hora de chegada":"" ?>
                          </label>
                          <div class="view-inputs">
                            <input placeholder="00" class="ddmmyy" type="text">
                            <input placeholder="00" class="ddmmyy" type="text">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <input placeholder="Empresa Aérea" type="text">
                          <i class="fa fa-plane" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-6">
                          <input placeholder="Número de vuelo" type="text">
                          <i class="fa fa-plane" aria-hidden="true"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
          <div class="btn-block">
            <input type="submit" onclick="proceder_pago()" name="submit" value="<?php echo ($lang == "es")?"Proceder al pago":"" ?><?php echo ($lang == "en")?"Proceed to payment":"" ?><?php echo ($lang == "pt")?"proceder ao pagamento":"" ?>" class="btn" />
          </div>
        </div>
        <div class="step_2" style="<?php echo ($step==2)?'display:block':'display:none'; ?>">
          <div class="payment-methods">
            <div class="heading2">
              <i class="fa fa-credit-card-alt" aria-hidden="true"></i> 
              <?php echo ($lang == "es")?"medios de pago":"" ?>
              <?php echo ($lang == "en")?"payment methods":"" ?>
              <?php echo ($lang == "pt")?"Meios de pagamento":"" ?>
            </div>
            <?php if ($transferencia_bancaria !== FALSE) { ?>
              <div class="block">
                <input onchange="cambiar_metodo_pago()" id="metodo_pago_banco" value="banco" name="payments" type="radio">
                <label for="metodo_pago_banco">
                  <span></span>
                  <div class="pull-left">
                    <h4>
                      <?php echo ($lang == "es")?"Pago Bancario":"" ?>
                      <?php echo ($lang == "en")?"wire transfer":"" ?>
                      <?php echo ($lang == "pt")?"transferência bancária":"" ?>
                    </h4>
                    <p><i class="fa fa-check" aria-hidden="true"></i> 
                      <?php echo ($lang == "es")?"Una vez procesada la solicitud recibirás los datos para depósito.":"" ?>
                      <?php echo ($lang == "en")?"Once the request has been processed you will receive the deposit information.":"" ?>
                      <?php echo ($lang == "pt")?"Uma vez processado o aplicativo receberá os dados para depósito.":"" ?>
                    </p>
                  </div>
                  <div class="pull-right"><img src="images/pay1.png" alt="pay1" /></div>
                </label>
              </div>
            <?php } ?>
            <?php if ($mp !== FALSE && sizeof($pedido->adultos)>0 && isset($currency) && $currency == "ARS") { ?>
              <div class="block">
                <input onchange="cambiar_metodo_pago()" id="metodo_pago_mp" value="mp" name="payments" type="radio">
                <label for="metodo_pago_mp">
                  <span></span>
                  <div class="pull-left">
                    <h4>Mercado Pago</h4>
                    <p><i class="fa fa-check" aria-hidden="true"></i> Paga en cuotas sin interés. <a target="_blank" href="https://www.mercadopago.com.ar/promociones">Ver Promociones</a></p>
                  </div>
                  <div class="pull-right"><img src="images/pay2.png" alt="pay2" /></div>
                </label>
              </div>
            <?php } ?>
            <?php if ($paypal_email !== FALSE && isset($currency) && $currency == "USD") { ?>
              <div class="block">
                <input onchange="cambiar_metodo_pago()" id="metodo_pago_paypal" value="paypal" name="payments" type="radio">
                <label for="metodo_pago_paypal">
                  <span></span>
                  <div class="pull-left">
                    <h4>Paypal</h4>
                    <p><i class="fa fa-check" aria-hidden="true"></i> 
                      <?php echo ($lang == "es")?"Pago con tarjeta de crédito desde cualquier parte del mundo.":"" ?>
                      <?php echo ($lang == "en")?"Payment by credit card from anywhere in the world.":"" ?>
                      <?php echo ($lang == "pt")?"Pagamento por cartão de crédito de qualquer lugar do mundo.":"" ?>
                    </p>
                  </div>
                  <div class="pull-right"><img src="images/pay3.png" alt="pay3" /></div>
                </label>
              </div>
            <?php } ?>
          </div>
          <div class="alerts">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <p>
              <?php echo ($lang == "es")?"Hemos revisado cuidadosamente la reserva, incluyendo fechas, nombres y horarios (sujeto a disponibilidad del prestador del servicio). Entiendo que debo presentarme con una identificación válida el dia del servicio y con el voucher que emitirá Southwind Experience.":"" ?>
              <?php echo ($lang == "en")?"We have carefully reviewed the reservation, including dates, names and schedules (subject to availability of the service provider). I understand that I must present myself with a valid identification on the day of service and with the voucher issued by Southwind Experience.":"" ?>
              <?php echo ($lang == "pt")?"Temos analisado cuidadosamente a reserva, incluindo datas, nomes e tempos (sujeito a disponibilidade do prestador de serviços). Eu entendo que devo apresentar-me com um ID válido no dia de serviço e o voucher emitirá Southwind Experience.":"" ?>
            </p>
          </div>
          <?php if (!empty($viaje->link_terminos)) { ?>
            <div class="terms">
              <input id="terminos" name="terminos" type="checkbox">
              <label for="terminos"><span></span> 
                <?php echo ($lang == "es")?"Al proceder con esta compra acepto estar en ":"" ?>
                <?php echo ($lang == "en")?"By proceeding with this purchase I agree to be in ":"" ?>
                <?php echo ($lang == "pt")?"Ao prosseguir com esta compra Eu concordo em estar em":"" ?>               
                <a href="<?php echo $viaje->link_terminos?>" target="_blank">
                  <?php echo ($lang == "es")?"condiciones físicas de salud y edad":"" ?>
                  <?php echo ($lang == "en")?"physical and health conditions":"" ?>
                  <?php echo ($lang == "pt")?"condições de saúde e idade físicas":"" ?>
                </a>
                <?php echo ($lang == "es")?"aptas para realizar la misma.":"" ?>
                <?php echo ($lang == "en")?"fit to perform the same.":"" ?>
                <?php echo ($lang == "pt")?"adequadas para ele.":"" ?>
              </label>
            </div>
          <?php } ?>
          <?php if (isset($link_terminos) && !empty($link_terminos)) { ?>
            <div class="terms">
              <input id="terms" name="terms" type="checkbox">
              <label for="terms"><span></span> 
                <?php echo ($lang == "es")?"Al proceder con esta compra acepto los ":"" ?>
                <?php echo ($lang == "en")?"By proceeding with this purchase I accept the ":"" ?>
                <?php echo ($lang == "pt")?"Para prosseguir com esta compra Eu aceito os ":"" ?>               
                <a target="_blank" href="<?php echo $link_terminos ?>">
                  <?php echo ($lang == "es")?"términos y condiciones":"" ?>
                  <?php echo ($lang == "en")?"terms and conditions":"" ?>
                  <?php echo ($lang == "pt")?"termos e condições":"" ?>
                </a>
                <?php echo ($lang == "es")?"de contrataci&oacute;n":"" ?>
                <?php echo ($lang == "en")?"of hiring.":"" ?>
                <?php echo ($lang == "pt")?"de emprego.":"" ?>
              </label>
            </div>
          <?php } ?>

          <?php if ($mp !== FALSE && sizeof($pedido->adultos)>0) { ?>
            <div class="btn-block finalizar_cont" id="finalizar_mp">
              <?php
              try {
                if (!is_numeric($pedido->total_general)) $pedido->total_general = 0;
                $items[] = array(
                  "id"=>$viaje->id,
                  "title"=>$viaje->nombre,
                  "currency_id"=>"ARS",
                  "quantity"=>1,
                  "unit_price"=>((float)$pedido->total_general) + 0,
                );
                $cliente = $pedido->adultos[0];

                // Creamos la preferencia de Mercado Pago
                $preference_data = array(
                  "items" => $items,
                  "payer" => array(
                    "name" => $cliente->nombre,
                    "email" => $cliente->email,
                    ),
                  "back_urls" => array(
                    "success" => mklink("web/reserva/?id=".$viaje->id."&fecha=".$fecha_reserva."&step=3&result=success&type=mp"),
                    "failure" => mklink("web/reserva/?id=".$viaje->id."&fecha=".$fecha_reserva."&step=3&result=failure&type=mp"),
                    "pending" => mklink("web/reserva/?id=".$viaje->id."&fecha=".$fecha_reserva."&step=3&result=pending&type=mp"),
                    ),
                  "auto_return" => "approved",
                  "notification_url" => mklink("ipn-viaje-mp/"),
                  "external_reference" => $pedido->id,
                );
                file_put_contents("salida_viaje.txt", print_r($pedido,TRUE));
                $preference = $mp->create_preference($preference_data); ?>
                <a onclick="pagar_mercado_pago()" class="btn upper" href="javascript:void(0)">
                  <?php echo ($lang == "es")?"Proceder al pago":"" ?>
                  <?php echo ($lang == "en")?"Proceed to payment":"" ?>
                  <?php echo ($lang == "pt")?"proceder ao pagamento":"" ?>
                </a>
              <?php } catch(Exception $e) { } ?>
            </div>
          <?php } ?>

          <?php if ($paypal_email !== FALSE && sizeof($pedido->adultos)>0) { 
            $cliente = $pedido->adultos[0]; ?>


            <form onsubmit="return validar_pago()" id="finalizar_paypal" class="btn-block finalizar_cont" action="https://www.paypal.com/cgi-bin/webscr" method="post">
              <input type="hidden" name="business" value="<?php echo $paypal_email ?>">
              <INPUT TYPE="hidden" name="cmd" value="_xclick">
              <INPUT TYPE="hidden" name="custom" value="<?php echo $pedido->id ?>">
              <INPUT TYPE="hidden" name="charset" value="utf-8">
              <INPUT TYPE="hidden" NAME="return" value="<?php echo mklink("web/reserva/?id=".$viaje->id."&fecha=".$fecha_reserva."&step=3&result=success&type=paypal"); ?>">
              <input type="hidden" name="quantity" value="1">
              <input type="hidden" name="item_name" value="<?php echo $viaje->nombre ?>">
              <input type="hidden" name="item_number" value="<?php echo $viaje->id ?>">
              <input type="hidden" name="amount" value="<?php echo $pedido->total_general ?>">
              <?php /*
              <input type="hidden" name="first_name" value="<?php echo $cliente->nombre ?>">
              <input type="hidden" name="email" value="<?php echo $cliente->email ?>">
              */ ?>

              <INPUT TYPE="hidden" NAME="notify_url" value="<?php echo mklink("ipn-viaje-paypal/") ?>">

              <!-- URL para seguir comprando -->
              <INPUT TYPE="hidden" NAME="shopping_url" value="<?php echo mklink("/"); ?>">

              <!-- URL cuando se cancela el pago -->
              <INPUT TYPE="hidden" NAME="cancel_return" value="<?php echo current_url(); ?>">

              <!-- No debe pedir un envio -->
              <INPUT TYPE="hidden" NAME="no_shipping" value="1">
              
              <input type="submit" name="submit" class="btn upper" value="<?php echo ($lang == "es")?"Proceder al pago":"" ?><?php echo ($lang == "en")?"Proceed to payment":"" ?><?php echo ($lang == "pt")?"proceder ao pagamento":"" ?>" />
            </form>
          <?php } ?>

          <?php if ($transferencia_bancaria !== FALSE) { ?>
            <div class="btn-block finalizar_cont" id="finalizar_banco">
              <button onclick="pagar_transferencia_bancaria()" class="btn upper">
                <?php echo ($lang == "es")?"Proceder al pago":"" ?>
                <?php echo ($lang == "en")?"Proceed to payment":"" ?>
                <?php echo ($lang == "pt")?"proceder ao pagamento":"" ?>
              </button>
            </div>
          <?php } ?>
        </div>
        <div class="step_3" style="<?php echo ($step==3)?'display:block':'display:none'; ?>">
          <?php if (isset($get_params["result"]) && $get_params["result"] == "success") { ?>
            <h1 style="font-weight: bold;text-transform: none;font-size: 40px;text-align: center;padding-top: 30px;">
              <?php echo ($lang == "es")?"Muchas gracias por su compra!!":"" ?>
              <?php echo ($lang == "en")?"Thank you for your purchase!!":"" ?>
              <?php echo ($lang == "pt")?"Muito obrigado por sua compra!!":"" ?>              
            </h1>
            <p style="padding: 0px 30px; text-align: center;font-size: 20px;margin-top: 20px;">
              <?php if (isset($get_params["type"]) && $get_params["type"] == "transfer") { ?>
                <?php echo ($lang == "es")?"Hemos enviado un correo a su casilla de email con los datos de pago. Por favor revise su bandeja de entrada.":"" ?>
                <?php echo ($lang == "en")?"We have sent an email to your email box with payment details. Please check your inbox.":"" ?>
                <?php echo ($lang == "pt")?"Enviamos um e-mail para sua caixa de e-mail com os dados de pagamento. Por favor, verifique sua caixa de entrada.":"" ?>
              <?php } else { ?>
                <?php echo ($lang == "es")?"Su compra ha sido realizada correctamente. Hemos enviado un correo a su casilla de email con los datos de la misma. Por favor revise su bandeja de entrada.":"" ?>
                <?php echo ($lang == "en")?"Your purchase has been completed successfully. We have sent an email to your email box with the information of the same. Please check your inbox.":"" ?>
                <?php echo ($lang == "pt")?"A sua compra foi feita corretamente. Enviamos um e-mail para sua caixa de e-mail com os dados a partir dele. Por favor, verifique sua caixa de entrada.":"" ?>
              <?php } ?>
            </p>
            <div style="text-align:center"><a class="btn" href="<?php echo mklink("/"); ?>" style="margin-top: 20px">
              <?php echo ($lang == "es")?"Volver al inicio":"" ?>
              <?php echo ($lang == "en")?"Go to home":"" ?>
              <?php echo ($lang == "pt")?"voltar ao início":"" ?>              
            </a></div>
          <?php } else if (isset($get_params["result"]) && $get_params["result"] == "failure") { ?>
            <h1 style="font-weight: bold;text-transform: none;font-size: 40px;text-align: center;padding-top: 30px;">
              <?php echo ($lang == "es")?"La compra ha sido cancelada":"" ?>
              <?php echo ($lang == "en")?"Your purchase has been canceled":"" ?>
              <?php echo ($lang == "pt")?"A compra foi cancelada":"" ?>
            </h1>
            <p style="padding: 0px 30px; text-align: center;font-size: 20px;margin-top: 20px;">
              <?php echo ($lang == "es")?"Su compra no se ha concretado. Si desea ayuda para terminar el proceso, por favor comuniquese con nosotros.":"" ?>
              <?php echo ($lang == "en")?"Your purchase has not finalized. If you would like help completing the process, please contact us.":"" ?>
              <?php echo ($lang == "pt")?"Sua compra não se concretizou. Se você quiser ajudar a completar o processo, entre em contato conosco.":"" ?>
            </p>
            <div style="text-align:center"><a class="btn" href="<?php echo mklink("/"); ?>" style="margin-top: 20px">
              <?php echo ($lang == "es")?"Volver al inicio":"" ?>
              <?php echo ($lang == "en")?"Go to home":"" ?>
              <?php echo ($lang == "pt")?"voltar ao início":"" ?>              
            </a></div>
          <?php } else if (isset($get_params["result"]) && $get_params["result"] == "pending") { ?>
            <h1 style="font-weight: bold;text-transform: none;font-size: 40px;text-align: center;padding-top: 30px;">
              <?php echo ($lang == "es")?"Muchas gracias por su compra!!":"" ?>
              <?php echo ($lang == "en")?"Thank you for your purchase!!":"" ?>
              <?php echo ($lang == "pt")?"Muito obrigado por sua compra!!":"" ?>              
            </h1>
            <p style="padding: 0px 30px; text-align: center;font-size: 20px;margin-top: 20px;">
              <?php echo ($lang == "es")?"Su compra permanecerá pendiente hasta que se realice el pago. Si desea ayuda para terminar el proceso, por favor comuniquese con nosotros.":"" ?>
              <?php echo ($lang == "en")?"Your purchase will remain pending until payment is made. If you would like help completing the process, please contact us.":"" ?>
              <?php echo ($lang == "pt")?"Sua compra permanecerá pendente até que o pagamento é feito. Se você quiser ajudar a completar o processo, entre em contato conosco.":"" ?>              
            </p>
            <div style="text-align:center"><a class="btn" href="<?php echo mklink("/"); ?>" style="margin-top: 20px">
              <?php echo ($lang == "es")?"Volver al inicio":"" ?>
              <?php echo ($lang == "en")?"Go to home":"" ?>
              <?php echo ($lang == "pt")?"voltar ao início":"" ?>
            </a></div>
          <?php } ?>
        </div>
      </div>
      <div class="secondary">
        <div id="rightbar">
          <div class="stotal">
            <h4><?php echo $viaje->nombre ?></h4>
            <?php if (!empty($viaje->custom_1)) { ?>
              <p class="info">
                <strong>
                  <?php echo ($lang == "es")?"Dificultad:":"" ?>
                  <?php echo ($lang == "en")?"Difficulty:":"" ?>
                  <?php echo ($lang == "pt")?"Dificuldade:":"" ?>
                </strong> 
                <?php echo $viaje->custom_1 ?>
              </p>
            <?php } ?>
            <?php if (!empty($viaje->custom_2)) { ?>
              <p class="info">
                <strong>
                  <?php echo ($lang == "es")?"Duración:":"" ?>
                  <?php echo ($lang == "en")?"Duration:":"" ?>
                  <?php echo ($lang == "pt")?"Duração:":"" ?>
                </strong> 
                <?php echo $viaje->custom_2 ?>
              </p>
            <?php } ?>
            <?php if (!empty($pedido->observaciones)) { ?>
              <p class="info">
                <strong>
                  <?php echo ($lang == "es")?"Horarios:":"" ?>
                  <?php echo ($lang == "en")?"Schedules:":"" ?>
                  <?php echo ($lang == "pt")?"Horário:":"" ?>
                </strong> 
                <?php echo $pedido->observaciones ?>
              </p>
            <?php } ?>
            
            <p class="info-last">
              <?php if ( ($viaje->custom_6 == "Si") || ($viaje->custom_7 == "Si")) { ?>
                <strong>
                  <?php echo ($lang == "es")?"Idiomas:":"" ?>
                  <?php echo ($lang == "en")?"Languages:":"" ?>
                  <?php echo ($lang == "pt")?"Línguas:":"" ?>
                </strong> 
              <?php } ?>
              <?php if ($viaje->custom_6 == "Si") { ?>
                <a href="javascript:void(0)"><img src="images/flag1.png" alt="flag1" /></a> 
              <?php } ?>
              <?php if ($viaje->custom_7 == "Si") { ?>
                <a href="javascript:void(0)"><img src="images/flag2.png" alt="flag2" /></a>
              <?php } ?>
            </p>
            <div id="precios_viaje"></div>
          </div>
          <div id="opcionales"></div>
          <div style="display: none;" class="gtotal">
            <div class="pull-left">
              Total:
            </div>
            <div class="pull-right">
              <h5 id="total_general"></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>