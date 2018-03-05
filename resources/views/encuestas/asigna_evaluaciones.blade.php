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
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="v-separator"></div>
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
				$.post("{{ url('encuestas/ajax/sv-programacion') }}", p, function(response) {
					if(response.success) {
						//
					}
					else alert(response.msg);
					$("input").prop("disabled", false);
					$("#btn-programar").show();
				}, "json").fail(function() {
					$("input").prop("disabled", false);
					$("#btn-programar").show();
				});
			});
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