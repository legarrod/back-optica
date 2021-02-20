<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;



$app->get('/api/prueba', function (Request $request, Response $response) {

    $sql =  "SELECT * FROM entraditas";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("no existen proveedores");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

?>