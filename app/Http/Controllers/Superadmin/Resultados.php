<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Resultados extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function seguimiento() {
        $usuario = Auth::user();
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 4
        ];
        return view("resultados.seguimiento")->with($arrOpts);
    }

    public function analisis() {
        $usuario = Auth::user();
        $gerencias = DB::table("ma_oficina")
            ->where("num_jerarquia", 1)
            ->where("id_empresa", $usuario->id_usuario)
            ->where("st_oficina", "S")
            ->select("id_oficina as id", "des_oficina as value")
            ->orderBy("value", "asc")
            ->get();
        $grafico_gerencias = DB::table("ev_evaluacion_num as eval")
            ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
            ->join("ev_subcategoria as sct", function($join_sct) {
                $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                    ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
            })
            ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
            ->groupBy("label")
            ->orderBy("label")
            ->get();
        $encuestas = DB::table("ma_encuesta")
            ->where("st_encuesta", "<>", "Retirada")
            ->where("id_empresa", $usuario->id_usuario)
            ->select("id_encuesta as id", "des_encuesta as value")
            ->get();
        $total = 0;
        foreach($grafico_gerencias as $r) $total += $r->y;
        $total = $total / count($grafico_gerencias);
        $arrOpts = [
            "usuario" => $usuario,
            "menu" => 4,
            "gerencias" => $gerencias,
            "grafico" => $grafico_gerencias,
            "prom" => $total,
            "encuestas" => $encuestas
        ];
        return view("resultados.analisis")->with($arrOpts);
    }

    //ajax

    public function ls_empresa() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($ecs)) {
            $grafico_gerencias = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "grafico" => $grafico_gerencias
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_oficinas() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($grn, $ecs)) {
            $oficinas = DB::table("ma_oficina")
                ->where("num_jerarquia", ">=", 1)
                ->where("id_empresa", $usuario->id_empresa)
                ->where("st_oficina", "S")
                ->where("id_oficina_n0", $grn)
                ->select("id_oficina as id", "des_oficina as value")
                ->orderBy("value", "asc")
                ->get();
            //carga datos para el grafico de gerencias
            $grafico = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("eval.id_usuario", "=", "upt.id_usuario")
                        ->on("eval.id_empresa", "=", "upt.id_empresa");
                })
                ->join("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa");
                })
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->where("ofc.id_oficina_n0", $grn)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "oficinas" => $oficinas,
                    "grafico" => $grafico
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_puestos() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($ofc, $ecs)) {
            $puestos = DB::table("ma_puesto")
                ->where("id_oficina", $ofc)
                ->where("st_vigente", "S")
                ->select("id_puesto as id", "des_puesto as value")
                ->orderBy("value", "asc")
                ->get();
            //carga datos para el grafico de gerencias
            $grafico = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("eval.id_usuario", "=", "upt.id_usuario")
                        ->on("eval.id_empresa", "=", "upt.id_empresa");
                })
                ->join("ma_puesto as pst", function($join_pst) {
                    $join_pst->on("upt.id_puesto", "=", "pst.id_puesto")
                        ->on("upt.id_empresa", "=", "pst.id_empresa");
                })
                ->join("ma_oficina as ofc", function($join_ofc) {
                    $join_ofc->on("pst.id_oficina", "=", "ofc.id_oficina")
                        ->on("pst.id_empresa", "=", "ofc.id_empresa");
                })
                ->where("ofc.id_oficina", $ofc)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "puestos" => $puestos,
                    "grafico" => $grafico
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_personal() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($pst)) {
            $colaboradores = DB::table("us_usuario_puesto as upt")
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("upt.id_usuario", "=", "usr.id_usuario")
                        ->on("upt.id_empresa", "=", "usr.id_empresa");
                })
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->where("upt.id_puesto", $pst)
                ->where("upt.id_empresa", $usuario->id_empresa)
                ->where("upt.st_vigente", "S")
                ->select("upt.id_puesto as pid", "upt.id_usuario as id", DB::raw("concat(ent.des_nombre_1,' ',ent.des_nombre_2,' ',ent.des_nombre_3) as value"))
                ->orderBy("value", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "colaboradores" => $colaboradores
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ch_colaborador() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($uid, $pid, $ecs)) {
            $datos = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->where("eval.id_usuario", $uid)
                ->where("eval.id_puesto", $pid)
                ->where("eval.id_empresa", $usuario->id_empresa)
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "datos" => $datos
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function ls_recordatorios() {
        extract(Request::input());
        if(isset($eva, $peva, $eid)) {
            $usuario = Auth::user();
            $recordatorios = DB::table("ev_recordatorio as rec")
                ->join("us_usuario_puesto as upt", function($join_upt) {
                    $join_upt->on("rec.id_usuario", "=", "upt.id_usuario")
                        ->on("rec.id_puesto", "=", "upt.id_puesto")
                        ->on("rec.id_empresa", "=", "upt.id_empresa");
                })
                ->join("us_usuario as usr", function($join_usr) {
                    $join_usr->on("usr.id_empresa", "=", "upt.id_empresa")
                        ->on("usr.id_usuario", "=", "upt.id_usuario");
                })
                ->where("rec.id_encuesta", $eid)
                ->where("rec.id_empresa", $usuario->id_empresa)
                ->where("rec.id_usuario", $eva)
                ->where("rec.id_puesto", $peva)
                ->select(DB::raw("date_format(rec.fe_envio,'%Y-%m-%d %H:%i:%s') as fecha"), "usr.des_email as mail")
                ->orderBy("rec.fe_envio", "desc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "datos" => $recordatorios
                ]
            ]);
        }
        return Response::json([
            "success" => false,
            "msg" => "Parámetros incorrectos"
        ]);
    }

    public function send_recordatorio() {
        extract(Request::input());
        if(isset($eva, $peva, $eid)) {
            $usuario = Auth::user();
            //enviar el pinshi mail
            $evaluador = DB::table("us_usuario as usr")
                ->join("ma_entidad as ent", "usr.cod_entidad", "=", "ent.cod_entidad")
                ->select("ent.des_nombre_3 as nombre", "ent.des_nombre_1 as apepat", "usr.id_usuario as id", "usr.des_email as mail")
                ->where("usr.id_usuario", $eva)
                ->first();
            //lee la plantilla del mensaje
            $xml_path = implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), "notificacion.xml"]);
            $mailBody = new \stdClass();
            if(file_exists($xml_path)) {
                $xml = simplexml_load_file($xml_path);
                $mailBody->saludo = (string) $xml->message->saludo;
                $mailBody->mensaje = (string) $xml->message->mensaje;
                $mailBody->enlace = (string) $xml->message->enlace;
            }
            else {
                $mailBody->saludo = "Estimado(a)";
                $mailBody->mensaje = "Al parecer, tienes evaluaciones pendientes por responder. Recuerda que tu participación es de suma importancia para conocer más acerca del desempeño de nuestros colaboradores.\nPara terminar de evaluar, ingresa al portal de gestión de competencias, o haz clic en el siguiente enlace:";
                $mailBody->enlace = "Ingresar al sistema de evaluación de competencias";
            }
            //go go power rangers
            $data = [
                "usuario" => $evaluador,
                "mailbody" => $mailBody
            ];
            \Mail::send("email.recordatorio_encuesta", $data, function($message) use($evaluador) {
                $message->to($evaluador->mail, $evaluador->nombre . " " . $evaluador->apepat)
                    ->subject("Tienes evaluaciones pendientes");
                $message->from(env("MAIL_FROM"), env("MAIL_NAME"));
            });
            //actualiza la bd
            DB::table("ev_recordatorio")->insert([
                "id_encuesta" => $eid,
                "id_empresa" => $usuario->id_empresa,
                "id_usuario" => $eva,
                "id_puesto" => $peva,
                "fe_envio" => date("Y-m-d H:i:s")
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

    public function ch_empresa() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($ecs)) {
            $grafico_gerencias = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            $points = [];
            $series = [];
            foreach ($grafico_gerencias as $row) {
                $points[] = (double) $row->y;
                $series[] = $row->label;
            }
            //carga libreria de graficos
            include("../../pchart/class/pData.class.php");
            include("../../pchart/class/pDraw.class.php");
            include("../../pchart/class/pRadar.class.php");
            include("../../pchart/class/pImage.class.php");
            /* Create and populate the pData object */
            $MyData = new \pData();   
            $MyData->addPoints(array(40,20,15,10,8,4),"ScoreA");
            $MyData->addPoints(array(8,10,12,20,30,15),"ScoreB"); 
            $MyData->addPoints(array(4,8,16,32,16,8),"ScoreC"); 
            $MyData->setSerieDescription("ScoreA","Application A");
            $MyData->setSerieDescription("ScoreB","Application B");
            $MyData->setSerieDescription("ScoreC","Application C");
            //define las series
            $MyData->addPoints(array("Size","Speed","Reliability","Functionalities","Ease of use","Weight"),"Labels");
            $MyData->setAbscissa("Labels");
            //crea el objeto pchart
            $myPicture = new \pImage(360,240,$MyData);
            //dibuja el fondo
            $Settings = array("R"=>245, "G"=>245, "B"=>245, "Dash"=>1, "DashR"=>255, "DashG"=>255, "DashB"=>255);
            $myPicture->drawFilledRectangle(0,0,360,240,$Settings); 
            //dibuja borde de la imagen
            $myPicture->drawRectangle(0,0,359,239,array("R"=>0,"G"=>0,"B"=>0));
            //escribe titulo de la imagen
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>12));
            $myPicture->drawText(10,20,"pRadar - Draw radar charts",array("R"=>40,"G"=>40,"B"=>40));
            //coloca las propiedades
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/GeosansLight.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
            //crea el grafico de radar
            $SplitChart = new \pRadar();
            //dibuja el grafico de radar
            $myPicture->setGraphArea(60,25,300,200);
            //$Options = array("Layout"=>RADAR_LAYOUT_STAR,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50), "FontName"=>"../fonts/calibri.ttf","FontSize"=>10);
            $Options = array("Layout"=>RADAR_LAYOUT_CIRCLE,"LabelPos"=>RADAR_LABELS_HORIZONTAL,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>50,"EndR"=>32,"EndG"=>109,"EndB"=>174,"EndAlpha"=>30), "FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>10);
            $SplitChart->drawRadar($myPicture,$MyData,$Options);
            //escribe la leyenda del grafico
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>10));
            $myPicture->drawLegend(15,220,array("Style"=>LEGEND_BOX,"Mode"=>LEGEND_HORIZONTAL));
            /* Render the picture (choose the best way) */
            $myPicture->render("D:\\files\\helisur\\example.radar.png");
            $myPicture->autoOutput("example.radar.png");
        }
        else return "Parámetros incorrectos";
    }

    public function chart_demo() {
        $usuario = Auth::user();
        extract(Request::input());
        if(isset($ecs)) {
            $grafico_gerencias = DB::table("ev_evaluacion_num as eval")
                ->join("ma_pregunta as prg", "eval.id_pregunta", "=", "prg.id_pregunta")
                ->join("ev_subcategoria as sct", function($join_sct) {
                    $join_sct->on("sct.id_categoria", "=", "prg.id_categoria")
                        ->on("sct.id_subcategoria", "=", "prg.id_subcategoria");
                })
                ->whereIn("eval.id_encuesta", $ecs)
                ->select("sct.des_subcategoria as label", DB::raw("avg(eval.num_respuesta) as y"))
                ->groupBy("label")
                ->orderBy("label")
                ->get();
            //escribe el xls
            $subtot = 0;
            $cont = 0;
            $data = "<table>";
            $data .= "<tr>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Competencia</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Puntaje</th>
            </tr>";
            $points = [];
            $series = [];
            foreach ($grafico_gerencias as $idx => $row) {
                $points[] = (double) $row->y;
                $series[] = $row->label;
                $subtot += $row->y;
                $cont++;
                $data .= "<tr>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($row->label) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($row->y) . "</td>
                    <td>" . ($idx == 0 ? "<img src=\"XXX\">" : "") . "</td>
                </tr>";
            }
            $data .= "<tr>
                <th style=\"border:1px solid #e0e0e0;vertical-align:middle;background:#ffffff\">Total General</th>
                <th style=\"border:1px solid #e0e0e0;vertical-align:middle;background:#f2f2f2\">" . ($subtot / $cont) . "</th>
            </tr>";
            //carga libreria de graficos
            include("../../pchart/class/pData.class.php");
            include("../../pchart/class/pDraw.class.php");
            include("../../pchart/class/pRadar.class.php");
            include("../../pchart/class/pImage.class.php");
            /* Create and populate the pData object */
            $MyData = new \pData();   
            $MyData->addPoints($points,"Helisur");
            $MyData->setSerieDescription("Helisur","Todos los colaboradores");
            //define las series
            $MyData->addPoints($series,"Competencias");
            $MyData->setAbscissa("Competencias");
            //crea el objeto pchart
            $myPicture = new \pImage(720,480,$MyData);
            //dibuja el fondo
            $Settings = array("R"=>253, "G"=>253, "B"=>253, "Dash"=>1, "DashR"=>255, "DashG"=>255, "DashB"=>255);
            $myPicture->drawFilledRectangle(0,0,720,480,$Settings); 
            //dibuja borde de la imagen
            $myPicture->drawRectangle(0,0,719,479,array("R"=>0,"G"=>0,"B"=>0));
            //escribe titulo de la imagen
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>12));
            $myPicture->drawText(30,30,"Promedio de Puntajes",array("R"=>40,"G"=>40,"B"=>40));
            //coloca las propiedades
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/GeosansLight.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
            //crea el grafico de radar
            $SplitChart = new \pRadar();
            //dibuja el grafico de radar
            $myPicture->setGraphArea(60,25,600,400);
            $Options = array("Layout"=>RADAR_LAYOUT_CIRCLE,"WriteValues"=>TRUE,"LabelPos"=>RADAR_LABELS_HORIZONTAL,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>10,"EndR"=>32,"EndG"=>109,"EndB"=>174,"EndAlpha"=>10), "FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>10);
            $SplitChart->drawRadar($myPicture,$MyData,$Options);
            //escribe la leyenda del grafico
            $myPicture->setFontProperties(array("FontName"=>"../../pchart/fonts/calibri.ttf","FontSize"=>10));
            $myPicture->drawLegend(30,440,array("Style"=>LEGEND_BOX,"Mode"=>LEGEND_HORIZONTAL));
            //escribe la imagen
            $name = date("YmdHis") . ".png";
            $myPicture->render(public_path() . DIRECTORY_SEPARATOR . "charts" . DIRECTORY_SEPARATOR . $name);
            //guarda la iamgen
            str_replace("XXX", url("charts", [$name]), $data);
            //escribe el xls
            $file = "export.xls";
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$file");
            echo $data;
        }
        else return "Parámetros incorrectos";
    }

}