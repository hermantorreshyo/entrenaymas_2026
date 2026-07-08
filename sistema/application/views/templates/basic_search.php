<script type="text/template" id="basic_search_template">
  <div class="input-group">
    <input type="text" autocomplete="off" placeholder="<%= (isEmpty(basic_search_mark) ? "<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." : basic_search_mark) %>" class="form-control search_input basic_search">
    <span class="input-group-btn">
      <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
    </span>
  </div>
</script>