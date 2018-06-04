<!DOCTYPE html>
<html>
	<head>
		<title>Correo confirmado correctamente</title>
		@include("common.styles")
		<style type="text/css">
			html,body{background-color:#002f60}
		</style>
	</head>
	<body>
		<div class="navigation-bar fixed-top">
			<header class="navbar-light">
				<img src="{{ asset('images/helisur-logo-big.png') }}">
				<div class="nav-profile">
					<h1>Gestión de Competencias</h1>
					<img src="{{ asset('images/user-default.png') }}" style="overflow-x:hidden;width:0;">
				</div>
				<button class="navbar-toggler text-dark" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</header>
		</div>
		<div class="container">
			<div class="row">
				<div class="col">
					<h1 class="text-light">Dirección de correo electrónico confirmada.</h1>
					<hr>
					<p class="text-light">Ya puedes comenzar a participar en el proceso de evaluación de competencias. A continuación te brindamos tu usuario y clave para acceder al sistema. Recuerda que debes anotarlas en un lugar seguro y que, en caso las olvides, podrás verlas haciendo clic en el enlace de activación proporcionado en tu correo electrónico.</p>
					<p class="text-light"><b>Usuario: </b>{{ $usuario->alias }}</p>
					<p class="text-light"><b>Clave: </b>{{ $usuario->app[0] . $usuario->app[1] . $usuario->nom[0] . $usuario->nom[1] . $usuario->cod }}</p>
					<p><br></p>
					<p class="text-light">Para ingresar, pulsa el siguiente enlace. ¡Muchas gracias por tu apoyo!</p>
					<p>
						<a href="{{ url('/') }}" class="btn btn-light" target="_blank">Ingresar al sistema</a>
					</p>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		</script>
	</body>
</html>