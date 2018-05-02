<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/highcharts.css') }}">
		<style type="text/css">
			.dv-grafico{height:360px;padding:15px;width:100%}
			.ch-title{margin:10px 0}
			.ch-input-enc{display:none}
			.ch-input-enc+label{user-select:none}
			.ch-input-enc:checked+label{background-color:rgba(0,0,0,.1)}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- BODY -->
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="alert alert-secondary" role="alert">
						<div class="row">
							<div class="col-10">
								<a id="sl-gerencia" href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-gerencia">Seleccionar Gerencia</a>
								<a id="sl-area" href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-area">Seleccionar Área</a>
								<a id="sl-puesto" href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-puesto">Seleccionar Puesto</a>
								<a id="sl-personal" href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-personal">Seleccionar Colaborador</a>
							</div>
							<div class="col-2 text-right">
								<a id="sl-encuestas" href="#" class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-encuesta">Todas las encuestas</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="helisur-tab" data-toggle="tab" href="#helisur" role="tab" aria-controls="helisur" aria-selected="true">Helisur</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="gerencia-tab" data-toggle="tab" href="#gerencia" role="tab" aria-controls="gerencia" aria-selected="false">Gerencia</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="area-tab" data-toggle="tab" href="#area" role="tab" aria-controls="area" aria-selected="false">Área</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="colaborador-tab" data-toggle="tab" href="#colaborador" role="tab" aria-controls="colaborador" aria-selected="false">Colaborador</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="helisur" role="tabpanel" aria-labelledby="helisur-tab">
							<div class="container">
								<div class="row">
									<div class="col-4 col-md-3 col-table-container">
										<h4 class="text-primary ch-title">Promedios - Helisur</h4>
										<table class="table table-striped">
											<thead>
												<tr>
													<th>Competencia</th>
													<th>Puntaje</th>
												</tr>
											</thead>
											<tbody>
												@foreach($grafico as $idx => $competencia)
												<tr>
													<td>{{ $competencia->label }}</td>
													<td class="text-right">{{ number_format($competencia->y,3) }}</td>
												</tr>
												@endforeach
											</tbody>
											<tfoot>
												<tr>
													<th>Total general</th>
													<th class="text-right">{{ number_format($prom,3) }}</th>
												</tr>
											</tfoot>
										</table>
									</div>
									<div class="col-8 col-md-9">
										<div id="ch-helisur" class="dv-grafico"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="gerencia" role="tabpanel" aria-labelledby="gerencia-tab">
							<div class="container">
								<div class="row">
									<div class="col-4 col-md-3 col-table-container">
										<p class="text-secondary">Seleccione gerencia para generar el reporte</p>
									</div>
									<div class="col-8 col-md-9">
										<div id="ch-gerencia" class="dv-grafico"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="area" role="tabpanel" aria-labelledby="area-tab">
							<div class="container">
								<div class="row">
									<div class="col-4 col-md-3 col-table-container">
										<p class="text-secondary">Seleccione area para generar el reporte</p>
									</div>
									<div class="col-8 col-md-9">
										<div id="ch-area" class="dv-grafico"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="colaborador" role="tabpanel" aria-labelledby="colaborador-tab">
							<div class="container">
								<div class="row">
									<div class="col-4 col-md-3 col-table-container">
										<p class="text-secondary">Seleccione colaborador para generar el reporte</p>
									</div>
									<div class="col-8 col-md-9">
										<div id="ch-colaborador" class="dv-grafico"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- modal gerencia -->
		<div id="modal-gerencia" class="modal fade"  tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seleccionar gerencia</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="list-group">
							@foreach($gerencias as $gerencia)
							<a href="#" class="list-group-item list-group-item-action opt-gerencia" data-id="{{ $gerencia->id }}">{{ $gerencia->value }}</a>
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal area -->
		<div id="modal-area" class="modal fade"  tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seleccionar área</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>Modal body text goes here.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal puesto -->
		<div id="modal-puesto" class="modal fade"  tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seleccionar puesto</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>Modal body text goes here.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal personal -->
		<div id="modal-personal" class="modal fade"  tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seleccionar personal</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>Modal body text goes here.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal encuesta -->
		<div id="modal-encuesta" class="modal fade"  tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Seleccionar encuesta</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="list-group">
							@foreach($encuestas as $encuesta)
							<input type="checkbox" class="ch-input-enc" id="ch-encuesta-{{ $encuesta->id }}" value="{{ $encuesta->id }}">
							<label class="list-group-item list-group-item-action" for="ch-encuesta-{{ $encuesta->id }}">{{ $encuesta->value }}</label>
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="sv-encuesta">Cargar reporte</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script src="{{ asset('highcharts/highcharts.js') }}"></script>
		<script src="{{ asset('highcharts/highcharts-more.js') }}"></script>
		<script src="{{ asset('highcharts/modules/exporting.js') }}"></script>
		<script type="text/javascript">
			var chartWidth;
			function ls_encuestas() {
				var ips = $(".ch-input-enc:checked");
				var encuestas = new Array();
				$.each(ips, function() {
					encuestas.push($(this).val());
				});
				return encuestas;
			}
			function graficar_gerencias() {
				var p = { _token:"{{ csrf_token() }}",ecs:ls_encuestas() };
				$.post("{{ url('resultados/ajax/ls-empresa') }}", p, function(response) {
					if(response.success) {
						var arr_categorias = [], arr_series = [];
						var grf_gerencias = response.data.grafico;
						for(var i in grf_gerencias) {
							arr_categorias.push(grf_gerencias[i].label);
							arr_series.push(parseFloat(grf_gerencias[i].y));
						}
						$("#ch-helisur").highcharts({
					        chart: { polar: true, type: 'line' },
					        title: { text: 'Promedio de PUNTAJE', x: -80 },
					        pane: { size: '80%' },
					        xAxis: {
					            categories: arr_categorias,
					            tickmarkPlacement: 'on',
					            lineWidth: 0
					        },
					        yAxis: {
					            gridLineInterpolation: 'polygon',
					            lineWidth: 0,
					            min: 0
					        },
					        tooltip: {
					            shared: true,
					            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.3f} pts.</b><br/>'
					        },
					        legend: {
					            align: 'right',
					            verticalAlign: 'top',
					            y: 70,
					            layout: 'vertical'
					        },
					        series: [{
					            name: "Helisur",
					            data: arr_series,
					            pointPlacement: 'on'
					        }]

					    });
					    chartWidth = $("#ch-helisur").width();
					}
					else alert("Parámetros incorrectos");
				}, "json");
			}
		</script>
		<script type="text/javascript">
			function optPersonalOnClick(evt) {
				var a = $(this);
				evt.preventDefault();
				$(".opt-personal.active").removeClass("active");
				a.addClass("active");
				var txt = a.html().substring(0,35) + (a.html().length > 35 ? "..." : "");
				$("#sl-personal").data("id", a.data("id")).html(txt).removeClass("btn-danger").addClass("btn-success").attr("title", a.html());
				$("#modal-personal").modal("hide");
				//carga datos para el grafico
				var p = { _token:"{{ csrf_token() }}", uid:a.data("id"), pid:a.data("pid"), ecs:ls_encuestas() };
				$.post("{{ url('resultados/ajax/ch-colaborador') }}", p, function(response) {
					if(response.success) {
						var graficos = response.data.datos;
						var arr_categorias = [], arr_series = [];
						var tbody = $("<tbody/>");
						var total = 0, cont = 0;
						for(var i in graficos) {
							var puntaje = parseFloat(graficos[i].y);
							var label = graficos[i].label;
							tbody.append(
								$("<tr/>").append(
									$("<td/>").html(label)
								).append(
									$("<td/>").addClass("text-right").html(puntaje.toFixed(3))
								)
							);
							total += puntaje;
							cont++;
							arr_categorias.push(label);
							arr_series.push(puntaje);
						}
						if(cont > 0) total /= cont;
						$("#colaborador .col-table-container").empty().append(
							$("<table/>").addClass("table table-striped").append(
								$("<thead/>").append(
									$("<tr/>").append(
										$("<th/>").html("Competencia")
									).append(
										$("<th/>").html("Puntaje")
									)
								)
							).append(tbody).append(
								$("<tfoot/>").append(
									$("<tr/>").append(
										$("<th/>").html("Total general")
									).append(
										$("<th/>").addClass("text-right").html(total.toFixed(3))
									)
								)
							)
						);
						//inserta el grafico
						$("#ch-colaborador").highcharts({
					        chart: { type: 'column', width: chartWidth },
					        title: { text: 'Promedio de PUNTAJE' },
					        subtitle: { text: 'Colaborador: ' +  $("#sl-personal").html()},
					        xAxis: { categories: arr_categorias },
					        yAxis: {
					            min: 0,
					            title: { text: 'Puntaje' }
					        },
					        tooltip: {
					            headerFormat: '<span style="font-size:10px">{point.key}: {point.y:.3f}</span><table>',
					            pointFormat: '',
					            footerFormat: '</table>',
					            shared: true,
					            useHTML: true
					        },
					        plotOptions: {
					            column: {
					                pointPadding: 0.2,
					                borderWidth: 0
					            }
					        },
					        series: [{
					            name: $("#sl-personal").html(),
					            data: arr_series

					        }]
					    });
					}
					else alert(response.msg);
				}, "json");
			}
			function optPuestoOnClick(evt) {
				var a = $(this);
				evt.preventDefault();
				$(".opt-puesto.active").removeClass("active");
				a.addClass("active");
				var txt = a.html().substring(0,35) + (a.html().length > 35 ? "..." : "");
				$("#sl-puesto").data("id", a.data("id")).html(txt).removeClass("btn-danger").addClass("btn-success").attr("title", a.html());
				$("#modal-puesto").modal("hide");
				//resetea los botones
				$("#sl-personal").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Colaborador");
				$("#modal-personal .modal-body").empty();
				//rearmar modals
				var p = { _token:"{{ csrf_token() }}",pst:a.data("id") };
				$.post("{{ url('resultados/ajax/ls-personal') }}", p, function(response) {
					if(response.success) {
						var colaboradores = response.data.colaboradores;
						var div = $("<div/>").addClass("list-group");
						for(var i in colaboradores) {
							var personal = colaboradores[i];
							div.append(
								$("<a/>").attr({
									"href": "#",
									"data-id": personal.id,
									"data-pid": personal.pid
								}).addClass("list-group-item list-group-item-action opt-puesto").html(personal.value).on("click", optPersonalOnClick)
							);
						}
						$("#modal-personal .modal-body").append(div);
					}
					else alert(response.msg);
				}, "json");
			}
			function optAreaOnClick(evt) {
				var a = $(this);
				evt.preventDefault();
				$(".opt-area.active").removeClass("active");
				a.addClass("active");
				var txt = a.html().substring(0,35) + (a.html().length > 35 ? "..." : "");
				$("#sl-area").data("id", a.data("id")).html(txt).removeClass("btn-danger").addClass("btn-success").attr("title", a.html());
				$("#modal-area").modal("hide");
				//resetea los botones
				$("#sl-puesto").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Puesto");
				$("#sl-personal").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Colaborador");
				$("#modal-puesto .modal-body").empty();
				$("#modal-personal .modal-body").empty();
				//rearmar modals
				var p = { _token:"{{ csrf_token() }}",ofc:a.data("id"),ecs:ls_encuestas() };
				$.post("{{ url('resultados/ajax/ls-puestos') }}", p, function(response) {
					if(response.success) {
						var puestos = response.data.puestos;
						var div = $("<div/>").addClass("list-group");
						for(var i in puestos) {
							var puesto = puestos[i];
							div.append(
								$("<a/>").attr({
									"href": "#",
									"data-id": puesto.id
								}).addClass("list-group-item list-group-item-action opt-puesto").html(puesto.value).on("click", optPuestoOnClick)
							);
						}
						$("#modal-puesto .modal-body").append(div);
						//arma el pinshi grafico
						var graficos = response.data.grafico;
						var arr_categorias = [], arr_series = [];
						var tbody = $("<tbody/>");
						var total = 0, cont = 0;
						for(var i in graficos) {
							var puntaje = parseFloat(graficos[i].y);
							var label = graficos[i].label;
							tbody.append(
								$("<tr/>").append(
									$("<td/>").html(label)
								).append(
									$("<td/>").addClass("text-right").html(puntaje.toFixed(3))
								)
							);
							total += puntaje;
							cont++;
							arr_categorias.push(label);
							arr_series.push(puntaje);
						}
						if(cont > 0) total /= cont;
						$("#area .col-table-container").empty().append(
							$("<table/>").addClass("table table-striped").append(
								$("<thead/>").append(
									$("<tr/>").append(
										$("<th/>").html("Competencia")
									).append(
										$("<th/>").html("Puntaje")
									)
								)
							).append(tbody).append(
								$("<tfoot/>").append(
									$("<tr/>").append(
										$("<th/>").html("Total general")
									).append(
										$("<th/>").addClass("text-right").html(total.toFixed(3))
									)
								)
							)
						);
						//inserta el grafico
						$("#ch-area").highcharts({
					        chart: { type: 'column', width: chartWidth },
					        title: { text: 'Promedio de PUNTAJE' },
					        subtitle: { text: 'Área: ' +  $("#sl-area").html()},
					        xAxis: { categories: arr_categorias },
					        yAxis: {
					            min: 0,
					            title: { text: 'Puntaje' }
					        },
					        tooltip: {
					            headerFormat: '<span style="font-size:10px">{point.key}: {point.y:.3f}</span><table>',
					            pointFormat: '',
					            footerFormat: '</table>',
					            shared: true,
					            useHTML: true
					        },
					        plotOptions: {
					            column: {
					                pointPadding: 0.2,
					                borderWidth: 0
					            }
					        },
					        series: [{
					            name: $("#sl-area").html(),
					            data: arr_series

					        }]
					    });
					}
					else alert(response.msg);
				}, "json");
			}
			$(".opt-gerencia").on("click", function(evt) {
				var a = $(this);
				evt.preventDefault();
				$(".opt-gerencia.active").removeClass("active");
				a.addClass("active");
				var txt = a.html().substring(0,35) + (a.html().length > 35 ? "..." : "");
				$("#sl-gerencia").data("id", a.data("id")).html(txt).removeClass("btn-danger").addClass("btn-success").attr("title", a.html());
				$("#modal-gerencia").modal("hide");
				//resetea los botones
				$("#sl-area").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Área");
				$("#sl-puesto").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Puesto");
				$("#sl-personal").removeClass("btn-success").addClass("btn-danger").data("id","").html("Seleccionar Colaborador");
				$("#modal-area .modal-body").empty();
				$("#modal-puesto .modal-body").empty();
				$("#modal-personal .modal-body").empty();
				//rearmar modals
				var p = { _token:"{{ csrf_token() }}",grn:a.data("id"),ecs:ls_encuestas() };
				$.post("{{ url('resultados/ajax/ls-oficinas') }}", p, function(response) {
					if(response.success) {
						var oficinas = response.data.oficinas;
						var div = $("<div/>").addClass("list-group");
						for(var i in oficinas) {
							var oficina = oficinas[i];
							div.append(
								$("<a/>").attr({
									"href": "#",
									"data-id": oficina.id
								}).addClass("list-group-item list-group-item-action opt-area").html(oficina.value).on("click", optAreaOnClick)
							);
						}
						$("#modal-area .modal-body").append(div);
						//arma el pinshi grafico
						var graficos = response.data.grafico;
						var arr_categorias = [], arr_series = [];
						var tbody = $("<tbody/>");
						var total = 0, cont = 0;
						for(var i in graficos) {
							var puntaje = parseFloat(graficos[i].y);
							var label = graficos[i].label;
							tbody.append(
								$("<tr/>").append(
									$("<td/>").html(label)
								).append(
									$("<td/>").addClass("text-right").html(puntaje.toFixed(3))
								)
							);
							total += puntaje;
							cont++;
							arr_categorias.push(label);
							arr_series.push(puntaje);
						}
						if(cont > 0) total /= cont;
						$("#gerencia .col-table-container").empty().append(
							$("<table/>").addClass("table table-striped").append(
								$("<thead/>").append(
									$("<tr/>").append(
										$("<th/>").html("Competencia")
									).append(
										$("<th/>").html("Puntaje")
									)
								)
							).append(tbody).append(
								$("<tfoot/>").append(
									$("<tr/>").append(
										$("<th/>").html("Total general")
									).append(
										$("<th/>").addClass("text-right").html(total.toFixed(3))
									)
								)
							)
						);
						//inserta el grafico
						$("#ch-gerencia").highcharts({
					        chart: { type: 'column', width: chartWidth },
					        title: { text: 'Promedio de PUNTAJE' },
					        subtitle: { text: 'Gerencia: ' +  $("#sl-gerencia").html()},
					        xAxis: { categories: arr_categorias },
					        yAxis: {
					            min: 0,
					            title: { text: 'Puntaje' }
					        },
					        tooltip: {
					            headerFormat: '<span style="font-size:10px">{point.key}: {point.y:.3f}</span><table>',
					            pointFormat: '',
					            footerFormat: '</table>',
					            shared: true,
					            useHTML: true
					        },
					        plotOptions: {
					            column: {
					                pointPadding: 0.2,
					                borderWidth: 0
					            }
					        },
					        series: [{
					            name: $("#sl-gerencia").html(),
					            data: arr_series

					        }]
					    });
					}
					else alert(response.msg);
				}, "json");
			});
			$(".ch-input-enc").prop("checked", true);
			//iniciar
			graficar_gerencias();
			$("#sv-encuesta").on("click", function(evt) {
				evt.preventDefault();
				//recarga el grafico general
				graficar_gerencias();
				//recarga las gerencias
				if($(".opt-gerencia.active").length > 0) $(".opt-gerencia.active").trigger("click");
				//recarga los puestos
				if($(".opt-area.active").length > 0) $(".opt-area.active").trigger("click");
				//recarga los usuarios
				if($(".opt-personal.active").length > 0) $(".opt-personal.active").trigger("click");
				$("#modal-encuesta").modal("hide");
			})
		</script>
	</body>
</html>