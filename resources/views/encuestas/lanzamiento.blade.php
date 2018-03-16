<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
			.no-margin>*{margin:2px 0}
			.no-margin>.text-secondary{font-size:11px}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="container">
			<div class="row">
				<div class="col">
					<h3 class="text-primary">Lanzamiento de encuestas</h3>
					<p class="text-secondary">Envíe las encuestas de manera individual, o utilice la opción de "Lanzamiento global" para enviarlas todas de una sola vez.</p>
					<a href="#" class="btn btn-success mb-2" id="btn-lanzalaaaa"><i class="fa fa-reply-all"></i> Lanzamiento global</a>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
				</div>
			</div>
			<div class="row">
				<div class="col">
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
								{{ $encuesta->publico }} evaluaciónes programadas
								@endif
							</p>
							<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-ilanzamiento" data-eid="{{ $encuesta->id }}" data-nom="{{ $encuesta->nombre }}">Lanzar</a>
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
		</div>
		<!-- modal-ilanzamiento -->
		<div id="modal-ilanzamiento" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Lanzamiento de encuesta: </h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-ilanzamiento-id">
						<div class="row">
							<div class="col">
								<table class="table">
									<thead>
										<tr>
											<th>Cargo</th>
											<th>Evaluador</th>
											<th>Encuestas</th>
											<th>e-mail</th>
										</tr>
									</thead>
									<tbody id="modal-ilanzamiento-tbody"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btn-snd-encuesta"><i class="fa fa-paper-plane"></i> Lanzar la encuesta</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var xArgs;
			$("#modal-ilanzamiento").on("show.bs.modal", function(args) {
				var ds = xArgs ? xArgs : args.relatedTarget.dataset;
				$("#modal-ilanzamiento .modal-title").html("Lanzamiento de encuesta: " + ds.nom);
				document.getElementById("modal-ilanzamiento-id").value = ds.eid;
				var p = {
					_token: "{{ csrf_token() }}",
					eid: (ds.eid + "").split(",")
				};
				$.post("{{ url('encuestas/ajax/ls-destinatarios') }}", p, function(response) {
					if(response.success) {
						$("#modal-ilanzamiento-tbody").empty();
						var data = response.data.lista;
						for(var i in data) {
							var fila = data[i];
							$("#modal-ilanzamiento-tbody").append(
								$("<tr/>").append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(fila.puesto)
									).append(
										$("<p/>").addClass("text-secondary").html(fila.oficina)
									)
								).append(
									$("<td/>").html(fila.nombre)
								).append(
									$("<td/>").html(fila.cantidad)
								).append(
									$("<td/>").append(
										$("<a/>").attr("href","#").addClass("btn btn-xs " + (fila.stmail == 'S' ? 'btn-success' : 'btn-danger')).html(fila.email)
									)
								)
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
			$("#btn-snd-encuesta").on("click", function() {
				$("#btn-snd-encuesta").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					eid: document.getElementById("modal-ilanzamiento-id").value.split(",")
				};
				$.post("{{ url('encuestas/ajax/snd-encuesta') }}", p, function(response) {
					if(response.success) {
						alert("Se enviaron las encuestas. La página se recargará automáticamente");
						location.reload();
					}
					else alert(response.msg);
					$("#btn-snd-encuesta").show();
				}, "json");
			});
			$("#btn-lanzalaaaa").on("click", function(event) {
				event.preventDefault();
				$("#btn-lanzalaaaa").hide();
				var p = {_token:"{{ csrf_token() }}"};
				$.post("{{ url('encuestas/ajax/ls-encuestas-lanzar') }}", p, function(response) {
					if(response.success) {
						var ids = response.data;
						document.getElementById("modal-ilanzamiento-id").value = ids;
						$("#btn-lanzalaaaa").show();
						xArgs = {
							nom: "Todas",
							eid: ids
						};
						$("#modal-ilanzamiento").modal("show");
					}
				}, "json");
			});
		</script>
	</body>
</html>