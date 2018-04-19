<table style="border-collapse:collapse;border-width:0;">
	<tr>
		<td style="background-color:#e0e0e0;vertical-align:bottom;text-align:left;padding:10px 40px 10px 10px;">
			<img src="{{ asset('images/helisur-logo-big.png') }}" style="width:128px;">
		</td>
		<td style="padding:10px;">
			<h1 style="font-family:Verdana;color:#0d47a1;">Bienvenido, {{ $usuario->nombre }}</h1>
			<hr>
			<p style="font-family:Verdana;color:#808080;">Has sido seleccionado para formar parte de la evaluación de competencias de Helisur.</p>
			<p style="font-family:Verdana;color:#808080;">Antes de comenzar, necesitamos que verifiques tu cuenta de correo. Para ello, solo deberás hacer clic en el siguiente enlace:</p>
			<p style="font-family:Verdana;text-align:right;"><a href="{{ url('verificar', [$hash1, $usuario->id . '_' . $hash2]) }}">Activar mi cuenta</a></p>
		</td>
	</tr>
</table>
