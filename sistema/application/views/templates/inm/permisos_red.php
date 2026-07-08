<script type="text/template" id="permisos_red_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("permisos_red") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-share-alt icono_principal"></i>Red Inmovar</h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class='oh mb10'>
              <a href='javascript:void(0)' class='btn btn-info fr invitar_colega'>Invita a tu colega</a>
            </div>
            <div class="b-a">
              <table id="permisos_red_tabla" class="table table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th class="w50"></th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Ciudad</th>
                    <th>Permisos</th>
                  </tr>
                </thead>
                <tbody class="tbody">
                  <% for(var i=0;i< results.length;i++) { %>
                    <% var m = results[i] %>
                    <tr data-id="<%= m.id %>">
                      <td class="p0">
                        <% if (!isEmpty(m.logo)) { %>
                          <img src="/sistema/<%= m.logo %>" class="customcomplete-image"/>
                        <% } %>
                      </td>                      
                      <td><span class="text-info"><%= m.razon_social %></span></td>
                      <td>
                        <% if (!isEmpty(m.telefono_web)) { %>
                          <a data-toggle="tooltip" title="<%= m.telefono_web %>" data-telefono="<%= m.telefono_web %>" class="enviar_whatsapp" href="javascript:void(0)"><i class="fa fa-whatsapp iconito active success"></i></a>
                        <% } %>
                        <% if (!isEmpty(m.email)) { %>
                          <a data-toggle="tooltip" title="<%= m.email %>" href="mailto:<%= m.email %>"><i class="fa fa-envelope iconito active"></i></a>
                        <% } %>
                      </td>
                      <td><%= m.localidad %></td>
                      <td>
                        <a class="estado_1 btn btn-sm btn-default <%= (m.estado >= 1)?"active btn-success":"" %>" href="javascript:void(0)"><i class="fa mr10 fa-link"></i> RED INTERNA</a>
                        <a class="estado_2 btn btn-sm btn-default <%= (m.estado >= 2)?"active btn-info":"" %>" href="javascript:void(0)"><i class="fa mr10 fa-laptop"></i> MI WEB</a>
                        <a target="_blank" href="app/#propiedades_red/<%= m.id %>" data-toggle="tooltip" title="Ver propiedades compartidas" class="btn btn-sm btn-default"><i class="fa fa-home"></i></a>
                      </td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>

          <div class="clearfix mt20 tar">
            <button class="btn btn-success guardar">Guardar</button>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
</script>

<script type="text/template" id="invitar_colega_template">
  <div class="panel panel-default">
    <div class="panel-heading bold">Invitar a un colega</div>
    <div class="panel-body">
      <div class="form-group">
        <label>Nombre del colega / inmobiliaria</label>
        <input type="text" id="invitar_colega_inmobiliaria" class="form-control no-model">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="text" id="invitar_colega_email" class="form-control no-model">
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="enviar btn btn-success">Enviar</button>
    </div>
  </div>
</script>