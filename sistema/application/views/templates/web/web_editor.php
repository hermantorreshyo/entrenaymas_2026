<script type="text/template" id="web_editor_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Editor Web</h1>
  </div>
  <div class="wrapper-md">
	<div class="tab-container">
	  <ul class="nav nav-tabs" role="tablist">
		<li class="active">
		  <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>CSS</a>
		</li>
		<li>
		  <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Javascript</a>
		</li>
	  </ul>
	  <div class="tab-content">
		<div id="tab1" class="tab-pane active panel-body">
		  <div class="form-horizontal">
			<textarea class="form-control" style="height:400px" id="web_editor_texto_css" name="texto_css"><%= texto_css %></textarea>
			<div class="line line-dashed b-b line-lg pull-in"></div>
			<div class="form-group">
				<div class="col-xs-12">    
					<button class="btn btn-success guardar">Guardar</button>
				</div>
			</div>
		  </div>
		</div>
		<div id="tab2" class="tab-pane panel-body">
		  <div class="form-horizontal">
			<textarea class="form-control" style="height:400px" id="web_editor_texto_js" name="texto_js"><%= texto_js %></textarea>
			<div class="line line-dashed b-b line-lg pull-in"></div>
			<div class="form-group">
				<div class="col-xs-12">    
					<button class="btn btn-success guardar">Guardar</button>
				</div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>  
</script>