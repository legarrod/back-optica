<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;




$app->get('/api/cotizaciones/id={id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $sql =  "SELECT * FROM cotizaciones where id = '$id' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $cotizacion = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($cotizacion);
        } else {
            echo json_encode("Esta Cotizacion no existe");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


$app->get('/api/cotizaciones/All', function (Request $request, Response $response) {

    $sql =  "SELECT * FROM cotizaciones where estado = 'pendiente'  ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $cotizaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($cotizaciones);
        } else {
            echo json_encode("no existen cotizaciones");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});




$app->post('/api/cotizaciones/aprobado', function (Request $request, Response $response) {
    $id = $request->getParam('id');
    $sql1 =  "UPDATE cotizaciones SET estado = 'Aprobada' WHERE id = '$id' ";
    $sql = "SELECT * FROM cotizaciones where id = '$id'";
    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);

        if ($resultado->rowCount() > 0) {
              $resultado1= $query->query($sql1);
          if($resultado1){
            echo json_encode("Cotizacion aprobada");
          }else{
                echo json_encode("Cotizacion NO aprobada");
          }

        } else {
            echo json_encode("no existe esta cotizacion");
        }




    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});





?>
