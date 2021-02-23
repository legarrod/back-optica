<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET obtener todas las citas del sistema
//http://localhost/back-optica/public/citas/api/citas
$app->get('/api/citas', function (Request $request, Response $response) {

    $sql1 =  "SELECT * FROM citas_pacientes
    INNER JOIN pacientes
    ON citas_pacientes.fk_id_paciente = pacientes.id";

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

//Obtener resumen de citas para cards de citas inicial 
$app->get('/api/citas_cards', function (Request $request, Response $response) {

    $sql1 =  "SELECT citas_pacientes.id_cita_paciente, citas_pacientes.fecha_creacion, citas_pacientes.fecha_cita, pacientes.nombre, pacientes.apellidos FROM citas_pacientes
    INNER JOIN pacientes
    ON citas_pacientes.fk_id_paciente = pacientes.id";

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

//Obtener informacion de una cita por la codigo del paciente
$app->get('/api/citaporpaciente/{id_paciente}', function (Request $request, Response $response) {

    $id_paciente = $request->getAttribute('id_paciente');
    $sql1 =  "SELECT * FROM citas_pacientes
    INNER JOIN pacientes
    ON citas_pacientes.fk_id_paciente = pacientes.id
    where id_cita_paciente = $id_paciente";

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

//GET obtener todas las citas por fecha
$app->get('/api/citas/{fecha}', function (Request $request, Response $response) {

    $fecha_cita = $request->getAttribute('fecha_cita');
    $sql =  "SELECT * FROM citas_pacientes where fecha_cita = '$fecha_cita' ";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        if ($resultado->rowCount() > 0) {
            $citas_pacientes = $resultado->fetch(PDO::FETCH_ASSOC);
            echo json_encode($citas_pacientes);
        } else {
            echo json_encode("No existen citas en esta fecha");
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
//http://localhost/back-optica/public/citas/api/citas/crear_cita
// {
//     "fk_id_paciente": 1,
//     "nombre_doctor": "Carlos",
//     "fk_id_sede": 1,
//     "fk_id_estado": "",
//     "fecha_creacion": "1991-02-11",
//     "fecha_cita": "",
//     "anamnesis": "",
//     "biomicrodcopia": "",
//     "od_rx_uso": "",
//     "oi_rx_uso": "",
//     "oi_ap": "",
//     "oi_af": "",
//     "od_ap": "",
//     "od_af": "",
//     "od_avvlsc": "",
//     "od_avvpsc": "",
//     "od_avccvt": "",
//     "od_avccvp": "",
//     "od_refraccion": "",
//     "od_rx_final": "",
//     "oi_avvlsc": "",
//     "oi_avvpsc": "",
//     "oi_avccvt": "",
//     "oi_avccvp": "",
//     "oi_refraccion": "",
//     "oi_rx_final": "",
//     "valor_cita": "",
//     "observaciones": ""
// }
$app->post('/api/citas/crear_cita', function (Request $request, Response $response) {

    $fk_id_paciente = $request->getParam('fk_id_paciente');
    $nombre_doctor = $request->getParam('nombre_doctor');
    $fk_id_sede = $request->getParam('fk_id_sede');//no debe estar nulo
    $fk_id_estado = $request->getParam('fk_id_estado');
    $fecha_creacion = $request->getParam('fecha_creacion');
    $fecha_cita = $request->getParam('fecha_cita');
    $anamnesis = $request->getParam('anamnesis');
    $biomicrodcopia = $request->getParam('biomicrodcopia');
    $od_rx_uso = $request->getParam('od_rx_uso');
    $oi_rx_uso = $request->getParam('oi_rx_uso');
    $oi_ap = $request->getParam('oi_ap');
    $oi_af = $request->getParam('oi_af');
    $od_ap = $request->getParam('od_ap');
    $od_af = $request->getParam('od_af');
    $od_avvlsc = $request->getParam('od_avvlsc');
    $od_avvpsc = $request->getParam('od_avvpsc');
    $od_avccvt = $request->getParam('od_avccvt');
    $od_avccvp = $request->getParam('od_avccvp');
    $od_refraccion = $request->getParam('od_refraccion');
    $od_rx_final = $request->getParam('od_rx_final');
    $oi_avvlsc = $request->getParam('oi_avvlsc');
    $oi_avvpsc = $request->getParam('oi_avvpsc');
    $oi_avccvt = $request->getParam('oi_avccvt');
    $oi_avccvp = $request->getParam('oi_avccvp');
    $oi_refraccion = $request->getParam('oi_refraccion');
    $oi_rx_final = $request->getParam('oi_rx_final');
    $valor_cita = $request->getParam('valor_cita');
    $observaciones = $request->getParam('observaciones');

     $sql = "INSERT INTO citas_pacientes (id_cita_paciente, fk_id_paciente, nombre_doctor, fk_id_sede, fk_id_estado, fecha_creacion, fecha_cita, anamnesis, biomicrodcopia, od_rx_uso, oi_rx_uso, oi_ap, oi_af, od_ap, od_af, od_avvlsc, od_avvpsc, od_avccvt, od_avccvp, od_refraccion, od_rx_final, oi_avvlsc, oi_avvpsc, oi_avccvt, oi_avccvp, oi_refraccion, oi_rx_final, valor_cita, observaciones) VALUES 
            (NULL,:fk_id_paciente,:nombre_doctor,:fk_id_sede,:fk_id_estado,:fecha_creacion,:fecha_cita,:anamnesis,:biomicrodcopia,:od_rx_uso,:oi_rx_uso,:oi_ap,:oi_af,:od_ap,:od_af,:od_avvlsc,:od_avvpsc,:od_avccvt,:od_avccvp,:od_refraccion,:od_rx_final,:oi_avvlsc,:oi_avvpsc,:oi_avccvt,:oi_avccvp,:oi_refraccion,:oi_rx_final,:valor_cita,:observaciones)";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':fk_id_paciente', $fk_id_paciente);
        $resultado->bindParam(':nombre_doctor', $nombre_doctor);
        $resultado->bindParam(':fk_id_sede', $fk_id_sede);
        $resultado->bindParam(':fk_id_estado', $fk_id_estado);
        $resultado->bindParam(':fecha_creacion', $fecha_creacion);
        $resultado->bindParam(':fecha_cita', $fecha_cita);
        $resultado->bindParam(':anamnesis', $anamnesis);
        $resultado->bindParam(':biomicrodcopia', $biomicrodcopia);
        $resultado->bindParam(':od_rx_uso', $od_rx_uso);
        $resultado->bindParam(':oi_rx_uso', $oi_rx_uso);
        $resultado->bindParam(':oi_ap', $oi_ap);
        $resultado->bindParam(':oi_af', $oi_af);
        $resultado->bindParam(':od_ap', $od_ap);
        $resultado->bindParam(':od_af', $od_af);
        $resultado->bindParam(':od_avvlsc', $od_avvlsc);
        $resultado->bindParam(':od_avvpsc', $od_avvpsc);
        $resultado->bindParam(':od_avccvt', $od_avccvt);
        $resultado->bindParam(':od_avccvp', $od_avccvp);
        $resultado->bindParam(':od_refraccion', $od_refraccion);
        $resultado->bindParam(':od_rx_final', $od_rx_final);
        $resultado->bindParam(':oi_avvlsc', $oi_avvlsc);
        $resultado->bindParam(':oi_avvpsc', $oi_avvpsc);
        $resultado->bindParam(':oi_avccvt', $oi_avccvt);
        $resultado->bindParam(':oi_avccvp', $oi_avccvp);
        $resultado->bindParam(':oi_refraccion', $oi_refraccion);
        $resultado->bindParam(':oi_rx_final', $oi_rx_final);
        $resultado->bindParam(':valor_cita', $valor_cita);
        $resultado->bindParam(':observaciones', $observaciones);
        

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

$app->put('/api/citas/update/{id_cita_paciente}', function (Request $request, Response $response) {

    $id_cita_paciente = $request->getAttribute('id_cita_paciente');
    $fk_id_paciente = $request->getParam('fk_id_paciente');
    $nombre_doctor = $request->getParam('nombre_doctor');
    $fk_id_sede = $request->getParam('fk_id_sede');//no debe estar nulo
    $fk_id_estado = $request->getParam('fk_id_estado');
    $fecha_creacion = $request->getParam('fecha_creacion');
    $fecha_cita = $request->getParam('fecha_cita');
    $anamnesis = $request->getParam('anamnesis');
    $biomicrodcopia = $request->getParam('biomicrodcopia');
    $od_rx_uso = $request->getParam('od_rx_uso');
    $oi_rx_uso = $request->getParam('oi_rx_uso');
    $oi_ap = $request->getParam('oi_ap');
    $oi_af = $request->getParam('oi_af');
    $od_ap = $request->getParam('od_ap');
    $od_af = $request->getParam('od_af');
    $od_avvlsc = $request->getParam('od_avvlsc');
    $od_avvpsc = $request->getParam('od_avvpsc');
    $od_avccvt = $request->getParam('od_avccvt');
    $od_avccvp = $request->getParam('od_avccvp');
    $od_refraccion = $request->getParam('od_refraccion');
    $od_rx_final = $request->getParam('od_rx_final');
    $oi_avvlsc = $request->getParam('oi_avvlsc');
    $oi_avvpsc = $request->getParam('oi_avvpsc');
    $oi_avccvt = $request->getParam('oi_avccvt');
    $oi_avccvp = $request->getParam('oi_avccvp');
    $oi_refraccion = $request->getParam('oi_refraccion');
    $oi_rx_final = $request->getParam('oi_rx_final');
    $valor_cita = $request->getParam('valor_cita');
    $observaciones = $request->getParam('observaciones');

     $sql = "UPDATE citas_pacientes SET 
        id_cita_paciente = :id_cita_paciente,
        fk_id_paciente = :fk_id_paciente,
        nombre_doctor = :nombre_doctor,
        fk_id_sede = :fk_id_sede,
        fk_id_estado = :fk_id_estado,
        fecha_creacion = :fecha_creacion,
        fecha_cita = :fecha_cita,
        anamnesis = :anamnesis,
        biomicrodcopia = :biomicrodcopia,
        od_rx_uso = :od_rx_uso,
        oi_rx_uso = :oi_rx_uso,
        oi_ap = :oi_ap,
        oi_af = :oi_af,
        od_ap = :od_ap,
        od_af = :od_af,
        od_avvlsc = :od_avvlsc,
        od_avvpsc = :od_avvpsc,
        od_avccvt = :od_avccvt,
        od_avccvp = :od_avccvp,
        od_refraccion = :od_refraccion,
        od_rx_final = :od_rx_final,
        oi_avvlsc = :oi_avvlsc,
        oi_avvpsc = :oi_avvpsc,
        oi_avccvt = :oi_avccvt,
        oi_avccvp = :oi_avccvp,
        oi_refraccion = :oi_refraccion,
        oi_rx_final = :oi_rx_final,
        valor_cita = :valor_cita,
        observaciones = :observaciones WHERE id_cita_paciente = $id_cita_paciente";


    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);

        $resultado->bindParam(':fk_id_paciente', $fk_id_paciente);
        $resultado->bindParam(':nombre_doctor', $nombre_doctor);
        $resultado->bindParam(':fk_id_sede', $fk_id_sede);
        $resultado->bindParam(':fk_id_estado', $fk_id_estado);
        $resultado->bindParam(':fecha_creacion', $fecha_creacion);
        $resultado->bindParam(':fecha_cita', $fecha_cita);
        $resultado->bindParam(':anamnesis', $anamnesis);
        $resultado->bindParam(':biomicrodcopia', $biomicrodcopia);
        $resultado->bindParam(':od_rx_uso', $od_rx_uso);
        $resultado->bindParam(':oi_rx_uso', $oi_rx_uso);
        $resultado->bindParam(':oi_ap', $oi_ap);
        $resultado->bindParam(':oi_af', $oi_af);
        $resultado->bindParam(':od_ap', $od_ap);
        $resultado->bindParam(':od_af', $od_af);
        $resultado->bindParam(':od_avvlsc', $od_avvlsc);
        $resultado->bindParam(':od_avvpsc', $od_avvpsc);
        $resultado->bindParam(':od_avccvt', $od_avccvt);
        $resultado->bindParam(':od_avccvp', $od_avccvp);
        $resultado->bindParam(':od_refraccion', $od_refraccion);
        $resultado->bindParam(':od_rx_final', $od_rx_final);
        $resultado->bindParam(':oi_avvlsc', $oi_avvlsc);
        $resultado->bindParam(':oi_avvpsc', $oi_avvpsc);
        $resultado->bindParam(':oi_avccvt', $oi_avccvt);
        $resultado->bindParam(':oi_avccvp', $oi_avccvp);
        $resultado->bindParam(':oi_refraccion', $oi_refraccion);
        $resultado->bindParam(':oi_rx_final', $oi_rx_final);
        $resultado->bindParam(':valor_cita', $valor_cita);
        $resultado->bindParam(':observaciones', $observaciones);

        if ($resultado->execute()) {
            echo json_encode("Cita actualizado correctamente");
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
