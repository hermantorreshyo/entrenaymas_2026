<script type="text/template" id="web_categorias_tree_panel_template">

    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Categor&iacute;as de P&aacute;ginas</h1>
    </div>
    
    <div class="wrapper-md pb0">
        <div class="panel panel-default">
            <div class="panel-heading oh">
                <a class="btn btn-success btn-sm btn-addon" href="app/#web_categoria">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-xs">Agregar</span>
                </a>
            </div>
            <div class="panel-body oh">
                <div id="web_categorias_tree" style="height: 500px; overflow: auto;"></div>
            </div>
            <div class="panel-footer oh">
                <div class="info">Doble click para editar un elemento</div>
            </div>
        </div>
    </div>
</script>


<script type="text/template" id="web_categorias_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Categoria
    <% } else { %>
        <%= nombre_es %>
    <% } %>	      
  </h1>
</div>

<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
        <div class="panel-heading">
            <span class="font-bold">Ingrese los datos</span>
        </div>
        <div class="panel-body">
        
            <div class="form-horizontal">

                <div class="form-group">
                    <label class="col-lg-2 control-label">Nombre</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="nombre_es" class="form-control" id="web_categorias_nombre_es" value="<%= nombre_es %>"/>
                        <% } else { %>
                            <span><%= nombre_es %></span>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Pertenece a</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="id_padre" id="web_categorias_padre"></select>
                    </div>
                </div> 								
                
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn guardar btn-success">Guardar</button>
                            <% if (id != undefined) { %>
                                <button class="btn btn-danger eliminar fr">Eliminar</button>
                            <% } %>                            
                        </div>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</div>

</script>