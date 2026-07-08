<button type="button" class="btn btn-primary dn launch-payment-modal" data-toggle="modal" data-target="#paymentModal"></button>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>

<script>
function openIframePaycomet(periodicity,user_id) {
  $.ajax({
    "url": "/sistema/paycomet/function/crear_suscripcion_registro_iframe",
    "dataType": "json",
    "type":"post",
    "data":{
      "periodicity":periodicity,
      "user_id":user_id,
    },
    "success": function(r) {
      if (r.error == 1) {
        alert(r.mensaje);
        return;
      }
      $("#paymentModal .modal-body").html("<iframe style='width:100%; border:none; height:320px;' src='"+r.redirect+"'></iframe>");
      $(".launch-payment-modal").trigger("click");
    },
  });
}
</script>