<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
			.card-header{padding:0.1rem 0.5rem}
			.card-body{padding:0.5rem 1rem}
			.card-body>ul{list-style:none;margin:0;padding:0;}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<div class="container">
			<div class="row">
				<div class="col">
					<h3 class="text-primary">{{ $encuesta->nombre }} | Programar evaluadores</h3>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-4">
					<p class="text-secondary">
						<a href="#" class="btn btn-success btn-sm"><i class="fa fa-refresh"></i> Cargar los puestos que evaluarán</a>
						<a href="#" class="btn btn-info btn-sm"><i class="fa fa-info"></i> Ayuda</a>
					</p>
					<div class="multi-collapse">
					@foreach($jerarquias as $idx => $jerarquia)
						<div class="card">
							<div class="card-header" id="dv-acc-header-{{ $jerarquia->numero }}">
								<h5 class="mb-0">
									<button class="btn btn-link" data-toggle="collapse" data-target="#dv-acc-puesto-{{ $jerarquia->numero }}" aria-expanded="true" aria-controls="collapseOne">Nivel {{ $jerarquia->numero }}</button>
								</h5>
							</div>
							<div id="dv-acc-puesto-{{ $jerarquia->numero }}" class="collapse" aria-labelledby="dv-acc-header-{{ $jerarquia->numero }}" data-parent="#accordion">
								<div class="card-body">
									<ul>
										<li><label><input type="checkbox" id="chb-acc-all-{{ $jerarquia->numero }}" class="ch-all"><tag>&nbsp;Seleccionar todo</tag></label></li>
										@foreach($puestos[$idx] as $jdx => $puesto)
										<li><label><input type="checkbox" value="{{ $puesto->id }}" class="ch-unique">&nbsp;{{ $puesto->puesto }} [{{ $puesto->oficina }}]</label></li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					@endforeach
					</div>
				</div>
				<div class="col-8">columna derecha</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			$(".ch-all").on("change", function() {
				var ch = $(this);
				if(ch.prop("checked")) {
					ch.parent().parent().parent().children("li").children("label").children(".ch-unique").prop("checked", true);
					ch.next().html("&nbsp;Desmarcar todo");
				}
				else {
					ch.parent().parent().parent().children("li").children("label").children(".ch-unique").prop("checked", false);
					ch.next().html("&nbsp;Seleccionar todo");
				}
			});
		</script>
	</body>
</html>