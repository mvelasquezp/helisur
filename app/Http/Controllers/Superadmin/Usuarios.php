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
/*ejecuta esta modificacion en la bd
ALTER TABLE `hls`.`us_usuario` 
ADD COLUMN `fe_ingreso` DATETIME NULL AFTER `id_empresa`;
ALTER TABLE `hls`.`us_usuario` 
ADD COLUMN `st_verifica_mail` CHAR(1) NOT NULL DEFAULT 'N' AFTER `fe_ingreso`;
*/
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
            ->where("usr.id_empresa", $usuario->id_empresa)
            ->select("usr.cod_entidad as codigo","ent.des_nombre_1 as apepat","ent.des_nombre_2 as apemat","ent.des_nombre_3 as nombres",
                DB::raw("date_format(usr.fe_ingreso,'%Y-%m-%d') as ingreso"),"ofc.des_oficina as area","pst.des_puesto as cargo","usr.des_email as email",
                "usr.des_telefono as telefono","usr.st_vigente as estado","usr.st_verifica_mail as vmail")
            ->orderBy("ent.des_nombre_1", "asc")
            ->orderBy("ent.des_nombre_2", "asc")
            ->orderBy("ent.des_nombre_3", "asc")
            ->get();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 1,
            "usuarios" => $usuarios
        ];
        return view("usuarios.lista")->with($arrOpts);
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
                "usr.id_usuario as uid", "ofc.num_jerarquia as jer")
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
            $cargos = DB::table("ma_puesto as pst")
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
                "des_oficina" => $nom,
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

}