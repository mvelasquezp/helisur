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
            .p-result>img{width:24px}
            .list-group-item{padding:}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<form id="form-final" class="col" action="{{ url('responder/valorar') }}" method="post">
					<h2 class="text-primary">Ya casi has terminado</h2>
					<p class="text-secondary">Para culminar con la evaluación, te agradeceremos responder unas últimas preguntas</p>
					<input type="hidden" name="eid" value="{{ $eid }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="eva" value="{{ $encuesta->eva }}">
					<input type="hidden" name="peva" value="{{ $encuesta->peva }}">
					<hr>
					<ul class="list-group">
                        @foreach($evaluados as $evaluado)
						<li class="list-group-item">
							<div class="row" id="ip-{{ $evaluado->uid }}-{{ $evaluado->pid }}">
								<div class="col-12 col-md-6 no-margin text-center">
									<img src="{{ url('imagen', [$evaluado->uid]) }}" style="width:25%">
									<p class="text-info">{{ $evaluado->evaluado }}</p>
									<br>
									<p class="text-dark">En una escala del 1 al 10, y siendo 1 la valoración mínima y 10 la máxima, cómo calificarías a <b>{{ $evaluado->evaluado }}</b></p>
									<br>
									<input type="range" name="valoracion[]" min="1" max="10" value="5" step="1" class="slider">
									<p class="p-result">Puntaje asignado: <b>5</b></p>
								</div>
								<div class="col-12 col-md-6">
									<input type="hidden" name="ids[]" value="{{ implode('|', [$evaluado->uid, $evaluado->pid]) }}" data-uid="{{ $evaluado->uid }}" data-pid="{{ $evaluado->pid }}">
									<p class="text-success">Menciona tres aspectos positivos o fortalezas</p>
									<div class="form-group">
										<input type="text" name="fs1[]" class="form-control form-control-sm form-mejora mandatory" placeholder="Aspecto positivo 1">
									</div>
									<div class="form-group">
										<input type="text" name="fs2[]" class="form-control form-control-sm form-mejora" placeholder="Aspecto positivo 2">
									</div>
									<div class="form-group">
										<input type="text" name="fs3[]" class="form-control form-control-sm form-mejora" placeholder="Aspecto positivo 3">
									</div>
									<p class="text-danger">Menciona tres aspectos a mejorar</p>
									<div class="form-group">
										<input type="text" name="db1[]" class="form-control form-control-sm form-mejora mandatory" placeholder="Aspecto a mejorar 1">
									</div>
									<div class="form-group">
										<input type="text" name="db2[]" class="form-control form-control-sm form-mejora" placeholder="Aspecto a mejorar 2">
									</div>
									<div class="form-group">
										<input type="text" name="db3[]" class="form-control form-control-sm form-mejora" placeholder="Aspecto a mejorar 3">
									</div>
								</div>
							</div>
						</li>
                        @endforeach
					</ul>
					<p class="text-right">
						<br>
						<a href="#" id="sv-avance" class="btn btn-primary"><i class="fa fa-floppy-o"></i><span> Guardar avance</span></a>
						<button class="btn btn-success"><i class="fa fa-check"></i> Finalizar encuesta</button>
					</p>
				</form>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var labels = ["", "Totalmente en desacuerdo", "En desacuerdo", "Ni de acuerdo ni en desacuerdo", "De acuerdo", "Totalmente de acuerdo"];
			$(".slider").val(5).on("change", function(evt) {
				var input = $(this);
				var valor = input.val();
				input.next().children("b").html(valor);
			});
			$("#form-final").on("submit", function() {
				var finputs = $(".mandatory");
				var ready = true;
				$.each(finputs, function() {
					ready = ready && $(this).val() != "";
				});
				if(!ready) {
					alert("Debe ingresar al menos un aspecto positivo y un aspecto a mejorar para cada uno de sus evaluados.");
					event.preventDefault();
				}
			});
			$("#sv-avance").on("click", function(evt) {
				evt.preventDefault();
				var a = $(this);
				a.removeClass("btn-primary").addClass("btn-light").children("span").html(" Por favor, espere...");
				//armar array
				var bloques = $("ul.list-group").children(".list-group-item");
				var avance = new Array();
				$.each(bloques, function() {
					var bloque = $(this).children(".row");
					avance.push({
						uid: bloque.children("div").eq(1).children("input[type=hidden]").eq(0).data("uid"),
						pid: bloque.children("div").eq(1).children("input[type=hidden]").eq(0).data("pid"),
						pts: bloque.children("div").eq(0).children("input[type=range]").eq(0).val(),
						f1: bloque.children("div").eq(1).children(".form-group").eq(0).children("input[type=text]").eq(0).val(),
						f2: bloque.children("div").eq(1).children(".form-group").eq(1).children("input[type=text]").eq(0).val(),
						f3: bloque.children("div").eq(1).children(".form-group").eq(2).children("input[type=text]").eq(0).val(),
						m1: bloque.children("div").eq(1).children(".form-group").eq(3).children("input[type=text]").eq(0).val(),
						m2: bloque.children("div").eq(1).children(".form-group").eq(4).children("input[type=text]").eq(0).val(),
						m3: bloque.children("div").eq(1).children(".form-group").eq(5).children("input[type=text]").eq(0).val()
					});
				});
				//console.log(avance);
				var p = {
					_token: "{{ csrf_token() }}",
					eva: $("input[name=eva]").val(),
					peva: $("input[name=peva]").val(),
					enc: $("input[name=eid]").val(),
					avn: avance
				};
				$.post("{{ url('responder/ajax/sv-avance') }}", p, function(response) {
					if(!response.success) {
						alert(response.msg);
					}
					a.removeClass("btn-light").addClass("btn-primary").children("span").html(" Guardar avance");
				}, "json").fail(function() {
					a.removeClass("btn-light").addClass("btn-danger").children("span").html(" No se guardó avance");
				});
				//hacer post
			});
			$(".form-mejora").val("");
		</script>
		@if(isset($state))
		<script type="text/javascript">
			var valores = {!! json_encode($state) !!};
			for(var i in valores) {
				var valor = valores[i];
				var div = $("#ip-" + valor.uid + "-" + valor.pid);
				div.children("div").eq(0).children("input[type=range]").val(valor.pts);
				div.children("div").eq(0).children(".p-result").children("b").html(valor.pts);
				div.children("div").eq(1).children(".form-group").eq(0).children("input[type=text]").val(valor.f1);
				div.children("div").eq(1).children(".form-group").eq(1).children("input[type=text]").val(valor.f2);
				div.children("div").eq(1).children(".form-group").eq(2).children("input[type=text]").val(valor.f3);
				div.children("div").eq(1).children(".form-group").eq(3).children("input[type=text]").val(valor.m1);
				div.children("div").eq(1).children(".form-group").eq(4).children("input[type=text]").val(valor.m2);
				div.children("div").eq(1).children(".form-group").eq(5).children("input[type=text]").val(valor.m3);
			}
		</script>
		@endif
	</body>
</html>