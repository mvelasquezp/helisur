<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
            .text-secondary{font-size:0.75rem}
            .no-margin>*{margin:2px 0}
            .slider {-webkit-appearance:none;width:100%;height:10px;border-radius:5px;background:#d3d3d3;outline:none;opacity:0.7;-webkit-transition:.2s;transition:opacity .2s}
            .slider:hover{opacity:1;}
            .slider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:25px;height:25px;border-radius:50%;background:#0d47a1;cursor:pointer}
            .slider::-moz-range-thumb{width:32px;height:32px;border-radius:50%;background:#0d47a1;cursor:pointer}
            .p-result{margin-top:10px}
            .p-result>img,.p-paragraph>img{width:24px}
            .p-paragraph>b{font-size:1.15rem}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<form class="col-12 col-md-9" action="{{ url('responder/guardar') }}" method="post">
					<h2 class="text-primary">Pregunta #{{ ($encuesta->actual < 10 ? "0" : "") . $encuesta->actual }}<br><span class="text-dark">{{ $pregunta->texto }}</span></h2>
					<p class="text-secondary"><b class="text-danger">{{ $encuesta->nombre }}</b><br>{{ $pregunta->grupo }} > {{ $pregunta->concepto }} > {{ $pregunta->categoria }} > {{ $pregunta->subcategoria }}</p>
					<input type="hidden" name="eid" value="{{ $eid }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="pid" value="{{ $pregunta->pid }}">
					<input type="hidden" name="ptp" value="{{ $pregunta->ptp }}">
					<input type="hidden" name="nmp" value="{{ $encuesta->actual }}">
					<input type="hidden" name="eva" value="{{ $encuesta->eva }}">
					<input type="hidden" name="peva" value="{{ $encuesta->peva }}">
					<hr>
					<ul class="list-group">
                        @foreach($evaluados as $evaluado)
						<li class="list-group-item">
							<div class="row">
								<div class="col-3 col-md-2 col-lg-1">
									<img src="{{ url('imagen', [$evaluado->uid]) }}" class="img-fluid">
								</div>
								<div class="col-9 col-md-4 col-lg-5 no-margin">
									<p class="text-dark">{{ $evaluado->evaluado }}</p>
									<p class="text-secondary">{{ $evaluado->puesto }} | {{ $evaluado->oficina }}</p>
								</div>
								<div class="col-12 col-md-6 col-lg-6">
									<input type="hidden" name="ids[]" value="{{ implode('|', [$evaluado->uid, $evaluado->pid]) }}">
									<input type="range" name="puntaje[]" min="1" max="5" value="3" step="1" class="slider">
									<p class="text-secondary p-result">
										<img src="{{ asset('images/faces/3.png') }}">
										<span>Ni de acuerdo ni en desacuerdo</span>
									</p>
								</div>
							</div>
						</li>
                        @endforeach
					</ul>
					<p class="text-right">
						<br>
						<button class="btn btn-success"><i class="fa fa-check"></i> Siguiente pregunta</button>
					</p>
				</form>
				<div class="col-12 col-md-3">
					<div class="alert alert-secondary">
						<h4 class="text-dark">Leyenda</h4>
						<hr>
						<p class="p-paragraph">
							<img src="{{ asset('images/faces/1.png') }}">
							<b style="color:#e53935;">1 </b> Totalmente en desacuerdo 
						</p>
						<hr>
						<p class="p-paragraph">
							<img src="{{ asset('images/faces/2.png') }}">
							<b style="color:#ef6c00;">2</b> En desacuerdo 
						</p>
						<hr>
						<p class="p-paragraph">
							<img src="{{ asset('images/faces/3.png') }}">
							<b style="color:#f9a825;">3</b> Ni de acuerdo ni en desacuerdo 
						</p>
						<hr>
						<p class="p-paragraph">
							<img src="{{ asset('images/faces/4.png') }}">
							<b style="color:#1976d2;">4</b> De acuerdo 
						</p>
						<hr>
						<p class="p-paragraph">
							<img src="{{ asset('images/faces/5.png') }}">
							<b style="color:#4caf50;">5</b> Totalmente de acuerdo 
						</p>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var labels = ["", "Totalmente en desacuerdo", "En desacuerdo", "Ni de acuerdo ni en desacuerdo", "De acuerdo", "Totalmente de acuerdo"];
			$(".slider").val(3).on("change", function(evt) {
				var input = $(this);
				var valor = input.val();
				input.next().children("img").attr("src", "{{ asset('images/faces') }}/" + valor + ".png");
				input.next().children("span").html(labels[valor]);
			});
		</script>
	</body>
</html>