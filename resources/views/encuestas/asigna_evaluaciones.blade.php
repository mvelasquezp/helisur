<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
			.card-header{padding:0.1rem 0.5rem}
			.card-body{padding:0.5rem 1rem}
			.card-body>ul{list-style:none;margin:0;padding:0;}
			.list-group-item>label{margin:0}
			.v-separator{height:10px}
			.hd-puesto{display:none}
			.page-title{margin:0}
			.no-margin>*{margin:2px 0}
			.no-margin>.text-secondary{font-size:11px}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="v-separator"></div>
		@if(count($programacion) > 0)
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="alert alert-info" role="alert">
						<form class="form-inline">
							<input type="hidden" id="ins-eid" value="{{ $encuesta->id }}">
							<label for="ins-evaluador">
								Evaluador&nbsp;
								<input type="text" class="form-control form-control-sm" id="ins-evaluador" placeholder="Quién evaluará" style="width:320px">
							</label><input type="hidden" id="ins-eva"><input type="hidden" id="ins-peva">&nbsp;
							<label for="ins-evaluado">
								Evaluado&nbsp;
								<input type="text" class="form-control form-control-sm" id="ins-evaluado" placeholder="Quién será evaluado" style="width:320px">
							</label><input type="hidden" id="ins-evo"><input type="hidden" id="ins-pevo">
							&nbsp;<a href="#" id="btn-evaluacion-agrega" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Agregar</a>
							&nbsp;<a href="{{ url('encuestas/programacion') }}" class="btn btn-primary btn-sm">Volver a la programación</a>
						</form>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<table class="table table-striped">
						<thead>
							<tr>
								<th colspan="3" class="text-center">
									<h5 class="text-primary">Usuarios programados para la encuesta: {{ $encuesta->nombre }}</h5>
								</th>
							</tr>
							<tr>
								<th>Evaluador</th>
								<th>Evaluado</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($programacion as $idx => $fila)
							<tr>
								<td class="no-margin">
									<p class="text-dark">{{ $fila->neva }}</p>
									<p class="text-secondary">{{ $fila->peva }} | {{ $fila->oeva }}</p>
								</td>
								<td class="no-margin">
									<p class="text-dark">{{ $fila->nevo }}</p>
									<p class="text-secondary">{{ $fila->pevo }} | {{ $fila->oevo }}</p>
								</td>
								<td>
									@if(strcmp($fila->estado,"Programado") == 0)
									<a href="#" class="btn btn-danger btn-xs btn-retira" data-eva="{{ $fila->auid }}" data-peva="{{ $fila->apid }}" data-evo="{{ $fila->ouid }}" data-pevo="{{ $fila->opid }}" data-enc="{{ $encuesta->id }}" data-nom="{{ $encuesta->nombre }}"><i class="fa fa-remove"></i> Retirar</a>
									@elseif(strcmp($fila->estado,"Finalizada") == 0)
									<a href="#" class="btn btn-success btn-xs"><i class="fa fa-check"></i> Terminada</a>
									@else
									<a href="#" class="btn btn-primary btn-xs" data-eva="{{ $fila->auid }}" data-peva="{{ $fila->apid }}" data-evo="{{ $fila->ouid }}" data-pevo="{{ $fila->opid }}" data-enc="{{ $encuesta->id }}" data-nom="{{ $encuesta->nombre }}"><i class="fa fa-refresh"></i> Ingresar</a>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal programa usuario -->
		<div class="modal fade bd-example-modal-lg" id="modal-usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Seleccionar usuario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col">
								<form class="form-inline">
									<input type="hidden" id="mod-modo">
									<label for="mod-keyword">
										Qué buscar&nbsp;
										<input type="text" class="form-control form-control-sm" id="mod-keyword" placeholder="Ingrese nombre" style="width:320px">
									</label>
									&nbsp;<button id="mod-busca" type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
								</form>
							</div>
						</div>
						<div class="row"></div>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Puesto</th>
									<th>Nombre</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="modal-usuario-tbody"></tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button id="modal-confirma-sv" type="button" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Realizar la programación</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			document.getElementById("ins-evaluador").value = "";
			document.getElementById("ins-eva").value = "";
			document.getElementById("ins-peva").value = "";
			document.getElementById("ins-evaluado").value = "";
			document.getElementById("ins-evo").value = "";
			document.getElementById("ins-pevo").value = "";
		</script>
		@else
		<div class="container">
			<div class="row">
				<div class="col-4">
					<ul class="list-group">
					@foreach($jerarquias as $idx => $jerarquia)
						<li class="list-group-item list-group-item-action">
							<label for="ch-selector-{{ $jerarquia->numero }}" class="d-flex justify-content-between align-items-center" style="width:100%;">
								<span>Nivel {{ $jerarquia->numero }}</span>
								<input id="ch-selector-{{ $jerarquia->numero }}" type="checkbox" class="ch-selector" value="{{ $jerarquia->numero }}">
							</label>
						</li>
					@endforeach
					</ul>
				</div>
				<div class="col-8">
					<div class="row">
						<div class="col">
							<div class="d-flex justify-content-between align-items-center">
								<h5 class="page-title text-primary">{{ $encuesta->nombre }} | Programar evaluadores</h5>
								<a href="#" id="btn-programar" class="btn btn-success btn-sm">Programar las evaluaciones</a>
							</div>
							<div class="v-separator"></div>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Jer.</th>
										<th>Área</th>
										<th>Puesto</th>
										<th>
											<a href="#" id="btn-ch-all" class="btn btn-xs btn-primary">Seleccionar todo</a>
										</th>	
									</tr>
								</thead>
								<tbody id="tbody-pst"></tbody>
							</table>
						</div>
					</div>
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
						<input type="hidden" id="modal-eid" value="{{ $encuesta->id }}">
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
		@endif
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			$(".ch-selector").prop("disabled", false).prop("checked", false);
			$(".ch-selector").on("change", function() {
				var ch = $(this);
				$(".ch-selector").prop("disabled", true);
				var jrs = new Array();
				var inputs = $(".ch-selector:checked");
				$.each(inputs, function() {
					jrs.push($(this).val());
				});
				var p = {
					_token: "{{ csrf_token() }}",
					jrs: jrs
				};
				$.post("{{ url('encuestas/ajax/ls-cargos') }}", p, function(response) {
					if(response.success) {
						$("#tbody-pst").empty();
						var puestos = response.data.puestos;
						for(var i in puestos) {
							var puesto = puestos[i];
							$("#tbody-pst").append(
								$("<tr/>").append(
									$("<td/>").html(puesto.num)
								).append(
									$("<td/>").html(puesto.oficina)
								).append(
									$("<td/>").html(puesto.puesto)
								).append(
									$("<td/>").append(
										$("<label/>").addClass("btn btn-xs btn-danger").append(
											$("<span/>").html("No programado")
										).append(
											$("<input/>").addClass("hd-puesto").attr("type","checkbox").val(puesto.id).on("change",hdPuestoOnChange)
										)
									)
								)
							);
						}
					}
					else alert(response.msg);
					$(".ch-selector").prop("disabled", false);
				}, "json").fail(function(error) {
					console.log(error);
					$(".ch-selector").prop("disabled", false);
				});
			});
			$("#btn-ch-all").on("click", function(event) {
				event.preventDefault();
				$(".hd-puesto").prop("checked", true).trigger("change");
			});
			$("#btn-programar").on("click", function(event) {
				event.preventDefault();
				var ids = $(".hd-puesto:checked");
				var inputs = new Array();
				$.each(ids, function() {
					inputs.push($(this).val());
				});
				var p = {
					_token: "{{ csrf_token() }}",
					eid: "{{ $encuesta->id }}",
					arr: inputs
				};
				$("input").prop("disabled", true);
				$("#btn-programar").hide();
				$.post("{{ url('encuestas/ajax/ls-programacion') }}", p, function(response) {
					$("#modal-confirma-tbody").empty();
					if(response.success) {
						$("#modal-confirma").modal("show");
						var data = response.data.ecs;
						document.getElementById("modal-eid").value = response.data.eid;
						for(var i in data) {
							var par = data[i];
							$("#modal-confirma-tbody").append(
								$("<tr/>").addClass("tr-selected").attr("data-selected",1).append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(par.neva)
									).append(
										$("<p/>").addClass("text-secondary").html(par.peva + " | " + par.oeva)
									)
								).append(
									$("<td/>").addClass("no-margin").append(
										$("<p/>").addClass("text-dark").html(par.nevo)
									).append(
										$("<p/>").addClass("text-secondary").html(par.pevo + " | " + par.oevo)
									)
								).append(
									$("<td/>").append(
										$("<a/>").attr("href","#").addClass("btn btn-danger btn-xs").append(
											$("<i/>").addClass("fa fa-remove")
										).append(" Retirar").on("click", toggleRow)
									)
								).append(
									$("<input/>").addClass("tr-input").attr("type","hidden").val(par.eva + "|" + par.pta + "|" + par.evo + "|" + par.pto)
								)
							);
						}
					}
					else alert(response.msg);
					$("input").prop("disabled", false);
					$("#btn-programar").show();
				}, "json").fail(function() {
					$("input").prop("disabled", false);
					$("#btn-programar").show();
				});
			});
			$("#modal-confirma-sv").on("click", function(event) {
				event.preventDefault();
				var inputs = $(".tr-selected .tr-input");
				var arr_post = new Array();
				$.each(inputs, function() {
					arr_post.push($(this).val());
				});
				var p = {
					_token: "{{ csrf_token() }}",
					eid: document.getElementById("modal-eid").value,
					arr: arr_post
				};
				$.post("{{ url('encuestas/ajax/sv-programacion') }}", p, function(response) {
					if(response.success) {
						alert("Evaluaciones programadas");
						location.reload();
					}
					else alert(response.msg);
				}, "json");
			});
			$("#mod-busca").on("click", function(event) {
				event.preventDefault();
				$("#modal-usuario-tbody").empty();
				var p = {
					_token: "{{ csrf_token() }}",
					txt: document.getElementById("mod-keyword").value
				};
				$.post("{{ url('encuestas/ajax/bsq-usuarios') }}", p, function(response) {
					var data = response.data.usuarios;
					for(var i in data) {
						var usuario = data[i];
						$("#modal-usuario-tbody").append(
							$("<tr/>").append(
								$("<td/>").addClass("no-margin").append(
									$("<p/>").addClass("text-dark").html(usuario.puesto)
								).append(
									$("<p/>").addClass("text-secondary").html(usuario.oficina)
								)
							).append(
								$("<td/>").addClass("no-margin").html(usuario.nombre)
							).append(
								$("<td/>").append(
									$("<a/>").attr({
										"href": "#",
										"data-uid": usuario.uid,
										"data-pid": usuario.pid,
										"data-nom": usuario.nombre
									}).addClass("btn btn-success btn-xs").html("Seleccionar").on("click", function(event) {
										event.preventDefault();
										var a = $(this);
										if(document.getElementById("mod-modo").value == "eva") {
											document.getElementById("ins-evaluador").value = a.data("nom");
											document.getElementById("ins-eva").value = a.data("uid");
											document.getElementById("ins-peva").value = a.data("pid");
										}
										else {
											document.getElementById("ins-evaluado").value = a.data("nom");
											document.getElementById("ins-evo").value = a.data("uid");
											document.getElementById("ins-pevo").value = a.data("pid");
										}
										$("#modal-usuario").modal("hide");
									})
								)
							)
						);
					}
				}, "json");
			});
			$("#ins-evaluador").on("focus", function() {
				document.getElementById("mod-modo").value = "eva";
				document.getElementById("mod-keyword").value = "";
				$("#modal-usuario-tbody").empty();
				$("#modal-usuario").modal("show");
			});
			$("#ins-evaluado").on("focus", function() {
				document.getElementById("mod-modo").value = "evo";
				document.getElementById("mod-keyword").value = "";
				$("#modal-usuario-tbody").empty();
				$("#modal-usuario").modal("show");
			});
			$("#btn-evaluacion-agrega").on("click", function(event) {
				event.preventDefault();
				var p = {
					_token: "{{ csrf_token() }}",
					eva: document.getElementById("ins-eva").value,
					peva: document.getElementById("ins-peva").value,
					evo: document.getElementById("ins-evo").value,
					pevo: document.getElementById("ins-pevo").value,
					eid: document.getElementById("ins-eid").value
				};
				$.post("{{ url('encuestas/ajax/sv-programacion-individual') }}", p, function(response) {
					if(response.success) {
						alert("Evaluación programada");
						location.reload();
					}
					else alert(response.msg);
				}, "json");
			});
			$(".btn-retira").on("click", function(evt) {
				evt.preventDefault();
				var a = $(this);
				if(window.confirm("¿Está seguro de retirar la evaluación seleccionada? Podrá volver incorporar a los colaboradores retirados siempre y cuando la encuesta no haya sido lanzada.")) {
					var p = {
						_token: "{{ csrf_token() }}",
						eva: a.data("eva"),
						peva: a.data("peva"),
						evo: a.data("evo"),
						pevo: a.data("pevo"),
						eid: a.data("enc")
					};
					$.post("{{ url('encuestas/ajax/retira-evaluacion') }}", p, function(response) {
						if(response.success) location.reload();
						else alert(response.msg);
					}, "json");
				}
			});
			$("#modal-usuario").on("shown.bs.modal", function(evt) {
				document.getElementById("mod-keyword").focus();
			});
			function toggleRow(event) {
				event.preventDefault();
				var a = $(this);
				var tr = a.parent().parent();
				if(tr.data("selected") == 1) {
					tr.data("selected", 0);
					tr.removeClass("tr-selected");
					a.removeClass("btn-danger").addClass("btn-success").empty().append(
						$("<i/>").addClass("fa fa-plus")
					).append(" Agregar");
				}
				else {
					tr.data("selected", 1);
					tr.addClass("tr-selected");
					a.removeClass("btn-success").addClass("btn-danger").empty().append(
						$("<i/>").addClass("fa fa-remove")
					).append(" Retirar");
				}
			}
			function hdPuestoOnChange(event) {
				var input = $(this);
				if(input.prop("checked")) {
					input.prev().html("Programado");
					input.parent().removeClass("btn-danger").addClass("btn-primary");
				}
				else {
					input.prev().html("No programado");
					input.parent().removeClass("btn-primary").addClass("btn-danger");
				}
			}
		</script>
	</body>
</html>