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
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="grupos-tab" data-toggle="tab" href="#grupos" role="tab" aria-controls="grupos" aria-selected="true">Grupos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="conceptos-tab" data-toggle="tab" href="#conceptos" role="tab" aria-controls="conceptos" aria-selected="false">Conceptos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="categorias-tab" data-toggle="tab" href="#categorias" role="tab" aria-controls="categorias" aria-selected="false">Categorías</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="subcategorias-tab" data-toggle="tab" href="#subcategorias" role="tab" aria-controls="subcategorias" aria-selected="false">Subcategorías</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
							<table class="table table-hover">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th>Grupo</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($grupos as $idx => $grupo)
									<tr>
										<td>
											<span class="btn btn-primary btn-xs">{{ $idx + 1 }}</span>
										</td>
										<td>{{ $grupo->nombre }}</td>
										<td><a href="#" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Retirar</a></td>
									</tr>
									@endforeach
									<tr>
										<td></td>
										<td><input type="text" class="form-control form-control-sm" id="ins-grupo" placeholder="Ingrese nombre para crear un nuevo grupo"></td>
										<td><a href="#" class="btn btn-success btn-xs" id="btn-ins-grupo"><i class="fa fa-plus"></i> Crear</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="conceptos" role="tabpanel" aria-labelledby="conceptos-tab">
							<table class="table table-hover">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th>Concepto</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($conceptos as $idx => $concepto)
									<tr>
										<td>
											<span class="btn btn-primary btn-xs">{{ $idx + 1 }}</span>
										</td>
										<td>{{ $concepto->nombre }}</td>
										<td><a href="#" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Retirar</a></td>
									</tr>
									@endforeach
									<tr>
										<td></td>
										<td><input type="text" class="form-control form-control-sm" id="ins-concepto" placeholder="Ingrese nombre para crear un nuevo concepto"></td>
										<td><a href="#" class="btn btn-success btn-xs" id="btn-ins-concepto"><i class="fa fa-plus"></i> Crear</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="categorias" role="tabpanel" aria-labelledby="categorias-tab">
							<table class="table table-hover">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th>Categoría</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($categorias as $idx => $categoria)
									<tr>
										<td>
											<span class="btn btn-primary btn-xs">{{ $idx + 1 }}</span>
										</td>
										<td>{{ $categoria->nombre }}</td>
										<td><a href="#" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Retirar</a></td>
									</tr>
									@endforeach
									<tr>
										<td></td>
										<td><input type="text" class="form-control form-control-sm" id="ins-categoria" placeholder="Ingrese nombre para crear una nueva categoría"></td>
										<td><a href="#" class="btn btn-success btn-xs" id="btn-ins-categoria"><i class="fa fa-plus"></i> Crear</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="subcategorias" role="tabpanel" aria-labelledby="subcategorias-tab">
							<table class="table table-hover">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th width="15%">Categoría</th>
										<th>Subcategoría</th>
										<th width="15%"></th>
									</tr>
								</thead>
								<tbody>
									@foreach($subcategorias as $idx => $subcategoria)
									<tr>
										<td>
											<span class="btn btn-primary btn-xs">{{ $idx + 1 }}</span>
										</td>
										<td>{{ $subcategoria->categoria }}</td>
										<td>{{ $subcategoria->nombre }}</td>
										<td><a href="#" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Retirar</a></td>
									</tr>
									@endforeach
									<tr>
										<td></td>
										<td>
											<select class="form-control form-control-sm" id="ins-subcategoria-cat">
												<option value="0" selected disabled>Seleccione</option>
												@foreach($categorias as $idx => $categoria)
												<option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
												@endforeach
											</select>
										</td>
										<td><input type="text" class="form-control form-control-sm" id="ins-subcategoria" placeholder="Ingrese nombre para crear una nueva subcategoría"></td>
										<td><a href="#" class="btn btn-success btn-xs" id="btn-ins-subcategoria"><i class="fa fa-plus"></i> Crear</a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			function RegistraGrupo() {
				var nombre = document.getElementById("ins-grupo").value;
				var p = {_token:"{{ csrf_token() }}", nom: nombre};
				if(nombre != "") {
					$("#btn-ins-grupo").hide();
					$.post("{{ url('preguntas/ajax/ins-grupo') }}", p, function(response) {
						if(response.success) {
							document.getElementById("ins-grupo").value = "";
							location.reload();
						}
						else {
							$("#btn-ins-grupo").show();
							alert(response.msg);
						}
					}, "json").error(function(error) {
						$("#btn-ins-grupo").show();
					});
				}
				else alert("Ingrese correctamente el nombre");
			}
			function RegistraConcepto() {
				var nombre = document.getElementById("ins-concepto").value;
				var p = {_token:"{{ csrf_token() }}", nom: nombre};
				if(nombre != "") {
					$("#btn-ins-concepto").hide();
					$.post("{{ url('preguntas/ajax/ins-concepto') }}", p, function(response) {
						if(response.success) {
							document.getElementById("ins-concepto").value = "";
							location.reload();
						}
						else {
							$("#btn-ins-concepto").show();
							alert(response.msg);
						}
					}, "json").error(function(error) {
						$("#btn-ins-concepto").show();
					});
				}
				else alert("Ingrese correctamente el nombre");
			}
			function RegistraCategoria() {
				var nombre = document.getElementById("ins-categoria").value;
				var p = {_token:"{{ csrf_token() }}", nom: nombre};
				if(nombre != "") {
					$("#btn-ins-categoria").hide();
					$.post("{{ url('preguntas/ajax/ins-categoria') }}", p, function(response) {
						if(response.success) {
							document.getElementById("ins-categoria").value = "";
							location.reload();
						}
						else {
							$("#btn-ins-categoria").show();
							alert(response.msg);
						}
					}, "json").error(function(error) {
						$("#btn-ins-categoria").show();
					});
				}
				else alert("Ingrese correctamente el nombre");
			}
			function RegistraSubcategoria() {
				var nombre = document.getElementById("ins-subcategoria").value;
				var categoria = document.getElementById("ins-subcategoria-cat").value;
				var p = {_token:"{{ csrf_token() }}", nom: nombre, cat: categoria};
				if(nombre != "" && categoria != 0) {
					$("#btn-ins-subcategoria").hide();
					$.post("{{ url('preguntas/ajax/ins-subcategoria') }}", p, function(response) {
						if(response.success) {
							document.getElementById("ins-subcategoria").value = "";
							location.reload();
						}
						else {
							$("#btn-ins-subcategoria").show();
							alert(response.msg);
						}
					}, "json").error(function(error) {
						$("#btn-ins-subcategoria").show();
					});
				}
				else alert("Ingrese correctamente la categoría y el nombre");
			}
			$("#btn-ins-grupo").on("click", RegistraGrupo);
			$("#btn-ins-concepto").on("click", RegistraConcepto);
			$("#btn-ins-categoria").on("click", RegistraCategoria);
			$("#btn-ins-subcategoria").on("click", RegistraSubcategoria);
		</script>
	</body>
</html>