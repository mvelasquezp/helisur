<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<root>
	@foreach($usuarios as $idx => $usuario)
	<avance>
		<uid>{{ $usuario["uid"] }}</uid>
		<pid>{{ $usuario["pid"] }}</pid>
		<puntaje>{{ $usuario["pts"] }}</puntaje>
		<fortalezas>
			<fortaleza>{{ $usuario["f1"] }}</fortaleza>
			<fortaleza>{{ $usuario["f2"] }}</fortaleza>
			<fortaleza>{{ $usuario["f3"] }}</fortaleza>
		</fortalezas>
		<mejoras>
			<mejora>{{ $usuario["m1"] }}</mejora>
			<mejora>{{ $usuario["m2"] }}</mejora>
			<mejora>{{ $usuario["m3"] }}</mejora>
		</mejoras>
	</avance>
	@endforeach
</root>