<h1>Bienvenido, {{ $usuario->nombre }}</h1>
<hr>
<p>Has sido seleccionado para formar parte de la evaluación de competencias de Helisur.</p>
<p>Antes de comenzar, necesitamos que verifiques tu cuenta de correo. Para ello, solo deberás hacer clic en el siguiente enlace:</p>
<p style="text-align:right;"><a href="{{ url('verificar', [$hash1, $usuario->id . '_' . $hash2]) }}">Activar mi cuenta</a></p>