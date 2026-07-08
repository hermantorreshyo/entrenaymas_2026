<script type="text/template" id="emails_resultados_template">
</script>

<script type="text/template" id="emails_item_resultados_template">
    <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" data-id="<%= id %>" /></td>
</script>


<script type="text/template" id="email_template">
<div class="panel panel-default mb0">
    <div class="panel-heading font-bold">
        Enviar Email
        <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
    </div>
    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-12">
                    <select id="email_from" class="form-control">
                        <option><%= EMAIL_USUARIO %></option>
                        <option><%= EMAIL %></option>
                        <% if (email_from != EMAIL && email_from != EMAIL_USUARIO && !isEmpty(email_from)) { %>
                            <option><%= email_from %></option>
                        <% } %>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="text" placeholder="Para: " name="email_to" id="email_nombre" value="<%= email_to %>" class="form-control"/>
                </div>
            </div>            
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="text" name="asunto" id="email_asunto" placeholder="Asunto: " value="<%= asunto %>" class="form-control"/>
                </div>
            </div>

            <?php
            single_file_upload(array(
                "name"=>"archivo",
                "label"=>"Adjuntar",
                "url"=>"/sistema/emails/function/save_file/",
            )); ?>

            <% if (adjuntos.length > 0) { %>
                <div class="form-group">
                    <div class="col-xs-12">
                        <span class="m-r">Adjuntos: </span>
                        <% for (var i=0;i<adjuntos.length;i++) { %>
                            <% var adjunto = adjuntos[i]; %>
                            <button data-position="<%= i %>" class="btn btn-default m-b"><%= adjunto.nombre %><i class="ml5 eliminar_adjunto glyphicon glyphicon-remove"></i></button>
                        <% } %>
                    </div>
                </div>
            <% } %>
            <div class="form-group">
                <div class="col-xs-12">
                    <textarea name="texto" id="email_texto"><%= texto %></textarea>
                </div>
            </div>            
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button class="cerrar_lightbox btn btn-danger">Cancelar</button>
        <button class="btn guardar pull-right btn-info btn-addon">
            <i class="fa fa-send"></i><span>Enviar</span>
        </button>
    </div>
</div>
</script>
