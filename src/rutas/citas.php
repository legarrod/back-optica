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

//obtener citas por estado
$app->get('/api/citasestado/{id_estado}', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $id_estado = $request->getAttribute('id_estado');
        $sql =  "SELECT citas_pacientes.id_cita_paciente, citas_pacientes.fecha_creacion, citas_pacientes.fecha_cita, citas_pacientes.hora, pacientes.nombre, pacientes.apellidos FROM citas_pacientes
        INNER JOIN pacientes
        ON citas_pacientes.fk_id_paciente = pacientes.id
        where fk_id_estado = $id_estado
        ORDER BY  citas_pacientes.fecha_cita, citas_pacientes.hora ASC";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
});

//Obtener citas por fecha
$app->get('/api/citasporfecha/{fecha}', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $fecha = $request->getAttribute('fecha');
        $sql =  "SELECT citas_pacientes.id_cita_paciente, citas_pacientes.fecha_creacion, citas_pacientes.fecha_cita, citas_pacientes.hora, pacientes.nombre, pacientes.apellidos FROM citas_pacientes
        INNER JOIN pacientes
        ON citas_pacientes.fk_id_paciente = pacientes.id
        where fecha_cita = '{$fecha}'
        ORDER BY citas_pacientes.hora ASC";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
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

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $id_paciente = $request->getAttribute('id_paciente');
        $sql =  "SELECT * FROM citas_pacientes
        INNER JOIN pacientes
        ON citas_pacientes.fk_id_paciente = pacientes.id
        where id_cita_paciente = $id_paciente";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
});

//Obtener informacion de citas por paciente
$app->get('/api/citaporcedula/{cc}', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $cc = $request->getAttribute('cc');
        $sql =  "SELECT * FROM citas_pacientes
        INNER JOIN pacientes
        ON citas_pacientes.fk_id_paciente = pacientes.id
        where cedula = $cc";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
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


  return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "INSERT INTO citas_pacientes (id_cita_paciente, 
                                                    fk_id_paciente, 
                                                    nombre_doctor, 
                                                    fk_id_sede, 
                                                    fk_id_estado, 
                                                    fecha_creacion, 
                                                    fecha_cita, 
                                                    hora, 
                                                    actual_av_derecho, 
                                                    actual_av_izquierdo, 
                                                    actual_cilindro_derecho, 
                                                    actual_cilindro_izquierdo, 
                                                    actual_eje_derecho, 
                                                    actual_eje_izquierdo, 
                                                    actual_esferico_derecho, 
                                                    actual_esferico_izquierdo, 
                                                    cerca_av_derecho, 
                                                    cerca_av_izquierdo, 
                                                    cerca_cilindro_derecho, 
                                                    cerca_cilindro_izquierdo, 
                                                    cerca_eje_derecho, 
                                                    cerca_eje_izquierdo, 
                                                    cerca_esferico_derecho, 
                                                    cerca_esferico_izquierdo, 
                                                    lejos_av_derecho, 
                                                    lejos_av_izquierdo, 
                                                    lejos_cilindro_derecho, 
                                                    lejos_cilindro_izquierdo, 
                                                    lejos_eje_derecho, 
                                                    lejos_eje_izquierdo, 
                                                    lejos_esferico_derecho, 
                                                    lejos_esferico_izquierdo, 
                                                    valor_cita, 
                                                    observaciones) VALUES 
                                                    (NULL,
                                                    :fk_id_paciente,
                                                    :nombre_doctor,
                                                    :fk_id_sede,
                                                    :fk_id_estado,
                                                    :fecha_creacion,
                                                    :fecha_cita, 
                                                    :hora, 
                                                    :actual_av_derecho,
                                                    :actual_av_izquierdo,
                                                    :actual_cilindro_derecho,
                                                    :actual_cilindro_izquierdo,
                                                    :actual_eje_derecho,
                                                    :actual_eje_izquierdo,
                                                    :actual_esferico_derecho,
                                                    :actual_esferico_izquierdo,
                                                    :cerca_av_derecho,
                                                    :cerca_av_izquierdo,
                                                    :cerca_cilindro_derecho,
                                                    :cerca_cilindro_izquierdo,
                                                    :cerca_eje_derecho,
                                                    :cerca_eje_izquierdo,
                                                    :cerca_esferico_derecho,
                                                    :cerca_esferico_izquierdo,
                                                    :lejos_av_derecho,
                                                    :lejos_av_izquierdo,
                                                    :lejos_cilindro_derecho,
                                                    :lejos_cilindro_izquierdo, 
                                                    :lejos_eje_derecho, 
                                                    :lejos_eje_izquierdo, 
                                                    :lejos_esferico_derecho, 
                                                    :lejos_esferico_izquierdo, 
                                                    :valor_cita,
                                                    :observaciones)";
        $dbConexion = new DBConexion(new Conexion());
       $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);




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

$app->put('/api/citas/update/', function (Request $request, Response $response) {

   return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "UPDATE `citas_pacientes` SET 
        `fk_id_paciente`            = :fk_id_paciente,
        `nombre_doctor`             = :nombre_doctor,
        `fk_id_sede`                = :fk_id_sede,
        `fk_id_estado`              = :fk_id_estado,
        `fecha_creacion`            = :fecha_creacion,
        `fecha_cita`                = :fecha_cita,
        `actual_av_derecho`         = :actual_av_derecho,
        `actual_av_izquierdo`       = :actual_av_izquierdo,
        `actual_cilindro_derecho`   = :actual_cilindro_derecho,
        `actual_cilindro_izquierdo` = :actual_cilindro_izquierdo,
        `actual_eje_derecho`        = :actual_eje_derecho,
        `actual_eje_izquierdo`      = :actual_eje_izquierdo,
        `actual_esferico_derecho`   = :actual_esferico_derecho,
        `actual_esferico_izquierdo` = :actual_esferico_izquierdo,
        `cerca_av_derecho`          = :cerca_av_derecho,
        `cerca_av_izquierdo`        = :cerca_av_izquierdo,
        `cerca_cilindro_derecho`    = :cerca_cilindro_derecho,
        `cerca_cilindro_izquierdo`  = :cerca_cilindro_izquierdo,
        `cerca_eje_derecho:`        = :cerca_eje_derecho,
        `cerca_eje_izquierdo`       = :cerca_eje_izquierdo,
        `cerca_esferico_derecho`    = :cerca_esferico_derecho,
        `cerca_esferico_izquierdo`  = :cerca_esferico_izquierdo,
        `lejos_av_derecho`          = :lejos_av_derecho,
        `lejos_av_izquierdo`        = :lejos_av_izquierdo,
        `lejos_cilindro_derecho`    = :lejos_cilindro_derecho,
        `lejos_cilindro_izquierdo`  = :lejos_cilindro_izquierdo,
        `lejos_eje_derecho`         = :lejos_eje_derecho,
        `lejos_eje_izquierdo`       = :lejos_eje_izquierdo,
        `lejos_esferico_derecho`    = :lejos_esferico_derecho,
        `lejos_esferico_izquierdo`  = :lejos_esferico_izquierdo,
        `valor_cita`                = :valor_cita,
        `observaciones`             = :observaciones
        WHERE id_cita_paciente      = :id_cita_paciente";
        $dbConexion = new DBConexion(new Conexion());
       $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);
});

?>
