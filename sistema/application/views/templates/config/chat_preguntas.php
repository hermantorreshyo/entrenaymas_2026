<script type="text/template" id="chat_preguntas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / <b>Preguntas predefinidas de chat</b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#chat_pregunta"><i class="fa fa-plus"></i>Nuevo</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="chat_preguntas_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="sorting" data-sort-by="orden">Orden</th>
                <th>Clave</th>
                <th>Pregunta</th>
                <th class="w100"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
	</div>  
</script>


<script type="text/template" id="chat_preguntas_item">
  <td><span class='ver'><%= orden %></span></td>
	<td>
    <span class='ver'>
      <%= (tipo=="questions")?"Pregunta":"" %>
      <%= (tipo=="welcome")?"Bienvenida":"" %>
      <%= (tipo=="bye")?"Despedida":"" %>
      <%= (tipo=="possitive_sentences")?"Frases positivas":"" %>
      <%= (tipo=="chat_message")?"Mensaje de bot":"" %>
    </span>
  </td>
  <td><span class='ver'><%= pregunta %></span></td>
  <td class="p5 td_acciones">
    <div class="btn-group dropdown ml10">
      <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus"></i>
      </button>    
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
</script>

<script type="text/template" id="chat_preguntas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i> 
    Configuraci&oacute;n /
    Preguntas predefinidas de chat / 
    <b><%= (id == undefined) ? 'Nueva' : 'Editar' %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Datos</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Preguntas</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="chat_preguntas_pregunta_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="chat_preguntas_pregunta_2" class="btn btn-default btn-lang" data-id="chat_preguntas_pregunta_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="chat_preguntas_pregunta_cont">
                    <textarea class="form-control" name="pregunta" name="pregunta" id="chat_preguntas_pregunta"><%= pregunta %></textarea>
                  </div>
                  <div class="form-control-cont" id="chat_preguntas_pregunta_en_cont">
                    <textarea class="form-control" name="pregunta_en" name="pregunta_en" id="chat_preguntas_pregunta_en"><%= pregunta_en %></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Respuesta afirmativa</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="chat_preguntas_success_text_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="chat_preguntas_success_text_2" class="btn btn-default btn-lang" data-id="chat_preguntas_success_text_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="chat_preguntas_success_text_cont">
                    <textarea class="form-control" name="success_text" name="success_text" id="chat_preguntas_pregunta"><%= success_text %></textarea>
                  </div>
                  <div class="form-control-cont" id="chat_preguntas_success_text_en_cont">
                    <textarea class="form-control" name="success_text_en" name="success_text_en" id="chat_preguntas_success_text_en"><%= success_text_en %></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group lang-control">
                <div class="clearfix">
                  <label class="control-label m-t-xs">Respuesta negativa</label>
                  <div class="lang-control-btn">
                    <label class="btn btn-default btn-lang active" data-id="chat_preguntas_fail_text_cont" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                    <label id="chat_preguntas_fail_text_2" class="btn btn-default btn-lang" data-id="chat_preguntas_fail_text_en_cont" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-control-cont active" id="chat_preguntas_fail_text_cont">
                    <textarea class="form-control" name="fail_text" name="fail_text" id="chat_preguntas_pregunta"><%= fail_text %></textarea>
                  </div>
                  <div class="form-control-cont" id="chat_preguntas_fail_text_en_cont">
                    <textarea class="form-control" name="fail_text_en" name="fail_text_en" id="chat_preguntas_fail_text_en"><%= fail_text_en %></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo</label>
                    <select id="chat_preguntas_tipo" name="tipo" class="form-control">
                      <option <%= (tipo=="questions")?"selected":"" %> value="questions">Pregunta</option>
                      <option <%= (tipo=="welcome")?"selected":"" %> value="welcome">Bienvenida</option>
                      <option <%= (tipo=="bye")?"selected":"" %> value="bye">Despedida</option>
                      <option <%= (tipo=="possitive_sentences")?"selected":"" %> value="possitive_sentences">Frases positivas</option>
                      <option <%= (tipo=="chat_message")?"selected":"" %> value="chat_message">Mensaje de bot</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Orden</label>
                    <input type="text" class="form-control" id="chat_preguntas_orden" value="<%= orden %>" name="orden">
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>

</script>