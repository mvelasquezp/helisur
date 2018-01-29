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
		"usuario" => Auth::user()
	];
    return view("usuario.home")->with($arrOpts);
});
//autenticacion de usuarios
Route::group(["prefix" => "login"], function() {
	Route::get("/", ["as" => "login", "uses" => "Autenticacion@form_login"]);
	Route::post("verificar", "Autenticacion@post_login");
	Route::get("logout", "Autenticacion@logout");
});
