<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Con esto optenemos o consultamos todas las entradas de la tabla entradas
//http://localhost/back-optica/public/entradas/api/entradas

$app->get('/api/entradas', function (Request $request, Response $response) {

    $numero_factura = $request->getAttribute('numero_factura');
    
    if ($numero_factura) {
        # code...
    }

    $sql =  "SELECT * FROM entradas";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("No hay entradas creadas");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


//con esto optenemos la entrada segun el numero de factura
//http://localhost/back-optica/public/entradas/api/entradas/co123

$app->get('/api/entradas/{numero_factura}', function (Request $request, Response $response) {

    $numero_factura = $request->getAttribute('numero_factura');

    $sql =  "SELECT * FROM entradas where numero_factura = '$numero_factura'";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("No hay entradas creadas con este número de factura");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

//ADD PRODUCT
//http://localhost/back-optica/public/entradas/api/entradas/crear_entrada
// Content-type: application/json
// {
//     "fecha_entrada": "1991-02-11",
//     "fk_id_sede": 1,
//     "numero_factura": "CO123",
//     "fk_id_proveedores": 1
// }

$app->post('/api/entradas/crear_entrada', function (Request $request, Response $response) {

    $fecha_entrada = $request->getParam('fecha_entrada');
    $fk_id_sede = $request->getParam('fk_id_sede');
    $numero_factura = $request->getParam('numero_factura');
    $fk_id_proveedores = $request->getParam('fk_id_proveedores');

    $sql =  "INSERT INTO entradas (id_entrada, fecha_entrada, fk_id_sede, numero_factura, fk_id_proveedores)
     VALUES (NULL,:fecha_entrada,:fk_id_sede,:numero_factura,:fk_id_proveedores)";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':fecha_entrada', $fecha_entrada);
        $resultado->bindParam(':fk_id_sede', $fk_id_sede);
        $resultado->bindParam(':numero_factura', $numero_factura);
        $resultado->bindParam(':fk_id_proveedores', $fk_id_proveedores);

        if ($resultado->execute()) {
            echo json_encode("Entrada creada con exito");
        } else {
            echo json_encode("Hubo un error al crear la entrada intenta de nuevo");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


//UPDATE PRODUCT
//http://localhost/back-optica/public/entradas/api/entradas/update/CO123
// Content-type: application/json
// {
//     "fecha_entrada": "2021-10-25",
//     "fk_id_sede": 1,
//     "numero_factura": "CO123",
//     "fk_id_proveedores": 1
// }

$app->put('/api/entradas/update/{numero_factura}', function (Request $request, Response $response) {

    $fecha_entrada = $request->getParam('fecha_entrada');
    $fk_id_sede = $request->getParam('fk_id_sede');
    $numero_factura = $request->getParam('numero_factura');
    $fk_id_proveedores = $request->getParam('fk_id_proveedores');



    $sql =  "UPDATE entradas SET
    id_entrada = NULL,
    fecha_entrada = :fecha_entrada,
    fk_id_sede = :fk_id_sede,
    numero_factura =  :numero_factura,
    fk_id_proveedores = :fk_id_proveedores  WHERE numero_factura = :numero_factura ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':fecha_entrada', $fecha_entrada);
        $resultado->bindParam(':fk_id_sede', $fk_id_sede);
        $resultado->bindParam(':numero_factura', $numero_factura);
        $resultado->bindParam(':fk_id_proveedores', $fk_id_proveedores);

        if ($resultado->execute()) {
            echo json_encode("Entrada actualizada con exito");
        } else {
            echo json_encode("Hubo un error al actualizar la entrada intenta de nuevo");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});



//DELETE PRODUCT


$app->delete('/api/entradas/delete/{numero_factura}', function (Request $request, Response $response) {

    $numero_factura = $request->getParam('numero_factura');
echo($numero_factura);

    $sql =  "DELETE FROM entradas where numero_factura = :numero_factura";
	
    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':numero_factura', $numero_factura);

        if ($resultado->execute()) {
            echo json_encode("Entrada eliminada correctamente");
        } else {
            echo json_encode("Hubo un error, la entrada no se pudo eliminar intenta nuevamente");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

?>
