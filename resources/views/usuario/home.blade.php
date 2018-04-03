<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
			.no-margin>p{margin:0}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<div class="col no-margin">
					<p class="text-secondary">Bienvenido</p>
					<h2 class="text-primary">{{ $entidad->des_nombre_3 }}</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
				@if(count($pendientes) > 0)
					@foreach($pendientes as $idx => $pendiente)
					<div id="dv-encuesta-{{ $pendiente->eid }}" class="card bg-light" style="display:inline-block;margin:0 0.15rem;width:18rem">
						<div class="card-body">
							<h5 class="card-title">{{ $pendiente->encuesta }}</h5>
							<h6 class="card-subtitle mb-2 text-muted">
								{{ $pendiente->cant }} preguntas<br>
								{{ $pendiente->encuestas }} evaluado(s)
							</h6>
							<p class="card-text text-muted">
								@if(strcmp($pendiente->estado,"En curso") == 0)
								Progreso: {{ $pendiente->prog }} / {{ $pendiente->cant }}
								@elseif(strcmp($pendiente->estado,"Valorando") == 0)
								Preguntas de valoración
								@elseif(strcmp($pendiente->estado,"Finalizada") == 0)
								Encuesta terminada
								@else
								Encuesta lista para comenzar
								@endif
							</p>
							@if(strcmp($pendiente->estado,"Finalizada") == 0)
							<a href="javascript:void(0)" class="btn btn-success"><i class="fa fa-smile-o"></i> Gracias por participar</a>
							@else
							<a href="{{ url('responder', [$pendiente->eid]) }}" class="btn btn-primary">{{ $pendiente->prog == 0 ? "Iniciar" : "Continuar" }}</a>
							@endif
						</div>
					</div>
					@endforeach
				@else
					<p class="text-dark">No tiene encuestas pendientes de responder</p>
				@endif
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		</script>
	</body>
</html>