<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;



$app->get('/api/proveedores', function (Request $request, Response $response) {

    $sql =  "SELECT * FROM proveedores";

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


  $app->post('/api/proveedores/addproveedor', function (Request $request, Response $response) {

    $nit = $request->getParam('nit');
    $nombre = $request->getParam('nombre');
    $razon_social = $request->getParam('razon_social');
    $pais = $request->getParam('pais');
    $ciudad = $request->getParam('ciudad');
    $direccion = $request->getParam('direccion');
    $telefono = $request->getParam('telefono');
    $correo = $request->getParam('correo');
    $clave = $request->getParam('clave');
    $verificarexiste = "SELECT COUNT(*) as cuantos from proveedores where nit  = :nit ";


    $sql =  "INSERT INTO proveedores (nit,nombre,razon_social,pais,ciudad,direccion,telefono,correo,clave)
     VALUES (:nit,:nombre,:razon_social,:pais,:ciudad,:direccion,:telefono,:correo,:clave) ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':nit', $nit);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':razon_social', $razon_social);
        $resultado->bindParam(':pais', $pais);
        $resultado->bindParam(':ciudad', $ciudad);
        $resultado->bindParam(':direccion', $direccion);
        $resultado->bindParam(':telefono', $telefono);
        $resultado->bindParam(':correo',$correo);
        $resultado->bindParam(':clave',$clave);
        $comprobarexiste=$query->prepare($verificarexiste);
        $comprobarexiste->bindParam(':nit',$nit);
        $comprobarexiste->execute();

        if($comprobarexiste->fetchColumn() == 0){
          if ($resultado->execute()) {
              echo json_encode("Gracias por registrarte como proveedor");
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






// //GET CLIENT BY ID
//
//
// $app->get('/api/clientes/cc={cc}', function (Request $request, Response $response) {
//
//     $idprod = $request->getAttribute('cc');
//
//     $sql =  "SELECT * FROM clientes where cedula = '$idprod' ";
//
//     try {
//
//         $cnx = new Conexion();
//         $query = $cnx->Conectar();
//         $resultado = $query->query($sql);
//         if ($resultado->rowCount() > 0) {
//             $productos = $resultado->fetch(PDO::FETCH_ASSOC);
//             echo json_encode($productos);
//         } else {
//             echo json_encode("no existen clientes con esta cedula");
//         }
//     } catch (PDOException $error) {
//
//         $errores =  array(
//             "text" => $error->getMessage()
//         );
//
//         return json_encode($errores);
//     }
// });
//
//
//
//
// //ADD CLIENT
//
// $app->post('/api/clientes/addcliente', function (Request $request, Response $response) {
//
//     $nombre = $request->getParam('nombre');
//     $apellido = $request->getParam('apellido');
//     $cedula = $request->getParam('cedula');
//     $tipo_persona = $request->getParam('tipo_persona');
//     $correo = $request->getParam('correo');
//     $pw = $request->getParam('password');
//
//
//
//     $sql =  "INSERT INTO clientes (cedula,nombre,apellido,tipo_persona,correo,password)
//      VALUES (:cedula,:nombre,:apellido,:tipo_persona,:correo,:password) ";
//
//     try {
//
//         $cnx = new Conexion();
//         $query = $cnx->Conectar();
//         $resultado = $query->prepare($sql);
//         $resultado->bindParam(':cedula', $cedula);
//         $resultado->bindParam(':nombre', $nombre);
//         $resultado->bindParam(':apellido', $apellido);
//         $resultado->bindParam(':tipo_persona', $tipo_persona);
//         $resultado->bindParam(':correo', $correo);
//         $resultado->bindParam(':password', $pw);
//
//         if ($resultado->execute()) {
//             echo json_encode("agregado correctamente");
//         } else {
//             echo json_encode("no fue agregado");
//         }
//     } catch (PDOException $error) {
//
//         $errores =  array(
//             "text" => $error->getMessage()
//         );
//
//         return json_encode($errores);
//     }
// });
//
//
// //UPDATE PRODUCT
//
// $app->put('/api/productos/update', function (Request $request, Response $response) {
//
//     $id = $request->getParam('id');
//     $nombre = $request->getParam('nombre');
//     $descripcion = $request->getParam('descripcion');
//     $imagen = $request->getParam('imagen');
//     $stock = $request->getParam('stock');
//
//
//
//     $sql =  "UPDATE productos SET
//     nombre= :nombre,
//     descripcion = :descripcion,
//     imagen =  :imagen,
//     stock = :stock  WHERE id = :id ";
//
//     try {
//
//         $cnx = new Conexion();
//         $query = $cnx->Conectar();
//         $resultado = $query->prepare($sql);
//         $resultado->bindParam(':nombre', $nombre);
//         $resultado->bindParam(':descripcion', $descripcion);
//         $resultado->bindParam(':imagen', $imagen);
//         $resultado->bindParam(':stock', $stock);
//         $resultado->bindParam(':id', $id);
//
//         if ($resultado->execute()) {
//             echo json_encode("actualizado correctamente");
//         } else {
//             echo json_encode("no fue actualizado");
//         }
//     } catch (PDOException $error) {
//
//         $errores =  array(
//             "text" => $error->getMessage()
//         );
//
//         return json_encode($errores);
//     }
// });
//
//
//
// //DELETE PRODUCT
//
//
// $app->delete('/api/productos/delete/id={id}', function (Request $request, Response $response) {
//
//     $id = $request->getAttribute('id');
//
//
//
//     $sql =  "DELETE FROM productos where id = :id";
//
//     try {
//
//         $cnx = new Conexion();
//         $query = $cnx->Conectar();
//         $resultado = $query->prepare($sql);
//         $resultado->bindParam(':id', $id);
//
//         if ($resultado->execute()) {
//             echo json_encode("eliminado correctamente");
//         } else {
//             echo json_encode("no fue eliminado");
//         }
//     } catch (PDOException $error) {
//
//         $errores =  array(
//             "text" => $error->getMessage()
//         );
//
//         return json_encode($errores);
//     }
// });
//

?>
