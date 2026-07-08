<script type="text/template" id="fotos_template">
    
    <div>
        <button class="button azul nueva_foto mt5 mb5">Agregar Foto</button>
    </div>
    
	<div class="tabla_container">
		<table>
			<thead>
                <tr>
                    <th>Vista Previa</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                </tr>
            </thead>
			<tbody>
                <tr>
                    <td colspan="10" style="text-align:center"><img src="/resources/images/loading.gif"/></td>
                </tr>            
            </tbody>
		</table>
		<div class="pagination_container"></div>
	</div>

</script>

<script type="text/template" id="item_fotos_template">
    <td>
        <% if (!isEmpty(path)) { %>
            <img src='/<%= path %>' style="max-width:50px; max-height:50px" />
        <% } %>
    </td>
    <td>
        <img src="resources/images/print.png" class="print" alt="Print" title="Imprimir"/>	
    </td>	
    <td>
        <img src="resources/images/delete.png" class="delete" alt="Edit" title="Eliminar"/>	
    </td>
</script>

<script type="text/template" id="upload_template">
    <div class="upload_form_cont">
        <form id="upload_form" method="post" action="/fotos/function/upload/" enctype="multipart/form-data">
            <div class="titulo">
                Subir Imagenes
            </div>
            <div id="dropbox">
                <span class="message">Arrastre la imagen que desea subir aqui</span>
            </div>
        </form>
        <img id="preview"/>
    </div>
    <div class="row" id="upload_template_cerrar_container" style="display:none">
        <button class="button azul cerrar">Cerrar</button>
    </div>
</script>