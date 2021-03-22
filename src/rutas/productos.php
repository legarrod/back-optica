<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL PRODUCTS

$app->get('/api/productos', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "SELECT idproducto, nombre, descripcion FROM inventario";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);

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
    $codigo = $request->getParam('codigo');
    $fk_id_entrada = $request->getParam('fk_id_entrada');


    $sql =  "INSERT INTO inventario (idproducto, nombre, descripcion, codigo, fk_id_entrada, imagen) 
                VALUES (NULL, :nombre, :descripcion, :codigo, NULL, :imagen) ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':codigo', $codigo);
        $resultado->bindParam(':imagen', $imagen);

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

    $nombre         = $request->getParam('nombre');
    $descripcion    = $request->getParam('descripcion');
    $imagen         = $request->getParam('imagen');
    $codigo         = $request->getParam('codigo');
    $idproducto     = $request->getParam('idproducto');
    $fk_id_entrada  = $request->getParam('fk_id_entrada');

    $sql =  "UPDATE inventario SET 
                nombre          = :nombre,
                descripcion     = :descripcion,
                codigo          = :codigo,
                fk_id_entrada   = :fk_id_entrada,
                imagen          = :imagen WHERE idproducto = :idproducto";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        
        $resultado->bindParam(':nombre',        $nombre);
        $resultado->bindParam(':descripcion',   $descripcion);
        $resultado->bindParam(':codigo',        $codigo);
        $resultado->bindParam(':imagen',        $imagen);
        $resultado->bindParam(':idproducto',    $idproducto);
        $resultado->bindParam(':fk_id_entrada', $fk_id_entrada);

        if ($resultado->execute()) {
            echo json_encode("actualizado correctamente");
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



//DELETE PRODUCT


$app->delete('/api/productos/delete/id={id}', function (Request $request, Response $response) {

    $idproducto = $request->getAttribute('id');



    $sql =  "DELETE FROM inventario where idproducto = :idproducto";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':idproducto', $idproducto);

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
