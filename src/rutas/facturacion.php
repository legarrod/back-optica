<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Obtenemos todas las facturas
$app->get('/api/facturas', function (Request $request, Response $response) {

   return try_catch_wrapper(function(){
        //throw new Exception('malo');
        $sql =  "SELECT * FROM con_facturas";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
});

//Obetener abonos por factura
$app->get('/api/abonosfactura/{id}', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        $id = $request->getAttribute('id');
        //throw new Exception('malo');
        $sql =  "SELECT fk_id_factura AS 'numero_factura', valor_abono AS 'valor', fecha_abono AS 'fecha', usuario_registro_abono AS 'usuario', nota_abono AS 'nota' FROM abonos WHERE fk_id_factura = $id";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//Obetener informacion por codigo de factura
$app->get('/api/facturacodigo/{id}', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        $id = $request->getAttribute('id');
        //throw new Exception('malo');
        $sql =  "SELECT id_factura AS id FROM con_facturas WHERE numero_factura = '{$id}'";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//Obtener informacion por numero de cedula

$app->get('/api/facturacedula/{cc}', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        $cc = $request->getAttribute('cc');
        //throw new Exception('malo');
        $sql =  "SELECT fac.id_factura AS id, fac.numero_factura, CONCAT(pac.nombre) AS paciente, fac.estado_factura AS estado, pac.direccion, pac.celular, fac.valor_factura AS valor_total, pac.cedula AS cedula, fac.observaciones AS observacion, fac.fechasalida AS fecha_creacion
        FROM con_facturas AS fac
        INNER JOIN pacientes AS pac ON pac.id = fac.cc_usuario
        WHERE pac.cedula = '{$cc}'";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//Obtener detalles por factura

$app->get('/api/detallefactura/{id}', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        $id = $request->getAttribute('id');
        //throw new Exception('malo');
        $sql =  "SELECT inv.nombre, det.cantidad, det.valor_producto
        FROM con_detalle_factura AS det
        LEFT JOIN inventario AS inv ON inv.idproducto = det.id_producto
        WHERE det.id_factura = '{$id}'";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//Obtener informacion para exportar a excel
$app->get('/api/exportar', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "SELECT pac.cedula AS Cedula, CONCAT(pac.nombre, ' ', pac.apellidos) AS 'Nombre y Apellidos', pac.fecha_nacimiento AS 'Fecha Nacimiento',
            pac.celular AS 'Telefono', pac.direccion AS 'Direccion', pac.ocupacion AS 'Ocupacion', fac.numero_factura AS Factura, fac.fechasalida AS 'Fecha Creacion',
            fac.valor_factura AS 'Valor', fac.observaciones AS Observaciones, abo.valor_abono AS Abonos, abo.fecha_abono AS 'Fecha Abono', abo.nota_abono AS Nota,
            inv.nombre AS producto
        FROM pacientes AS pac
        LEFT JOIN con_facturas AS fac ON pac.id = fac.cc_usuario
        LEFT JOIN abonos AS abo ON  fac.id_factura = abo.fk_id_factura
        LEFT JOIN con_detalle_factura AS det ON fac.id_factura = det.id_factura
        LEFT JOIN inventario AS inv ON det.id_producto = inv.idproducto
        ";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//OBTENER FACTURA, ABONOS Y DETALLE POR NUMERO DE FACTURA

$app->get('/api/facturacompleta', function (Request $request, Response $response) {
    return try_catch_wrapper(function() {
        
        //throw new Exception('malo');
        $sql = "SELECT fac.id_factura AS id, pac.cedula AS cedula, fac.numero_factura, fac.estado_factura AS estado, pac.nombre AS paciente,
        fac.estado_factura AS estado, fac.valor_factura, (SELECT SUM(valor_abono) FROM abonos WHERE fac.id_factura = fk_id_factura) AS total_abonos,
        fac.valor_factura - (SELECT SUM(valor_abono) FROM abonos WHERE fac.id_factura = fk_id_factura) AS total_deuda, fac.observaciones AS nota
        FROM con_facturas AS fac
        LEFT JOIN pacientes AS pac ON pac.id = fac.cc_usuario 
        ORDER BY fac.id_factura DESC";
        
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});


//con este endpoint vamos a crear las facturas 

$app->post('/api/facturas/crearfactura', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "INSERT INTO con_facturas (fechasalida, valor_factura, cc_usuario, numero_factura, estado_factura, observaciones) 
                VALUES (:fechasalida, :valor_factura, :cc_usuario, :numero_factura, :estado_factura, :observaciones)";
        $dbConexion = new DBConexion(new Conexion());
       $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);
});

//Con este endpoint vamos acrear los abonos para cada factura
$app->post('/api/facturas/crearabono', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "INSERT INTO abonos (fk_id_factura, valor_abono, fecha_abono, usuario_registro_abono, nota_abono) 
                VALUES (:fk_id_factura, :valor_abono, :fecha_abono, :usuario_registro_abono, :nota_abono)";
        $dbConexion = new DBConexion(new Conexion());
       $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);
});

//Con este enpoint vamos a crear el detalle de la factura
$app->post('/api/facturas/creardetallefactura', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "INSERT INTO con_detalle_factura (id_factura, id_producto, cantidad, valor_producto) 
                VALUES (:id_factura, :id_producto, :cantidad, :valor_producto)";
        $dbConexion = new DBConexion(new Conexion());
        $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);
});

$app->put('/api/facturas/actualizarestado/', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();
    
   
 
    
    
    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        
       
        $sql =  "UPDATE con_facturas SET estado_factura = :estado WHERE id_factura = :id_factura";
        $dbConexion = new DBConexion(new Conexion());
        $params = $request->getParams(); 
       
        $resultado = $dbConexion->executePrepare($sql, $params);
        return $resultado ?: [];
    }, $response);
});



?>