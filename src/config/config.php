<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Conexion
{

    private $host = "151.106.96.201";
    private $user = "u805390524_admin";
    private $pw = "vWPP3b[gfu6@";
    private $db  = "u805390524_optica";

    public function Conectar()
    {
        $cnx  = "mysql:host=$this->host;dbname=$this->db";
        $conectar = new PDO($cnx, $this->user, $this->pw);
        $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $conectar->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conectar;
    }
}

function try_catch_wrapper(Closure $closure, $response)
{
    $statusCode = 200;

    try {

        $respuesta = [
            "status_code" => $statusCode,
            "data" => $closure()
        ];

    } catch (\Exception $exception) {
        $statusCode = 500;
        $respuesta = [
            "status_code" => $statusCode,
            "message" => $exception->getMessage()
        ];
    }

    return $response->withJson($respuesta , $statusCode, JSON_PRETTY_PRINT);    
}
?>