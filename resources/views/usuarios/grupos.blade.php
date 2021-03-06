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
					<h3 class="text-primary">Grupos de afinidad</h3>
					<p class="text-secondary">Utilice los grupos de afinidad para definir qué áreas se relacionan entre si antes de realizar una encuesta. De este modo, podrá realizar una programación automática de encuestas de manera más sencilla.</p>
					@if(count($grupos) > 0)
					@foreach($grupos as $idx => $grupo)
					<div class="card bg-light" style="display:inline-block;margin:0 0.15rem;width:18rem">
						<div class="card-body">
							<h5 class="card-title">{{ $grupo->nombre }}</h5>
							@if($grupo->areas > 0)
							<p class="card-text">{{ $grupo->areas }} oficina(s) registrada(s)</p>
							@else
							<p class="card-text text-danger">No hay oficinas asignadas</p>
							@endif
							<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-asignar" data-gid="{{ $grupo->id }}" data-nom="{{ $grupo->nombre }}">Ver áreas</a>
							<a href="#" class="btn btn-danger btn-remove" data-gid="{{ $grupo->id }}"><i class="fa fa-trash"></i> Eliminar</a>
						</div>
					</div>
					@endforeach
					@else
					<div class="alert alert-danger" role="alert">
						No hay grupos registrados. Utilice la opción "Nuevo grupo" para añadir un grupo de afinidad.
					</div>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
					<form class="form-inline">
						<div class="form-check mb-2 mr-sm-2">
							<label class="form-check-label" for="gr-nombre">Nuevo grupo</label>
						</div>
						<input type="text" class="form-control mb-2 mr-sm-2" id="gr-nombre" placeholder="Ingrese nombre de nuevo grupo" style="width:16rem;">
						<a href="#" class="btn btn-success mb-2" id="btn-grp-guardar"><i class="fa fa-plus"></i> Nuevo grupo</a>
					</form>
				</div>
			</div>
		</div>
		<!-- modal nuevo grupo -->
		<div class="modal fade" id="modal-asignar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Nuevo grupo</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Oficina</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="modal-asignar-tbody"></tbody>
							<tfoot class="bg-dark">
								<tr>
									<td><select class="form-control form-control-sm" id="gr-oficina"></select></td>
									<td>
										<input type="hidden" id="modal-asignar-gid">
										<button id="modal-asignar-btn" class="btn btn-xs btn-success">Agregar</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="modal-footer">
						<button id="modal-asignar-sv" type="button" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var oficinas = {!! json_encode($oficinas) !!};
			document.getElementById("gr-nombre").value = "";
			function RetiraOficina(evt) {
				evt.preventDefault();
				var a = $(this);
				var grupo = $("#modal-asignar .modal-title").html();
				var oficina = a.parent().prev().html();
				if(window.confirm("¿Seguro que desea retirar la oficina " + oficina + " del grupo " + grupo + "?")) {
					var p = { _token:"{{ csrf_token() }}", oid:a.data("oid"), gid:document.getElementById("modal-asignar-gid").value };
					$.post("{{ url('usuarios/ajax/retirar-oficina') }}", p, function(response) {
						if(response.success) {
							a.parent().parent().remove();
							alert("Se retiró a la oficina " + oficina + " del grupo de afinidad " + grupo);
						}
					}, "json");
				}
			}
			$("#btn-grp-guardar").on("click", function(e) {
				e.preventDefault();
				$("#btn-grp-guardar").hide();
				var p = { _token:"{{ csrf_token() }}",nom:document.getElementById("gr-nombre").value };
				$.post("{{ url('usuarios/ajax/sv-grupo') }}", p, function(response) {
					if(response.success) location.reload();
					else alert(response.msg);
				}, "json").fail(function(err) {
					console.log(err);
					$("#btn-grp-guardar").show();
				});
			});
			$("#modal-asignar-btn").on("click", function(e) {
				var p = {
					_token: "{{ csrf_token() }}",
					gid: document.getElementById("modal-asignar-gid").value,
					oid: document.getElementById("gr-oficina").value
				};
				$.post("{{ url('usuarios/ajax/sv-oficina') }}", p, function(response) {
					if(response.success) {
						$("#modal-asignar-tbody").append(
							$("<tr/>").append(
								$("<td/>").html($("#gr-oficina option[value=" + p.oid + "]").html())
							).append(
								$("<td/>").append(
									$("<a/>").attr("href","#").data("oid",p.oid).addClass("btn btn-danger btn-xs").append(
										$("<i/>").addClass("fa fa-trash")
									).append(" Retirar").on("click", RetiraOficina)
								)
							)
						);
					}
				}, "json");
			});
			$("#modal-asignar").on("show.bs.modal", function(e) {
				var grupo = e.relatedTarget.dataset.gid;
				var combo = $("#gr-oficina");
				document.getElementById("modal-asignar-gid").value = grupo;
				$("#modal-asignar .modal-title").html(e.relatedTarget.dataset.nom);
				combo.empty().append(
					$("<option/>").val(0).html("Seleccione oficina")
				);
				for(var i in oficinas) {
					var oficina = oficinas[i];
					combo.append(
						$("<option/>").val(oficina.value).html(oficina.text)
					);
				}
				var p = { _token:"{{ csrf_token() }}", gid:grupo };
				$.post("{{ url('usuarios/ajax/ls-areas-afines') }}", p, function(response) {
					if(response.success) {
						var tbody = $("#modal-asignar-tbody");
						var data = response.oficinas;
						tbody.empty();
						for(var i in data) {
							var fila = data[i];
							tbody.append(
								$("<tr/>").append(
									$("<td/>").html(fila.text)
								).append(
									$("<td/>").append(
										$("<a/>").attr("href","#").data("oid",fila.value).addClass("btn btn-danger btn-xs").append(
											$("<i/>").addClass("fa fa-trash")
										).append(" Retirar").on("click", RetiraOficina)
									)
								)
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
			$("#modal-asignar-sv").on("click", function(e) {
				location.reload();
			});
			$(".btn-remove").on("click", function(evt) {
				evt.preventDefault();
				var a = $(this);
				var nombre = a.prev().prev().prev().html();
				if(window.confirm("¿Seguro que desea eliminar el grupo " + nombre + "?")) {
					var p = { _token:"{{ csrf_token() }}", gid:a.data("gid") };
					$.post("{{ url('usuarios/ajax/retirar-grupo') }}", p, function(response) {
						if(response.success) {
							a.parent().parent().remove();
							alert("se retiró el grupo " + nombre);
						}
						else alert(response.msg);
					}, "json");
				}
			});
		</script>
	</body>
</html>