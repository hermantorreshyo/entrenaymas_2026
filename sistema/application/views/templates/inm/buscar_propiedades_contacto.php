<div class="panel-heading clearfix">
  <div class="row pl10 pr10 pt10">
    <div class="col-md-3 col-sm-6 col-xs-12 mh50 pr5 pl5">
      <div class="form-group">
        <div class="input-group">
          <select multiple="multiple" class="form-control no-model" id="propiedades_buscar_localidades"></select>
          <span class="input-group-btn">
            <div class="btn-group dropdown pull-right ml0">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <% for (var i=0;i< Math.min(window.localidades.length,10); i++) { %>
                  <% var localidad = window.localidades[i] %>
                  <li><a href="javascript:void(0)" data-id="<%= localidad.id %>" data-nombre="<%= localidad.nombre %>" class="setear_localidad"><%= localidad.nombre %> (<%= localidad.cantidad %>)</a></li>
                <% } %>
              </ul>
            </div>
          </span>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12 mh50 pr5 pl5">
      <div class="form-group">
        <select style="width: 100%" id="propiedades_buscar_tipos_operacion">
          <% for(var i=0;i< window.tipos_operacion.length;i++) { %>
            <% var o = tipos_operacion[i]; %>
            <option <%= (window.propiedades_id_tipo_operacion == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
          <% } %>
        </select>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 mh50 pr5 pl5">
      <div class="form-group">
        <div class="input-group">
          <select style="width: 100%" id="propiedades_buscar_tipos_inmueble">
            <% for(var i=0;i< window.tipos_inmueble.length;i++) { %>
              <% var o = tipos_inmueble[i]; %>
              <option <%= (window.propiedades_id_tipo_inmueble == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
            <% } %>
          </select>
          <span class="input-group-btn">
            <button data-toggle="tooltip" title="Apto para cr&eacute;dito hipotecario" id="propiedades_buscar_apto_banco" class="btn btn-default <%= (window.propiedades_apto_banco == 1)?'btn-info':'' %>">
              <i class="fa fa-bank"></i>
            </button>
          </span>
          <span class="input-group-btn">
            <button data-toggle="tooltip" title="Acepta permuta" id="propiedades_buscar_acepta_permuta" class="btn btn-default ml0 <%= (window.propiedades_acepta_permuta == 1)?'btn-info':'' %>">
              <i class="fa fa-exchange"></i>
            </button>
          </span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 mh50 pr5 pl5">
      <div class="form-group">
        <div class="input-group">
          <span class="input-group-btn">
            <div class="btn-group dropdown ml0 mr0">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <span id="propiedades_buscar_monto_moneda"><%= window.propiedades_monto_moneda %></span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="setear_moneda">$</a></li>
                <li><a href="javascript:void(0)" class="setear_moneda">U$D</a></li>
              </ul>
            </div>
          </span>
          <span class="input-group-btn w40p">
            <input type="text" value="<%= window.propiedades_monto %>" placeholder="Desde" id="propiedades_buscar_monto" class="form-control no-model">
          </span>
          <span class="input-group-btn w40p">
            <input type="text" value="<%= window.propiedades_monto_2 %>" placeholder="Hasta" id="propiedades_buscar_monto_2" class="form-control no-model">
          </span>
          <span class="input-group-btn">
            <button class="btn btn-success guardar_busqueda"><span class="hidden-xs">Guardar B&uacute;squeda</span><i class="show-xs fa fa-floppy-o"></i></button>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix">
    <button class="btn-advanced-search advanced-search-btn"><i class="fa fa-plus-circle"></i><span><?php echo lang(array("es"=>"M&aacute;s Filtros","en"=>"More Filters")); ?></span></button>
  </div>
</div>
<% var display = (window.propiedades_id_tipo_inmueble != 0 || !isEmpty(window.propiedades_direccion) || !isEmpty(window.propiedades_monto) || window.propiedades_apto_banco == 1 || !isEmpty(window.propiedades_dormitorios) || !isEmpty(window.propiedades_banios)) ? "display:block" : "display:none" %>
<div class="advanced-search-div bg-light dk" style="<%= display %>">
  <div class="wrapper oh">
    <h4 class="m-t-xs"><i class="fa fa-filter"></i> Filtros:</h4>
    <div class="row pl10 pr10">
      <div class="col-md-3 col-sm-6 col-xs-12 h50 pr5 pl5">
        <input value="<%= window.propiedades_filter %>" type="text" id="propiedades_buscar" placeholder="T&iacute;tulo o c&oacute;digo..." autocomplete="off" class="form-control">
      </div>
      <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
        <div class="form-group">
          <select style="width: 100%" id="propiedades_buscar_tipos_estado">
            <% for(var i=0;i< window.tipos_estado.length;i++) { %>
              <% var o = tipos_estado[i]; %>
              <option <%= (window.propiedades_id_tipo_estado == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
        <div class="form-group">
          <input type="text" class="form-control no-model" value="<%= window.propiedades_direccion %>" id="propiedades_buscar_direccion" placeholder="Nombre de calle" />
        </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
        <select class="form-control no-model" id="propiedades_buscar_dormitorios">
          <option <%= (isEmpty(window.propiedades_dormitorios)) ? "selected":"" %> value="">Dormitorios</option>
          <option <%= (window.propiedades_dormitorios == "1") ? "selected":"" %> value="1">1</option>
          <option <%= (window.propiedades_dormitorios == "2") ? "selected":"" %> value="2">2</option>
          <option <%= (window.propiedades_dormitorios == "3") ? "selected":"" %> value="3">3</option>
          <option <%= (window.propiedades_dormitorios == "4") ? "selected":"" %> value="4">4</option>
          <option <%= (window.propiedades_dormitorios == "5") ? "selected":"" %> value="5">5</option>
          <option <%= (window.propiedades_dormitorios == "6") ? "selected":"" %> value="6">6</option>
          <option <%= (window.propiedades_dormitorios == "7") ? "selected":"" %> value="7">M&aacute;s</option>
        </select>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
        <select class="form-control no-model" id="propiedades_buscar_banios">
          <option <%= (isEmpty(window.propiedades_banios)) ? "selected":"" %> value="">Ba&ntilde;os</option>
          <option <%= (window.propiedades_banios == "1") ? "selected":"" %> value="1">1</option>
          <option <%= (window.propiedades_banios == "2") ? "selected":"" %> value="2">2</option>
          <option <%= (window.propiedades_banios == "3") ? "selected":"" %> value="3">3</option>
          <option <%= (window.propiedades_banios == "4") ? "selected":"" %> value="4">4</option>
          <option <%= (window.propiedades_banios == "5") ? "selected":"" %> value="5">5</option>
          <option <%= (window.propiedades_banios == "6") ? "selected":"" %> value="6">6</option>
          <option <%= (window.propiedades_banios == "7") ? "selected":"" %> value="7">M&aacute;s</option>
        </select>
      </div>
    </div>
  </div>
</div>
