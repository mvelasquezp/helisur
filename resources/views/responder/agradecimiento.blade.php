<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<form class="col" action="{{ url('responder/guardar') }}" method="post">
					<h2 class="text-primary">Encuesta terminada</h2>
					<p class="text-dark">Gracias por responder a la encuesta <b>{{ $encuesta->nombre }}</b>. Sus respuestas han sido almacenadas correctamente. A continuación, podrá cerrar esta ventana.</p>
					<br>
					<p class="text-right">
						<br>
						<a href="{{ url('/') }}" class="btn btn-success"><i class="fa fa-check"></i> Volver a la lista de preguntas</a>
					</p>
				</form>
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