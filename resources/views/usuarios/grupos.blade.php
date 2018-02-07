<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
	</head>
	<body>
		@include("common.navbar")
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="card text-white bg-success" style="width:18rem;">
						<div class="card-body">
							<h5 class="card-title">Grupos de afinidad</h5>
							<hr>
							<p class="card-text">Utilice los grupos de afinidad para establecer una relación entre diferentes áreas antes de aplicar una encuesta.</p>
							<a href="#" class="card-link">Nuevo grupo</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
	</body>
</html>