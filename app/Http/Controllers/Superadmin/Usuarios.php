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

    public function organigrama() {
        $usuario = Auth::user();
        $oficinas = DB::table("ma_oficina as ofc")
            ->leftJoin("us_usuario as usr", function($join_usr) {
                $join_usr->on("usr.id_usuario", "=", "ofc.id_encargado")
                    ->on("usr.id_empresa", "=", "ofc.id_empresa");
            })
            ->leftJoin("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("ofc.id_empresa", $usuario->id_empresa)
            ->select("ofc.id_oficina as id", "ofc.id_ancestro as parentId", "ofc.des_oficina as cargo",
                DB::raw("ifnull(concat(ent.des_nombre_3,' ',ent.des_nombre_1,' ',ent.des_nombre_2),'(sin asignar)') as nombre"),
                "usr.id_usuario as uid")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "oficinas" => $oficinas
        ];
        return view("usuarios.organigrama")->with($arrOpts);
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
            $dependencias = DB::table("ma_puesto as pst")
                ->leftJoin("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("pst.id_puesto", "=", "upt.id_puesto")
                        ->on("pst.id_empresa", "=", "upt.id_empresa");
                })
                ->leftJoin("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa");
                })
                ->leftJoin("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("pst.id_oficina", $oid)
                ->where("pst.st_vigente", "S")
                ->where("upt.st_vigente", "S")
                ->where("usr.st_vigente", "S")
                ->select("pst.id_puesto as pid", "pst.des_puesto as puesto", DB::raw("concat(ent.des_nombre_3,' ',ent.des_nombre_1,' ',ent.des_nombre_2) as nombre"))
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "puesto" => $data,
                    "dependencias" => $dependencias
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

}