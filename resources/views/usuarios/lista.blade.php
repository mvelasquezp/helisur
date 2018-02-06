<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
	    	.list-group-item h5{font-size:0.9rem}
	    	.list-group-item p{font-size:0.8rem;margin-bottom:0.15rem !important}
	    	.list-group-item small{font-size:0.7rem}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container-fluid">
			<div class="row">
				<div class="col">
					<table class="table table-striped">
						<thead class="thead-dark">
							<tr>
								<th>Doc.Identidad</th>
								<th>Ape.Paterno</th>
								<th>Ape.Materno</th>
								<th>Nombres</th>
								<th>Fch.Ingreso</th>
								<th>Área</th>
								<th>Cargo</th>
								<th>Correo</th>
								<th>Teléfono</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($usuarios as $idx => $empleado)
							<tr>
								<td>{{ $empleado->codigo }}</td>
								<td>{{ $empleado->apepat }}</td>
								<td>{{ $empleado->apemat }}</td>
								<td>{{ $empleado->nombres }}</td>
								<td class="text-center">{{ $empleado->ingreso }}</td>
								<td>
									@if($empleado->ofid != 0)
									<a style="cursor:pointer" class="btn btn-primary btn-xs text-light">{{ $empleado->area }}</a>
									@else
									{{ $empleado->area }}
									@endif
								</td>
								<td>
									@if($empleado->ptid != 0)
									<a style="cursor:pointer" class="btn btn-secondary btn-xs text-light">{{ $empleado->cargo }}</a>
									@else
									{{ $empleado->cargo }}
									@endif
								</td>
								<td>
									@if(strcmp($empleado->vmail,'S') == 0)
									<a href="#" class="btn btn-success btn-xs">{{ $empleado->email }}</a>
									@else
									<a href="#" class="btn btn-danger btn-xs" title="Enviar recordatorio">{{ $empleado->email }}</a>
									@endif
								</td>
								<td>{{ $empleado->telefono }}</td>
								<td><a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-registro" data-uid="{{ $empleado->id }}"><i class="fa fa-pencil"></i> Editar</a></td>
							</tr>
							@endforeach
							<tr class="table-info">
								<td><input type="text" class="form-control form-control-sm" id="nu-dni" placeholder="RUC / DNI / CE" style="width:7rem;"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-apepat" placeholder="Apellido paterno"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-apemat" placeholder="Apellido materno"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-nombres" placeholder="Nombres"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-fingreso" placeholder="dd/mm/yyyy" style="width:5.5rem;"></td>
								<td>
									<a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal-oficina" id="nu-btn-oficina">Seleccionar</a>
									<input type="hidden" id="nu-oficina">
								</td>
								<td>
									<a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal-puesto" id="nu-btn-puesto">Seleccionar</a>
									<input type="hidden" id="nu-puesto">
								</td>
								<td><input type="text" class="form-control form-control-sm" id="nu-email" placeholder="Dirección email"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-telefono" placeholder="Nro. Teléfono" style="width:5.5rem;"></td>
								<td>
									<a href="#" class="btn btn-primary btn-xs" id="sv-usuario"><i class="fa fa-plus"></i> Guardar</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal oficinas -->
		<div class="modal fade" id="modal-oficina" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<input type="text" class="form-control form-control-sm" id="of-filtro" placeholder="Seleccione oficina">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="list-group" id="of-lista"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- modal puestos -->
		<div class="modal fade" id="modal-puesto" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<input type="text" class="form-control form-control-sm" id="ps-filtro" placeholder="Seleccione puesto">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="list-group" id="ps-lista"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- modal nuevo puesto -->
		<div class="modal fade" id="modal-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 id="modal-registro-header">Editando usuario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<input type="hidden" id="mu-uid">
							<input type="hidden" id="mu-dni">
							<div class="form-row">
								<div class="col">
									<label for="mu-apepat">Apellido Paterno</label>
									<input type="text" class="form-control form-control-sm" id="mu-apepat" placeholder="Ingrese apellido paterno">
								</div>
								<div class="col">
									<label for="mu-apemat">Apellido Materno</label>
									<input type="text" class="form-control form-control-sm" id="mu-apemat" placeholder="Ingrese apellido materno">
								</div>
							</div>
							<div class="form-group">
								<label for="mu-nombres">Nombres</label>
								<input type="text" class="form-control form-control-sm" id="mu-nombres" placeholder="Ingrese nombres">
							</div>
							<div class="form-row">
								<div class="col">
									<label for="mu-fingreso">Fecha de ingreso</label>
									<input type="text" class="form-control form-control-sm" id="mu-fingreso" placeholder="dd/mm/yyyy">
								</div>
								<div class="col">
									<label for="mu-email">Correo</label>
									<input type="text" class="form-control form-control-sm" id="mu-email" placeholder="Ingrese email">
								</div>
							</div>
							<div class="form-row">
								<div class="col-6">
									<label for="mu-telefono">Teléfono</label>
									<input type="text" class="form-control form-control-sm" id="mu-telefono" placeholder="Ingrese teléfono">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label for="mu-oficina">Área</label>
									<select id="mu-oficina" class="form-control form-control-sm">
										<option value="0" selected>(sin asignar)</option>
										@foreach($oficinas as $oficina)
										<option value="{{ $oficina->value }}">{{ $oficina->text }}</option>
										@endforeach
									</select>
								</div>
								<div class="col">
									<label for="mu-puesto">Cargo</label>
									<select id="mu-puesto" class="form-control form-control-sm"></select>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="mu-guardar">Actualizar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			document.getElementById("nu-oficina").value = 0;
			document.getElementById("nu-puesto").value = 0;
			var oficinas = {!! json_encode($puestos) !!};
			function MuestraOficinas(texto) {
				var of_lista = $("#of-lista");
				texto = texto.toLowerCase();
				of_lista.empty();
				for(var i in oficinas) {
					var oficina = oficinas[i];
					if(oficina.nombre.toLowerCase().indexOf(texto) > -1) {
						of_lista.append(
							$("<a/>").attr("href","#").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
								$("<div/>").addClass("d-flex w-100 justify-content-between").append(
									$("<h5/>").addClass("mb-1").html(oficina.nombre)
								)
							).append(
								$("<p/>").addClass("mb-1").append(
									$("<b/>").html("Jefatura: ")
								).append(oficina.ancestro)
							).append(
								$("<small/>").addClass("mb-1").append(
									$("<b/>").html("Encargado: ")
								).append(oficina.encargado)
							).data("oid",oficina.id).data("onom",oficina.nombre).on("click", function(evt) {
								evt.preventDefault();
								document.getElementById("nu-oficina").value = $(this).data("oid");
								document.getElementById("nu-btn-oficina").innerHTML = $(this).data("onom");
								$("#modal-oficina").modal("hide");
							})
						);
					}
				}
			}
			//eventos
			$("#modal-oficina").on("show.bs.modal", function() {
				document.getElementById("of-filtro").value = "";
				MuestraOficinas('');
			});
			$("#modal-puesto").on("show.bs.modal", function() {
				var p = {
					_token: "{{ csrf_token() }}",
					ofc: document.getElementById("nu-oficina").value
				};
				$.post("{{ url('usuarios/ajax/ls-puestos') }}", p, function(response) {
					if(response.success) {
						var puestos = response.puestos;
						var ps_lista = $("#ps-lista");
						ps_lista.empty();
						for(var i in puestos) {
							var puesto = puestos[i];
							ps_lista.append(
								$("<a/>").attr("href","#").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
									$("<div/>").addClass("d-flex w-100 justify-content-between").append(
										$("<h5/>").addClass("mb-1").html(puesto.text)
									)
								).data("pid",puesto.value).data("pnom",puesto.text).on("click", function(evt) {
									evt.preventDefault();
									document.getElementById("nu-puesto").value = $(this).data("pid");
									document.getElementById("nu-btn-puesto").innerHTML = $(this).data("pnom");
									$("#modal-puesto").modal("hide");
								})
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
			$("#modal-registro").on("show.bs.modal", function(args) {
				var uid = args.relatedTarget.dataset.uid;
				document.getElementById("mu-uid").value = uid;
				var p = {
					_token: "{{ csrf_token() }}",
					uid: uid
				};
				$.post("{{ url('usuarios/ajax/dt-usuario') }}", p, function(response) {
					if(response.success) {
						var usuario = response.data.usuario;
						document.getElementById("modal-registro-header").innerHTML = ("Editando [" + usuario.cod + "]");
						document.getElementById("mu-dni").value = usuario.cod;
						document.getElementById("mu-apepat").value = usuario.app;
						document.getElementById("mu-apemat").value = usuario.apm;
						document.getElementById("mu-nombres").value = usuario.nom;
						document.getElementById("mu-fingreso").value = usuario.fng;
						document.getElementById("mu-email").value = usuario.eml;
						document.getElementById("mu-telefono").value = usuario.tlf;
						$("#mu-oficina option[value=" + usuario.oid + "]").prop("selected", true);
						var puestos = response.data.puestos;
						$("#mu-puesto").empty().append(
							$("<option/>").val(0).html("(sin asignar)")
						);
						for(var i in puestos) {
							var puesto = puestos[i];
							$("#mu-puesto").append(
								$("<option/>").val(puesto.value).html(puesto.text)
							);
						}
						$("#mu-puesto option[value=" + usuario.pid + "]").prop("selected", true);
					}
					else {
						alert(response.msg);
						$("#modal-registro").modal("hide");
					}
				}, "json");
			});
			$("modal-registro").on("hidden.bs.modal", function() {
				document.getElementById("modal-registro-header").innerHTML = "Editando usuario";
				document.getElementById("mu-apepat").value = "";
				document.getElementById("mu-apemat").value = "";
				document.getElementById("mu-nombres").value = "";
				document.getElementById("mu-fingreso").value = "";
				document.getElementById("mu-email").value = "";
				document.getElementById("mu-telefono").value = "";
				$("#mu-oficina option[value=0]").prop("selected", true);
				$("#mu-puesto").empty().append(
					$("<option/>").val(0).html("(sin asignar)")
				);
			});
			$("#sv-usuario").on("click", function(e) {
				e.preventDefault();
				var p = {
					_token: "{{ csrf_token() }}",
					cod: document.getElementById("nu-dni").value,
					app: document.getElementById("nu-apepat").value,
					apm: document.getElementById("nu-apemat").value,
					nom: document.getElementById("nu-nombres").value,
					fng: document.getElementById("nu-fingreso").value,
					eml: document.getElementById("nu-email").value,
					tlf: document.getElementById("nu-telefono").value,
					ofc: document.getElementById("nu-oficina").value,
					pst: document.getElementById("nu-puesto").value
				};
				$.post("{{ url('usuarios/ajax/sv-usuario') }}", p, function(response) {
					if(response.success) {
						document.getElementById("nu-dni").value = "";
						document.getElementById("nu-apepat").value = "";
						document.getElementById("nu-apemat").value = "";
						document.getElementById("nu-nombres").value = "";
						document.getElementById("nu-fingreso").value = "";
						document.getElementById("nu-email").value = "";
						document.getElementById("nu-telefono").value = "";
						document.getElementById("nu-oficina").value = 0;
						document.getElementById("nu-btn-oficina").innerHTML = "Seleccionar";
						document.getElementById("nu-puesto").value = 0;
						document.getElementById("nu-btn-puesto").innerHTML = "Seleccionar";
						location.reload();
					}
					else alert(response.msg);
				}, "json");
			});
			$("#mu-oficina").on("change", function(e) {
				var p = {
					_token: "{{ csrf_token() }}",
					ofc: document.getElementById("mu-oficina").value
				};
				$.post("{{ url('usuarios/ajax/ls-puestos') }}", p, function(response) {
					if(response.success) {
						var puestos = response.puestos;
						var ps_lista = $("#mu-puesto");
						ps_lista.empty().append(
							$("<option/>").val(0).html("(sin asignar)")
						);
						for(var i in puestos) {
							var puesto = puestos[i];
							ps_lista.append(
								$("<option/>").val(puesto.value).html(puesto.text)
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
			$("#mu-guardar").on("click", function(e) {
				e.preventDefault();
				$("#mu-guardar").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					uid: document.getElementById("mu-uid").value,
					cod: document.getElementById("mu-dni").value,
					app: document.getElementById("mu-apepat").value,
					apm: document.getElementById("mu-apemat").value,
					nom: document.getElementById("mu-nombres").value,
					fng: document.getElementById("mu-fingreso").value,
					eml: document.getElementById("mu-email").value,
					tlf: document.getElementById("mu-telefono").value,
					ofc: document.getElementById("mu-oficina").value,
					pst: document.getElementById("mu-puesto").value
				};
				$.post("{{ url('usuarios/ajax/ed-usuario') }}", p, function(response) {
					if(response.success) {
						location.reload();
					}
					else {
						alert(response.msg);
						$("#mu-guardar").show();
					}
				}, "json").fail(function(error) {
					$("#mu-guardar").show();
				});
			});
			$("#of-filtro").on("keyup", function() {
				var input = $(this);
				MuestraOficinas(input.val());
			});
		</script>
	</body>
</html>