
		<div class="navigation-bar fixed-top">
			<header class="navbar-light">
				<img src="{{ asset('images/helisur-logo-big.png') }}">
				<div class="nav-profile">
					<h1>Gestión de Competencias</h1>
					<img src="{{ asset('images/user-default.png') }}">
					<div class="nav-profile-options">
						<p>Bienvenid@</p>
						<h3>{{ $usuario->des_alias }} <a id="caret-menu" href="#"><i class="fa fa-caret-down"></i></a></h3>
						<div class="profile-menu">
							<div class="list-group">
								<a href="#" class="list-group-item list-group-item-secondary"><i class="fa fa-user"></i> Ver perfil</a>
								<a href="{{ url('login/logout') }}" class="list-group-item list-group-item-secondary"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
							</div>
						</div>
					</div>
				</div>
				<button class="navbar-toggler text-dark" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</header>
			<nav class="navbar navbar-expand-lg navbar-dark bg-ustar">
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item dropdown d-md-block d-lg-none bg-info">
							<a class="nav-link">Gestión de Competencias</a>
						</li>
						<li class="nav-item dropdown d-md-block d-lg-none">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> {{ $usuario->v_Nombres }} {{ $usuario->v_Apellidos }}</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-user-o"></i> Ver perfil</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
						<!-- -->
						<li class="nav-item {{ $menu == 0 ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('/') }}"><i class="fa fa-home"></i> Resumen</a>
						</li>
						<li class="nav-item dropdown {{ $menu == 1 ? 'active' : '' }}">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users"></i> Usuarios</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-sitemap"></i> Organigrama</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-user"></i> Registro de usuarios</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
						<li class="nav-item dropdown {{ $menu == 2 ? 'active' : '' }}">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-check-square-o"></i> Preguntas</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('preguntas/clasificacion') }}"><i class="fa fa-bars"></i> Clasificación</a>
								<a class="dropdown-item" href="{{ url('preguntas/banco') }}"><i class="fa fa-archive"></i> Banco de preguntas</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
						<li class="nav-item dropdown {{ $menu == 3 ? 'active' : '' }}">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list-ul"></i> Encuestas</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-clock-o"></i> Programación de encuestas</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-history"></i> Encuestas anteriores</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-tasks"></i> Informe de encuestas</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-reply-all"></i> Lanzamiento global</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
						<li class="nav-item dropdown {{ $menu == 4 ? 'active' : '' }}">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bar-chart"></i> Resultados</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-eye"></i> Seguimiento</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-line-chart"></i> Análisis</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
					</ul>
				</div>
			</nav>
		</div>