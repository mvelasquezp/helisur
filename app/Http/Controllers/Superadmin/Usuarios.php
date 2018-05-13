<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Usuarios extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function registro() {
        $usuario = Auth::user();
        $usuarios = DB::table("us_usuario as usr")
            ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                $join_upt->on("usr.id_empresa", "=", "upt.id_empresa")
                    ->on("usr.id_usuario", "=", "upt.id_usuario")
                    ->on("upt.st_vigente", "=", DB::raw("'S'"));
            })
            ->leftJoin("ma_puesto as pst", function($join_pst) {
                $join_pst->on("upt.id_empresa", "=", "pst.id_empresa")
                    ->on("upt.id_puesto", "=", "pst.id_puesto");
            })
            ->leftJoin("ma_oficina as ofc", function($join_ofc) {
                $join_ofc->on("pst.id_empresa", "=", "ofc.id_empresa")
                    ->on("pst.id_oficina", "=", "ofc.id_oficina");
            })
            ->leftJoin("ma_grupo_ocupacional as gpo", function($join_gpo) {
                $join_gpo->on("gpo.id_empresa", "=", "usr.id_empresa")
                    ->on("gpo.id_grupo_ocupacional", "=", "usr.id_grupo_ocupacional");
            })
            ->where("usr.id_empresa", $usuario->id_empresa)
            ->select("usr.cod_entidad as codigo","ent.des_nombre_1 as apepat","ent.des_nombre_2 as apemat","ent.des_nombre_3 as nombres",
                DB::raw("date_format(usr.fe_ingreso,'%Y-%m-%d') as ingreso"),DB::raw("ifnull(ofc.des_oficina,'(no asignado)') as area"),
                DB::raw("ifnull(pst.des_puesto,'(no asignado)') as cargo"),"usr.des_email as email","usr.des_telefono as telefono",
                "usr.st_vigente as estado","usr.st_verifica_mail as vmail","usr.id_usuario as id", DB::raw("ifnull(upt.id_puesto,0) as ptid"),
                DB::raw("ifnull(ofc.id_oficina,0) as ofid"),DB::raw("ifnull(gpo.id_grupo_ocupacional,0) as cgpo"),
                DB::raw("ifnull(gpo.des_grupo_ocupacional,'(sin grupo)') as ngpo"))
            ->orderBy("ent.des_nombre_1", "asc")
            ->orderBy("ent.des_nombre_2", "asc")
            ->orderBy("ent.des_nombre_3", "asc")
            ->get();
        $puestos = DB::table("ma_oficina as ofc")
            ->leftJoin("ma_oficina as prn", function($join_prn) {
                $join_prn->on("ofc.id_ancestro", "=", "prn.id_oficina")
                    ->on("ofc.id_empresa", "=", "prn.id_empresa");
            })
            ->leftJoin("us_usuario as usr", function($join_usr) {
                $join_usr->on("ofc.id_encargado", "=", "usr.id_usuario")
                    ->on("ofc.id_empresa", "=", "usr.id_empresa");
            })
            ->leftJoin("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("ofc.st_oficina", "S")
            ->select("ofc.id_oficina as id", "ofc.des_oficina as nombre",
                DB::raw("ifnull(ofc.id_ancestro,0) as aid,ifnull(prn.des_oficina,'(sin jefatura)') as ancestro"),
                DB::raw("ifnull(concat(des_nombre_3,' ',des_nombre_2,' ',des_nombre_1),'(sin asignar)') as encargado"))
            ->orderBy("ofc.des_oficina", "asc")
            ->get();
        $oficinas = DB::table("ma_oficina")
            ->where("st_oficina", "S")
            ->select("id_oficina as value", "des_oficina as text")
            ->orderBy("des_oficina")
            ->get();
        $grupos = DB::table("ma_grupo_ocupacional")
            ->where("id_empresa", $usuario->id_empresa)
            ->where("st_vigente", "S")
            ->select("id_grupo_ocupacional as value", "des_grupo_ocupacional as text")
            ->orderBy("des_grupo_ocupacional", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "usuarios" => $usuarios,
            "puestos" => $puestos,
            "oficinas" => $oficinas,
            "grupos" => $grupos
        ];
        return view("usuarios.lista")->with($arrOpts);
    }

    public function grupos() {
        $usuario = Auth::user();
        $grupos = DB::table("ev_afinidad as afn")
            ->leftJoin("ev_afinidad_oficina as aof", function($join_aof) {
                $join_aof->on("afn.id_afinidad", "=", "aof.id_afinidad")
                    ->on("afn.id_empresa", "=", "aof.id_empresa");
            })
            ->where("afn.st_vigente", "S")
            ->select("afn.id_afinidad as id", "afn.des_afinidad as nombre", DB::raw("count(aof.id_oficina) as areas"))
            ->groupBy("id","nombre")
            ->get();
        $oficinas = DB::table("ma_oficina")
            ->where("st_oficina", "S")
            ->select("id_oficina as value", "des_oficina as text")
            ->orderBy("des_oficina", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "grupos" => $grupos,
            "oficinas" => $oficinas
        ];
        return view("usuarios.grupos")->with($arrOpts);
    }

    public function grupos_ocupacionales() {
        $usuario = Auth::user();
        $grupos = DB::table("ma_grupo_ocupacional as gpo")
            ->where("gpo.st_vigente", "S")
            ->where("gpo.id_empresa", $usuario->id_empresa)
            ->select("gpo.id_grupo_ocupacional as value", "gpo.des_grupo_ocupacional as text")
            ->orderBy("gpo.id_grupo_ocupacional","asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "grupos" => $grupos
        ];
        return view("usuarios.gocupacionales")->with($arrOpts);
    }

    public function organigrama() {
        $usuario = Auth::user();
        $oficinas = DB::table("ma_oficina as ofc")
            ->leftJoin("ma_puesto as pst", function($join_usr) {
                $join_usr->on("ofc.id_encargado", "=", "pst.id_puesto")
                    ->on("ofc.id_empresa", "=", "pst.id_empresa")
                    ->on("pst.st_vigente", "=", DB::raw("'S'"));
            })
            ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                $join_upt->on("pst.id_puesto", "=", "upt.id_puesto")
                    ->on("pst.id_empresa", "=", "upt.id_empresa")
                    ->on("upt.st_vigente", "=", DB::raw("'S'"));
            })
            ->leftJoin("us_usuario as usr", function($join_usr) {
                $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                    ->on("upt.id_empresa", "=", "usr.id_empresa");
            })
            ->leftJoin("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("ofc.id_empresa", $usuario->id_empresa)
            ->where("ofc.st_oficina", "S")
            ->select("ofc.id_oficina as id", "ofc.id_ancestro as parentId", "ofc.des_oficina as cargo",
                DB::raw("ifnull(concat(ent.des_nombre_3,' ',ent.des_nombre_1,' ',ent.des_nombre_2),'(encargado no asignado)') as nombre"),
                "usr.id_usuario as uid", "ofc.num_jerarquia as jer", DB::raw("ifnull(pst.des_puesto,'(personal no asignado)') as puesto"))
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "oficinas" => $oficinas
        ];
        return view("usuarios.organigrama")->with($arrOpts);
    }

    public function activacion() {
        $usuario = Auth::user();
        @mkdir(env("APP_FILES_PATH"), 0774, true);
        $mailBody = new \stdClass();
        $xml_path = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "activacion.xml"]);
        if(file_exists($xml_path)) {
            $xml = simplexml_load_file($xml_path);
            $mailBody->saludo = (string) $xml->message->saludo;
            $mailBody->cuerpo = (string) $xml->message->mensaje;
            $mailBody->enlace = (string) $xml->message->enlace;
        }
        else {
            $mailBody->saludo = "Bienvenido,";
            $mailBody->cuerpo = "Has sido seleccionado para formar parte de la evaluación de competencias de Helisur.\nAntes de comenzar, necesitamos que verifiques tu cuenta de correo. Para ello, solo deberás hacer clic en el siguiente enlace:";
            $mailBody->enlace = "Activar mi cuenta";
        }
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "email" => $mailBody
        ];
        return view("usuarios.mail_activacion")->with($arrOpts);
    }

    public function notificacion() {
        $usuario = Auth::user();
        $usuario = Auth::user();
        @mkdir(env("APP_FILES_PATH"), 0774, true);
        $mailBody = new \stdClass();
        $xml_path = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "notificacion.xml"]);
        if(file_exists($xml_path)) {
            $xml = simplexml_load_file($xml_path);
            $mailBody->saludo = (string) $xml->message->saludo;
            $mailBody->cuerpo = (string) $xml->message->mensaje;
            $mailBody->enlace = (string) $xml->message->enlace;
        }
        else {
            $mailBody->saludo = "Estimado(a)";
            $mailBody->cuerpo = "Al parecer, tienes evaluaciones pendientes por responder. Recuerda que tu participación es de suma importancia para conocer más acerca del desempeño de nuestros colaboradores.\nPara terminar de evaluar, ingresa al portal de gestión de competencias, o haz clic en el siguiente enlace:";
            $mailBody->enlace = "Ingresar al sistema de evaluación de competencias";
        }
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "email" => $mailBody
        ];
        return view("usuarios.mail_notificacion")->with($arrOpts);
    }

    //peticiones ajax

    public function dt_oficina() {
        extract(Request::input());
        if(isset($oid)) {
            $data = DB::table("ma_oficina as ofc")
                ->leftJoin("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("ofc.id_encargado", "=", "pst.id_puesto")
                        ->on("ofc.id_empresa", "=", "pst.id_empresa");
                })
                ->where("ofc.id_oficina", $oid)
                ->select("ofc.id_oficina as oid", "ofc.des_oficina as nombre", DB::raw("ifnull(pst.des_puesto,'(sin asignar)') as encargado"))
                ->first();
            $cargos = DB::table("ma_puesto as pst")
                ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("pst.id_puesto", "=", "upt.id_puesto")
                        ->on("pst.id_empresa", "=", "upt.id_empresa")
                        ->on("upt.st_vigente", "=", DB::raw("'S'"));
                })
                ->leftJoin("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa")
                        ->on("usr.st_vigente", "=", DB::raw("'S'"));
                })
                ->leftJoin("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("pst.id_oficina", $oid)
                ->where("pst.st_vigente", "S")
                ->select("pst.id_puesto as pid", "upt.id_usuario as uid", "pst.des_puesto as puesto", DB::raw("ifnull(concat(ent.des_nombre_3,' ',ent.des_nombre_1,' ',ent.des_nombre_2),'(sin asignar)') as nombre"))
                ->get();
            $dependencias = DB::table("ma_oficina")
                ->where("id_ancestro", $oid)
                ->select("id_oficina as oid", "des_oficina as nombre")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "puesto" => $data,
                    "cargos" => $cargos,
                    "dependencias" => $dependencias
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_puesto() {
        extract(Request::input());
        if(isset($oid, $nom, $jer)) {
            $usuario = Auth::user();
            $nJer = $jer + 1;
            $id = DB::table("ma_oficina")->insertGetId([
                "des_oficina" => strtoupper($nom),
                "st_oficina" => "S",
                "id_empresa" => $usuario->id_empresa,
                "num_jerarquia" => $nJer,
                "id_ancestro" => $oid
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

    public function sv_cargo() {
        extract(Request::input());
        if(isset($ofc, $nom, $jer)) {
            $usuario = Auth::user();
            $nJer = $jer + 1;
            $id = DB::table("ma_puesto")->insertGetId([
                "des_puesto" => strtoupper($nom),
                "num_jerarquia" => $jer,
                "id_oficina" => $ofc,
                "id_empresa" => $usuario->id_empresa
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

    public function sv_encargado() {
        extract(Request::input());
        if(isset($oid, $pid)) {
            $usuario = Auth::user();
            DB::table("ma_oficina")
                ->where("id_oficina", $oid)
                ->where("id_empresa", $usuario->id_empresa)
                ->update(["id_encargado" => $pid]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_puestos() {
        extract(Request::input());
        if(isset($ofc)) {
            $usuario = Auth::user();
            $puestos = DB::table("ma_puesto")
                ->where("id_oficina", $ofc)
                ->where("st_vigente", "S")
                ->select("id_puesto as value", "des_puesto as text")
                ->orderBy("des_puesto", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "puestos" => $puestos
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_usuario() {
        extract(Request::input());
        if(isset($cod,$app,$apm,$nom,$fng,$ofc,$pst)) {
            $usuario = Auth::user();
            $eml = isset($eml) ? $eml : "";
            $tlf = isset($tlf) ? $tlf : "";
            $vFecha = explode("/", $fng);
            $rsFng = implode("-", [$vFecha[2],$vFecha[1],$vFecha[0]]);
            $count = DB::table("ma_entidad")->where("cod_entidad", $cod)->count();
            if($count == 0) {
                DB::table("ma_entidad")->insert([
                    "cod_entidad" => $cod,
                    "des_nombre_1" => $app,
                    "des_nombre_2" => $apm,
                    "des_nombre_3" => $nom,
                    "tp_documento" => "DNI",
                    "id_usuario_registra" => $usuario->id_usuario
                ]);
                $alias = strtolower($nom[0] . $app . (strlen($apm) > 0 ? $apm[0] : ""));
                $password = $app[0] . $app[1] . $nom[0] . $nom[1] . $cod;
                $arr_to_insert = [
                    "des_alias" => $alias,
                    "des_email" => $eml,
                    "des_telefono" => $tlf,
                    "tp_usuario" => "U",
                    "st_vigente" => "S",
                    "password" => \Hash::make($password),
                    "id_usuario_registra" => $usuario->id_usuario,
                    "cod_entidad" => $cod,
                    "id_empresa" => $usuario->id_empresa,
                    "fe_ingreso" => $rsFng,
                    "st_verifica_mail" => "N"
                ];
                if($gpo != 0) $arr_to_insert["id_grupo_ocupacional"] = $gpo;
                $uid = DB::table("us_usuario")->insertGetId($arr_to_insert);
                if($ofc != 0 && $pst != 0) {
                    DB::table("us_usuario_puesto")
                        ->where("id_usuario", $uid)
                        ->where("id_empresa", $usuario->id_empresa)
                        ->update(["st_vigente" => "N"]);
                    $count = DB::table("us_usuario_puesto")
                        ->where("id_usuario", $uid)
                        ->where("id_empresa", $usuario->id_empresa)
                        ->where("id_puesto", $pst)
                        ->count();
                    if($count == 0) {
                        DB::table("us_usuario_puesto")->insert([
                            "id_usuario" => $uid,
                            "id_puesto" => $pst,
                            "st_vigente" => "S",
                            "id_empresa" => $usuario->id_empresa
                        ]);
                    }
                    else {
                        DB::table("us_usuario_puesto")
                            ->where("id_usuario", $uid)
                            ->where("id_empresa", $usuario->id_empresa)
                            ->where("id_puesto", $pst)
                            ->update(["st_vigente" => "S"]);
                    }
                }
                return Response::json([
                    "success" => true
                ]);
            }
            return Response::json([
                "success" => false,
                "msg" => "Ya existe un usuario con código " . $cod
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function dt_usuario() {
        extract(Request::input());
        if(isset($uid)) {
            $usuario = Auth::user();
            $data = DB::table("us_usuario as usr")
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("usr.id_usuario", "=", "upt.id_usuario")
                        ->on("usr.id_empresa", "=", "upt.id_empresa")
                        ->on("upt.st_vigente", "=", DB::raw("'S'"));
                })
                ->leftJoin("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa")
                        ->on("pst.st_vigente", "=", DB::raw("'S'"));
                })
                ->where("usr.id_usuario", $uid)
                ->where("usr.id_empresa", $usuario->id_empresa)
                ->select("ent.cod_entidad as cod", "ent.des_nombre_1 as app", "ent.des_nombre_2 as apm", "ent.des_nombre_3 as nom", 
                    DB::raw("date_format(usr.fe_ingreso,'%d/%m/%Y') as fng"), "usr.des_email as eml", "usr.des_telefono as tlf", 
                    DB::raw("ifnull(pst.id_oficina,0) as oid"), DB::raw("ifnull(upt.id_puesto,0) as pid"), DB::raw("ifnull(usr.id_grupo_ocupacional,0) as gpo"))
                ->first();
            $puestos = DB::table("ma_puesto")
                ->where("st_vigente", "S")
                ->where("id_oficina", $data->oid)
                ->select("id_puesto as value", "des_puesto as text")
                ->orderBy("des_puesto", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "usuario" => $data,
                    "puestos" => $puestos
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ed_usuario() {
        extract(Request::input());
        if(isset($uid,$cod,$app,$apm,$nom,$fng,$ofc,$pst)) {
            $usuario = Auth::user();
            $eml = isset($eml) ? $eml : "";
            $tlf = isset($tlf) ? $tlf : "";
            $data = DB::table("us_usuario as usr")
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("usr.id_usuario", "=", "upt.id_usuario")
                        ->on("usr.id_empresa", "=", "upt.id_empresa")
                        ->on("upt.st_vigente", "=", DB::raw("'S'"));
                })
                ->leftJoin("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa")
                        ->on("pst.st_vigente", "=", DB::raw("'S'"));
                })
                ->where("usr.id_usuario", $uid)
                ->where("usr.id_empresa", $usuario->id_empresa)
                ->select("usr.cod_entidad as cod", "usr.des_email as eml", DB::raw("ifnull(pst.id_oficina,0) as oid"),
                    DB::raw("ifnull(upt.id_puesto,0) as pid"), DB::raw("ifnull(usr.id_grupo_ocupacional,0) as gpo"))
                ->first();
            //
            $vFecha = explode("/", $fng);
            $rsFng = implode("-", [$vFecha[2],$vFecha[1],$vFecha[0]]);
            if(strcmp($data->cod,$cod) == 0) {
                DB::table("ma_entidad")->where("cod_entidad", $cod)->update([
                    "des_nombre_1" => $app,
                    "des_nombre_2" => $apm,
                    "des_nombre_3" => $nom
                ]);
                //verifica si cambió el email
                $dataToUpdate = [
                    "fe_ingreso" => $rsFng,
                    "des_telefono" => $tlf
                ];
                if(strcmp($eml, $data->eml) != 0) {
                    $dataToUpdate["des_email"] = $eml;
                    $dataToUpdate["st_verifica_mail"] = "N";
                }
                //checa el grupo ocupacional
                if(strcmp($gpo, $data->gpo) != 0) {
                    $dataToUpdate["id_grupo_ocupacional"] = $gpo;
                }
                //
                DB::table("us_usuario")->where("id_usuario", $uid)->update($dataToUpdate);
                if($ofc != 0 && $pst != 0 && ($data->oid != $ofc || $data->pid != $pst)) {
                    DB::table("us_usuario_puesto")
                        ->where("id_usuario", $uid)
                        ->where("id_empresa", $usuario->id_empresa)
                        ->update(["st_vigente" => "N"]);
                    $count = DB::table("us_usuario_puesto")
                        ->where("id_usuario", $uid)
                        ->where("id_empresa", $usuario->id_empresa)
                        ->where("id_puesto", $pst)
                        ->count();
                    if($count == 0) {
                        DB::table("us_usuario_puesto")->insert([
                            "id_usuario" => $uid,
                            "id_puesto" => $pst,
                            "st_vigente" => "S",
                            "id_empresa" => $usuario->id_empresa
                        ]);
                    }
                    else {
                        DB::table("us_usuario_puesto")
                            ->where("id_usuario", $uid)
                            ->where("id_empresa", $usuario->id_empresa)
                            ->where("id_puesto", $pst)
                            ->update(["st_vigente" => "S"]);
                    }
                }
                return Response::json([
                    "success" => true
                ]);
            }
            return Response::json([
                "success" => false,
                "puestos" => "La información ingresada es inválida. Intente nuevamente"
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_grupo() {
        extract(Request::input());
        if(isset($nom)) {
            $usuario = Auth::user();
            DB::table("ev_afinidad")->insert([
                "id_empresa" => $usuario->id_empresa,
                "des_afinidad" => $nom,
                "id_usuario_registra" => $usuario->id_usuario,
                "st_vigente" => "S"
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

    public function sv_gocupacional() {
        extract(Request::input());
        if(isset($nom)) {
            $usuario = Auth::user();
            DB::table("ma_grupo_ocupacional")->insert([
                "id_empresa" => $usuario->id_empresa,
                "des_grupo_ocupacional" => $nom
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

    public function ls_areas_afines() {
        extract(Request::input());
        if(isset($gid)) {
            $usuario = Auth::user();
            $oficinas = DB::table("ev_afinidad_oficina as aof")
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("aof.id_oficina", "=", "ofc.id_oficina")
                        ->on("aof.id_empresa", "=", "ofc.id_empresa")
                        ->on("ofc.st_oficina", "=", DB::raw("'S'"));
                })
                ->where("aof.id_afinidad", $gid)
                ->select("aof.id_oficina as value", "ofc.des_oficina as text")
                ->get();
            return Response::json([
                "success" => true,
                "oficinas" => $oficinas
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function sv_oficina() {
        extract(Request::input());
        if(isset($gid, $oid)) {
            $usuario = Auth::user();
            DB::table("ev_afinidad_oficina")->insert([
                "id_afinidad" => $gid,
                "id_empresa" => $usuario->id_empresa,
                "id_oficina" => $oid,
                "id_usuario_registra" => $usuario->id_usuario,
                "st_vigente" => "S"
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

    public function retira_usuario() {
        extract(Request::input());
        if(isset($uid, $pid)) {
            $usuario = Auth::user();
            DB::table("us_usuario_puesto")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_usuario", $uid)
                ->where("id_puesto", $pid)
                ->update(["st_vigente" => "N"]);
            DB::table("us_usuario")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_usuario", $uid)
                ->update(["st_vigente" => "N"]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function activa_usuario() {
        extract(Request::input());
        if(isset($uid)) {
            $usuario = Auth::user();
            $ultimo_upt = DB::table("us_usuario_puesto")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_usuario", $uid)
                ->where("st_vigente", "N");
            if($ultimo_upt->count() > 0) {
                $ultimo_upt = $ultimo_upt
                    ->select("id_puesto as pid")
                    ->orderBy("created_at", "desc")
                    ->first();
                DB::table("us_usuario_puesto")
                    ->where("id_empresa", $usuario->id_empresa)
                    ->where("id_usuario", $uid)
                    ->where("id_puesto", $ultimo_upt->pid)
                    ->update(["st_vigente" => "S"]);
            }
            DB::table("us_usuario")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_usuario", $uid)
                ->update(["st_vigente" => "S"]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function upload_picture() {
        extract(Request::input());
        if(isset($_FILES["file"], $_POST["cod"])) {
            $fileType = $_FILES["file"]["type"];
            $fileError = $_FILES["file"]["error"];
            $fileContent = file_get_contents($_FILES["file"]["tmp_name"]);
            if($fileError == UPLOAD_ERR_OK){
                $path = implode(DIRECTORY_SEPARATOR, [public_path(), "images", "pictures"]);
                @mkdir($path, 0774, true);
                $fullpath = implode(DIRECTORY_SEPARATOR, [$path, $cod . ".jpg"]);
                //@mkdir($)
                file_put_contents($fullpath, $fileContent);
                return Response::json([
                    "success" => true
                ]);
            }
            switch($fileError) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = "Error al intentar subir un archivo que excede el tamaño permitido.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:  
                    $message = "Error al intentar subir un archivo que excede el tamaño permitido.";
                    break;
                case UPLOAD_ERR_PARTIAL:    
                    $message = "Error: no terminó la acción de subir el archivo.";
                    break;
                case UPLOAD_ERR_NO_FILE:    
                    $message = "Error: ningún archivo fue subido.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR: 
                    $message = "Error: servidor no configurado para carga de archivos.";
                    break;
                case UPLOAD_ERR_CANT_WRITE: 
                    $message= "Error: posible falla al grabar el archivo.";
                    break;
                case UPLOAD_ERR_EXTENSION: 
                    $message = "Error: carga de archivo no completada.";
                    break;
                default: $message = "Error: carga de archivo no completada.";
                    break;
            }
            return Response::json([
                "success" => false,
                "msg" => $message
            ]);

        }
        return Response::json([
            "success" => false,
            "msg" => "Debe seleccionar una imagen para subir"
        ]);
    }

    public function retirar_upt() {
        extract(Request::input());
        if(isset($uid, $pid)) {
            $usuario = Auth::user();
            DB::table("us_usuario_puesto")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_usuario", $uid)
                ->where("id_puesto", $pid)
                ->update([
                    "st_vigente" => "N"
                ]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Debe seleccionar una imagen para subir"
        ]);
    }

    public function retirar_grupo() {
        extract(Request::input());
        if(isset($gid)) {
            $usuario = Auth::user();
            DB::table("ev_afinidad")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_afinidad", $gid)
                ->update([
                    "st_vigente" => "N"
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

    public function retirar_gocupacional() {
        extract(Request::input());
        if(isset($gid)) {
            $usuario = Auth::user();
            DB::table("ma_grupo_ocupacional")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_grupo_ocupacional", $gid)
                ->update([
                    "st_vigente" => "N"
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

    public function retirar_oficina() {
        extract(Request::input());
        if(isset($oid, $gid)) {
            $usuario = Auth::user();
            DB::table("ev_afinidad_oficina")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_afinidad", $gid)
                ->where("id_oficina", $oid)
                ->delete();
            return Response::json([
                "success" => true
        ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Debe seleccionar una imagen para subir"
        ]);
    }

    public function eliminar_oficina() {
        extract(Request::input());
        if(isset($oid)) {
            $usuario = Auth::user();
            //dar de baja a los empleados de la oficina
            DB::table("us_usuario_puesto")
                ->where("id_empresa", $usuario->id_empresa)
                ->whereRaw("id_puesto in (select pst.id_puesto from ma_puesto as pst where pst.id_oficina = ? and pst.id_empresa = ?)", [$oid, $usuario->id_empresa])
                ->update([
                    "st_vigente" => "N"
                ]);
            DB::table("ma_oficina")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_oficina", $oid)
                ->update([
                    "st_oficina" => "N"
                ]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Debe seleccionar una imagen para subir"
        ]);
    }

    public function actualiza_correo_activacion() {
        extract(Request::input());
        if(isset($sld, $msj, $nlc)) {
            $path = env("APP_FILES_PATH");
            @mkdir($path, 0774, true);
            $file = $path . DIRECTORY_SEPARATOR . "activacion.xml";
            if(file_exists($file)) unlink($file);
            $xml_content = view("xml.mail_activacion")->with(["saludo" => $sld, "mensaje" => $msj, "enlace" => $nlc]);
            file_put_contents($file, $xml_content);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
        
    }

    public function actualiza_correo_notificacion() {
        extract(Request::input());
        if(isset($sld, $msj, $nlc)) {
            $path = env("APP_FILES_PATH");
            @mkdir($path, 0774, true);
            $file = $path . DIRECTORY_SEPARATOR . "notificacion.xml";
            if(file_exists($file)) unlink($file);
            $xml_content = view("xml.mail_notificacion")->with(["saludo" => $sld, "mensaje" => $msj, "enlace" => $nlc]);
            file_put_contents($file, $xml_content);
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