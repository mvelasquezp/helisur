<table style="border-collapse:collapse;border-width:0;">
	<tr>
		<td style="background-color:#e0e0e0;vertical-align:bottom;text-align:left;padding:10px 40px 10px 10px;">
			<img src="{{ asset('images/helisur-logo-big.png') }}" style="width:128px;">
		</td>
		<td style="padding:10px;">
			<h1 style="font-family:Verdana;color:#0d47a1;">Estimado(a) {{ $usuario->nombre }}</h1>
			<hr>
			<p style="font-family:Verdana;color:#808080;">Al parecer, tienes evaluaciones pendientes por responder. Recuerda que tu participación es de suma importancia para conocer más acerca del desempeño de nuestros colaboradores.</p>
			<p style="font-family:Verdana;color:#808080;">Para terminar de evaluar, ingresa al portal de gestión de competencias, o haz clic en el siguiente enlace:</p>
			<p style="font-family:Verdana;text-align:right;"><a href="{{ url('/') }}">Ingresar al sistema de evaluación de competencias</a></p>
			<p><br></p>
			<p style="font-family:Verdana;color:#808080;">Que tengas buen día!</p>
		</td>
	</tr>
</table>