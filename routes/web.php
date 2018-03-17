<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//autenticacion de usuarios
Route::group(["prefix" => "login"], function() {
	Route::get("/", ["as" => "login", "uses" => "Autenticacion@form_login"]);
	Route::post("verificar", "Autenticacion@post_login");
	Route::get("logout", "Autenticacion@logout");
});
//modulo publico
Route::middleware("auth")->namespace("Publico")->group(function() {
	Route::get("/", "Empleados@resumen");
	Route::get("responder/{eid}/{nup}", "Empleados@responder");
});
//modulo de resumen
Route::middleware(["superadmin", "auth"])->namespace("Superadmin")->group(function() {
	//modulo de usuarios
	Route::prefix("usuarios")->group(function() {
		//usuarios
		Route::get("organigrama", "Usuarios@organigrama");
		Route::get("grupos", "Usuarios@grupos");
		Route::get("registro", "Usuarios@registro");
		Route::prefix("ajax")->group(function() {
			//usuarios/ajax
			Route::post("dt-oficina", "Usuarios@dt_oficina");
			Route::post("sv-puesto", "Usuarios@sv_puesto");
			Route::post("sv-cargo", "Usuarios@sv_cargo");
			Route::post("sv-encargado", "Usuarios@sv_encargado");
			Route::post("ls-puestos", "Usuarios@ls_puestos");
			Route::post("sv-usuario", "Usuarios@sv_usuario");
			Route::post("dt-usuario", "Usuarios@dt_usuario");
			Route::post("ed-usuario", "Usuarios@ed_usuario");
			Route::post("sv-grupo", "Usuarios@sv_grupo");
			Route::post("ls-areas-afines", "Usuarios@ls_areas_afines");
			Route::post("sv-oficina", "Usuarios@sv_oficina");
		});
	});
	//modulo de preguntas
	Route::prefix("preguntas")->group(function() {
		//preguntas
		Route::get("clasificacion", "Preguntas@clasificacion");
		Route::get("banco", "Preguntas@banco");
		Route::prefix("ajax")->group(function() {
			//preguntas/ajax
			Route::post("ins-grupo", "Preguntas@ins_grupo");
			Route::post("ins-concepto", "Preguntas@ins_concepto");
			Route::post("ins-categoria", "Preguntas@ins_categoria");
			Route::post("ins-subcategoria", "Preguntas@ins_subcategoria");
			Route::post("ins-pregunta", "Preguntas@ins_pregunta");
		});
	});
	//modulo de encuestas
	Route::prefix("encuestas")->group(function() {
		//encuestas
		Route::prefix("programacion")->group(function() {
			Route::get("/", "Encuestas@programacion");
			Route::get("evaluadores/{eid}", "Encuestas@evaluadores");
		});
		Route::get("anteriores", "Encuestas@anteriores");
		Route::get("informe", "Encuestas@informe");
		Route::get("lanzamiento", "Encuestas@lanzamiento");
		Route::prefix("ajax")->group(function() {
			//encuestas/ajax
			Route::post("sv-encuesta", "Encuestas@sv_encuesta");
			Route::post("dt-encuesta", "Encuestas@dt_encuesta");
			Route::post("ls-cargos", "Encuestas@ls_cargos");
			Route::post("ls-programacion", "Encuestas@ls_programacion");
			Route::post("sv-programacion", "Encuestas@sv_programacion");
			Route::post("bsq-usuarios", "Encuestas@bsq_usuarios");
			Route::post("sv-programacion-individual", "Encuestas@sv_programacion_individual");
			Route::post("sv-programa-encuesta", "Encuestas@sv_programa_encuesta");
			Route::post("sv-atualiza-encuesta", "Encuestas@sv_atualiza_encuesta");
			Route::post("ls-destinatarios", "Encuestas@ls_destinatarios");
			Route::post("snd-encuesta", "Encuestas@snd_encuesta");
			Route::post("ls-encuestas-lanzar", "Encuestas@ls_encuestas_lanzar");
			Route::post("ls-encuestas-informe", "Encuestas@ls_encuestas_informe");
			Route::post("dt-progreso-encuesta", "Encuestas@dt_progreso_encuesta");
		});
	});
	//modulo de resultados
});
/*
querys para ejecutar en bd

alter table ev_evaluacion add fe_comienzo datetime default null;
*/