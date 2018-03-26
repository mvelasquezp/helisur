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
	Route::prefix("resultados")->group(function() {
		Route::get("seguimiento", "Resultados@seguimiento");
		Route::get("analisis", "Resultados@analisis");
		Route::prefix("ajax")->group(function() {
			//resultados/ajax
			Route::post("ls-oficinas", "Resultados@ls_oficinas");
			Route::post("ls-puestos", "Resultados@ls_puestos");
			Route::post("ls-personal", "Resultados@ls_personal");
			Route::post("ch-colaborador", "Resultados@ch_colaborador");
		});
	});
});

/*
mas querys

delimiter $$
CREATE TRIGGER trg_ma_oficina_bins BEFORE insert ON ma_oficina
FOR EACH ROW
begin
	if NEW.num_jerarquia <= 1 then
		set NEW.id_oficina_n0 = NEW.id_oficina;
	else
        set NEW.id_oficina_n0 = (select id_oficina_n0 from ma_oficina where id_oficina = NEW.id_ancestro and id_empresa = NEW.id_empresa);
	end if;
end$$
delimiter ;

update ma_oficina set id_oficina_n0 = 2 where id_ancestro = 2;
	update ma_oficina set id_oficina_n0 = 2 where id_ancestro = 4;
	update ma_oficina set id_oficina_n0 = 2 where id_ancestro = 5;
	update ma_oficina set id_oficina_n0 = 2 where id_ancestro = 6;
    
update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 3;
	update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 7;
	update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 8;
	update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 9;
	update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 10;
	update ma_oficina set id_oficina_n0 = 3 where id_ancestro = 11;
	
update ma_oficina set id_oficina_n0 = 23 where id_ancestro = 23;
	update ma_oficina set id_oficina_n0 = 23 where id_ancestro = 24;

update ma_oficina set id_oficina_n0 = 25 where id_ancestro = 25;
	update ma_oficina set id_oficina_n0 = 25 where id_ancestro = 26;

update ma_oficina set id_oficina_n0 = 27 where id_ancestro = 27;
	update ma_oficina set id_oficina_n0 = 27 where id_ancestro = 28;
	update ma_oficina set id_oficina_n0 = 27 where id_ancestro = 29;

update ma_oficina set id_oficina_n0 = 30 where id_ancestro = 30;
	update ma_oficina set id_oficina_n0 = 30 where id_ancestro = 31;
	update ma_oficina set id_oficina_n0 = 30 where id_ancestro = 32;
	update ma_oficina set id_oficina_n0 = 30 where id_ancestro = 33;

update ma_oficina set id_oficina_n0 = 39 where id_ancestro = 39;
	update ma_oficina set id_oficina_n0 = 39 where id_ancestro = 40;
	update ma_oficina set id_oficina_n0 = 39 where id_ancestro = 41;
	update ma_oficina set id_oficina_n0 = 39 where id_ancestro = 42;

*/