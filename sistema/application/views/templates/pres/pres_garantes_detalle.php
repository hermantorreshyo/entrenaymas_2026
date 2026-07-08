<div class="row">
  <div class="col-md-4">
    <div class="detalle_texto">Informaci&oacute;n general</div>
  </div>
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Apellido</label>
                <% if (edicion) { %>
                  <input type="text" required name="apellido" id="pres_garantes_apellido" value="<%= apellido %>" class="form-control"/>
                <% } else { %>
                  <span><%= apellido %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <% if (edicion) { %>
                  <input type="text" required name="nombre" id="pres_garantes_nombre" value="<%= nombre %>" class="form-control"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Fecha Nacimiento</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="pres_garantes_fecha_nac" name="fecha_nac" value="<%= fecha_nac %>"/>
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>        
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Sexo</label>
                  <select name="sexo" <%= (edicion)?"":"disabled" %> class="form-control" id="pres_garantes_sexo">
                    <option <%= (sexo == "M") ? "selected":"" %> value="M">Masculino</option>
                    <option <%= (sexo == "F") ? "selected":"" %> value="F">Femenino</option>
                  </select>    
              </div>  
            </div>
            <div class="col-md-6">
              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Foto",
                "url"=>"/sistema/garantes/function/save_image/",
                "width"=>(isset($empresa->config["garante_image_width"]) ? $empresa->config["garante_image_width"] : 256),
                "height"=>(isset($empresa->config["garante_image_height"]) ? $empresa->config["garante_image_height"] : 256),
                "quality"=>(isset($empresa->config["garante_image_quality"]) ? $empresa->config["garante_image_quality"] : 0.92),
                "thumbnail_width"=>(isset($empresa->config["garante_thumbnail_width"]) ? $empresa->config["garante_thumbnail_width"] : 0),
                "thumbnail_height"=>(isset($empresa->config["garante_thumbnail_height"]) ? $empresa->config["garante_thumbnail_height"] : 0),
              )); ?>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Tipo de Documento</label>
                  <select <%= (edicion)?"":"disabled" %> class="form-control" id="pres_garantes_tipo_documento">
                    <option <%= (id_tipo_documento == 96) ? "selected":"" %> value="96">DNI</option>
                    <option <%= (id_tipo_documento == 89) ? "selected":"" %> value="89">Libreta Enrolamiento</option>
                    <option <%= (id_tipo_documento == 90) ? "selected":"" %> value="90">Libreta Civica</option>
                    <option <%= (id_tipo_documento == 94) ? "selected":"" %> value="94">Pasaporte</option>
                    <option <%= (id_tipo_documento == 99) ? "selected":"" %> value="99">Sin identificacion</option>
                  </select>    
              </div>  
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Numero Documento </label>
                <% if (edicion) { %>
                  <input type="text" name="documento" class="form-control" id="pres_garantes_documento" value="<%= documento %>"/>
                <% } else { %>
                  <span><%= documento %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">CUIL </label>
                <% if (edicion) { %>
                  <input type="text" name="cuit" class="form-control" id="pres_garantes_cuit" value="<%= cuit %>"/>
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
                "es"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu garante.",
                "en"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu garante.",
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
                <input type="text" value="<%= localidad %>" id="pres_garantes_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">C&oacute;digo Postal</label>
                <input type="text" name="codigo_postal" value="<%= codigo_postal %>" id="pres_garantes_codigo_postal" class="form-control"/>
              </div>  
            </div>
          </div>
          <div class="form-group">
            <label class="control-label">Direccion </label>
            <% if (edicion) { %>
              <input type="text" name="direccion" class="form-control" id="pres_garantes_direccion" value="<%= direccion %>"/>
            <% } else { %>
              <span><%= direccion %></span>
            <% } %>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Tel&eacute;fonos</label>
                <input type="text" name="telefono" placeholder="Telefono 1" class="form-control" id="pres_garantes_telefono" value="<%= telefono %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <input type="text" name="telefono_obs" placeholder="Ej: Celular" class="form-control" id="pres_garantes_telefono_obs" value="<%= telefono_obs %>"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_2" placeholder="Tel&eacute;fono 2" class="form-control" id="pres_garantes_telefono_2" value="<%= telefono_2 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_2_obs" placeholder="Ej: Casa" class="form-control" id="pres_garantes_telefono_2_obs" value="<%= telefono_2_obs %>"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_3" placeholder="Tel&eacute;fono 3" class="form-control" id="pres_garantes_telefono_3" value="<%= telefono_3 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_3_obs" placeholder="Ej: Trabajo" class="form-control" id="pres_garantes_telefono_3_obs" value="<%= telefono_3_obs %>"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_4" placeholder="Tel&eacute;fono 4" class="form-control" id="pres_garantes_telefono_4" value="<%= telefono_4 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_4_obs" placeholder="Ej: Casa de la madre" class="form-control" id="pres_garantes_telefono_4_obs" value="<%= telefono_4_obs %>"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_5" placeholder="Tel&eacute;fono 5" class="form-control" id="pres_garantes_telefono_5" value="<%= telefono_5 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_5_obs" placeholder="Ej: Casa del padre" class="form-control" id="pres_garantes_telefono_5_obs" value="<%= telefono_5_obs %>"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_6" placeholder="Tel&eacute;fono 6" class="form-control" id="pres_garantes_telefono_6" value="<%= telefono_6 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="telefono_6_obs" placeholder="Ej: Otro telefono particular" class="form-control" id="pres_garantes_telefono_6_obs" value="<%= telefono_6_obs %>"/>
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
                "es"=>"Datos laborales",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ Ver opciones",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Informaci&oacute;n relacionada a la historia laboral del garante.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand" style="<%= (estados_laborales.length > 0)?'display:block':'' %>">
        <div class="padder">
          <div class="">
            <div class="clearfix">
              <button class="btn btn-info nuevo_estado_laboral">+ Agregar</button>
            </div>
            <div id="pres_garante_estados_laborales" class="mt10 b-a table-responsive"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group mb0 clearfix">
            <label class="control-label">
              Datos bancarios
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ Ver opciones",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Informaci&oacute;n asociada a la cuenta bancaria del garante.",
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
                <label class="control-label">Banco</label>
                <input type="text" name="banco" value="<%= banco %>" id="pres_garantes_banco" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">CBU</label>
                <input type="text" name="cbu" value="<%= cbu %>" id="pres_garantes_cbu" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Tarjeta de credito</label>
                <input type="text" name="tarjeta" value="<%= tarjeta %>" id="pres_garantes_tarjeta" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Emisor de tarjeta</label>
                <input type="text" name="emisor_tarjeta" value="<%= emisor_tarjeta %>" id="pres_garantes_emisor_tarjeta" class="form-control"/>
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
                "es"=>"Documentacion asociada",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ Ver opciones",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Agregue la documentacion requerida para el garante.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand" style="<%= (documentaciones.length > 0)?'display:block':'' %>">
        <div class="padder">
          <div class="">
            <div class="clearfix">
              <button class="btn btn-info nueva_documentacion">+ Agregar</button>
            </div>
            <div id="pres_garante_documentaciones" class="mt10"></div>
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
                "es"=>"Observaciones",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ Ver opciones",
              )); ?>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body expand" style="<%= (!isEmpty(observaciones))?'display:block':'' %>">
        <div class="padder">
          <div class="form-group">
            <% if (edicion) { %>
              <textarea placeholder="Escriba aqui otros datos de contacto o notas de su garante..." style="height:100px" class="form-control" name="observaciones" id="garante_observaciones"><%= observaciones %></textarea>
            <% } else { %>
              <span><%= observaciones %></span>
            <% } %>
          </div>

          <div class="form-group">
            <% if (edicion) { %>
              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> ><i></i>
                  El garante est&aacute; activo.
                </label>
              </div>
            <% } else { %>
              <span><%= ((activo==0) ? "Garante inactivo" : "Garante activo") %></span>
            <% } %>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>