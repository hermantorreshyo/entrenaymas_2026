<script type="text/template" id="sindi_bonos_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text-o icono_principal"></i><b>Bonos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-barcode" aria-hidden="true"></i> Codigo</span>
              <input id="buscador_codigo" autocomplete="off" type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-7">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i> Nombre</span>
              <input id="buscador_nombre" autocomplete="off" type="text" class="form-control no-spinner">
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-info btnbuscar btn-block"><i class="fa fa-search"></i> Buscar</button>
          </div>
        </div>    
      </div>  
    </div>

    <div class="panel panel-default mb0">

        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li id="consultas_link" class="active">
            <a href="#consultas_tab" role="tab" data-toggle="tab">
              <i class="fa text-info fa-file-text-o m-r-xs"></i>
              Consultas
            </a>
          </li>
          <li id="practicas_link">
            <a href="#practicas_tab" role="tab" data-toggle="tab">
              <i class="fa text-primary fa-file-text-o m-r-xs"></i>
              Practicas
            </a>
          </li>
          <li id="reintegros_link">
            <a href="#reintegros_tab" role="tab" data-toggle="tab">
              <i class="fa text-warning fa-file-text-o m-r-xs"></i>
              Reintegros
            </a>
          </li>
          <li id="recetarios_link">
            <a href="#recetarios_tab" role="tab" data-toggle="tab">
              <i class="fa text-success fa-file-text-o m-r-xs"></i>
              Recetarios
            </a>
          </li>
        </ul>

        <div class="tab-content">

          <div id="consultas_tab" class="tab-pane active">
          </div>

          <div id="practicas_tab" class="tab-pane">
          </div>

          <div id="reintegros_tab" class="tab-pane">
          </div>

          <div id="recetarios_tab" class="tab-pane">
          </div>

        </div>


    </div>
  </div>  
</script>