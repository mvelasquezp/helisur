<table style="border-collapse:collapse;border-width:0;">
	<tr>
		<td style="background-color:#e0e0e0;vertical-align:bottom;text-align:left;padding:10px 40px 10px 10px;">
			<img src="{{ asset('images/helisur-logo-big.png') }}" style="width:128px;">
		</td>
		<td style="padding:10px;">
			<h1 style="font-family:Verdana;color:#0d47a1;">Estimado, {{ $usuario->nombre }}</h1>
			<hr>
			<p style="font-family:Verdana;color:#808080;">Tu contraseña ha sido restablecida con éxito.</p>
			<p style="font-family:Verdana;color:#808080;">Podrás volver a ingresar al sistema usando las siguientes credenciales</p>
			<p style="font-family:Verdana;color:#808080;"><b>Usuario: </b>{{ $usuario->alias }}</p>
			<p style="font-family:Verdana;color:#808080;"><b>Clave: </b>{{ $password }}</p>
			<p style="font-family:Verdana;color:#3f51b5;text-align:right;"><a href="{{ url('login') }}" target="_blank">Ingresar al sistema</a></p>
		</td>
	</tr>
</table>
