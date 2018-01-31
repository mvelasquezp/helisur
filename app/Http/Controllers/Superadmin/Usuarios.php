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
            ->select("ofc.id_oficina as id", "ofc.id_ancestro as parentId", "ofc.des_oficina as name",
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

}