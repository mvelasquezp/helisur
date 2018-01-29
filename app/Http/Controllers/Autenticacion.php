<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
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
        return view("auth.login");
    }

    public function post_login() {
        extract(Request::input());
        if(isset($user, $pswd)) {
            if(Auth::attempt(["des_alias" => $user, "password" => $pswd], true)) {
                return redirect("/");
            }
            else {
                return "usuario y/o clave incorrectos [$user, $pswd]";
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