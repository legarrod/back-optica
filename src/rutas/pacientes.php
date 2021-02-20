<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET TRAE TODOS LOS PACIENTES
$app->get('/api/pacientes', function (Request $request, Response $response) {

    $sql1 =  "SELECT * FROM pacientes";

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

//GET CONSULTAR PACIENTE POR DOCUMENTO
$app->get('/api/pacientes/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');
    $sql =  "SELECT * FROM pacientes where cedula = '$id' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $paciente = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($paciente);
        } else {
            echo json_encode("No existen paciente con este ID");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

//POST CREAR UN NUEVO PACIENTE
//FORMATO PARA ENVIAR INFORMACION A LA BD
// Content-type: application/json
// {
//     "ciudad": 2,
//     "nombre": "Jose Maria",
//     "apellidos": "Cordoba Montes",
//     "fecha_nacimiento": "1991-02-11",
//     "celular": "3258965412",
//     "fecha_registro": "2021-01-10",
//     "direccion": "Barrio la arcadia, manzana 20 casa 10",
//     "ocupacion": "Maestro de obra",
//     "cedula": "13452",
//     "foto": ""
// }
$app->post('/api/pacientes/nuevo', function (Request $request, Response $response) {

    $ciudad = $request->getParam('ciudad');
    $nombre = $request->getParam('nombre');
    $apellidos = $request->getParam('apellidos');
    $fecha_nacimiento = $request->getParam('fecha_nacimiento');
    $celular = $request->getParam('celular');
    $fecha_registro = $request->getParam('fecha_registro');
    $direccion = $request->getParam('direccion');
    $ocupacion = $request->getParam('ocupacion');
    $cedula = $request->getParam('cedula');
    $foto = $request->getParam('foto');

     $sql = "INSERT INTO pacientes (id, ciudad, nombre, apellidos, fecha_nacimiento, celular, fecha_registro, direccion, ocupacion, cedula, foto) VALUES 
            (NULL,:ciudad,:nombre,:apellidos,:fecha_nacimiento,:celular,:fecha_registro,:direccion,:ocupacion,:cedula,:foto)";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':ciudad', $ciudad);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellidos', $apellidos);
        $resultado->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $resultado->bindParam(':celular', $celular);
        $resultado->bindParam(':fecha_registro', $fecha_registro);
        $resultado->bindParam(':direccion', $direccion);
        $resultado->bindParam(':ocupacion', $ocupacion);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':foto', $foto);
        

        if ($resultado->execute()) {
            echo json_encode("Paciente agregado correctamente");
        } else {
            echo json_encode("Hubo un error intenta de nuevo");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

//PUT PARA ACTUALIZAR UN REGISTRO
// Content-type: application/json
// {
//     "ciudad": 2,
//     "nombre": "Jose Maria",
//     "apellidos": "Cordoba Montes",
//     "fecha_nacimiento": "1991-02-11",
//     "celular": "3258965412",
//     "fecha_registro": "2021-01-10",
//     "direccion": "Ya no vive",
//     "ocupacion": "Maestro de obra",
//     "cedula": "13452",
//     "foto": ""
// }

$app->put('/api/pacientes/update/{cedula}', function (Request $request, Response $response) {

    $ciudad = $request->getParam('ciudad');
    $nombre = $request->getParam('nombre');
    $apellidos = $request->getParam('apellidos');
    $fecha_nacimiento = $request->getParam('fecha_nacimiento');
    $celular = $request->getParam('celular');
    $fecha_registro = $request->getParam('fecha_registro');
    $direccion = $request->getParam('direccion');
    $ocupacion = $request->getParam('ocupacion');
    $cedula = $request->getParam('cedula');
    $foto = $request->getParam('foto');

     $sql = "UPDATE pacientes SET 
        id = NULL,
        ciudad = :ciudad,
        nombre = :nombre,
        apellidos = :apellidos,
        fecha_nacimiento = :fecha_nacimiento,
        celular = :celular,
        fecha_registro = :fecha_registro,
        direccion = :direccion,
        ocupacion = :ocupacion,
        cedula = :cedula,
        foto = :foto WHERE cedula = :cedula";


    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':ciudad', $ciudad);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellidos', $apellidos);
        $resultado->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $resultado->bindParam(':celular', $celular);
        $resultado->bindParam(':fecha_registro', $fecha_registro);
        $resultado->bindParam(':direccion', $direccion);
        $resultado->bindParam(':ocupacion', $ocupacion);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':foto', $foto);

        if ($resultado->execute()) {
            echo json_encode("Paciente actualizado correctamente");
        } else {
            echo json_encode("Hubo un error en la actualizacion intenta de nuevo");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

$app->delete('/api/pacientes/delete/{cedula}', function (Request $request, Response $response) {

    $cedula = $request->getAttribute('cedula');



    $sql =  "DELETE FROM pacientes where cedula = :cedula";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':cedula', $cedula);

        if ($resultado->execute()) {
            echo json_encode("Paciente eliminado correctamente");
        } else {
            echo json_encode("Hubo un error paciente no eliminado intenta de nuevo");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

?>
