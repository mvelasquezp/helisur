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
					<hr>
					<form class="form-inline">
						<div class="form-check mb-2 mr-sm-2">
							<label class="form-check-label" for="if-encuesta">Encuesta</label>
						</div>
						<input type="hidden" id="if-eid">
						<input type="text" class="form-control mb-2 mr-sm-2" id="if-encuesta" placeholder="Clic para seleccionar" style="width:32rem;">
					</form>
				</div>
			</div>
			<div id="table-container" class="row">
				<hr>
				<div class="col">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Evaluador</th>
								<th>Evaluado</th>
								<th>Estado</th>
								<th>Progreso</th>
								<th>Fe.Inicio</th>
								<th>Ult.Acceso</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="main-tbody"></tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal-encuestas -->
		<div id="modal-encuestas" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Revisar</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Encuesta</th>
											<th>Detalles</th>
											<th>Duración</th>
											<th>Estado</th>
											<th></th>
										</tr>
									</thead>
									<tbody id="modal-encuestas-tbody"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal-encuestas -->
		<div id="modal-seguimiento" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seguimiento</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-seguimiento-eid">
						<input type="hidden" id="modal-seguimiento-eva">
						<input type="hidden" id="modal-seguimiento-peva">
						<div class="row">
							<div class="col">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Fecha</th>
											<th>Destino</th>
										</tr>
									</thead>
									<tbody id="modal-seguimiento-tbody"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary btn-sm" id="modal-seguimiento-btn"><i class="fa fa-paper-plane"></i> Enviar recordatorio</button>
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			document.getElementById("if-encuesta").value = "";
			document.getElementById("if-eid").value = "";
			function CargarDatosEncuesta(event) {
				event.preventDefault();
				var a = $(this);
				document.getElementById("if-eid").value = a.data("eid");
				document.getElementById("if-encuesta").value = a.data("nom");
				$("#main-tbody").empty();
				var p = {
					_token: "{{ csrf_token() }}",
					eid: document.getElementById("if-eid").value
				};
				$.post("{{ url('encuestas/ajax/dt-progreso-encuesta') }}", p, function(response) {
					if(response.success) {
						var datos = response.data.lista;
						for(var i in datos) {
							var fila = datos[i];
							var iProgreso = "";
							switch(fila.estado) {
								case "En curso":
								case "Programado":
								case "Pendiente": iProgreso = fila.progreso + " / " + fila.preguntas; break;
								case "Valorando": iProgreso = "Valorando"; break;
								default: iProgreso = "Terminada"; break;
							}
							$("#main-tbody").append(
								$("<tr/>").append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(fila.evaluador)
									).append(
										$("<p/>").addClass("text-secondary").html(fila.apuesto + " - " + fila.aoficina)
									)
								).append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(fila.evaluado)
									).append(
										$("<p/>").addClass("text-secondary").html(fila.opuesto + " - " + fila.ooficina)
									)
								).append(
									//$("<td/>").html(fila.estado)
									$("<td/>").append(
										$("<a/>").addClass("btn btn-xs text-light " + (fila.estado == "En curso" ? "btn-success" : (fila.estado == "Programado" ? "btn-danger" : (fila.estado == "Pendiente" ? "btn-warning" : "btn-info")))).html(fila.estado)
									)
								).append(
									$("<td/>").addClass("text-center").html(iProgreso)
								).append(
									$("<td/>").html(fila.inicio)
								).append(
									$("<td/>").html(fila.ultimo)
								).append(
									$("<td/>").append(
										$("<a/>").addClass("btn btn-xs btn-warning text-dark").append(
											$("<i/>").addClass("fa fa-envelope-o")
										).append(" Seguimiento").attr({
											"href": "#",
											"data-toggle": "modal",
											"data-target": "#modal-seguimiento",
											"data-nom": fila.evaluador,
											"data-eid": fila.eid,
											"data-eva": fila.eva,
											"data-peva": fila.peva
										})
									)
								)
							);
						}
						$("#modal-encuestas").modal("hide");
					}
					else alert(response.msg);
				}, "json");
			}
			function CargarRecordatorios(p) {
				$.post("{{ url('resultados/ajax/ls-recordatorios') }}", p, function(response) {
					if(response.success) {
						var data = response.data.datos;
						$("#modal-seguimiento-tbody").empty();
						if(data.length > 0) {
							for(var i in data) {
								var recordatorio = data[i];
								$("#modal-seguimiento-tbody").append(
									$("<tr/>").append(
										$("<td/>").html(recordatorio.fecha)
									).append(
										$("<td/>").html(recordatorio.mail)
									)
								);
							}
						}
						else {
							$("#modal-seguimiento-tbody").append(
								$("<tr/>").append(
									$("<td/>").attr("colspan", 2).html("No se enviaron reordatorios al evaluador")
								)
							);
						}
					}
					else alert(response.msg);
				}, "json");
			}
			//eventos
			$("#if-encuesta").on("click", function() {
				$("#modal-encuestas-tbody").empty();
				$("#modal-encuestas").modal("show");
				var p = { _token:"{{ csrf_token() }}" };
				$.post("{{ url('encuestas/ajax/ls-encuestas-informe') }}", p, function(response) {
					if(response.success) {
						var encuestas = response.data.encuestas;
						for(var i in encuestas) {
							var encuesta = encuestas[i];
							$("#modal-encuestas-tbody").append(
								$("<tr/>").append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(encuesta.encuesta)
									).append(
										$("<p/>").addClass("text-secondary").html(encuesta.descripcion)
									)
								).append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(encuesta.preguntas + " pregunta(s)")
									).append(
										$("<p/>").addClass("text-dark").html(encuesta.cantidad + " evaluacion(es)")
									)
								).append(
									$("<td/>").html(encuesta.inicio + " al " + encuesta.fin)
								).append(
									$("<td/>").append(
										$("<a/>").addClass("btn btn-xs text-light " + (encuesta.estado == "En curso" ? "btn-success" : (encuesta.estado == "Programado" ? "btn-danger" : (encuesta.estado == "Pendiente" ? "btn-warning" : "btn-info")))).html(encuesta.estado)
									)
								).append(
									$("<td/>").append(
										$("<a/>").attr({
											"href": "#",
											"data-eid": encuesta.eid,
											"data-nom": encuesta.encuesta
										}).addClass("btn btn-xs btn-primary").on("click", CargarDatosEncuesta).html("Ver")
									)
								)
							);
						}
					}
				}, "json");
			});
			$("#modal-seguimiento").on("show.bs.modal", function(event) {
				var data = event.relatedTarget.dataset;
				var p = {
					_token: "{{ csrf_token() }}",
					eva: data.eva,
					peva: data.peva,
					eid: data.eid
				};
				document.getElementById("modal-seguimiento-eid").value = p.eid;
				document.getElementById("modal-seguimiento-eva").value = p.eva;
				document.getElementById("modal-seguimiento-peva").value = p.peva;
				$("#modal-seguimiento .modal-title").html(data.nom);
				CargarRecordatorios(p);
			});
			$("#modal-seguimiento-btn").on("click", function(event) {
				event.preventDefault();
				if(window.confirm("Se enviará un recordatorio a " + $("#modal-seguimiento .modal-title").html() + ". ¿Continuar?")) {
					var p = {
						_token: "{{ csrf_token() }}",
						eva: document.getElementById("modal-seguimiento-eva").value,
						peva: document.getElementById("modal-seguimiento-peva").value,
						eid: document.getElementById("modal-seguimiento-eid").value
					};
					$.post("{{ url('resultados/ajax/send-recordatorio') }}", p, function(response) {
						if(response.success) {
							CargarRecordatorios(p);
						}
						else alert(response.msg);
					}, "json");
				}
			});
		</script>
	</body>
</html>