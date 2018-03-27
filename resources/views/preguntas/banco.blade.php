<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<div class="col">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th width="5%">#</th>
								<th width="10%">Grupo</th>
								<th width="10%">Concepto</th>
								<th width="10%">Categoría</th>
								<th width="10%">Subcategoría</th>
								<th>Pregunta</th>
								<th width="5%"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($preguntas as $idx => $pregunta)
							<tr>
								<td>
									<span class="btn btn-primary btn-xs">{{ $idx + 1 }}</span>
								</td>
								<td>{{ $pregunta->grupo }}</td>
								<td>{{ $pregunta->concepto }}</td>
								<td>{{ $pregunta->categoria }}</td>
								<td>{{ $pregunta->subcategoria }}</td>
								<td>{{ $pregunta->texto }}</td>
								<td><a href="#" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Retirar</a></td>
							</tr>
							@endforeach
							<tr>
								<td></td>
								<td>
									<select id="ins-grupo" class="form-control form-control-sm">
										<option value="0" selected disabled>Seleccione</option>
										@foreach($grupos as $grupo)
										<option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<select id="ins-concepto" class="form-control form-control-sm">
										<option value="0" selected disabled>Seleccione</option>
										@foreach($conceptos as $concepto)
										<option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<select id="ins-categoria" class="form-control form-control-sm">
										<option value="0" selected disabled>Seleccione</option>
										@foreach($categorias as $categoria)
										<option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<select id="ins-subcategoria" class="form-control form-control-sm">
										<option value="0" selected disabled>Seleccione</option>
										@foreach($subcategorias as $subcategoria)
										<option value="{{ $subcategoria->id }}">{{ $subcategoria->nombre }}</option>
										@endforeach
									</select>
								</td>
								<td><input type="text" class="form-control form-control-sm" id="ins-pregunta" placeholder="Ingrese texto de la nueva pregunta a crear"></td>
								<td><a href="#" class="btn btn-success btn-xs" id="btn-ins-pregunta"><i class="fa fa-plus"></i> Crear</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			function GuardarPregunta() {
				$("#btn-ins-pregunta").hide();
				var grupo = document.getElementById("ins-grupo").value;
				var concepto = document.getElementById("ins-concepto").value;
				var categoria = document.getElementById("ins-categoria").value;
				var subcategoria = document.getElementById("ins-subcategoria").value;
				var pregunta = document.getElementById("ins-pregunta").value;
				var p = { _token: "{{ csrf_token() }}", grp: grupo, cnc: concepto, cat: categoria,sct: subcategoria,prg: pregunta };
				$.post("{{ url('preguntas/ajax/ins-pregunta') }}", p, function(response) {
					if(response.success) {
						document.getElementById("ins-pregunta").value = "";
						location.reload();
					}
					else {
						alert(response.msg);
						$("#btn-ins-pregunta").hide();
					}
				}, "json");
			}
			$("#btn-ins-pregunta").on("click", GuardarPregunta);
			$("#ins-categoria").on("change", function() {
				var cat = $(this).val();
				var p = { _token:"{{ csrf_token() }}",cat:cat };
				$.post("{{ url('preguntas/ajax/ls-subcategorias') }}", p, function(response) {
					if(response.success) {
						$("#ins-subcategoria").empty();
						var scats = response.data;
						for(var i in scats) {
							var scat = scats[i];
							$("#ins-subcategoria").append(
								$("<option/>").val(scat.value).html(scat.text)
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
		</script>
	</body>
</html>