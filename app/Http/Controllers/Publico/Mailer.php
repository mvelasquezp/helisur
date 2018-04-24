<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Mail;
use Request;
use Response;
use App\User as User;

class Mailer extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    private function generateRandomString($length = 10) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function mail() {
        $data = array('name'=>"Virat Gandhi");
        Mail::send('email.sample', $data, function($message) {
            $message->to('mvelasquezp88@gmail.com', 'Tutorials Point')
                ->subject('Laravel HTML Testing Mail');
            $message->from('mvelasquez@corporacionlife.com.pe','Miguel Velasquez');
        });
        return "HTML Email Sent. Check your inbox.";
    }

    public function activacion() {
        extract(Request::input());
        if(isset($uid, $pid)) {
            $user = Auth::user();
            $usuario = DB::table("us_usuario_puesto as upt")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_empresa", "=", "usr.id_empresa")
                        ->on("upt.id_usuario", "=", "usr.id_usuario");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("upt.st_vigente", "S")
                ->where("upt.id_usuario", $uid)
                ->where("upt.id_puesto", $pid)
                ->select("ent.des_nombre_3 as nombre", "ent.des_nombre_1 as apepat", "usr.id_usuario as id", "usr.des_email as mail", "ent.cod_entidad as codigo")
                ->first();
            //lee la plantilla del mensaje
            $xml_path = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "activacion.xml"]);
            $mailBody = new \stdClass();
            if(file_exists($xml_path)) {
                $xml = simplexml_load_file($xml_path);
                $mailBody->saludo = (string) $xml->message->saludo;
                $mailBody->mensaje = (string) $xml->message->mensaje;
                $mailBody->enlace = (string) $xml->message->enlace;
            }
            else {
                $mailBody->saludo = "Bienvenido,";
                $mailBody->mensaje = "Has sido seleccionado para formar parte de la evaluación de competencias de Helisur.\nAntes de comenzar, necesitamos que verifiques tu cuenta de correo. Para ello, solo deberás hacer clic en el siguiente enlace:";
                $mailBody->enlace = "Activar mi cuenta";
            }
            $data = [
                "usuario" => $usuario,
                "hash1" => $user->id_empresa . "_" . $this->generateRandomString(32),
                "hash2" => $this->generateRandomString(32),
                "mailbody" => $mailBody
            ];
            //actualiza el flag del usuario
            DB::table("us_usuario")
                ->where("id_usuario", $uid)
                ->where("id_empresa", $user->id_empresa)
                ->update([
                    "st_verifica_mail" => "P",
                    "password" => \Hash::make($usuario->apepat[0] . $usuario->apepat[1] . $usuario->nombre[0] . $usuario->nombre[1] . $usuario->codigo)
                ]);
            //envia el correo
            Mail::send("email.activacion", $data, function($message) use($usuario) {
                $message->to($usuario->mail, $usuario->nombre . " " . $usuario->apepat)
                    ->subject("Activacion de su cuenta de correo");
                $message->from(env("MAIL_FROM"), env("MAIL_NAME"));
            });
            return Response::json([
                "success" => true
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function activacion_all() {
        ini_set("max_execution_time", 300);
        $user = Auth::user();
        $usuarios = DB::table("us_usuario_puesto as upt")
            ->join("us_usuario as usr", function($join_usr) {
                $join_usr->on("upt.id_empresa", "=", "usr.id_empresa")
                    ->on("upt.id_usuario", "=", "usr.id_usuario");
            })
            ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
            ->where("upt.st_vigente", "S")
            ->where("usr.st_verifica_mail", "N")
            ->select("ent.des_nombre_3 as nombre", "ent.des_nombre_1 as apepat", "usr.id_usuario as id", "usr.des_email as mail", "ent.cod_entidad as codigo")
            ->take(10)
            ->get();
        //lee la plantilla del mensaje
        $xml_path = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "activacion.xml"]);
        $mailBody = new \stdClass();
        if(file_exists($xml_path)) {
            $xml = simplexml_load_file($xml_path);
            $mailBody->saludo = (string) $xml->message->saludo;
            $mailBody->mensaje = (string) $xml->message->mensaje;
            $mailBody->enlace = (string) $xml->message->enlace;
        }
        else {
            $mailBody->saludo = "Bienvenido,";
            $mailBody->mensaje = "Has sido seleccionado para formar parte de la evaluación de competencias de Helisur.\nAntes de comenzar, necesitamos que verifiques tu cuenta de correo. Para ello, solo deberás hacer clic en el siguiente enlace:";
            $mailBody->enlace = "Activar mi cuenta";
        }
        foreach($usuarios as $idx => $usuario) {
            DB::table("us_usuario")
                ->where("id_usuario", $usuario->id)
                ->where("id_empresa", $user->id_empresa)
                ->update([
                    "st_verifica_mail" => "P",
                    "password" => \Hash::make($usuario->apepat[0] . $usuario->apepat[1] . $usuario->nombre[0] . $usuario->nombre[1] . $usuario->codigo)
                ]);
            $data = [
                "usuario" => $usuario,
                "hash1" => $this->generateRandomString(32),
                "hash2" => $this->generateRandomString(32),
                "mailbody" => $mailBody
            ];
            Mail::send("email.activacion", $data, function($message) use($usuario) {
                $message->to($usuario->mail, $usuario->nombre . " " . $usuario->apepat)
                    ->subject("Activacion de su cuenta de correo");
                $message->from(env("MAIL_FROM"), env("MAIL_NAME"));
            });
        }
        return Response::json([
            "success" => true
        ]);
    }

    public function reset_password() {
        extract(Request::input());
        if(isset($uid, $pid)) {
            $user = Auth::user();
            $usuario = DB::table("us_usuario_puesto as upt")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_empresa", "=", "usr.id_empresa")
                        ->on("upt.id_usuario", "=", "usr.id_usuario");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("upt.st_vigente", "S")
                ->where("upt.id_usuario", $uid)
                ->where("upt.id_puesto", $pid)
                ->select("ent.des_nombre_3 as nombre", "des_nombre_1 as apepat", "usr.id_usuario as id", "usr.des_email as mail", "usr.des_alias as alias")
                ->first();
            $new_password = $this->generateRandomString(16);
            $data = [
                "usuario" => $usuario,
                "password" => $new_password
            ];
            //actualiza la clave
            DB::table("us_usuario")
                ->where("id_usuario", $uid)
                ->where("id_empresa", $user->id_empresa)
                ->update([
                    "password" => \Hash::make($new_password)
                ]);
            //envia el correo
            Mail::send("email.reset_password", $data, function($message) use($usuario) {
                $message->to($usuario->mail, $usuario->nombre . " " . $usuario->apepat)
                    ->subject("Activacion de su cuenta de correo");
                $message->from(env("MAIL_FROM"), env("MAIL_NAME"));
            });
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