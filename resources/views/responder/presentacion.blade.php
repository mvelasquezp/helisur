<!DOCTYPE html>
<html>
    <head>
        <title>Sistema de Gestión de Competencias de Helisur</title>
        @include("common.styles")
    </head>
    <body>
        @include("common.navbar")
        <!-- pagina -->
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="text-primary">{{ $encuesta->nombre }}</h1>
                    <p class="text-dark">Gracias por participar en el proceso de evaluación de competencias.</p>
                    <p class="text-dark">En esta encuesta evaluarás a las siguientes personas:</p>
                    <ul class="list-group">
                        @foreach($evaluados as $evaluado)
                        <li class="list-group-item list-group-item-action flex-column align-items-start list-group-item">
                            <div class="row">
                                <div class="col-4 col-md-2 col-lg-1">
                                    <img src="{{ url('imagen', [$evaluado->uid]) }}" class="img-fluid">
                                </div>
                                <div class="col-8 col-md-10 col-lg-11">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-success">{{ $evaluado->evaluado }}</h5>
                                    </div>
                                    <p class="mb-1">{{ $evaluado->puesto }}</p>
                                    <small class="text-muted">{{ $evaluado->oficina }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <br>
                    <p class="text-dark">Recuerda que no es necesario responder todas las preguntas de una sola vez. Si lo deseas, puedes cerrar esta ventana en cualquier momento y, la próxima vez que entres, podrás continuar con la encuesta en donde te quedaste.</p>
                    <p class="text-dark">Para comenzar a evaluar, utiliza el botón "Comenzar con la evaluación" que se muestra a continuación.</p>
                    <p class="text-right">
                        <a href="{{ url('responder/comenzar', [$eid]) }}" class="btn btn-lg btn-success"><i class="fa fa-play"></i> Comenzar con la evaluación</a>
                    </p>
                    <p class="text-secondary text-right">Ten presente que el plazo para responder esta encuesta vence el <b>{{ $encuesta->plazo }}</b></p>
                </div>
            </div>
        </div>
        <!-- JS -->
        @include("common.scripts")
    </body>
</html>