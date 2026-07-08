<script type="text/template" id="sindi_auditor_limites_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-user-md icono_principal"></i>Auditor / <b>Limites</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel panel-default mb0">
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">

          <li id="tab_consulta_link" class="active">
            <a href="#tab1_limites" role="tab" data-toggle="tab">
            <i class="fa text-danger fa-ban m-r-xs"></i>
              Consultas
            </a>
          </li>
          <li id="tab_recetario_link">
            <a href="#tab_recetarios" role="tab" data-toggle="tab">
              <i class="fa text-danger fa-ban m-r-xs"></i>
              Recetarios
            </a>
          </li>
          <li id="tab_practica_link">
            <a href="#tab_practicas" role="tab" data-toggle="tab">
              <i class="fa text-danger fa-ban m-r-xs"></i>
              Practicas
            </a>
          </li>
        </ul>

        <div class="tab-content">

          <div id="tab1_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0 active">
          </div>

          <div id="tab_recetarios" class="tab-pane panel-body pt0 pr0 pl0 pb0">
            <ul class="nav nav-tabs nav-tabs-2" role="tablist">
              <li id="ra_link" class="active">
                <a href="#tab2_limites" role="tab" data-toggle="tab">
                  <i class="fa text-success fa-file-text-o m-r-xs"></i>
                  Recetarios
                </a>
              </li>
              <li id="ra70_link">
                <a href="#tab3_limites" role="tab" data-toggle="tab">
                  <i class="fa text-success fa-file-text-o m-r-xs"></i>
                  Recetarios 70%
                </a>
              </li>
              <li id="ra100_link">
                <a href="#tab4_limites" role="tab" data-toggle="tab">
                  <i class="fa text-success fa-file-text-o m-r-xs"></i>
                  Recetarios 100%
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="tab2_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0 active">
              </div>
              <div id="tab3_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0">
              </div>
              <div id="tab4_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0">
              </div>
            </div>
          </div>

          <div id="tab_practicas" class="tab-pane panel-body pt0 pr0 pl0 pb0">
            <ul class="nav nav-tabs nav-tabs-2" role="tablist">
              <li id="la_link" class="active">
                <a href="#tab5_limites" role="tab" data-toggle="tab">
                  <i class="fa text-primary fa-file-text-o m-r-xs"></i>
                  Por Afiliados
                </a>
              </li>
              <li id="lce_link">
                <a href="#tab6_limites" role="tab" data-toggle="tab">
                  <i class="fa text-primary fa-file-text-o m-r-xs"></i>
                  Por Condicion Especial
                </a>
              </li>
              <li id="ltp_link">
                <a href="#tab7_limites" role="tab" data-toggle="tab">
                  <i class="fa text-primary fa-file-text-o m-r-xs"></i>
                  Por Tipo de Practica
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="tab5_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0 active">
              </div>
              <div id="tab6_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0">
              </div>
              <div id="tab7_limites" class="tab-pane panel-body pt0 pr0 pl0 pb0">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</script>