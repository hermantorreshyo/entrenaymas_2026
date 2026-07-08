<script type="text/template" id="empresa_colvar_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / 
    <b>Mi Empresa</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">
        <div class="detalle_texto">
          <?php 
          $clave = "Configuracion / Mi Empresa / Colvar";
          echo lang(array(
            "es"=>(isset($videos[$clave]["nombre_es"]) ? $videos[$clave]["nombre_es"] : "" ),
            "en"=>(isset($videos[$clave]["nombre_en"]) ? $videos[$clave]["nombre_en"] : "" ),
          )); ?>
        </div>
        <div class="detalle_texto_info text-muted">
          <?php echo lang(array(
            "es"=>(isset($videos[$clave]["texto_es"]) ? $videos[$clave]["texto_es"] : "" ),
            "en"=>(isset($videos[$clave]["texto_en"]) ? $videos[$clave]["texto_en"] : "" ),
          )); ?>
        </div>
        <?php if (isset($videos[$clave]["video_es"]) && !empty($videos[$clave]["video_es"])) { ?>
          <a onclick="workspace.open_video(this)" data-iframe='<?php echo $videos[$clave]["video_es"] ?>'>
            Ver video
          </a>
        <?php } ?>
      </div>

      <div class="col-md-8">

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre de la instituci&oacute;n</label>
                <input type="text" name="nombre" class="form-control" id="empresas_detalle_nombre" value="<%= nombre %>"/>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono</label>
                    <input type="text" name="telefono" class="form-control" id="empresas_detalle_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" name="email" class="form-control" id="empresas_detalle_email" value="<%= email %>"/>
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
                <label class="control-label">Logos e im&aacute;genes</label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Suba el logo de su empresa.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <% if (id != undefined) { %>
                <div class="form-group">
                  <?php
                  single_upload(array(
                  "name"=>"logo",
                  "label"=>"Encabezado de informes",
                  "url"=>"empresas/function/save_image/",
                  "resizable"=>1,
                  "description"=>"Utilizado en listados, reportes, etc. Tama&ntilde;o recomendado: 450 x 280 p&iacute;xeles"
                  )); ?>
                </div>
                <div class="form-group">
                  <?php
                  single_upload(array(
                  "name"=>"path",
                  "label"=>"Foto de perfil de sistema",
                  "url"=>"empresas/function/save_image/",
                  "width"=>400,
                  "height"=>400,
                  "description"=>"Utilizado como imagen de perfil del sistema. Tama&ntilde;o recomendado: 200 x 200 p&iacute;xeles"
                  )); ?>
                </div>
              <% } %>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">Datos impositivos</label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Ingrese su raz&oacute;n social, CUIT, tipo de IVA, etc.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Raz&oacute;n Social</label>
                <input type="text" name="razon_social" class="form-control" id="empresas_detalle_razon_social" value="<%= razon_social %>"/>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo de contribuyente</label>
                    <select class="form-control" name="tipo_contribuyente" id="empresas_detalle_tipo_contribuyente">
                      <option value="2" <%= (id_tipo_contribuyente == 2) ? "selected": "" %>>Monotributo</option>
                      <option value="1" <%= (id_tipo_contribuyente == 1) ? "selected": "" %>>Responsable Inscripto</option>
                      <option value="3" <%= (id_tipo_contribuyente == 3) ? "selected": "" %>>Exento</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">CUIT</label>
                    <input type="text" name="cuit" class="form-control" id="empresas_detalle_cuit" value="<%= cuit %>"/>
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
                <label class="control-label">Configuraci&oacute;n avanzada</label>
                <a id="expand_mapa" class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  Par&aacute;metros especificos de la institucion.
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Perfil de la institucion</label>
                <select class="form-control" name="perfil_escuela" id="empresas_detalle_perfil_escuela">
                  <option value="0" <%= (perfil_escuela == 0) ? "selected": "" %>>Primaria</option>
                  <option value="1" <%= (perfil_escuela == 1) ? "selected": "" %>>Secundaria</option>
                  <option value="2" <%= (perfil_escuela == 2) ? "selected": "" %>>Instituto terciario</option>
                  <option value="3" <%= (perfil_escuela == 3) ? "selected": "" %>>Universidad</option>
                </select>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="empresas_detalle_asistencia_docente_por_materia" name="asistencia_docente_por_materia" class="checkbox" value="1" <%= (asistencia_docente_por_materia == 1)?"checked":"" %> >
                    <i></i>
                    Cargar la asistencia del docente por comision y materia, en vez de solo por fecha.
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="empresas_detalle_asistencia_alumno_por_materia" name="asistencia_alumno_por_materia" class="checkbox" value="1" <%= (asistencia_alumno_por_materia == 1)?"checked":"" %> >
                    <i></i>
                    Cargar la asistencia de los alumnos por comision y materia, en vez de solo por fecha.
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>

  </div>
</div>
</script>