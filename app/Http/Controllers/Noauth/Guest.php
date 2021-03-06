<?php

namespace App\Http\Controllers\Noauth;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Guest extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        //$this->middleware("guest");
    }

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
    }

    //excel

    public function excel() {
        //header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=hoja_calculo.xls');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Pragma: no-cache");
        header("Expires: 0");
        //return view("xls");
        echo "<table>";
        echo "<tr>";
        echo "<td style=\"background-color:#ff0000;\">columna 0</td>";
        echo "<td style=\"color:#0000ff;\">columna 1</td>";
        echo "<td>columna 2</td>";
        echo "<td>columna 3</td>";
        echo "</tr>";
        echo "</table>";
    }

}