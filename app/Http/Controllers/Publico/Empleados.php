<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Empleados extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function resumen() {
        $usuario = Auth::user();
        $entidad = DB::table("ma_entidad")->where("cod_entidad", $usuario->cod_entidad)->first();
        $pendientes = DB::table("ev_evaluacion as eval")
            ->join("ma_encuesta as enc", function($join_enc) {
                $join_enc->on("eval.id_empresa", "=", "enc.id_empresa")
                    ->on("eval.id_encuesta", "=", "enc.id_encuesta");
            })
            ->where("eval.id_evaluador", $usuario->id_usuario)
            ->where("eval.id_empresa", $usuario->id_empresa)
            ->select("enc.id_encuesta as eid", "enc.des_encuesta as encuesta", DB::raw("date_format(enc.fe_inicio,'%Y-%m-%d') as inicio"),
                DB::raw("date_format(enc.fe_fin,'%Y-%m-%d') as fin"), "enc.num_preguntas as cant", "eval.nu_progreso as prog",
                DB::raw("count(eval.id_usuario) as encuestas"))
            ->groupBy("eid", "encuesta", "inicio", "fin", "cant", "prog")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "entidad" => $entidad,
            "menu" => 0,
            "pendientes" => $pendientes
        ];
        return view("usuario.home")->with($arrOpts);
    }

    public function responder($eid, $nup) {
        return "Encuesta $eid | Preguta #$nup";
    }

}