<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/api/counters/All', function (Request $request, Response $response) {

    $sql1 =  "SELECT COUNT(*) as totalcotizaciones FROM cotizaciones";
    $sql2 =  "SELECT COUNT(*) as totalproductos FROM productos";
    $sql3 =  "SELECT COUNT(*) as totalclientes FROM clientes";
    $sql4 =  "SELECT COUNT(*) as totalproveedores FROM proveedores";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado1 = $query->query($sql1);
        $cotizaciones = $resultado1->fetchAll(PDO::FETCH_OBJ);

        $resultado2 = $query->query($sql2);
        $productos  = $resultado2->fetchAll(PDO::FETCH_OBJ);

        $resultado3 = $query->query($sql3);
        $clientes  = $resultado3->fetchAll(PDO::FETCH_OBJ);


        $resultado4 = $query->query($sql4);
        $proveedores  = $resultado4->fetchAll(PDO::FETCH_OBJ);

        $resultadosgenerales = array(
         $cotizaciones,
        $productos,
        $clientes,
        $proveedores
        );

        echo json_encode($resultadosgenerales);


    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

$app->get('/api/writers', function (Request $request, Response $response) {

    $sql1 =  "SELECT * FROM writers";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado1 = $query->query($sql1);
        $writes = $resultado1->fetchAll(PDO::FETCH_OBJ);

        $resultadosgenerales = array($writes);

        echo json_encode($resultadosgenerales);


    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});



?>
