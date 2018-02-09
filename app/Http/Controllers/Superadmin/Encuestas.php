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
            return Response::json([
                "success" => true,
                "data" => [
                    "encuesta" => $encuesta,
                    "preguntas" => $preguntas
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

}