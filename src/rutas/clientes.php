<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS


$app->get('/api/clientes', function (Request $request, Response $response) {

    $sql =  "SELECT * FROM clientes";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } else {
            echo json_encode("no existen clientes");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});



//GET CLIENT BY ID


$app->get('/api/clientes/cc={cc}', function (Request $request, Response $response) {

    $cc = $request->getAttribute('cc');

    $sql =  "SELECT * FROM clientes where cedula = '$cc' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $clientes = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($clientes);
        } else {
            echo json_encode("Cliente no esta registrado");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});


$app->get('/api/clientes/validar={cc}', function (Request $request, Response $response) {

    $cc = $request->getAttribute('cc');

    $sql =  "SELECT * FROM clientes where cedula = '$cc' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $clientes = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode("1");
        } else {
            echo json_encode("0");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});




//ADD CLIENT

$app->post('/api/clientes/addcliente', function (Request $request, Response $response) {

    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $cedula = $request->getParam('cedula');
    $pais = $request->getParam('pais');
    $ciudad = $request->getParam('ciudad');
    $direccion = $request->getParam('direccion');
    $telefono  = $request->getParam('telefono');
    $correo = $request->getParam('correo');

    $verificarexiste = "SELECT * FROM clientes where cedula = :cedula ";




    $sql =  "INSERT INTO clientes (cedula,nombre,apellido,pais,ciudad,direccion,telefono,correo	)
     VALUES (:cedula,:nombre,:apellido,:pais,:ciudad,:direccion,:telefono,:correo) ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellido', $apellido);
        $resultado->bindParam(':pais', $pais);
        $resultado->bindParam(':ciudad', $ciudad);
        $resultado->bindParam(':direccion', $direccion);
        $resultado->bindParam(':telefono', $telefono);
        $resultado->bindParam(':correo', $correo);



        $comprobarexiste=$query->prepare($verificarexiste);
        $comprobarexiste->bindParam(':cedula',$cedula);
        $comprobarexiste->execute();

        if($comprobarexiste->fetchColumn() == 0){
          if ($resultado->execute()) {
              echo json_encode("Gracias por registrarte");
          } else {
              echo json_encode("no fue agregado");
          }
        }else {
          echo json_encode("00");
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
