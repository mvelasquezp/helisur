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
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3,
            "encuestas" => $encuestas
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

}