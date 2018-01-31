<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/getorgchart.css') }}">
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<div class="col">
					{!! json_encode($oficinas) !!}
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/getorgchart.js') }}"></script>
		<script type="text/javascript">
		</script>
	</body>
</html>