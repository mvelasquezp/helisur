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
	Route::get("imagen/{uid}", "Empleados@imagen");
	Route::prefix("responder")->group(function() {
		Route::get("{eid}", "Empleados@responder");
		Route::get("comenzar/{eid}", "Empleados@comenzar");
		Route::post("guardar", "Empleados@guardar");
		Route::post("valorar", "Empleados@valorar");
	});
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
mas querys

ALTER TABLE `ev_evaluacion_num` 
ADD COLUMN `id_evaluador` INT NOT NULL AFTER `id_puesto`,
ADD COLUMN `id_puesto_evaluador` INT NOT NULL AFTER `id_evaluador`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_encuesta`, `id_empresa`, `id_pregunta`, `id_usuario`, `id_puesto`, `id_evaluador`, `id_puesto_evaluador`);

ALTER TABLE `ev_evaluacion_txt` 
ADD COLUMN `id_evaluador` INT NOT NULL AFTER `id_puesto`,
ADD COLUMN `id_puesto_evaluador` INT NOT NULL AFTER `id_evaluador`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_encuesta`, `id_empresa`, `id_pregunta`, `id_usuario`, `id_puesto`, `id_evaluador`, `id_puesto_evaluador`);

ALTER TABLE `ev_mejora` 
ADD COLUMN `id_evaluador` INT NOT NULL AFTER `id_pregunta`,
ADD COLUMN `id_puesto_evaluador` INT NOT NULL AFTER `id_evaluador`,
ADD COLUMN `num_valoracion` INT NULL DEFAULT 5 AFTER `id_puesto_evaluador`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_usuario`, `id_puesto`, `id_empresa`, `id_encuesta`, `id_pregunta`, `id_puesto_evaluador`, `id_evaluador`);

ALTER TABLE `ev_mejora` 
DROP FOREIGN KEY `fk_cuestionario_mejora`;
ALTER TABLE `ev_mejora` 
DROP COLUMN `id_pregunta`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_usuario`, `id_puesto`, `id_empresa`, `id_encuesta`, `id_puesto_evaluador`, `id_evaluador`),
DROP INDEX `fk_cuestionario_mejora` ,
ADD INDEX `fk_cuestionario_mejora` (`id_encuesta` ASC, `id_empresa` ASC);
ALTER TABLE `ev_mejora` 
ADD CONSTRAINT `fk_cuestionario_mejora`
  FOREIGN KEY (`id_encuesta` , `id_empresa`)
  REFERENCES `ev_cuestionario` (`id_encuesta` , `id_empresa`);

*/