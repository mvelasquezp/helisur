<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Preguntas extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function clasificacion() {
        $grupos = DB::table("ev_grupo")
            ->select("id_grupo as id", "des_grupo as nombre", "st_vigente as estado")
            ->orderBy("st_vigente", "desc")
            ->orderBy("des_grupo", "asc")
            ->get();
        $conceptos = DB::table("ev_concepto")
            ->select("id_concepto as id", "des_concepto as nombre", "st_vigente as estado")
            ->orderBy("st_vigente", "desc")
            ->orderBy("des_concepto", "asc")
            ->get();
        $categorias = DB::table("ev_categoria")
            ->select("id_categoria as id", "des_categoria as nombre", "st_vigente as estado")
            ->orderBy("st_vigente", "desc")
            ->orderBy("des_categoria", "asc")
            ->get();
        $subcategorias = DB::table("ev_subcategoria as scat")
            ->join("ev_categoria as cat", "scat.id_categoria", "=", "cat.id_categoria")
            ->select("scat.id_subcategoria as id", "cat.des_categoria as categoria", "scat.des_subcategoria as nombre", "scat.st_vigente as estado", "scat.id_categoria as gato")
            ->orderBy("scat.st_vigente", "desc")
            ->orderBy("scat.des_subcategoria", "asc")
            ->get();
        $arrOpts = [
            "usuario" => Auth::user(),
            "menu" => 2,
            "grupos" => $grupos,
            "conceptos" => $conceptos,
            "categorias" => $categorias,
            "subcategorias" => $subcategorias
        ];
        return view("preguntas.clasificacion")->with($arrOpts);
    }
    public function banco() {
        $grupos = DB::table("ev_grupo")
            ->where("st_vigente", "S")
            ->select("id_grupo as id", "des_grupo as nombre")
            ->orderBy("des_grupo", "asc")
            ->get();
        $conceptos = DB::table("ev_concepto")
            ->where("st_vigente", "S")
            ->select("id_concepto as id", "des_concepto as nombre")
            ->orderBy("des_concepto", "asc")
            ->get();
        $categorias = DB::table("ev_categoria")
            ->where("st_vigente", "S")
            ->select("id_categoria as id", "des_categoria as nombre")
            ->orderBy("des_categoria", "asc")
            ->get();
        $subcategorias = DB::table("ev_subcategoria")
            ->where("id_categoria", count($categorias) > 0 ? 1 : 0)
            ->where("st_vigente", "S")
            ->select("id_subcategoria as id", "des_subcategoria as nombre")
            ->orderBy("des_subcategoria", "asc")
            ->get();
        $preguntas = DB::table("ma_pregunta as prg")
            ->join("ev_grupo as grp", "prg.id_grupo", "=", "grp.id_grupo")
            ->join("ev_concepto as cnc", "prg.id_concepto", "=", "cnc.id_concepto")
            ->join("ev_categoria as cat", "prg.id_categoria", "=", "cat.id_categoria")
            ->join("ev_subcategoria as sct", function($join_sct) {
                $join_sct->on("prg.id_categoria", "=", "sct.id_categoria")
                    ->on("prg.id_subcategoria", "=", "sct.id_subcategoria");
            })
            ->where("prg.st_vigente", "S")
            ->select("prg.id_pregunta as id", "grp.des_grupo as grupo", "cnc.des_concepto as concepto", "cat.des_categoria as categoria",
                "sct.des_subcategoria as subcategoria", "prg.des_pregunta as texto")
            ->get();
        $arrOpts = [
            "usuario" => Auth::user(),
            "menu" => 2,
            "grupos" => $grupos,
            "conceptos" => $conceptos,
            "categorias" => $categorias,
            "subcategorias" => $subcategorias,
            "preguntas" => $preguntas
        ];
        return view("preguntas.banco")->with($arrOpts);
    }
    /*
    -- ejecuta estos querys prro!
    ALTER TABLE `hls`.`ev_grupo` 
    ADD COLUMN `st_vigente` CHAR NOT NULL DEFAULT 'S' AFTER `updated_at`;
    ALTER TABLE `hls`.`ev_concepto` 
    ADD COLUMN `st_vigente` CHAR NOT NULL DEFAULT 'S' AFTER `updated_at`;
    ALTER TABLE `hls`.`ev_categoria` 
    ADD COLUMN `st_vigente` CHAR NOT NULL DEFAULT 'S' AFTER `updated_at`;
    ALTER TABLE `hls`.`ev_subcategoria` 
    ADD COLUMN `st_vigente` CHAR NOT NULL DEFAULT 'S' AFTER `updated_at`;
    ALTER TABLE `hls`.`ma_pregunta` 
    ADD COLUMN `id_registra` INT(11) NOT NULL AFTER `st_vigente`;
    */
    //peticiones post
    public function ins_grupo() {
        extract(Request::input());
        if(isset($nom)) {
            $id = DB::table("ev_grupo")->insertGetId([
                "des_grupo" => strtoupper($nom),
                "st_vigente" => "S"
            ]);
            return Response::json([
                "success" => true,
                "id" => $id
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ins_concepto() {
        extract(Request::input());
        if(isset($nom)) {
            $id = DB::table("ev_concepto")->insertGetId([
                "des_concepto" => strtoupper($nom),
                "st_vigente" => "S"
            ]);
            return Response::json([
                "success" => true,
                "id" => $id
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ins_categoria() {
        extract(Request::input());
        if(isset($nom)) {
            $id = DB::table("ev_categoria")->insertGetId([
                "des_categoria" => strtoupper($nom),
                "st_vigente" => "S"
            ]);
            return Response::json([
                "success" => true,
                "id" => $id
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ins_subcategoria() {
        extract(Request::input());
        if(isset($nom, $cat)) {
            $id = DB::table("ev_subcategoria")->insertGetId([
                "id_categoria" => $cat,
                "des_subcategoria" => strtoupper($nom),
                "st_vigente" => "S"
            ]);
            return Response::json([
                "success" => true,
                "id" => $id
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ins_pregunta() {
        extract(Request::input());
        if(isset($grp,$cnc,$cat,$sct,$prg)) {
            $usuario = Auth::user();
            DB::table("ma_pregunta")->insert([
                "des_pregunta" => strtoupper($prg),
                "id_grupo" => $grp,
                "id_concepto" => $cnc,
                "id_categoria" => $cat,
                "id_subcategoria" => $sct,
                "st_vigente" => "S",
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

    public function ls_subcategorias() {
        extract(Request::input());
        if(isset($cat)) {
            $subcategorias = DB::table("ev_subcategoria")
                ->where("id_categoria", $cat)
                ->select("id_subcategoria as value", "des_subcategoria as text")
                ->orderBy("text", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => $subcategorias
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function del_grupo() {
        extract(Request::input());
        if(isset($gid)) {
            $grupos = DB::table("ev_grupo")
                ->where("id_grupo", $gid)
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

    public function del_concepto() {
        extract(Request::input());
        if(isset($nid)) {
            $grupos = DB::table("ev_concepto")
                ->where("id_concepto", $nid)
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

    public function del_categoria() {
        extract(Request::input());
        if(isset($cid)) {
            $grupos = DB::table("ev_categoria")
                ->where("id_categoria", $cid)
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

    public function del_subcategoria() {
        extract(Request::input());
        if(isset($cid, $sid)) {
            $grupos = DB::table("ev_subcategoria")
                ->where("id_categoria", $cid)
                ->where("id_subcategoria", $sid)
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

    public function del_pregunta() {
        extract(Request::input());
        if(isset($pid)) {
            DB::table("ma_pregunta")
                ->where("id_pregunta", $pid)
                ->update([ "st_vigente" => "N" ]);
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function act_grupo() {
        extract(Request::input());
        if(isset($gid)) {
            $grupos = DB::table("ev_grupo")
                ->where("id_grupo", $gid)
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

    public function act_concepto() {
        extract(Request::input());
        if(isset($nid)) {
            $grupos = DB::table("ev_concepto")
                ->where("id_concepto", $nid)
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

    public function act_categoria() {
        extract(Request::input());
        if(isset($cid)) {
            $grupos = DB::table("ev_categoria")
                ->where("id_categoria", $cid)
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

    public function act_subcategoria() {
        extract(Request::input());
        if(isset($cid, $sid)) {
            $grupos = DB::table("ev_subcategoria")
                ->where("id_categoria", $cid)
                ->where("id_subcategoria", $sid)
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

}