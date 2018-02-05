<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
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
								<th>Fecha de Ingreso</th>
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
								<td>{{ $empleado->ingreso }}</td>
								<td>{{ $empleado->area }}</td>
								<td>{{ $empleado->cargo }}</td>
								<td>
									@if(strcmp($empleado->vmail,'S') == 0)
									<a href="#" class="btn btn-primary btn-xs">{{ $empleado->email }}</a>
									@else
									<a href="#" class="btn btn-danger btn-xs">{{ $empleado->email }}</a>
									@endif
								</td>
								<td>{{ $empleado->telefono }}</td>
								<td><a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-registro"><i class="fa fa-pencil"></i> Editar</a></td>
							</tr>
							@endforeach
							<tr class="table-info">
								<td><input type="text" class="form-control form-control-sm" id="nu-dni" placeholder="Ingrese documento de identidad"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-apepat" placeholder="Ingrese apellido paterno"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-apemat" placeholder="Ingrese apellido materno"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-nombres" placeholder="Ingrese nombres"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-fingreso" placeholder="Fecha de ingreso"></td>
								<td></td>
								<td></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-email" placeholder="Ingrese email"></td>
								<td><input type="text" class="form-control form-control-sm" id="nu-telefono" placeholder="Ingrese teléfono"></td>
								<td>
									<a href="#" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Guardar</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- modal nuevo puesto -->
		<div class="modal fade" id="modal-registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<form>
							<div class="form-group">
								<label for="mu-apepat">Apellido Paterno</label>
								<input type="text" class="form-control form-control-sm" id="mu-apepat" placeholder="Ingrese apellido paterno">
							</div>
							<div class="form-group">
								<label for="mu-apemat">Apellido Materno</label>
								<input type="text" class="form-control form-control-sm" id="mu-apemat" placeholder="Ingrese apellido materno">
							</div>
							<div class="form-group">
								<label for="mu-nombres">Nombres</label>
								<input type="text" class="form-control form-control-sm" id="mu-nombres" placeholder="Ingrese nombres">
							</div>
							<div class="form-group">
								<label for="mu-fingreso">Fecha de ingreso</label>
								<input type="text" class="form-control form-control-sm" id="mu-fingreso" placeholder="Ingrese nombres">
							</div>
							<div class="form-group">
								<label for="mu-email">Correo</label>
								<input type="text" class="form-control form-control-sm" id="mu-email" placeholder="Ingrese email">
							</div>
							<div class="form-group">
								<label for="mu-telefono">Teléfono</label>
								<input type="text" class="form-control form-control-sm" id="mu-telefono" placeholder="Ingrese teléfono">
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="mu-guardar">Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
	</body>
</html>