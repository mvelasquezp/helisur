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

Route::get("/", function () {
	$arrOpts = [
		"usuario" => Auth::user(),
		"menu" => 0
	];
    return view("usuario.home")->with($arrOpts);
});
//autenticacion de usuarios
Route::group(["prefix" => "login"], function() {
	Route::get("/", ["as" => "login", "uses" => "Autenticacion@form_login"]);
	Route::post("verificar", "Autenticacion@post_login");
	Route::get("logout", "Autenticacion@logout");
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
		Route::get("programacion", "Encuestas@programacion");
		Route::get("anteriores", "Encuestas@anteriores");
		Route::get("informe", "Encuestas@informe");
		Route::get("lanzamiento", "Encuestas@lanzamiento");
		Route::prefix("ajax")->group(function() {
			//encuestas/ajax
			Route::post("sv-encuesta", "Encuestas@sv_encuesta");
			Route::post("dt-encuesta", "Encuestas@dt_encuesta");
		});
	});
	//modulo de resultados
});