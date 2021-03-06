<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<style type="text/css">
	    	.list-group-item h5{font-size:0.9rem}
	    	.list-group-item p{font-size:0.8rem;margin-bottom:0.15rem !important}
	    	.list-group-item small{font-size:0.7rem}
	    	.no-wrap{white-space:nowrap !important}
	    	.img-responsive{margin:10px;width:320px}
	    	.dv-helper{display:none;position:absolute;box-shadow:2px 2px 10px #000000}
	    	.th-helper{cursor:pointer}
	    	.th-helper:hover>.dv-helper{display:block}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container-fluid">
			<div class="row">
				<div class="col">
					<div class="alert alert-secondary" role="alert">
						<form id="form-busca" class="form-inline">
							<label for="tx-query">Buscar</label>&nbsp;
							<input type="text" class="form-control form-control-sm" id="tx-query" placeholder="¿Qué desea buscar?" style="width:50%">&nbsp;
							<button class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Buscar</button>
						</form>
					</div>
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
								<th>Grupo</th>
								<th class="th-helper">
									Correo <a href="#" class="text-light"><i class="fa fa-info-circle"></i></a>
									<div class="alert alert-light dv-helper">
										<p>Leyenda</p>
										<p><span class="btn btn-success btn-xs">e-mail</span> Correo activado</p>
										<p><span class="btn btn-warning btn-xs">e-mail</span> Correo notificado y no activo</p>
										<p><span class="btn btn-danger btn-xs">e-mail</span> Correo no activado</p>
									</div>
								</th>
								<th>Teléfono</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
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
								<td>
									<a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal-grupo" id="nu-btn-grupo">Seleccionar</a>
									<input type="hidden" id="nu-grupo">
								</td>
								<td><input type="text" class="form-control form-control-sm" id="nu-email" placeholder="Dirección email"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-telefono" placeholder="Nro. Teléfono" style="width:5.5rem;"></td>
								<td>
									<a href="#" class="btn btn-primary btn-sm" id="sv-usuario"><i class="fa fa-plus"></i> Guardar</a>
								</td>
							</tr>
							@foreach($usuarios as $idx => $empleado)
							<tr id="tr-{{ $empleado->id }}" class="tr-table">
								<td>{{ $empleado->codigo }}</td>
								<td>{{ $empleado->apepat }}</td>
								<td>{{ $empleado->apemat }}</td>
								<td>{{ $empleado->nombres }}</td>
								<td class="text-center">{{ $empleado->ingreso }}</td>
								<td>
									@if($empleado->ofid != 0)
									<a style="cursor:pointer" class="btn btn-primary btn-xs text-light" title="{{ $empleado->area }}">{{ substr($empleado->area,0,25) }}</a>
									@else
									{{ $empleado->area }}
									@endif
								</td>
								<td>
									@if($empleado->ptid != 0)
									<a style="cursor:pointer" class="btn btn-secondary btn-xs text-light" title="{{ $empleado->cargo }}">{{ substr($empleado->cargo,0,25) }}</a>
									@else
									{{ $empleado->cargo }}
									@endif
								</td>
								<td>
									@if($empleado->cgpo != 0)
									<a style="cursor:pointer" class="btn btn-warning btn-xs" title="{{ $empleado->ngpo }}">{{ substr($empleado->ngpo,0,25) }}</a>
									@else
									{{ $empleado->ngpo }}
									@endif
								</td>
								<td>
									@if(strcmp($empleado->vmail,'S') == 0)
									<a href="#" class="btn btn-success btn-xs" title="Reiniciar contraseña" data-toggle="modal" data-target="#modal-reset" data-uid="{{ $empleado->id }}" data-pid="{{ $empleado->ptid }}" data-mail="{{ $empleado->email }}">{{ $empleado->email }}</a>
									@elseif(strcmp($empleado->vmail,'P') == 0)
									<a href="#" class="btn btn-warning btn-xs" title="Enviar recordatorio" data-toggle="modal" data-target="#modal-activar" data-uid="{{ $empleado->id }}" data-pid="{{ $empleado->ptid }}" data-mail="{{ $empleado->email }}">{{ $empleado->email }}</a>
									@else
									<a href="#" class="btn btn-danger btn-xs" title="Enviar recordatorio" data-toggle="modal" data-target="#modal-activar" data-uid="{{ $empleado->id }}" data-pid="{{ $empleado->ptid }}" data-mail="{{ $empleado->email }}">{{ $empleado->email }}</a>
									@endif
								</td>
								<td>{{ $empleado->telefono }}</td>
								<td class="no-wrap">
									@if(strcmp($empleado->estado, "S") == 0)
										<a href="#" class="btn btn-danger btn-xs btn-retira" data-uid="{{ $empleado->id }}" data-pid="{{ $empleado->ptid }}" data-nombre="{{ $empleado->nombres }} {{ $empleado->apepat }} {{ $empleado->apemat }}"><i class="fa fa-remove"></i> Retirar</a>
									@else
										<a href="#" class="btn btn-primary btn-xs btn-activa" data-uid="{{ $empleado->id }}" data-nombre="{{ $empleado->nombres }} {{ $empleado->apepat }} {{ $empleado->apemat }}"><i class="fa fa-plus"></i> Activar</a>
									@endif
									<a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-registro" data-uid="{{ $empleado->id }}"><i class="fa fa-pencil"></i> Editar</a>
								</td>
							</tr>
							@endforeach
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
		<!-- modal grupos -->
		<div class="modal fade" id="modal-grupo" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<input type="text" class="form-control form-control-sm" id="go-filtro" placeholder="Seleccione grupo">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="list-group" id="go-lista"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- modal nuevo puesto -->
		<div class="modal fade bd-example-modal-lg" id="modal-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 id="modal-registro-header">Editando usuario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Perfil</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="picture" aria-selected="false">Imagen</a>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
										<div class="col-6">
											<label for="mu-gocupacional">Grupo Ocupacional</label>
											<select id="mu-gocupacional" class="form-control form-control-sm">
												<option value="0" selected>(sin asignar)</option>
												@foreach($grupos as $grupo)
												<option value="{{ $grupo->value }}">{{ $grupo->text }}</option>
												@endforeach
											</select>
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
							<div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="picture-tab">
								<form id="form-picture" class="form-inline" style="margin-top:5px;">
									<label for="picture-file">Seleccionar archivo</label>&nbsp;
    								<input type="file" class="form-control-file" id="picture-file">&nbsp;
									<button class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Subir foto</button>
								</form>
								<div class="row">
									<div class="col">
										<img id="picture-img" class="rounded mx-auto d-block img-responsive" src="">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary" id="mu-guardar">Actualizar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal correo activacion -->
		<div class="modal fade" id="modal-activar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Activación de correos</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-activar-uid">
						<input type="hidden" id="modal-activar-pid">
						<div class="row">
							<div class="col">
								<p class="text-dark">Se enviará un mensaje de activación a la cuenta de correo <b id="modal-activar-mail"></b>. Pulse el siguiente botón para hacerlo.</p>
								<p class="text-right"><a id="modal-activar-one" href="#" class="btn btn-success btn-sm"><i class="fa fa-envelope-o"></i> Enviar recordatorio</a></p>
								<hr>
								<p class="text-secondary">Si lo desea, utilice la siguiente opción para enviar notificaciones a todas las cuentas de correo sin verificar.</p>
								<p class="text-right"><a id="modal-activar-all" href="#" class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Enviar correo de activación a todas las cuentas</a></p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal reiniciar clave -->
		<div class="modal fade" id="modal-reset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Reinicio de contraseña</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-reset-uid">
						<input type="hidden" id="modal-reset-pid">
						<div class="row">
							<div class="col">
								<p class="text-dark">Se enviará un mensaje de recuperación de contraseña a la cuenta de correo <b id="modal-reset-mail"></b>. Pulse el siguiente botón para hacerlo.</p>
								<p class="text-right"><a id="modal-reset-password" href="#" class="btn btn-primary btn-sm"><i class="fa fa-envelope-o"></i> Enviar nueva clave</a></p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			document.getElementById("tx-query").value = "";
			document.getElementById("nu-oficina").value = 0;
			document.getElementById("nu-puesto").value = 0;
			var oficinas = {!! json_encode($puestos) !!};
			var usuarios = {!! json_encode($usuarios) !!};
			var grupos = {!! json_encode($grupos) !!};
			function BuscaUsuarios(evt) {
				evt.preventDefault();
				var query = document.getElementById("tx-query").value;
				if(query == "") $(".tr-table").show();
				else {
					query = query.toLowerCase();
					for(var i in usuarios) {
						var usuario = usuarios[i];
						if(usuario.codigo.toLowerCase().indexOf(query) > -1 || usuario.apepat.toLowerCase().indexOf(query) > -1 || usuario.apemat.toLowerCase().indexOf(query) > -1 || usuario.nombres.toLowerCase().indexOf(query) > -1 || usuario.area.toLowerCase().indexOf(query) > -1 || usuario.cargo.toLowerCase().indexOf(query) > -1 || usuario.email.toLowerCase().indexOf(query) > -1 || usuario.telefono.toLowerCase().indexOf(query) > -1) {
							$("#tr-" + usuario.id).show();
						}
						else $("#tr-" + usuario.id).hide();
					}
				}
			}
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
			function MuestraGrupos(texto) {
				var go_lista = $("#go-lista");
				texto = texto.toLowerCase();
				go_lista.empty();
				for(var i in grupos) {
					var grupo = grupos[i];
					if(grupo.text.toLowerCase().indexOf(texto) > -1) {
						go_lista.append(
							$("<a/>").attr("href","#").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
								$("<div/>").addClass("d-flex w-100 justify-content-between").append(
									$("<h5/>").addClass("mb-1").html(grupo.text)
								)
							).data("gid",grupo.value).data("gnom",grupo.text).on("click", function(evt) {
								evt.preventDefault();
								document.getElementById("nu-grupo").value = $(this).data("gid");
								document.getElementById("nu-btn-grupo").innerHTML = $(this).data("gnom");
								$("#modal-grupo").modal("hide");
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
			$("#modal-grupo").on("show.bs.modal", function() {
				document.getElementById("go-filtro").value = "";
				MuestraGrupos('');
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
						$("#mu-gocupacional option[value=" + usuario.gpo + "]").prop("selected", true);
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
						$("#picture-img").attr("src", "{{ url('imagen') }}/" + uid);
					}
					else {
						alert(response.msg);
						$("#modal-registro").modal("hide");
					}
				}, "json");
			});
			$("#modal-activar").on("show.bs.modal", function(args) {
				var data = args.relatedTarget.dataset;
				document.getElementById("modal-activar-mail").innerHTML = data.mail;
				document.getElementById("modal-activar-uid").value = data.uid;
				document.getElementById("modal-activar-pid").value = data.pid;
			});
			$("#modal-reset").on("show.bs.modal", function(args) {
				var data = args.relatedTarget.dataset;
				document.getElementById("modal-reset-mail").innerHTML = data.mail;
				document.getElementById("modal-reset-uid").value = data.uid;
				document.getElementById("modal-reset-pid").value = data.pid;
			});
			$("modal-registro").on("hidden.bs.modal", function() {
				document.getElementById("modal-registro-header").innerHTML = "Editando usuario";
				document.getElementById("mu-apepat").value = "";
				document.getElementById("mu-apemat").value = "";
				document.getElementById("mu-nombres").value = "";
				document.getElementById("mu-fingreso").value = "";
				document.getElementById("mu-email").value = "";
				document.getElementById("mu-telefono").value = "";
				$("#picture-img").removeAttr("src");
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
					pst: document.getElementById("nu-puesto").value,
					gpo: document.getElementById("nu-grupo").value
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
						document.getElementById("nu-grupo").value = 0;
						document.getElementById("nu-btn-grupo").innerHTML = "Seleccionar";
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
					pst: document.getElementById("mu-puesto").value,
					gpo: document.getElementById("mu-gocupacional").value
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
			$("#form-busca").on("submit", BuscaUsuarios);
			$(".btn-retira").on("click", function(evt) {
				evt.preventDefault();
				var a = $(this);
				if(window.confirm("¿Desea retirar al usuario " + a.data("nombre") + "?")) {
					a.hide();
					var p = { _token:"{{ csrf_token() }}", uid:a.data("uid"), pid:a.data("pid") };
					$.post("{{ url('usuarios/ajax/retira-usuario') }}", p, function(response) {
						a.show();
						if(response.success) location.reload();
						else alert(response.msg);
					}, "json").fail(function(err) {
						a.show();
					});
				}
			});
			$(".btn-activa").on("click", function(evt) {
				evt.preventDefault();
				var a = $(this);
				if(window.confirm("¿Desea activar al usuario " + a.data("nombre") + "?")) {
					a.hide();
					var p = { _token:"{{ csrf_token() }}", uid:a.data("uid") };
					$.post("{{ url('usuarios/ajax/activa-usuario') }}", p, function(response) {
						a.show();
						if(response.success) location.reload();
						else alert(response.msg);
					}, "json").fail(function(err) {
						a.show();
					});
				}
			});
			$("#form-picture").on("submit", function(evt) {
				evt.preventDefault();
				var formData = new FormData();
					formData.append("file", $("#picture-file")[0].files[0]);
					formData.append("cod", document.getElementById("mu-dni").value);
					formData.append("_token", "{{ csrf_token() }}");
				$.ajax({
					url : "{{ url('usuarios/ajax/upload-picture') }}",
					type : "POST",
					data : formData,
					processData: false,
					contentType: false,
					success : function(data) {
						$("#picture-img").attr("src", "{{ url('imagen') }}/" + document.getElementById("mu-uid").value + "?t=" + (Math.random() * 1000) + 1);
						$("#form-picture")[0].reset();
						alert("Se actualizó la imagen del colaborador");
					}
				});
			});
			$(".form-control-sm").on("keyup", function() {
				var input = $(this);
				input.val(input.val().toUpperCase());
				return true;
			});
			$("#modal-activar-one").on("click", function(evt) {
				evt.preventDefault();
				$("#modal-activar-one").hide();
				$("#modal-activar-all").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					uid: document.getElementById("modal-activar-uid").value,
					pid: document.getElementById("modal-activar-pid").value
				};
				$.post("{{ url('mailer/activacion') }}", p, function(response) {
					$("#modal-activar-one").show();
					$("#modal-activar-all").show();
					if(response.success) {
						alert("Se envió el correo de activación.");
//						location.reload();
					}
				}, "json");
			});
			$("#modal-activar-all").on("click", function(evt) {
				evt.preventDefault();
				if(window.confirm("Se enviarán los correos de confirmación a los usuarios que no han activado su correo. Esto podría tardar varios minutos dependiendo del total de direcciones de correo que no han sido confirmadas. ¿Desea continuar?")) {
					$("#modal-activar-one").hide();
					$("#modal-activar-all").hide();
					var p = { _token: "{{ csrf_token() }}" };
					$("#modal-activar .modal-footer button").html("Enviando los mensajes. Por favor, espere...");
					$.post("{{ url('mailer/activacion-all') }}", p, function(response) {
						$("#modal-activar-one").show();
						$("#modal-activar-all").show();
						if(response.success) {
							alert("Se enviaron los correos de activación.");
							location.reload();
						}
					}, "json").fail(function(err) {
console.log(err);
						$("#modal-activar-one").show();
						$("#modal-activar-all").show();
						$("#modal-activar .modal-footer button").html("Ocurrió un error");
					});
				}
			});
			$("#modal-reset-password").on("click", function(evt) {
				evt.preventDefault();
				$("#modal-reset-password").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					uid: document.getElementById("modal-reset-uid").value,
					pid: document.getElementById("modal-reset-pid").value
				};
				$.post("{{ url('mailer/reset-password') }}", p, function(response) {
					if(response.success) {
						alert("Se reinició la clave del usuario seleccionado y se le ha enviado un mensaje a su dirección de correo electrónico.");
					}
					else alert(response.msg);
					$("#modal-reset-password").show();
					$("#modal-reset").modal("hide");
				}, "json").fail(function(err) {
console.log(err);
					alert(err.statusText);
					$("#modal-reset-password").show();
				});
			});
		</script>
	</body>
</html>