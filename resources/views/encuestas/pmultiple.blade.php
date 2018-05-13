<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="v-separator"></div>
		<div class="container">
			<div class="row">
				<div class="col">
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Evaluador</th>
								<th colspan="2">Evaluado</th>
							</tr>
						</thead>
						<tbody>
							@foreach($usuarios as $idx => $usuario)
							<tr>
								<td></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal confirma encuestas -->
		<div class="modal fade bd-example-modal-lg" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Programar encuesta</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Evaluador</th>
									<th>Evaluado</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="modal-confirma-tbody"></tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button id="modal-confirma-sv" type="button" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Realizar la programación</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			//
		</script>
	</body>
</html>