<article class="card">
  <div class="card-body p-5">
    <form role="form" id="paycometPaymentForm" action="/sistema/paycomet/function/crear_suscripcion_registro/"  method="POST">
      <input type="hidden" id="user_id" name="user_id" value="">
      <input type="hidden" id="periodicity" name="periodicity" value="30">
      <input type="hidden" data-paycomet="jetID" value="<?php echo PAYCOMET_JET_ID ?>">
      <div class="form-group">
        <label for="username">Nombre Completo (como figura en tarjeta)</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-user"></i></span>
          </div>
          <input type="text" class="form-control" name="username" data-paycomet="cardHolderName" placeholder="" required="">
        </div> <!-- input-group.// -->
      </div> <!-- form-group.// -->

      <div class="form-group">
        <label for="cardNumber">Número de Tarjeta</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
          </div>
          <div id="paycomet-pan" style="padding:0px; height:48px;flex: 1;"></div>
          <input paycomet-style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;width: 96%; height: 42px; font-size: 16px; padding-left: 11px !important; padding-top: 4px; border: 1px solid rgb(224, 224, 224); background: no-repeat;" paycomet-name="pan" paycomet-placeholder="Introduce tu tarjeta...">
        </div> <!-- input-group.// -->
      </div> <!-- form-group.// -->      

      <div class="row">
        <div class="col-sm-8">
          <div class="form-group">
            <label><span class="hidden-xs">Vencimiento</span> </label>
            <div class="form-inline">
              <select class="form-control" style="width:45%" data-paycomet="dateMonth">
                <option>MM</option>
                <option value="01">01 - Enero</option>
                <option value="02">02 - Febrero</option>
                <option value="03">03 - Marzo</option>
                <option value="04">04 - Abril</option>
                <option value="05">05 - Mayo</option>
                <option value="06">06 - Junio</option>
                <option value="07">07 - Julio</option>
                <option value="08">08 - Agosto</option>
                <option value="09">09 - Septiembre</option>
                <option value="10">10 - Octubre</option>
                <option value="11">11 - Noviembre</option>
                <option value="12">12 - Diciembre</option>
              </select>
              <span style="width:10%; text-align: center"> / </span>
              <select class="form-control" style="width:45%" data-paycomet="dateYear">
                <option>YY</option>
                <option value="23">2023</option>
                <option value="24">2024</option>
                <option value="25">2025</option>
                <option value="26">2026</option>
                <option value="27">2027</option>
                <option value="28">2028</option>
                <option value="29">2029</option>
                <option value="30">2030</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <label data-toggle="tooltip" title=""
              data-original-title="3 digits code on back side of the card">CVV <i
                class="fa fa-question-circle"></i></label>
            <div id="paycomet-cvc2" style="height: 48px; padding:0px;"></div>
            <input paycomet-name="cvc2" paycomet-style="border-radius: 8px; border: 1px solid rgb(224, 224, 224); background: no-repeat; width: 90%; height: 42px; font-size:16px; padding-left:7px;" paycomet-placeholder="CVV2" class="form-control" required="" type="text">
          </div> <!-- form-group.// -->
        </div>

      </div> <!-- row.// -->
      <button class="subscribe btn btn-primary btn-block" type="submit"> Pagar </button>
    </form>
    <div id="paymentErrorMsg">

    </div>
  </div> <!-- card-body.// -->
</article> <!-- card.// -->
<script src="https://api.paycomet.com/gateway/paycomet.jetiframe.js?lang=es"></script>