<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Gestión de Competencias de Helisur</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/getorgchart.css') }}">
	    <style type="text/css">
	    	#dv-organigrama{height:540px;width:100%;}
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
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/getorgchart.js') }}"></script>
		<script type="text/javascript">
			var lastId = -1;
			var abierto = false;
			var peopleElement = document.getElementById("dv-organigrama");
	        var orgChart = new getOrgChart(peopleElement, {
	            primaryFields: ["cargo", "nombre"],
	            photoFields: ["image"],
	            dataSource: {!! json_encode($oficinas) !!},
	            scale: 0.5,
	            enableZoom: true,
	            enableEdit: false,
	            enableDetailsView: false,
	            enableZoomOnNodeDoubleClick: false,
	            clickNodeEvent: function(sender, args) {
	            	if(args.node.id != lastId) {
	            		lastId = args.node.id;
	            		var p = {
	            			_token: "{{ csrf_token() }}",
	            			oid: args.node.id
	            		};
	            		var container = $("#dv-navmenu");
	            		$.post("{{ url('usuarios/ajax/dt-oficina') }}", p, function(response) {
	            			if(response.success) {
	            				var dependencias = response.data.dependencias;
	            				var dv_dependencias = $("<div/>").addClass("list-group");
	            				for(var i in dependencias) {
	            					var dependencia = dependencias[i];
	            					dv_dependencias.append(
	            						$("<div/>").addClass("list-group-item list-group-item-action flex-column align-items-start").append(
	            							$("<div/>").addClass("d-flex w-100 justify-content-between").append(
	            								$("<h5/>").addClass("mb-1").html("Gerente General")
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
	            					$("<a/>").attr("href","#").addClass("list-group-item").append(
	            						$("<p/>").addClass("mb-1").html("Añadir un nuevo puesto")
            						)
            					);
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
        								$("<a/>").addClass("btn btn-success btn-xs").attr("href","#").html("Cambiar")
        							)
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
					console.log(args);
	            },
	            renderNodeEvent: function (sender, args) {
					if(args.node.data.pid == null) args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#1565c0;stroke:#0d47a1;\"");
	            	else args.content[1] = args.content[1].replace("rect", "rect style=\"fill:#00897b;stroke:#004d40;\"");
	            }
	        });
		</script>
	</body>
</html>