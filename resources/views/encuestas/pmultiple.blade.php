<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
			.tr-pivot{cursor:pointer}
			.tr-children{display:none}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="v-separator"></div>
		<div class="container">
			<div class="row">
				<div class="col">
					<input type="hidden" id="eva">
					<input type="hidden" id="peva">
					<table class="table">
						<thead>
							<tr>
								<th width="2%">#</th>
								<th width="40%">Evaluador</th>
								<th width="40%">Evaluado</th>
								<th width="18%"></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$count = 0;
						$current = 0;
						?>
						@foreach($usuarios as $idx => $usuario)
							@if($usuario->eva != $current)
							<?php
								$count++;
								$current = $usuario->eva;
							?>
							<tr id="tr-{{ $idx }}" data-eva="{{ $usuario->eva }}" data-peva="{{ $usuario->peva }}" class="tr-pivot">
								<td>{{ $count }}</td>
								<td>{{ $usuario->neva }}</td>
								<td></td>
								<td></td>
							</tr>
							@else
							<tr class="tr-{{ $usuario->eva }} tr-children">
								<td></td>
								<td></td>
								<td>{{ $usuario->nevo }}</td>
								<td>{{ $usuario->encuesta }}</td>
							</tr>
							@endif
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal encuestas -->
		<div class="modal fade" id="modal-encuestas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Seleccionar encuestas</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="95%">Encuesta</th>
									<th width="5%"></th>
								</tr>
							</thead>
							<tbody>
								@foreach($encuestas as $idx => $encuesta)
								<tr data-eid="{{ $encuesta->value }}">
									<td>{{ $encuesta->text }}</td>
									<td><a href="#" class="btn btn-danger btn-xs btn-encuesta"><i class="fa fa-minus"></i></a></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button id="modal-encuestas-sv" type="button" class="btn btn-primary"><i class="fa fa-chevron-right"></i> Elegir evaluados</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal evaluados -->
		<div class="modal fade" id="modal-evaluados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Seleccionar evaluados</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="95%"><input type="text" id="tb-evaluado-filter" class="form-control form-control-xs" placeholder="Evaluado"></th>
									<th width="5%"></th>
								</tr>
							</thead>
							<tbody id="modal-evaluados-tbody">
								@foreach($evaluados as $idx => $evaluado)
								<tr id="tr-{{ $evaluado->evo }}" data-evo="{{ $evaluado->evo }}" data-pevo="{{ $evaluado->pevo }}">
									<td>{{ $evaluado->nevo }}</td>
									<td><a href="#" class="btn btn-danger btn-xs btn-evaluado"><i class="fa fa-minus"></i></a></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button id="modal-evaluados-sv" type="button" class="btn btn-primary"><i class="fa fa-chevron-right"></i> Elegir evaluados</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var pivots = $(".tr-pivot");
			var evaluados = {!! json_encode($evaluados) !!};
			var encuestas = new Array();
			var arr_evaluados = new Array();
			function AsignarEncuestas(evt) {
				evt.preventDefault();
				var a = $(this);
				document.getElementById("eva").value = a.data("eva");
				document.getElementById("peva").value = a.data("peva");
				$($(this).data("class")).toggle();
				$("#modal-encuestas").modal("show");
			}
			function AsignarEvaluados(evt) {
				evt.preventDefault();
				var sl_encuestas = $("#modal-encuestas .btn-success");
				if(sl_encuestas.length > 0) {
					encuestas = new Array();
					$.each(sl_encuestas, function() {
						encuestas.push($(this).parent().parent().data("eid"));
					});
					$("#modal-encuestas").modal("hide");
					setTimeout(function() {
						document.getElementById("tb-evaluado-filter").value = "";
						$("#modal-evaluados").modal("show");
						MuestraEvaluados("");
					}, 500);
				}
				else alert("Seleccione por lo menos una encuesta para continuar");
			}
			function ChEncuesta(evt) {
				evt.preventDefault();
				var a = $(this);
				a.toggleClass("btn-success").toggleClass("btn-danger").children("i").toggleClass("fa-minus").toggleClass("fa-check");
			}
			function ChEvaluado(evt) {
				evt.preventDefault();
				var a = $(this);
				a.toggleClass("btn-success").toggleClass("btn-danger").children("i").toggleClass("fa-minus").toggleClass("fa-check");
			}
			function MuestraEvaluados(texto) {
				for(var i in evaluados) {
					texto = texto.toUpperCase();
					var evaluado = evaluados[i];
					if(evaluado.nevo == "" || evaluado.nevo.toUpperCase().indexOf(texto) > -1) {
						$("#modal-evaluados-tbody #tr-" + evaluado.evo).show();
					}
					else {
						$("#modal-evaluados-tbody #tr-" + evaluado.evo).hide();
					}
				}
			}
			function ProcesarEncuestas(evt) {
				evt.preventDefault();
				var sl_evaluados = $("#modal-evaluados .btn-success");
				if(sl_evaluados.length > 0) {
					arr_evaluados = new Array();
					$.each(sl_evaluados, function() {
						var tr = $(this).parent().parent();
						arr_evaluados.push(tr.data("evo") + "@" + tr.data("pevo"));
					});
					var p = {
						_token: "{{ csrf_token() }}",
						eva: document.getElementById("eva").value,
						peva: document.getElementById("peva").value,
						evos: arr_evaluados,
						encs: encuestas
					};
					$.post("{{ url('encuestas/ajax/asigna-evaluadores') }}", p, function(response) {
						if(response.success) {
							alert("Encuestas programadas!");
							location.reload();
						}
						else alert(response.msg);
					}, "json");
				}
				else alert("Seleccione por lo menos un evaluado para continuar");
			}
			function BuscaEvaluado() {
				var texto = document.getElementById("tb-evaluado-filter").value;
				MuestraEvaluados(texto);
			}
			$.each(pivots, function() {
				var tr = $(this);
				var ch_class = ".tr-" + tr.data("eva");
				var cant = $(ch_class).length;
				tr.on("click", function() {
					$(ch_class).toggle();
				}).children("td").eq(2).html("<i><b>" + cant + " evaluaciones programadas</b></i>").next().append(
					$("<a/>").attr({
						"href": "#",
						"data-eva": tr.data("eva"),
						"data-peva": tr.data("peva"),
						"data-class": ch_class
					}).addClass("btn btn-primary btn-xs").html("Programar más evaluaciones").on("click", AsignarEncuestas)
				);
			});
			$(".btn-encuesta").on("click", ChEncuesta);
			$(".btn-evaluado").on("click", ChEvaluado);
			$("#modal-encuestas-sv").on("click", AsignarEvaluados);
			$("#modal-evaluados-sv").on("click", ProcesarEncuestas);
			$("#tb-evaluado-filter").on("keyup", BuscaEvaluado);
		</script>
	</body>
</html>