<script type="text/template" id="importar_template">
<div class="panel panel-default">
	<div class="panel-heading">
		<b><%= titulo %></b>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<%= texto %>
		</div>
		<div class="form-group">
			<div class="bootstrap-filestyle-container">
				<input class="inputFile" ui-jq="filestyle" type="file" data-icon="false" data-classbutton="btn btn-default" data-classinput="form-control inline v-middle input-s" name="file" id="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
				<div class="bootstrap-filestyle input-group">
					<input type="text" class="form-control" disabled="">
					<span class="group-span-filestyle input-group-btn" tabindex="0">
						<label for="file" class="btn btn-default ">
							<span class="glyphicon glyphicon-folder-open m-r-xs"></span>
							Elegir Archivo
						</label>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer tar">
		<button class="btn btn-success aceptar">Aceptar</button>
	</div>
</div>
</script>