<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/getorgchart.css') }}">
	    <style type="text/css">
	    	#dv-organigrama{height:520px;width:100%;}
	    	#dv-navmenu{height:520px;overflow-y:auto;}
	    	.mb-1{font-size:0.9rem}
	    </style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container-fluid">
			<div class="row">
				<div id="dv-navmenu" class="col-3" style="display:none;">
					<img src="{{ asset('images/icons/loader.svg') }}">
				</div>
				<div class="col">
					<div id="dv-organigrama"></div>
				</div>
			</div>
		</div>
		<!-- modal nuevo puesto -->
		<div class="modal fade" id="modal-dependencia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Registrar nuevo puesto</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<input type="hidden" id="dp-oficina">
							<input type="hidden" id="dp-jerarquia">
							<div class="form-group">
								<label for="dp-cargo">Puesto</label>
								<input type="text" class="form-control" id="dp-cargo" placeholder="Ingrese nombre del nuevo puesto">
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="dp-guardar">Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal nuevo cargo -->
		<div class="modal fade" id="modal-cargo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Registrar nuevo cargo</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<input type="hidden" id="dc-oficina">
							<div class="form-group">
								<label for="dc-puesto">Puesto</label>
								<input type="text" class="form-control" id="dc-puesto" placeholder="Ingrese nombre del nuevo cargo">
							</div>
							<div class="form-group">
								<label for="dc-jerarquia">Tipo puesto:</label>
								<select id="dc-jerarquia" class="form-control form-control-sm">
									<option selected disabled>Seleccione</option>
									<option value=1>GERENTE GENERAL</option>
									<option value=2>GERENTE</option>
									<option value=3>SUB GERENTE / JEFE / CONTADOR GENERAL / COORDINADOR </option>
									<option value=4>ENCARGADO / PILOTO/ SUPERVISOR / SUPERVISOR BRIGADA / INSPECTOR / SUB-CONTADOR</option>
									<option value=5>ESPECIALISTA / ANALISTA / COPILOTO / SUPERVISOR TECNICO / INSTRUCTOR - COORDINADOR / ASISTENTE AUDITOR / COORDINADOR CCO</option>
									<option value=6>ASISTENTE / ASISTENTE GERENCIA / ASISTENTE INGENIERIA / MECANICO / AVIONICO / INSPECTOR PND / OPERCOM</option>
									<option value=7>ASISTENTE ADMINISTRATIVO / REPARADOR / ESTRUCTURISTA </option>
									<option value=8>PINTOR / AUXILIAR / AUXILIAR ADMINISTRATIVO</option>
									<option value=9>PRACTICANTES</option>
								</select>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="dc-guardar">Guardar</button>
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
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/getorgchart.js') }}"></script>
		<script type="text/javascript">
			var lastId = -1;
			var abierto = false;
			var peopleElement = document.getElementById("dv-organigrama");
	        var orgChart = new getOrgChart(peopleElement, {
	            primaryFields: ["cargo", "nombre", "puesto"],
	            photoFields: ["image"],
	            dataSource: {!! json_encode($oficinas) !!},
	            scale: 0.5,
	            enableZoom: true,
	            enableEdit: false,
	            enableDetailsView: false,
	            enableZoomOnNodeDoubleClick: false,
	            clickNodeEvent: function(sender, args) {
	            	var aoid = args.node.id;
	            	if(aoid != lastId) {
	            		lastId = aoid;
	            		var p = {
	            			_token: "{{ csrf_token() }}",
	            			oid: aoid
	            		};
	            		var container = $("#dv-navmenu");
	            		$.post("{{ url('usuarios/ajax/dt-oficina') }}", p, function(response) {
	            			if(response.success) {
	            				var cargos = response.data.cargos;
	            				var dv_cargos = $("<div/>").addClass("list-group");
	            				for(var k in cargos) {
	            					var cargo = cargos[k];
	            					dv_cargos.append(
	            						$("<div/>").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
	            							$("<div/>").addClass("d-flex w-100 justify-content-between").append(
	            								$("<h5/>").addClass("mb-1").html(cargo.puesto)
            								)
            							).append(
            								$("<small/>").addClass("text-muted").html(cargo.nombre)
            							).append(
            								$("<p/>").addClass("mb-1").append(
            									$("<a/>").attr("href","#").addClass("btn btn-info btn-xs text-light").html("Cambiar")
        									)
            							)
            						);
	            				}
	            				dv_cargos.append(
	            					$("<a/>").attr("href","#").attr({
	            						"data-oid": args.node.id,
	            						"data-toggle": "modal",
	            						"data-target": "#modal-cargo"
	            					}).addClass("list-group-item text-info").append(
	            						$("<p/>").addClass("mb-1").html("Añadir puesto")
            						)
            					);
	            				//
	            				var dependencias = response.data.dependencias;
	            				var dv_dependencias = $("<div/>").addClass("list-group");
	            				for(var i in dependencias) {
	            					var dependencia = dependencias[i];
	            					dv_dependencias.append(
	            						$("<div/>").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
	            							$("<div/>").addClass("d-flex w-100 justify-content-between").append(
	            								$("<h5/>").addClass("mb-1").html(dependencia.nombre)
            								)
            							).append(
            								$("<small/>").addClass("text-muted").html("Sin asignar.")
            							).append(
            								$("<p/>").addClass("mb-1").append(
            									$("<a/>").attr("href","#").addClass("btn btn-primary btn-xs text-light").html("Cambiar")
        									)
            							)
            						);
	            				}
	            				dv_dependencias.append(
	            					$("<a/>").attr("href","#").attr({
	            						"data-oid": args.node.id,
	            						"data-jer": args.node.data.jer,
	            						"data-toggle": "modal",
	            						"data-target": "#modal-dependencia"
	            					}).addClass("list-group-item").append(
	            						$("<p/>").addClass("mb-1").html("Añadir dependencia")
            						)
            					);
            					//
	            				var puesto = response.data.puesto;
	            				container.empty().append(
	            					$("<h3/>").addClass("text-primary").html(puesto.nombre)
            					).append(
            						$("<hr/>")
            					).append(
            						$("<p/>").append(
            							$("<b/>").html("Encargado: ")
        							).append(
        								$("<br/>")
        							).append(
        								$("<span/>").html(puesto.encargado + " ")
        							).append(
        								$("<a/>").addClass("btn btn-success btn-xs").attr({
        									"href": "#",
        									"data-toggle": "modal",
        									"data-target": "#modal-puesto",
        									"data-oid": aoid
        								}).html("Cambiar")
        							)
        						).append(
        							$("<p/>").append(
        								$("<b/>").html("Puestos del área:")
    								)
        						).append(dv_cargos).append(
        							$("<hr/>")
        						).append(
        							$("<p/>").append(
        								$("<b/>").html("Dependencias:")
    								)
        						).append(dv_dependencias);
	            			}
	            		}, "json");
	            		if(!abierto) {
	            			$("#dv-navmenu").show();
		            		$("#dv-organigrama").parent().removeClass("col").addClass("col-9");
		            		abierto = true;
	            		}
	            	}
	            	else {
	            		if(abierto) {
	            			$("#dv-navmenu").hide();
	            			$("#dv-organigrama").parent().addClass("col").removeClass("col-9");
	            			abierto = false;
	            		}
	            		else {
	            			$("#dv-navmenu").show();
		            		$("#dv-organigrama").parent().removeClass("col").addClass("col-9");
		            		abierto = true;
	            		}
	            	}
	            },
	            renderNodeEvent: function (sender, args) {
	            	switch(args.node.data.jer) {
	            		case 1:
	            			args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#00897b;stroke:#00796b;\"");
	            			break;
	            		case 2:
	            			args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#e53935;stroke:#c62828;\"");
	            			break;
	            		case 3:
	            			args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#f9a825;stroke:#f57f17;\"");
	            			break;
	            		case 4:
	            			args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#00897b;stroke:#00796b;\"");
	            			break;
            			default: break;
	            	}/*
					if(args.node.data.pid == null) args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#1565c0;stroke:#0d47a1;\"");
	            	else args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#00897b;stroke:#004d40;\"");*/
	            }
	        });
			//eventos modals
			$("#modal-dependencia").on("show.bs.modal", function(e) {
				document.getElementById("dp-cargo").value = "";
				document.getElementById("dp-jerarquia").value = e.relatedTarget.dataset.jer;
				document.getElementById("dp-oficina").value = e.relatedTarget.dataset.oid;
			});
			$("#modal-cargo").on("show.bs.modal", function(e) {
				document.getElementById("dc-puesto").value = "";
				$("#dc-jerarquia option[value=0]").prop("selected", true);
				document.getElementById("dc-oficina").value = e.relatedTarget.dataset.oid;
			});
			$("#modal-puesto").on("show.bs.modal", function(args) {
				var aoid = args.relatedTarget.dataset.oid;
				var p = {
					_token: "{{ csrf_token() }}",
					ofc: aoid
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
								).data("pid",puesto.value).data("pnom",puesto.text).data("oid",aoid).on("click", function(evt) {
									evt.preventDefault();
									var a = $(this);
									if(window.confirm("¿Desea asignar a " + a.data("pnom") + " como encargado del área?")) {
										var q = { _token:"{{ csrf_token() }}", pid:a.data("pid"), oid:a.data("oid") };
										$.post("{{ url('usuarios/ajax/sv-encargado') }}", q, function(rsp) {
											if(rsp.success) location.reload();
											else alert(rsp.msg);
										}, "json");
									}
								})
							);
						}
					}
					else alert(response.msg);
				}, "json");
			});
			$("#dp-guardar").on("click", function(e) {
				e.preventDefault();
				$("#dp-guardar").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					nom: document.getElementById("dp-cargo").value,
					oid: document.getElementById("dp-oficina").value,
					jer: document.getElementById("dp-jerarquia").value
				};
				$.post("{{ url('usuarios/ajax/sv-puesto') }}", p, function(response) {
					if(response.success) location.reload();
				}, "json");
			});
			$("#dc-guardar").on("click", function(e) {
				e.preventDefault();
				$("#dc-guardar").hide();
				var p = {
					_token: "{{ csrf_token() }}",
					nom: document.getElementById("dc-puesto").value,
					jer: document.getElementById("dc-jerarquia").value,
					ofc: document.getElementById("dc-oficina").value
				};
				$.post("{{ url('usuarios/ajax/sv-cargo') }}", p, function(response) {
					if(response.success) location.reload();
				}, "json").fail(function(err) {
					$("#dc-guardar").show();
				});
			});
		</script>
	</body>
</html>