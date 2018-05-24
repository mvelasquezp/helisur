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
        $this->middleware("auth", ["except" => [
            "verificar/*"
        ]]);
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
            ->where("enc.st_encuesta", "<>", "Retirada")
            ->select("enc.id_encuesta as eid", "enc.des_encuesta as encuesta", DB::raw("date_format(enc.fe_inicio,'%Y-%m-%d') as inicio"),
                DB::raw("date_format(enc.fe_fin,'%Y-%m-%d') as fin"), "eval.st_evaluacion as estado", "enc.num_preguntas as cant",
                "eval.nu_progreso as prog", DB::raw("count(eval.id_usuario) as encuestas"))
            ->groupBy("eid", "encuesta", "inicio", "fin", "estado", "cant", "prog")
            ->get();
        DB::table("us_usuario")
            ->where("id_usuario", $usuario->id_usuario)
            ->where("id_empresa", $usuario->id_empresa)
            ->update([ "fe_ultimo_acceso" => date("Y-m-d H:i:s") ]);
        $arrOpts = [
            "usuario" => $usuario,
            "entidad" => $entidad,
            "menu" => 0,
            "pendientes" => $pendientes
        ];
        return view("usuario.home")->with($arrOpts);
    }/*

    public function verificar($hash1, $hash2) {
        $vEmpresa = explode("_", $hash1);
        $vUsuario = explode("_", $hash2);
        $id_empresa = $vEmpresa[0];
        $id_usuario = $vUsuario[0];
        DB::table("us_usuario")
            ->where("id_empresa", $id_empresa)
            ->where("id_usuario", $id_usuario)
            ->update(["st_verifica_mail" => "S"]);
        //$app[0] . $app[1] . $nom[0] . $nom[1] . $cod;
        $usuario = DB::table("us_usuario as usr")
            ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("usr.id_empresa", $id_empresa)
            ->where("usr.id_usuario", $id_usuario)
            ->select("ent.des_nombre_1 as app", "ent.des_nombre_3 as nom", "ent.cod_entidad as cod", "usr.des_alias as alias")
            ->first();
        $arrData = [
            "usuario" => $usuario
        ];
        return view("usuario.registro")->with($arrData);
    }*/

    public function imagen($uid) {
        $usuario = DB::table("us_usuario")->where("id_usuario", $uid);
        $path = implode(DIRECTORY_SEPARATOR, [getcwd(), "images", "user-default.png"]);
        $mime = "Content-Type: image/png";
        if($usuario->count() > 0) {
            $usuario = $usuario->select("cod_entidad as cod")->first();
            $iPath = implode(DIRECTORY_SEPARATOR, [public_path(), "images", "pictures", $usuario->cod . ".jpg"]);
            if(file_exists($iPath)) {
                $path = $iPath;
                $mime = "Content-Type: image/jpeg";
            }
        }
        $fp = fopen($path, "rb");
        header($mime);
        header("Content-Length: " . filesize($path));
        fpassthru($fp);
        exit;
    }

    public function responder($eid) {
        $usuario = Auth::user();
        $encuesta = DB::table("ma_encuesta as enc")
            ->join("ev_evaluacion as eval", function($join_eval) {
                $join_eval->on("enc.id_empresa", "=", "eval.id_empresa")
                    ->on("enc.id_encuesta", "=", "eval.id_encuesta");
            })
            ->where("enc.id_encuesta", $eid)
            ->where("enc.id_empresa", $usuario->id_empresa)
            ->where("eval.id_evaluador", $usuario->id_usuario)
            ->select("enc.des_encuesta as nombre", DB::raw("date_format(enc.fe_fin, '%Y-%m-%d') as plazo"), "enc.num_preguntas as total",
                "st_evaluacion as estado", "eval.id_evaluador as eva", "eval.id_puesto_evaluador as peva", DB::raw("min(eval.nu_progreso) as actual"))
            ->groupBy("nombre", "plazo", "total", "estado", "eva", "peva")
            ->first();
        $evaluados = DB::table("ev_evaluacion as eval")
            ->join("us_usuario_puesto as upt", function($join_upt) {
                $join_upt->on("eval.id_empresa", "=", "upt.id_empresa")
                    ->on("eval.id_puesto", "=", "upt.id_puesto")
                    ->on("eval.id_usuario", "=", "upt.id_usuario");
            })
            ->join("ma_puesto as pst", function($join_pst) {
                $join_pst->on("upt.id_empresa", "=", "pst.id_empresa")
                    ->on("upt.id_puesto", "=", "pst.id_puesto");
            })
            ->join("ma_oficina as ofc", function($join_ofc) {
                $join_ofc->on("pst.id_empresa", "=", "ofc.id_empresa")
                    ->on("pst.id_oficina", "=", "ofc.id_oficina");
            })
            ->join("us_usuario as usr", function($join_usr) {
                $join_usr->on("upt.id_empresa", "=", "usr.id_empresa")
                    ->on("upt.id_usuario", "=", "usr.id_usuario");
            })
            ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("eval.id_evaluador", $usuario->id_usuario)
            ->where("eval.id_empresa", $usuario->id_empresa)
            ->where("eval.id_encuesta", $eid)
            ->where("eval.nu_progreso", $encuesta->actual)
            ->select(DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,' ',ent.des_nombre_3) as evaluado"), "pst.des_puesto as puesto",
                "ofc.des_oficina as oficina", "eval.id_usuario as uid", "eval.id_puesto as pid")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 0,
            "eid" => $eid,
            "encuesta" => $encuesta,
            "evaluados" => $evaluados
        ];
        switch($encuesta->actual) {
            case 0: //encuesta aun no inicia
                return view("responder.presentacion")->with($arrOpts);
            case $encuesta->total:
                switch($encuesta->estado) {
                    case "En curso":
                        $pregunta = DB::table("ev_cuestionario as qst")
                            ->join("ma_pregunta as prg", "qst.id_pregunta", "=", "prg.id_pregunta")
                            ->join("ev_grupo as grp", "prg.id_grupo", "=", "grp.id_grupo")
                            ->join("ev_concepto as cnc", "prg.id_concepto", "=", "cnc.id_concepto")
                            ->join("ev_categoria as cat", "prg.id_categoria", "=", "cat.id_categoria")
                            ->join("ev_subcategoria as sct", function($join_sct) {
                                $join_sct->on("prg.id_categoria", "=", "sct.id_categoria")
                                    ->on("prg.id_subcategoria", "=", "sct.id_subcategoria");
                            })
                            ->where("qst.id_empresa", $usuario->id_empresa)
                            ->where("qst.id_encuesta", $eid)
                            ->where("qst.num_orden", $encuesta->actual)
                            ->select("prg.des_pregunta as texto", "grp.des_grupo as grupo", "cnc.des_concepto as concepto",
                                "cat.des_categoria as categoria", "sct.des_subcategoria as subcategoria", "qst.id_pregunta as pid",
                                "qst.tp_pregunta as ptp")
                            ->first();
                        $arrOpts["pregunta"] = $pregunta;
                        return view("responder.pregunta")->with($arrOpts);
                    case "Valorando":
                        $valores = [];
                        $FilePath = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "avances", implode("-", [$encuesta->eva,$encuesta->peva,$eid,$usuario->id_empresa]) . ".xml"]);
                        if(file_exists($FilePath)) {
                            $xml = simplexml_load_file($FilePath);
                            foreach ($xml->avance as $evaluacion) {
                                $ev = new \stdClass();
                                    $ev->uid = (int) $evaluacion->uid;
                                    $ev->pid = (int) $evaluacion->pid;
                                    $ev->pts = (int) $evaluacion->puntaje;
                                    $ev->f1 = (string) $evaluacion->fortalezas->fortaleza[0];
                                    $ev->f2 = (string) $evaluacion->fortalezas->fortaleza[1];
                                    $ev->f3 = (string) $evaluacion->fortalezas->fortaleza[2];
                                    $ev->m1 = (string) $evaluacion->mejoras->mejora[0];
                                    $ev->m2 = (string) $evaluacion->mejoras->mejora[1];
                                    $ev->m3 = (string) $evaluacion->mejoras->mejora[2];
                                $valores[] = $ev;
                            }
                        }
                        $arrOpts["state"] = $valores;
                        return view("responder.valoracion")->with($arrOpts);
                    case "Finalizada":
                        return view("responder.agradecimiento")->with($arrOpts);
                }
            default:
                $pregunta = DB::table("ev_cuestionario as qst")
                    ->join("ma_pregunta as prg", "qst.id_pregunta", "=", "prg.id_pregunta")
                    ->join("ev_grupo as grp", "prg.id_grupo", "=", "grp.id_grupo")
                    ->join("ev_concepto as cnc", "prg.id_concepto", "=", "cnc.id_concepto")
                    ->join("ev_categoria as cat", "prg.id_categoria", "=", "cat.id_categoria")
                    ->join("ev_subcategoria as sct", function($join_sct) {
                        $join_sct->on("prg.id_categoria", "=", "sct.id_categoria")
                            ->on("prg.id_subcategoria", "=", "sct.id_subcategoria");
                    })
                    ->where("qst.id_empresa", $usuario->id_empresa)
                    ->where("qst.id_encuesta", $eid)
                    ->where("qst.num_orden", $encuesta->actual)
                    ->select("prg.des_pregunta as texto", "grp.des_grupo as grupo", "cnc.des_concepto as concepto",
                        "cat.des_categoria as categoria", "sct.des_subcategoria as subcategoria", "qst.id_pregunta as pid", "qst.tp_pregunta as ptp")
                    ->first();
                $arrOpts["pregunta"] = $pregunta;
                return view("responder.pregunta")->with($arrOpts);
        }
    }

    public function comenzar($eid) {
        $usuario = Auth::user();
        $upt = DB::table("us_usuario_puesto")
            ->where("id_empresa", $usuario->id_empresa)
            ->where("id_usuario", $usuario->id_usuario)
            ->where("st_vigente", "S")
            ->select("id_usuario as uid", "id_puesto as pid", "id_empresa as mid")
            ->first();
        $evaluados = DB::table("ev_evaluacion")
            ->where("id_evaluador", $upt->uid)
            ->where("id_empresa", $upt->mid)
            ->where("id_puesto_evaluador", $upt->pid)
            ->where("id_encuesta", $eid)
            ->where("nu_progreso", 0)
            ->where("st_evaluacion", "Programado")
            ->select("id_usuario as uid", "id_puesto as pid")
            ->get();
        foreach ($evaluados as $evaluacion) {
            DB::table("ev_evaluacion")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $upt->mid)
                ->where("id_evaluador", $upt->uid)
                ->where("id_puesto_evaluador", $upt->pid)
                ->where("id_usuario", $evaluacion->uid)
                ->where("id_puesto", $evaluacion->pid)
                ->update([
                    "st_evaluacion" => "En curso",
                    "nu_progreso" => 1,
                    "fe_comienzo" => date("Y-m-d H:i:s")
                ]);
        }
        return redirect("responder/" . $eid);
    }

    public function guardar() {
        extract(Request::input());
        $usuario = Auth::user();
        $arr_to_insert = [];
        $encuesta = DB::table("ma_encuesta")
            ->where("id_empresa", $usuario->id_empresa)
            ->where("id_encuesta", $eid)
            ->select("num_preguntas as preguntas")
            ->first();
        foreach($ids as $idx => $arrid) {
            $vId = explode("|", $arrid);
            $to_insert = [
                "num_orden" => $nmp,
                "num_respuesta" => $puntaje[$idx],
                "id_encuesta" => $eid,
                "id_empresa" =>  $usuario->id_empresa,
                "id_pregunta" => $pid,
                "id_usuario" => $vId[0], 
                "id_puesto" => $vId[1],
                "id_evaluador" => $eva,
                "id_puesto_evaluador" => $peva
            ];
            $arr_to_insert[] = $to_insert;
            if($nmp < $encuesta->preguntas) {
                DB::table("ev_evaluacion")
                    ->where("id_empresa", $usuario->id_empresa)
                    ->where("id_encuesta", $eid)
                    ->where("id_evaluador", $eva)
                    ->where("id_puesto_evaluador", $peva)
                    ->where("id_usuario", $vId[0])
                    ->where("id_puesto", $vId[1])
                    ->where("st_evaluacion", "En curso")
                    ->increment("nu_progreso", 1, ["fe_ultimo_acceso" => date("Y-m-d H:i:s")]);
            }
            else {
                DB::table("ev_evaluacion")
                    ->where("id_empresa", $usuario->id_empresa)
                    ->where("id_encuesta", $eid)
                    ->where("id_evaluador", $eva)
                    ->where("id_puesto_evaluador", $peva)
                    ->where("id_usuario", $vId[0])
                    ->where("id_puesto", $vId[1])
                    ->where("st_evaluacion", "En curso")
                    ->update([
                        "st_evaluacion" => "Valorando",
                        "fe_ultimo_acceso" => date("Y-m-d H:i:s")
                    ]);
            }
        }
        $table = strcmp($ptp, "S") == 0 ? "ev_evaluacion_num" : "ev_evaluacion_txt";
        DB::table($table)->insert($arr_to_insert);
        return redirect("responder/" . $eid);
    }

    public function valorar() {
        extract(Request::input());
        $usuario = Auth::user();
        $arr_to_insert = [];
        foreach ($valoracion as $idx => $valor) {
            $vId = explode("|", $ids[$idx]);
            $arr_to_insert[] = [
                "des_fortaleza_1" => $fs1[$idx],
                "des_fortaleza_2" => isset($fs2[$idx]) ? $fs2[$idx] : "",
                "des_fortaleza_3" => isset($fs3[$idx]) ? $fs3[$idx] : "",
                "des_debilidad_1" => $db1[$idx],
                "des_debilidad_2" => isset($db2[$idx]) ? $db2[$idx] : "",
                "des_debilidad_3" => isset($db3[$idx]) ? $db3[$idx] : "",
                "id_usuario" => $vId[0],
                "id_puesto" => $vId[1],
                "id_empresa" => $usuario->id_empresa,
                "id_encuesta" => $eid,
                "id_evaluador" => $eva,
                "id_puesto_evaluador" => $peva,
                "num_valoracion" => $valor
            ];
            DB::table("ev_evaluacion")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_encuesta", $eid)
                ->where("id_evaluador", $eva)
                ->where("id_puesto_evaluador", $peva)
                ->where("id_usuario", $vId[0])
                ->where("id_puesto", $vId[1])
                ->where("st_evaluacion", "Valorando")
                ->update([ "st_evaluacion" => "Finalizada" ]);
        }
        DB::table("ev_mejora")->insert($arr_to_insert);
        return redirect("responder/" . $eid);
    }

    public function sv_avance() {
        extract(Request::input());
        $usuario = Auth::user();
        if(isset($eva,$peva,$enc,$avn)) {
            $FileDir = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "avances"]);
            @mkdir($FileDir, 0777, true);
            $FilePath = implode(DIRECTORY_SEPARATOR, [$FileDir, implode("-", [$eva,$peva,$enc,$usuario->id_empresa]) . ".xml"]);
            if(file_exists($FilePath)) unlink($FilePath);
            $xml_content = view("xml.avance_valoracion")->with(["usuarios" => $avn]);
            file_put_contents($FilePath, $xml_content);
            return Response::json([ "success" => true ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

}