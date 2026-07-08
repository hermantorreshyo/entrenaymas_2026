<script type="text/template" id="datepicker_template">
    
    <!--<span class="mr5"><%= texto_desde %></span>-->
    <input type="text" class="form-control fecha_desde"/>
    
    <% if (mostrar_hasta) { %>
        <!--<span class="mr5 ml15"><%= texto_hasta %></span>-->
        <input type="text" class="form-control fecha_hasta"/>
    <% } %>
    
    <% if (permitir_borrar) { %>
        <img class="cp borrar" style="position:relative; top:2px; left:5px" src="/resources/images/delete.png"/>
    <% } %>
</script>