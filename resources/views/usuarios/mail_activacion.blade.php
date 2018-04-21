<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
	    	#btn-sv-msg{display:none}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<br>
			<div class="row">
				<div class="col-xs-12 col-4">
					<form>
						<div class="form-group">
							<label for="tb-saludo">Saludo</label>
							<input type="text" class="form-control" id="tb-saludo" placeholder="Saludo del e-mail" value="{{ $email->saludo }}">
						</div>
						<div class="form-mensaje">
							<label for="tb-mensaje">Cuerpo del mensaje</label>
							<textarea class="form-control" id="tb-mensaje" rows="8" style="resize:none">{{ $email->cuerpo }}</textarea>
						</div>
						<div class="form-group">
							<label for="tb-enlace">Texto del botón</label>
							<input type="text" class="form-control" id="tb-enlace" placeholder="Saludo del e-mail" value="{{ $email->enlace }}">
						</div>
						<a href="#" id="btn-preview" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Previsualizar</a>
					</form>
				</div>
				<div class="col-xs-12 col-8">
					<!-- no css -->
					<br>
					<table style="border-collapse:collapse;border-width:0;">
						<tr>
							<td style="background-color:#e0e0e0;vertical-align:bottom;text-align:left;padding:10px 40px 10px 10px;">
								<img src="{{ asset('images/helisur-logo-big.png') }}" style="width:128px;">
							</td>
							<td style="padding:10px;">
								<h1 id="tx-saludo" style="font-family:Verdana;color:#0d47a1;">{{ $email->saludo }} {{ $usuario->nombre }}</h1>
								<hr>
								<?php $vCuerpo = explode("\n", $email->cuerpo);?>
								<group id="tx-mensaje">
								@foreach($vCuerpo as $parrafo)
								<p style="font-family:Verdana;color:#808080;">{{ $parrafo }}</p>
								@endforeach
								</group>
								<p style="font-family:Verdana;text-align:right;"><a id="tx-enlace" href="javascript:void(0)">{{ $email->enlace }}</a></p>
							</td>
						</tr>
					</table>
					<p class="text-center">
						<br>
						<a id="btn-sv-msg" href="#" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Guardar mensaje</a>
					</p>
					<!-- fin no css -->
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			$("#btn-preview").on("click", function(event) {
				event.preventDefault();
				var mail = {
					saludo: document.getElementById("tb-saludo").value,
					mensaje: document.getElementById("tb-mensaje").value,
					enlace: document.getElementById("tb-enlace").value
				};
				document.getElementById("tx-saludo").innerHTML = mail.saludo;
				var vMensaje = mail.mensaje.split("\n");
				var nLineas = vMensaje.length;
				$("#tx-mensaje").empty();
				for(var i = 0; i < nLineas; i++) {
					$("#tx-mensaje").append(
						$("<p/>").css({
							"font-family": "Verdana",
							"color": "#808080"
						}).html(vMensaje[i])
					);
				}
				document.getElementById("tx-enlace").innerHTML = mail.enlace;
				$("#btn-sv-msg").fadeIn(150);
			});
		</script>
	</body>
</html>