<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL PRODUCTS

$app->get('/api/productos', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "SELECT idproducto, nombre, descripcion, codigo FROM inventario";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);

});

$app->get('/api/productos-details', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "SELECT fk_id_producto AS codigo, inv.nombre, inv.descripcion, SUM(stock) AS cantidad
        FROM
          (SELECT fk_id_producto, SUM(cantidad) AS stock FROM detalle_entrada GROUP BY fk_id_producto
            UNION ALL
           SELECT fk_id_producto, -SUM(cantidad) AS stock FROM detalle_salida GROUP BY fk_id_producto
          ) as subquery
        INNER JOIN inventario AS inv ON inv.codigo = subquery.fk_id_producto
        GROUP BY fk_id_producto";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);

});

//
$app->get('/api/productos-last-created', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $sql =  "SELECT MAX(codigo) AS codigo FROM inventario";
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

//agregar entrada
$app->post('/api/productos/entrada', function (Request $request, Response $response) {
   
    return try_catch_wrapper(function() use ($request){
        $params = $request->getParams();
        function consultar($codigo){
            $sql =  "SELECT * FROM detalle_entrada where fk_id_producto = '$codigo'";
            $dbConexion = new DBConexion(new Conexion());
            $resultado = $dbConexion->executeQuery($sql);       
            return empty($resultado);
         }

        if (consultar($params['fk_id_producto'])) {
            $sql =  "INSERT INTO detalle_entrada (id_detalle,fk_id_entrada, fecha_entrada, fk_id_producto, cantidad, costo_producto) 
            VALUES (NULL, :fk_id_entrada, :fecha_entrada, :fk_id_producto, :cantidad, :costo_producto)";
             $dbConexion = new DBConexion(new Conexion());
             $resultado = $dbConexion->executePrepare($sql, $params);
        }else{
            $sql_update = "UPDATE detalle_entrada SET 
            fk_id_producto = :fk_id_producto,
            fk_id_entrada = :fk_id_entrada,
            fecha_entrada = :fecha_entrada,
            cantidad = :cantidad,
            costo_producto = :costo_producto WHERE fk_id_producto = :fk_id_producto";
             $dbConexion = new DBConexion(new Conexion());
             $resultado = $dbConexion->executePrepare($sql_update, $params);
        };
       
        return $resultado ?: [];
    }, $response);
});

//agregar salida
$app->post('/api/productos/salida', function (Request $request, Response $response) {
   
    return try_catch_wrapper(function() use ($request){
        $params = $request->getParams();
        foreach ($params as $key => $value) {
            $sql =  "INSERT INTO detalle_salida (id_salida, fk_id_producto, cantidad, fecha_salida) 
            VALUES (NULL, :fk_id_producto, :cantidad, :fecha_salida)";
             $dbConexion = new DBConexion(new Conexion());
             $resultado = $dbConexion->executePrepare($sql, $value);    
          } 
        return $resultado ?: [];
    }, $response);
});

//ADD PRODUCT

$app->post('/api/productos/addProduct', function (Request $request, Response $response) {

    return try_catch_wrapper(function() use ($request){
        //throw new Exception('malo');
        $params = $request->getParams(); 
        function crearEntrada($params){
            $fk_id_entrada = 1;
            $fecha_entrada = '0000-00-00';
            $fk_id_producto = $params['codigo'];
            $cantidad = 0;
            $costo_producto = 0;
            $sql =  "INSERT INTO detalle_entrada (fk_id_entrada, fecha_entrada, fk_id_producto, cantidad, costo_producto) 
            VALUES (:fk_id_entrada, :fecha_entrada, :fk_id_producto, :cantidad, :costo_producto)";
             $dbConexion = new DBConexion(new Conexion());
            $cnx = new Conexion();
            $query = $cnx->Conectar();
            $resultado = $query->prepare($sql);
            $resultado->bindParam(':fk_id_entrada', $fk_id_entrada);
            $resultado->bindParam(':fecha_entrada',   $fecha_entrada);
            $resultado->bindParam(':fk_id_producto',$fk_id_producto);
            $resultado->bindParam(':cantidad',      $cantidad);
            $resultado->bindParam(':costo_producto',$costo_producto);
            $resultado->execute();
            return empty($resultado);
         }
  
        $sql =  "INSERT INTO inventario (idproducto, nombre, descripcion, codigo) 
        VALUES (NULL, :nombre, :descripcion, :codigo) ";
        $dbConexion = new DBConexion(new Conexion());
       
        $resultado = $dbConexion->executePrepare($sql, $params);
       if ($resultado) {
        crearEntrada($params);
       }
          
        
        return $resultado ?: [];
    }, $response);
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
