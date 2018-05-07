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

    public function lanzamiento() {
        $usuario = Auth::user();
        $encuestas = DB::table("ma_encuesta as enc")
            ->leftJoin("ev_evaluacion as evl", function($join_evl) {
                $join_evl->on("enc.id_encuesta", "=", "evl.id_encuesta")
                    ->on("enc.id_empresa", "=", "evl.id_empresa");
            })
            ->where("enc.id_empresa", $usuario->id_empresa)
            ->where("enc.st_encuesta", "Preparada")
            ->select("enc.id_encuesta as id","enc.des_encuesta as nombre",DB::raw("date_format(enc.fe_inicio,'%d-%m-%Y') as inicio"),
                DB::raw("date_format(enc.fe_fin,'%d-%m-%Y') as fin"),"enc.num_preguntas as preguntas",
                DB::raw("count(evl.id_usuario) as publico"))
            ->groupBy("id","nombre","inicio","fin","preguntas")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "encuestas" => $encuestas,
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
        $programacion = DB::table("ev_evaluacion as eval")
            ->join("us_usuario_puesto as upta", function($join_upta) {
                $join_upta->on("eval.id_usuario", "=", "upta.id_usuario")
                    ->on("eval.id_empresa", "=", "upta.id_empresa")
                    ->on("eval.id_puesto", "=", "upta.id_puesto");
            })
            ->join("us_usuario as usra", function($join_usra) {
                $join_usra->on("upta.id_usuario", "=", "usra.id_usuario")
                    ->on("upta.id_empresa", "=", "usra.id_empresa");
            })
            ->join("ma_entidad as enta", "usra.cod_entidad", "=", "enta.cod_entidad")
            ->join("ma_puesto as psta", function($join_psta) {
                $join_psta->on("eval.id_puesto", "=", "psta.id_puesto")
                    ->on("upta.id_empresa", "=", "psta.id_empresa");
            })
            ->join("ma_oficina as ofca", function($join_ofca) {
                $join_ofca->on("psta.id_oficina", "=", "ofca.id_oficina")
                    ->on("psta.id_empresa", "=", "ofca.id_empresa");
            })
            ->join("us_usuario_puesto as uptb", function($join_uptb) {
                $join_uptb->on("eval.id_evaluador", "=", "uptb.id_usuario")
                    ->on("eval.id_empresa", "=", "uptb.id_empresa")
                    ->on("eval.id_puesto_evaluador", "=", "uptb.id_puesto");
            })
            ->join("us_usuario as usrb", function($join_usrb) {
                $join_usrb->on("uptb.id_usuario", "=", "usrb.id_usuario")
                    ->on("uptb.id_empresa", "=", "usrb.id_empresa");
            })
            ->join("ma_entidad as entb", "usrb.cod_entidad", "=", "entb.cod_entidad")
            ->join("ma_puesto as pstb", function($join_pstb) {
                $join_pstb->on("eval.id_puesto_evaluador", "=", "pstb.id_puesto")
                    ->on("uptb.id_empresa", "=", "pstb.id_empresa");
            })
            ->join("ma_oficina as ofcb", function($join_ofcb) {
                $join_ofcb->on("pstb.id_oficina", "=", "ofcb.id_oficina")
                    ->on("pstb.id_empresa", "=", "ofcb.id_empresa");
            })
            ->where("eval.id_encuesta", $eid)
            ->where("eval.id_empresa", $usuario->id_usuario)
            ->select(DB::raw("concat(enta.des_nombre_1,' ',enta.des_nombre_2,', ',enta.des_nombre_3) as nevo"),"psta.des_puesto as pevo",
                "ofca.des_oficina as oevo",DB::raw("concat(entb.des_nombre_1,' ',entb.des_nombre_2,', ',entb.des_nombre_3) as neva"),
                "pstb.des_puesto as peva","ofcb.des_oficina as oeva","eval.id_usuario as ouid","eval.id_puesto as opid","eval.id_evaluador as auid",
                "eval.id_puesto_evaluador as apid", "eval.st_evaluacion as estado")
            ->orderBy("neva","asc")
            ->orderBy("nevo", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 3,
            "encuesta" => $encuesta,
            "jerarquias" => $jerarquias,
            "programacion" => $programacion
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
            $programacion = DB::table("ev_evaluacion as eval")
                ->join("us_usuario_puesto as upta", function($join_upta) {
                    $join_upta->on("eval.id_usuario", "=", "upta.id_usuario")
                        ->on("eval.id_empresa", "=", "upta.id_empresa");
                })
                ->join("us_usuario as usra", function($join_usra) {
                    $join_usra->on("upta.id_usuario", "=", "usra.id_usuario")
                        ->on("upta.id_empresa", "=", "usra.id_empresa");
                })
                ->join("ma_entidad as enta", "usra.cod_entidad", "=", "enta.cod_entidad")
                ->join("ma_puesto as psta", function($join_psta) {
                    $join_psta->on("eval.id_puesto", "=", "psta.id_puesto")
                        ->on("upta.id_empresa", "=", "psta.id_empresa");
                })
                ->join("ma_oficina as ofca", function($join_ofca) {
                    $join_ofca->on("psta.id_oficina", "=", "ofca.id_oficina")
                        ->on("psta.id_empresa", "=", "ofca.id_empresa");
                })
                ->join("us_usuario_puesto as uptb", function($join_uptb) {
                    $join_uptb->on("eval.id_evaluador", "=", "uptb.id_usuario")
                        ->on("eval.id_empresa", "=", "uptb.id_empresa");
                })
                ->join("us_usuario as usrb", function($join_usrb) {
                    $join_usrb->on("uptb.id_usuario", "=", "usrb.id_usuario")
                        ->on("uptb.id_empresa", "=", "usrb.id_empresa");
                })
                ->join("ma_entidad as entb", "usrb.cod_entidad", "=", "entb.cod_entidad")
                ->join("ma_puesto as pstb", function($join_pstb) {
                    $join_pstb->on("eval.id_puesto_evaluador", "=", "pstb.id_puesto")
                        ->on("uptb.id_empresa", "=", "pstb.id_empresa");
                })
                ->join("ma_oficina as ofcb", function($join_ofcb) {
                    $join_ofcb->on("pstb.id_oficina", "=", "ofcb.id_oficina")
                        ->on("pstb.id_empresa", "=", "ofcb.id_empresa");
                })
                ->where("eval.id_encuesta", $eid)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->select(DB::raw("concat(enta.des_nombre_1,' ',enta.des_nombre_2,', ',enta.des_nombre_3) as nevo"),"psta.des_puesto as pevo",
                    "ofca.des_oficina as oevo",DB::raw("concat(entb.des_nombre_1,' ',entb.des_nombre_2,', ',entb.des_nombre_3) as neva"),
                    "pstb.des_puesto as peva","ofcb.des_oficina as oeva","eval.id_evaluador as auid")
                ->orderBy("neva","asc")
                ->orderBy("nevo", "asc")
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

    public function ls_programacion() {
        extract(Request::input());
        if(isset($eid, $arr)) {
            $usuario = Auth::user();
            $arr_insert = [];
            foreach ($arr as $idx => $puesto) {
                $evaluadores = DB::table("us_usuario_puesto as upt")
                    ->join("ma_puesto as pst", function($join_pst) {
                        $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                            ->on("upt.id_empresa", "=", "pst.id_empresa");
                    })
                    ->join("ma_oficina as ofc", function($join_ofc) {
                        $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                            ->on("pst.id_empresa", "=", "ofc.id_empresa");
                    })
                    ->join("us_usuario as usr", function($join_usr) {
                        $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                            ->on("upt.id_empresa", "=", "usr.id_empresa");
                    })
                    ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                    ->where("upt.id_empresa", $usuario->id_empresa)
                    ->where("pst.id_puesto", $puesto)
                    ->where("upt.st_vigente", "S")
                    ->where(DB::raw("timestampdiff(month, usr.fe_ingreso, current_timestamp)"), ">=", 6)
                    ->select("ofc.des_oficina as ofi", "pst.des_puesto as pto", DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,', ',ent.des_nombre_3) as neva"),
                        "upt.id_usuario as eva", "upt.id_puesto as peva", DB::raw("ifnull(ofc.id_ancestro, 0) as anc"))
                    ->get();
                foreach ($evaluadores as $jdx => $eva) {
                    $colegas = DB::table("ev_afinidad_oficina as ao1")
                        ->join("ev_afinidad_oficina as ao2", function($join_ao2) {
                            $join_ao2->on("ao1.id_afinidad", "=", "ao2.id_afinidad")
                                ->on("ao1.id_afinidad", "=", "ao2.id_afinidad")
                                ->on("ao1.id_oficina", "<>", "ao2.id_oficina");
                        })
                        ->join("ma_puesto as psta", function($join_psta) {
                            $join_psta->on("ao1.id_oficina", "=", "psta.id_oficina")
                                ->on("ao1.id_empresa", "=", "psta.id_empresa");
                        })
                        ->join("us_usuario_puesto as upta", function($join_upta) {
                            $join_upta->on("psta.id_puesto", "=", "upta.id_puesto")
                                ->on("psta.id_empresa", "=", "upta.id_empresa");
                        })
                        ->join("ma_puesto as pstn", function($join_pstn) {
                            $join_pstn->on("ao2.id_oficina", "=", "pstn.id_oficina")
                                ->on("ao2.id_empresa", "=", "pstn.id_empresa");
                        })
                        ->join("us_usuario_puesto as uptn", function($join_uptn) {
                            $join_uptn->on("pstn.id_puesto", "=", "uptn.id_puesto")
                                ->on("pstn.id_empresa", "=", "uptn.id_empresa")
                                ->on("psta.num_jerarquia", "=", "pstn.num_jerarquia");
                        })
                        ->join("ma_oficina as ofcn", function($join_ofcn) {
                            $join_ofcn->on("ao2.id_oficina", "=", "ofcn.id_oficina")
                                ->on("ao2.id_empresa", "=", "ofcn.id_empresa");
                        })
                        ->join("us_usuario as usro", function($join_usro) {
                            $join_usro->on("uptn.id_usuario", "=", "usro.id_usuario")
                                ->on("uptn.id_empresa", "=", "ofcn.id_empresa");
                        })
                        ->join("ma_entidad as ento", "usro.cod_entidad", "=", "ento.cod_entidad")
                        ->where("ao1.st_vigente", "S")
                        ->where("ao2.st_vigente", "S")
                        ->where("upta.id_usuario", $eva->eva)
                        ->where(DB::raw("timestampdiff(month, usro.fe_ingreso, current_timestamp)"), ">=", 6)
                        ->select("ofcn.des_oficina as nofco", "pstn.des_puesto as npevo",
                            DB::raw("concat(ento.des_nombre_1,' ',ento.des_nombre_2,', ',ento.des_nombre_3) as nevo"),
                            "uptn.id_usuario as evo", "uptn.id_puesto as pevo")
                        ->orderBy(DB::raw("rand()"))
                        ->take(2)
                        ->get();
                    foreach ($colegas as $kdx => $evo) {
                        $arr_insert[] = [
                            "oevo" => $eva->ofi,
                            "pevo" => $eva->pto,
                            "nevo" => $eva->neva,
                            "evo" => $eva->eva,
                            "pto" => $eva->peva,
                            "oeva" => $evo->nofco,
                            "peva" => $evo->npevo,
                            "neva" => $evo->nevo,
                            "eva" => $evo->evo,
                            "pta" => $evo->pevo
                        ];
                    }
                    $chupes = DB::table("ev_afinidad_oficina as ao1")
                        ->join("ev_afinidad_oficina as ao2", function($join_ao2) {
                            $join_ao2->on("ao1.id_afinidad", "=", "ao2.id_afinidad")
                                ->on("ao1.id_afinidad", "=", "ao2.id_afinidad")
                                ->on("ao1.id_oficina", "<>", "ao2.id_oficina");
                        })
                        ->join("ma_puesto as psta", function($join_psta) {
                            $join_psta->on("ao1.id_oficina", "=", "psta.id_oficina")
                                ->on("ao1.id_empresa", "=", "psta.id_empresa");
                        })
                        ->join("us_usuario_puesto as upta", function($join_upta) {
                            $join_upta->on("psta.id_puesto", "=", "upta.id_puesto")
                                ->on("psta.id_empresa", "=", "upta.id_empresa");
                        })
                        ->join("ma_puesto as pstn", function($join_pstn) {
                            $join_pstn->on("ao2.id_oficina", "=", "pstn.id_oficina")
                                ->on("ao2.id_empresa", "=", "pstn.id_empresa");
                        })
                        ->join("us_usuario_puesto as uptn", function($join_uptn) {
                            $join_uptn->on("pstn.id_puesto", "=", "uptn.id_puesto")
                                ->on("pstn.id_empresa", "=", "uptn.id_empresa")
                                ->on("psta.num_jerarquia", "<", "pstn.num_jerarquia");
                        })
                        ->join("ma_oficina as ofcn", function($join_ofcn) {
                            $join_ofcn->on("ao2.id_oficina", "=", "ofcn.id_oficina")
                                ->on("ao2.id_empresa", "=", "ofcn.id_empresa");
                        })
                        ->join("us_usuario as usro", function($join_usro) {
                            $join_usro->on("uptn.id_usuario", "=", "usro.id_usuario")
                                ->on("uptn.id_empresa", "=", "ofcn.id_empresa");
                        })
                        ->join("ma_entidad as ento", "usro.cod_entidad", "=", "ento.cod_entidad")
                        ->where("ao1.st_vigente", "S")
                        ->where("ao2.st_vigente", "S")
                        ->where("upta.id_usuario", $eva->eva)
                        ->where(DB::raw("timestampdiff(month, usro.fe_ingreso, current_timestamp)"), ">=", 6)
                        ->select("ofcn.des_oficina as nofco", "pstn.des_puesto as npevo",
                            DB::raw("concat(ento.des_nombre_1,' ',ento.des_nombre_2,', ',ento.des_nombre_3) as nevo"),
                            "uptn.id_usuario as evo", "uptn.id_puesto as pevo")
                        ->take(2)
                        ->get();
                    foreach ($chupes as $kdx => $evo) {
                        $arr_insert[] = [
                            "oevo" => $eva->ofi,
                            "pevo" => $eva->pto,
                            "nevo" => $eva->neva,
                            "evo" => $eva->eva,
                            "pto" => $eva->peva,
                            "oeva" => $evo->nofco,
                            "peva" => $evo->npevo,
                            "neva" => $evo->nevo,
                            "eva" => $evo->evo,
                            "pta" => $evo->pevo
                        ];
                    }
                    $jefe = DB::table("ma_oficina as ofc")
                        ->join("ma_puesto as pst", function($join_pstn) {
                            $join_pstn->on("ofc.id_encargado", "=", "pst.id_oficina")
                                ->on("ofc.id_empresa", "=", "pst.id_empresa");
                        })
                        ->join("us_usuario_puesto as upt", function($join_ofc) {
                            $join_ofc->on("upt.id_empresa", "=", "ofc.id_empresa")
                                ->on("upt.id_puesto", "=", "ofc.id_encargado");
                        })
                        ->join("us_usuario as usr", function($join_usr) {
                            $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                                ->on("upt.id_empresa", "=", "usr.id_empresa");
                        })
                        ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                        ->where("ofc.id_oficina", $eva->ofi)
                        ->where(function($sql) use($eva) {
                            $sql->whereNotNull("ofc.id_encargado")
                                ->where("ofc.id_encargado", "<>", $eva->pto);
                        })
                        ->where("upt.st_vigente", "S")
                        ->select("ofc.des_oficina as nofco", "pst.des_puesto as npevo", "ofc.id_encargado as pevo",
                            DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,', ',ent.des_nombre_3) as nevo"), "upt.id_usuario as evo")
                        ->get();
                    if(count($jefe) > 0) {
                        $jefe = $jefe[0];
                        $arr_insert[] = [
                            "oevo" => $eva->ofi,
                            "pevo" => $eva->pto,
                            "nevo" => $eva->neva,
                            "evo" => $eva->eva,
                            "pto" => $eva->peva,
                            "oeva" => $jefe->nofco,
                            "peva" => $jefe->npevo,
                            "neva" => $jefe->nevo,
                            "eva" => $jefe->evo,
                            "pta" => $jefe->pevo
                        ];
                    }
                    else {
                        $jefe = DB::table("ma_oficina as ofc")
                            ->join("us_usuario_puesto as upt", function($join_upt) {
                                $join_upt->on("ofc.id_encargado", "=", "upt.id_puesto")
                                    ->on("ofc.id_empresa", "=", "upt.id_empresa");
                            })
                            ->join("ma_puesto as pst", function($join_pst) {
                                $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                                    ->on("upt.id_empresa", "=", "pst.id_empresa");
                            })
                            ->join("us_usuario as usr", function($join_usr) {
                                $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                                    ->on("upt.id_empresa", "=", "usr.id_empresa");
                            })
                            ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                            ->where("ofc.id_oficina", $eva->anc)
                            ->select("ofc.des_oficina as nofco", "pst.des_puesto as npevo", "ofc.id_encargado as pevo",
                                DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,', ',ent.des_nombre_3) as nevo"), "upt.id_usuario as evo")
                            ->get();
                            if(count($jefe) > 0) {
                                $jefe = $jefe[0];
                                $arr_insert[] = [
                                    "oevo" => $eva->ofi,
                                    "pevo" => $eva->pto,
                                    "nevo" => $eva->neva,
                                    "evo" => $eva->eva,
                                    "pto" => $eva->peva,
                                    "oeva" => $jefe->nofco,
                                    "peva" => $jefe->npevo,
                                    "neva" => $jefe->nevo,
                                    "eva" => $jefe->evo,
                                    "pta" => $jefe->pevo
                                ];
                            }
                    }
                }
            }
            return Response::json([
                "success" => true,
                "data" => [
                    "eid" => $eid,
                    "ecs" => $arr_insert
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
            $usuario = Auth::user();
            $arr_insert = [];
            $arr_auto = [];
            foreach ($arr as $idx => $row) {
                $vRow = explode("|", $row);
                //par.eva + "|" + par.pta + "|" + par.evo + "|" + par.pto
                if(!isset($arr_auto[$vRow[0]])) $arr_auto[$vRow[0]] = $vRow[1];
                $arr_insert[] = [
                    "id_encuesta" => $eid,
                    "id_empresa" => $usuario->id_empresa,
                    "id_usuario" => $vRow[2],
                    "id_puesto" => $vRow[3],
                    "id_evaluador" => $vRow[0],
                    "id_puesto_evaluador" => $vRow[1],
                    "st_evaluacion" => "Programado",
                    "nu_progreso" => 0
                ];
            }
            foreach ($arr_auto as $eva => $peva) {
                $arr_insert[] = [
                    "id_encuesta" => $eid,
                    "id_empresa" => $usuario->id_empresa,
                    "id_usuario" => $eva,
                    "id_puesto" => $peva,
                    "id_evaluador" => $eva,
                    "id_puesto_evaluador" => $peva,
                    "st_evaluacion" => "Programado",
                    "nu_progreso" => 0
                ];
            }
            DB::table("ev_evaluacion")->insert($arr_insert);
            return Response::json([
                "success" => true,
                "data" => $arr_insert
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function bsq_usuarios() {
        extract(Request::input());
        if(isset($txt)) {
            $usuario = Auth::user();
            $txt = "%" . strtoupper($txt) . "%";
            $usuarios = DB::table("us_usuario_puesto as upt")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->leftJoin("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa");
                })
                ->leftJoin("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->where("upt.st_vigente", "S")
                ->where("upt.id_empresa", $usuario->id_empresa)
                ->where(function($sql) use($txt) {
                    $sql->where("ent.des_nombre_1", "like", $txt)
                        ->orWhere("ent.des_nombre_2", "like", $txt)
                        ->orWhere("ent.des_nombre_3", "like", $txt);
                })
                ->select("upt.id_usuario as uid","upt.id_puesto as pid","pst.des_puesto as puesto","ofc.des_oficina as oficina",
                    DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,', ',ent.des_nombre_3) as nombre"))
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "usuarios" => $usuarios
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_programacion_individual() {
        extract(Request::input());
        if(isset($eva,$peva,$evo,$pevo,$eid)) {
            $usuario = Auth::user();
            $num = DB::table("ev_evaluacion")
                ->where("id_usuario", $evo)
                ->where("id_puesto", $pevo)
                ->where("id_evaluador", $eva)
                ->where("id_puesto_evaluador", $peva)
                ->where("id_encuesta", $eid)
                ->count();
            if($num == 0) {
                DB::table("ev_evaluacion")->insert([
                    "id_encuesta" => $eid,
                    "id_empresa" => $usuario->id_empresa,
                    "id_usuario" => $evo,
                    "id_puesto" => $pevo,
                    "id_evaluador" => $eva,
                    "id_puesto_evaluador" => $peva,
                    "st_evaluacion" => "Programado",
                    "nu_progreso" => 0
                ]);
                return Response::json([
                    "success" => true
                ]);
            }
            return Response::json([
                "success" => false,
                "msg" => "La evaluación ingresada ya ha sido programada"
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_programa_encuesta() {
        extract(Request::input());
        if(isset($eid, $ini, $fin, $prs)) {
            $usuario = Auth::user();
            $dsc = isset($dsc) ? $dsc : "";
            $vIni = explode("-", $ini);
            $vFin = explode("-", $fin);
            $arr_insert = [];
            foreach ($prs as $idx => $pregunta) {
                $arr_insert[] = [
                    "id_encuesta" => $eid,
                    "id_empresa" => $usuario->id_empresa,
                    "id_pregunta" => $pregunta,
                    "num_orden" => ($idx + 1),
                    "tp_pregunta" => "S"
                ];
            }
            DB::table("ev_cuestionario")->insert($arr_insert);
            $num_evaluaciones = DB::table("ev_evaluacion")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->count();
            $num_preguntas = count($prs);
            DB::table("ma_encuesta")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->update([
                    "des_descripcion" => $dsc,
                    "fe_inicio" => implode("-", [$vIni[2], $vIni[1], $vIni[0]]),
                    "fe_fin" => implode("-", [$vFin[2], $vFin[1], $vFin[0]]),
                    "num_preguntas" => $num_preguntas,
                    "st_encuesta" => ($num_preguntas > 0 && $num_evaluaciones > 0) ? "Preparada" : "Pendiente",
                    "id_registra" => $usuario->id_usuario
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

    public function sv_atualiza_encuesta() {
        extract(Request::input());
        if(isset($eid, $ini, $fin)) {
            $usuario = Auth::user();
            $dsc = isset($dsc) ? $dsc : "";
            $vIni = explode("-", $ini);
            $vFin = explode("-", $fin);
            $num_preguntas = DB::table("ev_cuestionario")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->count();
            $num_evaluaciones = DB::table("ev_evaluacion")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->count();
            DB::table("ma_encuesta")
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->whereIn("st_encuesta", ["Preparada","Pendiente"])
                ->update([
                    "des_descripcion" => $dsc,
                    "fe_inicio" => implode("-", [$vIni[2], $vIni[1], $vIni[0]]),
                    "fe_fin" => implode("-", [$vFin[2], $vFin[1], $vFin[0]]),
                    "st_encuesta" => ($num_preguntas > 0 && $num_evaluaciones > 0) ? "Preparada" : "Pendiente"
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

    public function ls_destinatarios() {
        extract(Request::input());
        if(isset($eid)) {
            $usuario = Auth::user();
            $lista = DB::table("ev_evaluacion as eval")
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("eval.id_evaluador", "=", "upt.id_usuario")
                        ->on("eval.id_puesto_evaluador", "=", "upt.id_puesto")
                        ->on("eval.id_empresa", "=", "upt.id_empresa");
                })
                ->join("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("eval.id_empresa", "=", "pst.id_empresa");
                })
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->whereIn("eval.id_encuesta", $eid)
                ->whereIn("eval.st_evaluacion", ["Programado", "En curso", "Valorando"])
                ->select("pst.des_puesto as puesto",DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,' ',ent.des_nombre_3) as nombre"),
                    "ofc.des_oficina as oficina","usr.des_email as email","usr.st_verifica_mail as stmail",DB::raw("count(distinct eval.id_usuario) as cantidad"))
                ->groupBy("puesto", "oficina", "nombre", "email", "stmail")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "lista" => $lista
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function snd_encuesta() {
        extract(Request::input());
        if(isset($eid)) {
            $usuario = Auth::user();
            DB::table("ma_encuesta")
                ->whereIn("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->update(["st_encuesta" => "En curso"]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_encuestas_lanzar() {
        $usuario = Auth::user();
        $encuestas = DB::table("ma_encuesta")
            ->where("id_empresa", $usuario->id_usuario)
            ->where("st_encuesta", "Preparada")
            ->select("id_encuesta as id")
            ->get();
        foreach($encuestas as $encuesta) {
            $arr_encuestas[] = "" . $encuesta->id;
        }
        return Response::json([
            "success" => true,
            "data" => implode(",", $arr_encuestas)
        ]);
    }

    // informe de encuestas

    public function ls_encuestas_informe() {
        $usuario = Auth::user();
        $encuestas = DB::table("ma_encuesta as enc")
            ->leftJoin("ev_evaluacion as eval", function($join_eval) {
                $join_eval->on("enc.id_encuesta", "=", "eval.id_encuesta")
                    ->on("enc.id_empresa", "=", "eval.id_empresa");
            })
            ->where("enc.id_empresa", $usuario->id_empresa)
            ->select("enc.id_encuesta as eid", "enc.des_encuesta as encuesta", "enc.des_descripcion as descripcion", "enc.num_preguntas as preguntas",
                DB::raw("date_format(enc.fe_inicio,'%Y-%m-%d') as inicio"), DB::raw("date_format(enc.fe_fin,'%Y-%m-%d') as fin"), "enc.st_encuesta as estado",
                DB::raw("count(1) as cantidad"))
            ->groupBy("eid", "encuesta", "descripcion", "inicio", "fin", "preguntas", "estado")
            ->orderBy("enc.fe_inicio", "asc")
            ->limit(25)
            ->get();
        return Response::json([
            "success" => true,
            "data" => [
                "encuestas" => $encuestas
            ]
        ]);
    }

    public function dt_progreso_encuesta() {
        extract(Request::input());
        if(isset($eid)) {
            $usuario = Auth::user();
            $evaluaciones = DB::table("ev_evaluacion as eval")
                ->join("ma_encuesta as enc", function($join_enc) {
                    $join_enc->on("eval.id_empresa", "=", "enc.id_empresa")
                        ->on("eval.id_encuesta", "=", "enc.id_encuesta");
                })
                ->join("us_usuario_puesto as eva", function($join_eva) {
                    $join_eva->on("eval.id_empresa", "=", "eva.id_empresa")
                        ->on("eval.id_evaluador", "=", "eva.id_usuario")
                        ->on("eval.id_puesto_evaluador", "=", "eva.id_puesto");
                })
                ->join("ma_puesto as evap", function($join_evap) {
                    $join_evap->on("eva.id_empresa", "=", "evap.id_empresa")
                        ->on("eva.id_puesto", "=", "evap.id_puesto");
                })
                ->join("ma_oficina as evao", function($join_evao) {
                    $join_evao->on("evap.id_empresa", "=", "evao.id_empresa")
                        ->on("evap.id_oficina", "=", "evao.id_oficina");
                })
                ->join("us_usuario as evau", function($join_evau) {
                    $join_evau->on("eva.id_empresa", "=", "evau.id_empresa")
                        ->on("eva.id_usuario", "=", "evau.id_usuario");
                })
                ->join("ma_entidad as enta", "evau.cod_entidad", "=", "enta.cod_entidad")
                ->join("us_usuario_puesto as evo", function($join_evo) {
                    $join_evo->on("eval.id_empresa", "=", "evo.id_empresa")
                        ->on("eval.id_usuario", "=", "evo.id_usuario")
                        ->on("eval.id_puesto", "=", "evo.id_puesto");
                })
                ->join("ma_puesto as evop", function($join_evop) {
                    $join_evop->on("evo.id_empresa", "=", "evop.id_empresa")
                        ->on("evo.id_puesto", "=", "evop.id_puesto");
                })
                ->join("ma_oficina as evoo", function($join_evoo) {
                    $join_evoo->on("evop.id_empresa", "=", "evoo.id_empresa")
                        ->on("evop.id_oficina", "=", "evoo.id_oficina");
                })
                ->join("us_usuario as evou", function($join_evou) {
                    $join_evou->on("evo.id_empresa", "=", "evou.id_empresa")
                        ->on("evo.id_usuario", "=", "evou.id_usuario");
                })
                ->join("ma_entidad as ento", "evou.cod_entidad", "=", "ento.cod_entidad")
                ->where("eval.id_encuesta", $eid)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->where("eval.st_evaluacion", "<>", "Anulada")
                ->select(DB::raw("concat(enta.des_nombre_1,' ',enta.des_nombre_2,' ',enta.des_nombre_3) as evaluador"),"evap.des_puesto as apuesto",
                    "evao.des_oficina as aoficina",DB::raw("concat(ento.des_nombre_1,' ',ento.des_nombre_2,' ',ento.des_nombre_3) as evaluado"),
                    "evop.des_puesto as opuesto","evoo.des_oficina as ooficina","eval.st_evaluacion as estado","eval.nu_progreso as progreso",
                    DB::raw("ifnull(date_format(eval.fe_comienzo,'%Y-%m-%d'),'(no iniciada)') as inicio"), "enc.num_preguntas as preguntas",
                    DB::raw("ifnull(date_format(eval.fe_ultimo_acceso,'%Y-%m-%d %H:%i:%s'),'(no iniciada)') as ultimo"), "eval.id_evaluador as eva",
                    "eval.id_puesto_evaluador as peva", "eval.id_encuesta as eid")
                ->orderBy("evaluador", "asc")
                ->orderBy("evaluado", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "lista" => $evaluaciones
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function del_encuesta() {
        extract(Request::input());
        if(isset($eid)) {
            $usuario = Auth::user();
            DB::table("ma_encuesta")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_encuesta", $eid)
                ->update([
                    "st_encuesta" => "Retirada"
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

    public function retira_evaluacion() {
        extract(Request::input());
        if(isset($eva, $peva, $evo, $pevo, $eid)) {
            $usuario = Auth::user();
            DB::table("ev_evaluacion")
                ->where("id_evaluador", $eva)
                ->where("id_puesto_evaluador", $peva)
                ->where("id_usuario", $evo)
                ->where("id_puesto", $pevo)
                ->where("id_encuesta", $eid)
                ->where("id_empresa", $usuario->id_empresa)
                ->update([
                    "st_evaluacion" => "Anulada",
                    "nu_progreso" => 0
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