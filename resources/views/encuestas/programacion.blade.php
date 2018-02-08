<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
	</head>
	<body>
		@include("common.navbar")
		<div class="container">
			<div class="row">
				<div class="col">
					<h3 class="text-primary">Programación de encuestas</h3>
					<p class="text-secondary">Desde aquí podrá crear las evaluaciones para aplicar al personal.</p>
					@if(count($encuestas) > 0)
					@foreach($encuestas as $idx => $encuesta)
					<div id="dv-encuesta-{{ $encuesta->id }}" class="card bg-light" style="display:inline-block;margin:0 0.15rem;width:18rem">
						<div class="card-body">
							<h5 class="card-title">{{ $encuesta->nombre }}</h5>
							<h6 class="card-subtitle mb-2 text-muted">
								@if($encuesta->preguntas == 0)
								Sin preguntas asignadas
								@else
								{{ $encuesta->preguntas }} preguntas
								@endif
							</h6>
							<p class="card-text text-muted">
								@if($encuesta->publico == 0)
								No se asignaron evaluaciones
								@else
								{{ $encuesta->publico }} usuario(s) programado(s)
								@endif
							</p>
							<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-gestionar" data-eid="{{ $encuesta->id }}" data-nom="{{ $encuesta->nombre }}">Administrar</a>
						</div>
					</div>
					@endforeach
					@else
					<div class="alert alert-danger" role="alert">
						No hay encuestas programadas. Utilice la opción "Nueva encuesta" para crear una encuesta.
					</div>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
					<form class="form-inline">
						<div class="form-check mb-2 mr-sm-2">
							<label class="form-check-label" for="gr-nombre">Nueva encuesta</label>
						</div>
						<input type="text" class="form-control mb-2 mr-sm-2" id="enc-nombre" placeholder="Ingrese nombre para la encuesta" style="width:16rem;">
						<a href="#" class="btn btn-success mb-2" id="btn-enc-guardar"><i class="fa fa-plus"></i> Nueva encuesta</a>
					</form>
				</div>
			</div>
		</div>
		<!-- modal-gestionar -->
		<div id="modal-gestionar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Gestionar</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-gestionar-id">
						<p>Modal body text goes here.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary">Save changes</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			$("#btn-enc-guardar").on("click", function(e) {
				e.preventDefault();
				var p = { _token:"{{ csrf_token() }}",nom:document.getElementById("enc-nombre").value };
				$.post("{{ url('encuestas/ajax/sv-encuesta') }}", p, function(response) {
					if(response.success) {
						document.getElementById("enc-nombre").value = "";
						location.reload();
					}
					else alert(response.msg);
				}, "json");
			});
			$("#modal-gestionar").on("show.bs.modal", function(args) {
				var ds = args.relatedTarget.dataset;
				$("#modal-gestionar .modal-title").html("Editando: " + ds.nom);
				document.getElementById("modal-gestionar-id").value = ds.eid;
			});
		</script>
	</body>
</html>