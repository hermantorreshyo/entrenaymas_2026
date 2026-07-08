<script type="text/template" id="pagination_template">
<td colspan="20" class="text-center footable-visible">
	<% if (ver_pagina_de) { %>
		<span class="total_items"><?php echo lang(array("es"=>"Total de items:","en"=>"Items:")); ?> <%= totalRecords %></span>
	<% } %>
	<% if (totalPages > 1) { %>
	  <ul class="pagination">
	    <% if (ver_botones) { %>
	      <li class="footable-page-arrow <% if (currentPage == 1) { %>disabled<% } %>">
	        <a class="serverfirst">&laquo;</a>
	      </li>
	      <li class="footable-page-arrow <% if (currentPage == 1) { %>disabled<% } %>">
	        <a class="serverprevious">&lsaquo;</a>
	      </li>
	    <% } %>

	    <% if (ver_numeros_pagina) {
	      for(p=0;p<totalPages;p++){ %>
	        <% if (Math.abs(currentPage-p-1)<3) { %>
	          <li class='footable-page <%= (p==(currentPage-1)) ? "active" : "" %>'>
	            <a class="page"><%= p+1 %></a>
	          </li>
	        <% } %>
	      <% } %>
	    <% } %>

	    <% if (ver_botones) { %>
	      <li class="footable-page-arrow <% if (currentPage == totalPages) { %>disabled<% } %>">
	        <a class="servernext">&rsaquo;</a>
	      </li>
	      <li class="footable-page-arrow <% if (currentPage == totalPages) { %>disabled<% } %>">
	        <a class="serverlast">&raquo;</a>
	      </li>
	    <% } %>        
	  </ul>
	<% } %>

  <% if (ver_filas_pagina) { %>
    <div class="pull-right cell serverhowmany">
      <span class="dib m-r-xs"><?php echo lang(array("es"=>"Ver","en"=>"Per page")); ?>: </span>
      <select class="dib w100 form-control">
        <option <%= (perPage==10)?"selected":"" %>>10</option>
        <option <%= (perPage==15)?"selected":"" %>>15</option>
        <option <%= (perPage==20)?"selected":"" %>>20</option>
        <option <%= (perPage==30)?"selected":"" %>>30</option>
        <option <%= (perPage==50)?"selected":"" %>>50</option>
        <option <%= (perPage==100)?"selected":"" %>>100</option>
        <option <%= (perPage==200)?"selected":"" %>>200</option>
        <option <%= (perPage==99999999999)?"selected":"" %> value="99999999999"><?php echo lang(array("es"=>"Todo","en"=>"All")); ?></option>
      </select>
    </div>
  <% } %>
</td>
<?php /*
	<div class="pagination_bar">
    
		<% if (ver_pagina_de) { %>
			<span class="cell first records">
				Pagina:
				<select>
					<% for(var i=1; i<= lastPage; i++) { %>
						<option <%= (currentPage == i) ? "selected" : "" %>><%= i %></option>
					<% } %>
				</select>
				de <span class="total"><%= totalPages %></span>
			</span>
		<% } %>

		<% if (ver_filas_pagina) { %>
			<span class="cell serverhowmany">
				
				<% if (ver_pagina_de) { %>
					&nbsp; | &nbsp;
				<% } %>
				
				Filas por Pagina:
				<select>
					<option <%= (perPage==10)?"selected":"" %>>10</option>
					<option <%= (perPage==20)?"selected":"" %>>20</option>
					<option <%= (perPage==50)?"selected":"" %>>50</option>
					<option <%= (perPage==100)?"selected":"" %>>100</option>
					<option <%= (perPage==200)?"selected":"" %>>200</option>
					<option <%= (perPage==9999999)?"selected":"" %> value="9999999">Todo</option>
				</select>
			</span>
		<% } %>

	</div>
	*/ ?>
</script>
