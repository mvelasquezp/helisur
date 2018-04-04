<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Resultados extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function seguimiento() {
        $usuario = Auth::user();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 4
        ];
        return view("resultados.seguimiento")->with($arrOpts);
    }

    public function analisis() {
        $usuario = Auth::user();
        $gerencias = DB::table("ma_oficina")
            ->where("num_jerarquia", 1)
            ->where("id_empresa", $usuario->id_usuario)
            ->where("st_oficina", "S")
            ->select("id_oficina as id", "des_oficina as value")
            ->orderBy("value", "asc")
            ->get();
        $grafico_gerencias = DB::table("ev_evaluacion_num as eval")
            ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
            ->join("ev_subcategoria as sct", function($join_sct) {
                $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                    ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
            })
            ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
            ->groupBy("label")
            ->orderBy("label")
            ->get();
        $total = 0;
        foreach($grafico_gerencias as $r) $total += $r->y;
        $total = $total / count($grafico_gerencias);
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 4,
            "gerencias" => $gerencias,
            "grafico" => $grafico_gerencias,
            "prom" => $total
        ];
        return view("resultados.analisis")->with($arrOpts);
    }

    //ajax

    public function ls_oficinas() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($grn)) {
            $oficinas = DB::table("ma_oficina")
                ->where("num_jerarquia", ">=", 1)
                ->where("id_empresa", $usuario->id_empresa)
                ->where("st_oficina", "S")
                ->where("id_oficina_n0", $grn)
                ->select("id_oficina as id", "des_oficina as value")
                ->orderBy("value", "asc")
                ->get();
            //carga datos para el grafico de gerencias
            $grafico = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("eval.id_usuario", "=", "upt.id_usuario")
                        ->on("eval.id_empresa", "=", "upt.id_empresa");
                })
                ->join("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa");
                })
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->where("ofc.id_oficina_n0", $grn)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "oficinas" => $oficinas,
                    "grafico" => $grafico
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_puestos() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($ofc)) {
            $puestos = DB::table("ma_puesto")
                ->where("id_oficina", $ofc)
                ->where("st_vigente", "S")
                ->select("id_puesto as id", "des_puesto as value")
                ->orderBy("value", "asc")
                ->get();
            //carga datos para el grafico de gerencias
            $grafico = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("eval.id_usuario", "=", "upt.id_usuario")
                        ->on("eval.id_empresa", "=", "upt.id_empresa");
                })
                ->join("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa");
                })
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->where("ofc.id_oficina", $ofc)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "puestos" => $puestos,
                    "grafico" => $grafico
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_personal() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($pst)) {
            $colaboradores = DB::table("us_usuario_puesto as upt")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("upt.id_puesto", $pst)
                ->where("upt.id_empresa", $usuario->id_empresa)
                ->where("upt.st_vigente", "S")
                ->select("upt.id_puesto as pid", "upt.id_usuario as id", DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,' ',ent.des_nombre_3) as value"))
                ->orderBy("value", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "colaboradores" => $colaboradores
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ch_colaborador() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($uid, $pid)) {
            $datos = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->where("eval.id_usuario", $uid)
                ->where("eval.id_puesto", $pid)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "datos" => $datos
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_recordatorios() {
        extract(Request::input());
        if(isset($eva, $peva, $eid)) {
            $usuario = Auth::user();
            $recordatorios = DB::table("ev_recordatorio as rec")
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("rec.id_usuario", "=", "upt.id_usuario")
                        ->on("rec.id_puesto", "=", "upt.id_puesto")
                        ->on("rec.id_empresa", "=", "upt.id_empresa");
                })
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("usr.id_empresa", "=", "upt.id_empresa")
                        ->on("usr.id_usuario", "=", "upt.id_usuario");
                })
                ->where("rec.id_encuesta", $eid)
                ->where("rec.id_empresa", $usuario->id_empresa)
                ->where("rec.id_usuario", $eva)
                ->where("rec.id_puesto", $peva)
                ->select(DB::raw("date_format(rec.fe_envio,'%Y-%m-%d %H:%i:%s') as fecha"), "usr.des_email as mail")
                ->orderBy("rec.fe_envio", "desc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "datos" => $recordatorios
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function send_recordatorio() {
        extract(Request::input());
        if(isset($eva, $peva, $eid)) {
            $usuario = Auth::user();
            //enviar el pinshi mail
            $evaluador = DB::table("us_usuario as usr")
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->select("ent.des_nombre_3 as nombre", "ent.des_nombre_1 as apepat", "usr.id_usuario as id", "usr.des_email as mail")
                ->where("usr.id_usuario", $eva)
                ->first();
            $data = [
                "usuario" => $evaluador
            ];
            \Mail::send("email.recordatorio_encuesta", $data, function($message) use($evaluador) {
                $message->to($evaluador->mail, $evaluador->nombre . " " . $evaluador->apepat)
                    ->subject("Tienes evaluaciones pendientes");
                $message->from(env("MAIL_FROM"), env("MAIL_NAME"));
            });
            //actualiza la bd
            DB::table("ev_recordatorio")->insert([
                "id_encuesta" => $eid,
                "id_empresa" => $usuario->id_empresa,
                "id_usuario" => $usuario->id_usuario,
                "id_puesto" => $peva,
                "fe_envio" => date("Y-m-d H:i:s")
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