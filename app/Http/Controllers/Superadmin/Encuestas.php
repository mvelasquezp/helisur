<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Encuestas extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function programacion() {
        $usuario = Auth::user();
        $encuestas = DB::table("ma_encuesta as enc")
            ->leftJoin("ev_evaluacion as evl", function($join_evl) {
                $join_evl->on("enc.id_encuesta", "=", "evl.id_encuesta")
                    ->on("enc.id_empresa", "=", "evl.id_empresa");
            })
            ->where("enc.id_empresa", $usuario->id_empresa)
            ->select("enc.id_encuesta as id","enc.des_encuesta as nombre",DB::raw("date_format(enc.fe_inicio,'%d-%m-%Y') as inicio"),
                DB::raw("date_format(enc.fe_fin,'%d-%m-%Y') as fin"),"enc.num_preguntas as preguntas","enc.st_encuesta as estado",
                DB::raw("count(evl.id_usuario) as publico"))
            ->groupBy("id","nombre","inicio","fin","preguntas","estado")
            ->orderBy("estado", "asc")
            ->get();
        $preguntas = DB::table("ma_pregunta as prg")
            ->join("ev_grupo as grp", "prg.id_grupo", "=", "grp.id_grupo")
            ->join("ev_concepto as cnc", "prg.id_concepto", "=", "cnc.id_concepto")
            ->join("ev_categoria as cat", "prg.id_categoria", "=", "cat.id_categoria")
            ->join("ev_subcategoria as sct", "prg.id_subcategoria", "=", "sct.id_subcategoria")
            ->where("prg.st_vigente", "S")
            ->select("prg.id_pregunta as id", "prg.des_pregunta as texto", "grp.des_grupo as grupo", "cnc.des_concepto as concepto",
                "cat.des_categoria as categoria", "sct.des_subcategoria as subcategoria")
            ->orderBy("prg.des_pregunta", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3,
            "encuestas" => $encuestas,
            "preguntas" => $preguntas
        ];
        return view("encuestas.programacion")->with($arrOpts);
    }

    public function anteriores() {
        $usuario = Auth::user();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3
        ];
        return view("encuestas.anteriores")->with($arrOpts);
    }

    public function informe() {
        $usuario = Auth::user();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3
        ];
        return view("encuestas.informe")->with($arrOpts);
    }

    public function lanzamiento() {
        $usuario = Auth::user();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3
        ];
        return view("encuestas.lanzamiento")->with($arrOpts);
    }

    //extras

    public function evaluadores($eid) {
        $usuario = Auth::user();
        $encuesta = DB::table("ma_encuesta")
            ->where("id_encuesta", $eid)
            ->where("id_empresa", $usuario->id_empresa)
            ->select("des_encuesta as nombre", "id_encuesta as id")
            ->first();
        $jerarquias = DB::table("ma_jerarquias as jer")
            ->select("jer.des_jerarquia as descripcion", "jer.num_jerarquia as numero")
            ->orderBy("jer.num_jerarquia", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3,
            "encuesta" => $encuesta,
            "jerarquias" => $jerarquias
        ];
        return view("encuestas.asigna_evaluaciones")->with($arrOpts);
        //return "<p>Programaré la encuesta $eid</p>";
    }

    //ajax

    public function sv_encuesta() {
        extract(Request::input());
        if(isset($nom)) {
            $usuario = Auth::user();
            DB::table("ma_encuesta")->insert([
                "des_encuesta" => strtoupper($nom),
                "id_registra" => $usuario->id_usuario,
                "id_empresa" => $usuario->id_empresa,
                "num_preguntas" => 0
            ]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function dt_encuesta() {
        extract(Request::input());
        if(isset($eid)) {
            $usuario = Auth::user();
            $encuesta = DB::table("ma_encuesta")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->select("des_encuesta as nom", "des_descripcion as descripcion", DB::raw("date_format(fe_inicio,'%d-%m-%Y') as inicio"),
                    DB::raw("date_format(fe_fin,'%d-%m-%Y') as fin"))
                ->first();
            $preguntas = DB::table("ev_cuestionario")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->select("id_encuesta as id", DB::raw("group_concat(distinct id_pregunta order by num_orden asc separator ',') as preguntas"))
                ->groupBy("id_encuesta")
                ->first();
            $programacion = DB::table("ev_evaluacion as evl")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("evl.id_usuario", "=", "usr.id_usuario")
                        ->on("evl.id_empresa", "=", "usr.id_empresa");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("evl.id_encuesta", $eid)
                ->where("evl.id_empresa", $usuario->id_empresa)
                ->select(DB::raw("concat(ent.des_nombre_3,' ',ent.des_nombre_1,' ',ent.des_nombre_2) as nombre"), "evl.st_evaluacion as estado", "evl.nu_progreso as progreso")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "encuesta" => $encuesta,
                    "preguntas" => $preguntas,
                    "programacion" => $programacion
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_cargos() {
        extract(Request::input());
        if(isset($jrs)) {
            $usuario = Auth::user();
            $puestos = DB::table("ma_puesto as pst")
                ->leftJoin("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_empresa", "=", "ofc.id_empresa")
                        ->on("pst.id_oficina", "=", "ofc.id_oficina");
                })
                ->whereIn("pst.num_jerarquia", $jrs)
                ->where("pst.id_empresa", $usuario->id_empresa)
                ->where("pst.st_vigente", "S")
                ->select("pst.id_puesto as id", "pst.num_jerarquia as num", DB::raw("ifnull(ofc.des_oficina,'(sin asignar)') as oficina"), "pst.des_puesto as puesto")
                ->orderBy("pst.num_jerarquia", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "puestos" => $puestos
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_programacion() {
        extract(Request::input());
        if(isset($eid, $arr)) {
            foreach ($arr as $idx => $puesto) {
                $usuarios = DB::table("us_usuario_puesto as upt")
                    ->join("us_usuario as usr", function($join_usr) {
                        $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                            ->on("upt.id_empresa", "=", "usr.id_empresa");
                    })
                    ->where("upt.st_vigente", "S")
                    ->where(DB::raw("timestampdiff(month, usr.fe_ingreso, current_timestamp)"), ">=", 6)
                    ->select("upt.id_usuario", "upt.id_puesto", "upt.id_empresa")
                    ->get();
            }
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

}