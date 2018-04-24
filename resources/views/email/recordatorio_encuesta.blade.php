<table style="border-collapse:collapse;border-width:0;">
	<tr>
		<td style="background-color:#e0e0e0;vertical-align:bottom;text-align:left;padding:10px 40px 10px 10px;">
			<img src="{{ asset('images/helisur-logo-big.png') }}" style="width:128px;">
		</td>
		<td style="padding:10px;">
			<h1 style="font-family:Verdana;color:#0d47a1;">{{ $mailbody->saludo }} {{ $usuario->nombre }}</h1>
			<hr>
			<p style="font-family:Verdana;color:#808080;">{!! str_replace("\n","<br>",$mailbody->mensaje) !!}</p>
			<p style="font-family:Verdana;text-align:right;"><a href="{{ url('/') }}">{{ $mailbody->enlace }}</a></p>
			<p><br></p>
			<p style="font-family:Verdana;color:#808080;">Que tengas buen día!</p>
		</td>
	</tr>
</table>