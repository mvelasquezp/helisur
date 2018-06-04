<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Session;
use App\User as User;

class Autenticacion extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("guest")->except(["logout"]);
    }

    public function form_login() {
        $error = Session::get("error");
        $data = [];
        if(strcmp($error, "") != 0) $data["error"] = $error;
        return view("auth.login")->with($data);
    }

    public function post_login() {
        extract(Request::input());
        if(isset($user, $pswd)) {
            if(Auth::attempt(["des_alias" => $user, "password" => $pswd], true)) {
                return redirect("/");
            }
            else {
                Session::flash("error", "El usuario y clave ingresados son incorrectos");
                return redirect("login");
            }
        }
        else {
            return "ingrese correctamente su usuario y clave";
        }
    }

    public function logout() {
        Auth::logout();
        return redirect("login");
    }

}