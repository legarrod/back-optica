<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL PRODUCTS

$app->get('/api/entradas', function (Request $request, Response $response) {

    $sql =  "SELECT * FROM entradas";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("no existen productos");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});



//GET ALL BY ID


$app->get('/api/productos/id={id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $sql =  "SELECT * FROM productos where id = '$id' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("no existen productos con este ID");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


//GET PRODUCT BY CATEGORY

$app->get('/api/productos/category={category}', function (Request $request, Response $response) {

    $categoria = $request->getAttribute('category');

    $sql =  "SELECT * FROM productos where categoria = '$categoria' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("no existen productos con esta categoria");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


//ADD PRODUCT

$app->post('/api/productos/addProduct', function (Request $request, Response $response) {

    $nombre = $request->getParam('nombre');
    $descripcion = $request->getParam('descripcion');
    $imagen = $request->getParam('imagen');
    $stock = $request->getParam('stock');



    $sql =  "INSERT INTO productos (nombre,descripcion,imagen,stock)
     VALUES (:nombre,:descripcion,:imagen,:stock) ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':imagen', $imagen);
        $resultado->bindParam(':stock', $stock);

        if ($resultado->execute()) {
            echo json_encode("agregado correctamente");
        } else {
            echo json_encode("no fue agregado");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


//UPDATE PRODUCT

$app->put('/api/productos/update', function (Request $request, Response $response) {

    $id = $request->getParam('id');
    $nombre = $request->getParam('nombre');
    $descripcion = $request->getParam('descripcion');
    $imagen = $request->getParam('imagen');
    $stock = $request->getParam('stock');



    $sql =  "UPDATE productos SET
    nombre= :nombre,
    descripcion = :descripcion,
    imagen =  :imagen,
    stock = :stock  WHERE id = :id ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':imagen', $imagen);
        $resultado->bindParam(':stock', $stock);
        $resultado->bindParam(':id', $id);

        if ($resultado->execute()) {
            echo json_encode("actualizado correctamente");
        } else {
            echo json_encode("no fue actualizado");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});



//DELETE PRODUCT


$app->delete('/api/productos/delete/id={id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');



    $sql =  "DELETE FROM productos where id = :id";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':id', $id);

        if ($resultado->execute()) {
            echo json_encode("eliminado correctamente");
        } else {
            echo json_encode("no fue eliminado");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

?>
