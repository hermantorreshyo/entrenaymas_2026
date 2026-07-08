<div class="container">
	<div class="reparacion-titulo oh">
		<div class="dt mla mra fl">
			<div class="dtc tal w50">
				<img src="/templates/comun/reparacion/img/capa-reparacion.png" width="45px">
			</div>
			<div class="dtc tal">
				<span class="status">Estado de Reparación</span>
			</div>
		</div>
	</div>	
  <div class="varcreative-panel">
    <div class="varcreative-panel-body">	
			<div class="row" id="row-datos">
			  <div class="col-md-6 col-xs-12">
			  	<div class="datos-izquierda">
			  		<span class="orden">ORDEN: </span><span class="numero"><?php echo $factura->numero ?></span>
			  	</div>
			  </div>
			  <div class="col-md-6 col-xs-12">
			  	<div class="datos-derecha">
				  	<p><span class="cliente">Cliente: </span><span class="nombre"><?php echo $factura->cliente ?></span></p>
				  	<?php foreach($factura->items as $item) { ?>
				  		<p><span class="asunto"><?php echo $item->nombre ?></span></p>
				  	<?php } ?>
				  	<?php if (!empty($factura->vencimiento)) { ?>
				  		<p><span class="fe">Fecha estimada:</span><span class="fecha"><?php echo $factura->vencimiento ?></span></p>
				  	<?php } ?>
				  </div>
			  </div>
			</div>
			<?php if ($factura->id_tipo_estado == 0) { ?>
				<div class="barra-container">
					<div class="row margin0">
						<div class="col-md-12">
							<div class="row" >
							  <div class="col-md-3 barra-final-empty barra-final-centrada">
							  	<div class="circle-inicio"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
							  </div>
							  <div class="col-md-3 hidden-xs barra-final-empty"></div>
							  <div class="col-md-3 hidden-xs barra-final-empty"></div>
							  <div class="col-md-3 hidden-xs barra-final-empty"></div>
						  </div>
					  </div>
					</div>
				</div>
				<div class="row" id="bottom">
				  <div class="col-md-4">
				  	<div class="progreso-inicio en-espera">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L1.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">Inicio de trabajo</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				  <div class="col-md-4"></div>
				  <div class="col-md-4"></div>
				</div>
			<?php } else if ($factura->id_tipo_estado == 1) { ?>
				<div class="barra-container">
					<div class="row margin0">
						<div class="col-md-12">
							<div class="row" >
							  <div class="col-md-3 hidden-xs barra-inicio">
							  	<div class="circle-inicio"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
							  </div>
							  <div class="col-md-3 hidden-xs barra-inicio"></div>
							  <div class="col-md-3 barra-final barra-final-centrada">
							  	<div class="circle-inicio"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
							  </div>
							  <div class="col-md-3 hidden-xs barra-final-empty"></div>
						  </div>
					  </div>
					</div>
				</div>
				<div class="row" id="bottom">
				  <div class="col-md-3">
				  	<div class="progreso-inicio">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L1.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">Inicio de trabajo</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				  <div class="col-md-2"></div>
				  <div class="col-md-4">
				  	<div class="progreso-inicio en-espera">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L1.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">En espera de repuesto</span>
				  			</div>
				  		</div>
				  	</div>				  	
				  </div>
				  <div class="col-md-3"></div>
				</div>
			<?php } else if ($factura->id_tipo_estado == 6) { ?>
				<div class="barra-container">
					<div class="row margin0">
					  <div class="col-md-3 barra-inicio hidden-xs">
					  	<div class="circle"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
						<div class="col-md-3 barra-inicio hidden-xs"><div></div></div>
					  <div class="col-md-3 barra-inicio hidden-xs">
					  	<div class="circle"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
					  <div class="col-md-3 barra-final-full">
					  	<div class="circle-green"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
				  </div>
				</div>
				<div class="row" id="bottom">
				  <div class="col-md-4">
				  	<div class="progreso-inicio">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L1.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">Inicio de trabajo</span>
				  			</div>
				  		</div>
				  	</div>				  	
				  </div>
				  <div class="col-md-4" style="text-align: center;">
				  	<div class="progreso-reparacion">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L0001.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">En Reparación</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				  <div class="col-md-4" style="text-align: right;">
						<div class="progreso-reparado">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L2.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">Reparado</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				</div>

			<?php } else if ($factura->id_tipo_estado == 7) { ?>
				<div class="barra-container">
					<div class="row margin0">
					  <div class="col-md-3 hidden-xs barra-inicio">
					  	<div class="circle"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
					  <div class="col-md-3 hidden-xs barra-inicio"><div></div></div>
					  <div class="col-md-3 hidden-xs barra-inicio">
					  	<div class="circle"><i class="fa fa-check" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
					  <div class="col-md-3 barra-final-full">
					  	<div class="circle-red"><i class="fa fa-close" style="font-size:30px;color:white; padding-top: 9px;"></i></div>
					  </div>
				  </div>
				</div>
				<div class="row" id="bottom">
				  <div class="col-md-4">
				  	<div class="progreso-inicio">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L1.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">Inicio de trabajo</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				  <div class="col-md-4" style="text-align: center;">
				  	<div class="progreso-reparacion">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L0001.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">En Reparación</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				  <div class="col-md-4" style="text-align: right;">
						<div class="progreso-no-reparado">
				  		<div class="dt mla mra">
				  			<div class="dtc tal w50">
				  				<img src="/templates/comun/reparacion/img/L2.png" width="50px">
				  			</div>
				  			<div class="dtc tal">
				  				<span class="progreso">No Reparado</span>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				</div>

			<?php } ?>
		</div>
	</div>
</div>

<?php if ($factura->id_tipo_estado == 6 || $factura->id_tipo_estado == 7) { ?>
	<div class="container impimir-container">
		<div class="tar barra-container">
			<a href="javascript:void(0)" onclick="window.print();" class="varcreative-boton-imprimir">Imprimir Comprobante</a>
		</div>
	</div>
<?php } ?>