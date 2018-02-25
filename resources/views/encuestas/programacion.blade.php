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
						<nav>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<a class="nav-item nav-link active" id="nav-resumen-tab" data-toggle="tab" href="#nav-resumen" role="tab" aria-controls="nav-resumen" aria-selected="true">Datos de encuesta</a>
								<a class="nav-item nav-link" id="nav-preguntas-tab" data-toggle="tab" href="#nav-preguntas" role="tab" aria-controls="nav-preguntas" aria-selected="false">Preguntas</a>
								<a class="nav-item nav-link" id="nav-usuarios-tab" data-toggle="tab" href="#nav-usuarios" role="tab" aria-controls="nav-usuarios" aria-selected="false">Evaluados</a>
							</div>
							<div class="tab-content" id="nav-tabContent">
								<!-- tab resumen -->
								<div class="tab-pane fade show active" id="nav-resumen" role="tabpanel" aria-labelledby="nav-resumen-tab">
									<div class="container">
										<form>
											<div class="form-row">
												<div class="form-group col">
													<label for="de-descripcion">Descripción</label>
													<input type="text" class="form-control" id="de-descripcion" placeholder="Descripción de la encuesta">
												</div>
											</div>
											<div class="form-row">
												<div class="form-group col-6">
													<label for="de-inicio">Inicio encuesta</label>
													<input type="text" class="form-control" id="de-inicio" placeholder="dd-mm-yyyy">
												</div>
												<div class="form-group col-6">
													<label for="de-fin">Fin encuesta</label>
													<input type="text" class="form-control" id="de-fin" placeholder="dd-mm-yyyy">
												</div>
											</div>
										</form>
									</div>
								</div>
								<!-- tab resumen -->
								<div class="tab-pane fade" id="nav-preguntas" role="tabpanel" aria-labelledby="nav-preguntas-tab">
									<div class="container">
										<div class="row">
											<div class="col">
												<table class="table table-striped">
													<thead>
														<tr class="thead-light">
															<th>
																Todas las preguntas<br>
																<input class="form-control form-control-sm" type="text" id="modal-gestionar-table-todo-filtro" placeholder="Ingrese texto para buscar" />
															</th>
															<th></th>
														</tr>
													</thead>
													<tbody id="modal-gestionar-table-todo"></tbody>
												</table>
											</div>
											<div class="col">
												<table class="table table-striped">
													<thead>
														<tr class="thead-light">
															<th>
																Preguntas seleccionadas<br>
																<input class="form-control form-control-sm" type="text" id="modal-gestionar-table-cuestionario-filtro" placeholder="Ingrese texto para buscar" />
															</th>
															<th></th>
														</tr>
													</thead>
													<tbody id="modal-gestionar-table-cuestionario"></tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<!-- tab resumen -->
								<div class="tab-pane fade" id="nav-usuarios" role="tabpanel" aria-labelledby="nav-usuarios-tab">
									<div class="row">
										<div class="col">
											<h5 class="text-danger">No se han programado evaluaciones para esta encuesta</h5>
											<p>Utilice el botón "Programar encuestas" para realizar la asignación de evaluadores y evaluados. El sistema programará las evaluaciones de manera automática, en base a la jerarquía de los puestos y las áreas afines.</p>
											<a href="#" class="btn btn-success btn-sm">Programar encuestas</a>
										</div>
									</div>
								</div>
							</div>
						</nav>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Guardar cambios</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var preguntas = {!! json_encode($preguntas) !!};
			//funciones
			function AgregarPregunta(e) {
				e.preventDefault();
				var a = $(this);
				var tr = a.parent().parent();
				$("#modal-gestionar-table-cuestionario").append(
					$("<tr/>").data("id",tr.data("id")).data("txt",tr.data("txt")).append(
						$("<td/>").html(tr.data("txt"))
					).append(
						$("<td/>").append(
							$("<a/>").attr("href","#").addClass("btn btn-danger btn-xs").append(
								$("<i/>").addClass("fa fa-remove")
							).on("click",RetirarPregunta)
						)
					)
				);
				tr.remove();
			}
			function RetirarPregunta(e) {
				e.preventDefault();
				var a = $(this);
				var tr = a.parent().parent();
				$("#modal-gestionar-table-todo").append(
					$("<tr/>").data("id",tr.data("id")).data("txt",tr.data("txt")).append(
						$("<td/>").html(tr.data("txt"))
					).append(
						$("<td/>").append(
							$("<a/>").attr("href","#").addClass("btn btn-success btn-xs").append(
								$("<i/>").addClass("fa fa-plus")
							).on("click",AgregarPregunta)
						)
					)
				);
				tr.remove();
			}
			//eventos
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
				//cargar informacion de la encuesta
				var p = { _token:"{{ csrf_token() }}",eid:ds.eid };
				$.post("{{ url('encuestas/ajax/dt-encuesta') }}", p, function(response) {
					if(response.success) {
						var dencuesta = response.data.encuesta;
						var dpreguntas = response.data.preguntas ? response.data.preguntas : '0';
						//carga resumen
						document.getElementById("de-descripcion").value = dencuesta.descripcion;
						document.getElementById("de-inicio").value = dencuesta.inicio;
						document.getElementById("de-fin").value = dencuesta.fin;
						//muestra preguntas
						$("#modal-gestionar-table-cuestionario").empty();
						$("#modal-gestionar-table-todo").empty();
						for(var i in preguntas) {
							var pregunta = preguntas[i];
							if(dpreguntas.indexOf(pregunta.id) > -1) {
								$("#modal-gestionar-table-cuestionario").append(
									$("<tr/>").data("id",pregunta.id).data("txt",pregunta.texto).append(
										$("<td/>").html(pregunta.texto)
									).append(
										$("<td/>").append(
											$("<a/>").attr("href","#").addClass("btn btn-danger btn-xs").append(
												$("<i/>").addClass("fa fa-remove")
											).on("click",RetirarPregunta)
										)
									)
								);
							}
							else {
								$("#modal-gestionar-table-todo").append(
									$("<tr/>").data("id",pregunta.id).data("txt",pregunta.texto).append(
										$("<td/>").html(pregunta.texto)
									).append(
										$("<td/>").append(
											$("<a/>").attr("href","#").addClass("btn btn-success btn-xs").append(
												$("<i/>").addClass("fa fa-plus")
											).on("click",AgregarPregunta)
										)
									)
								);
							}
						}
					}
					else alert(response.msg);
				}, "json");
			});
		</script>
	</body>
</html>