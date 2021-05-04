<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Obtenemos todos los usuarios
$app->get('/api/usuarios', function (Request $request, Response $response) {

       return try_catch_wrapper(function(){
        //throw new Exception('malo');
        $sql =  "SELECT * FROM usuarios";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
    
});

//Obetener usuario por cc
$app->get('/api/usuario/{cedula}', function (Request $request, Response $response) {
    
    return try_catch_wrapper(function() use ($request){
        $cedula = $request->getAttribute('cedula');
        //throw new Exception('malo');
        $sql =  "SELECT nombre, apellidos, fecha_nacimiento, celular, cedula FROM usuarios WHERE cedula = $cedula";
        $dbConexion = new DBConexion(new Conexion());
        $resultado = $dbConexion->executeQuery($sql);
        return $resultado ?: [];
    }, $response);
});

//con este endpoint vamos a crear nuevos usuarios

$app->post('/api/crearusuario', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();

//     $has = password_hash('hsgd23', PASSWORD_DEFAULT, ['cost' => 5]);
//     echo $has . '<br/>';
//    if (password_verify('hsgd23', $has)){
//        echo 'Todo salio bien';
//    };

    $usuario            = $request->getParam('usuario');
    //$contrasena         = password_hash($request->getParam('contrasena'), PASSWORD_DEFAULT, ['cost' => 5]);
    $contrasena         = $request->getParam('contrasena');
    $ciudad             = $request->getParam('ciudad');
    $nombre             = $request->getParam('nombre');
    $apellidos          = $request->getParam('apellidos');
    $fecha_nacimiento   = $request->getParam('fecha_nacimiento');
    $celular            = $request->getParam('celular');
    $fecha_registro     = $request->getParam('fecha_registro');
    $direccion          = $request->getParam('direccion');
    $ocupacion          = $request->getParam('ocupacion');
    $cedula             = $request->getParam('cedula');
    $verificarexiste    = "SELECT COUNT(*) as cuantos from usuarios where cedula  = :cedula ";

    $sql =  "INSERT INTO usuarios (usuario, contrasena, ciudad, nombre, apellidos, fecha_nacimiento, celular, fecha_registro, direccion, ocupacion, cedula) 
                VALUES (:usuario, :contrasena, :ciudad, :nombre, :apellidos, :fecha_nacimiento, :celular, :fecha_registro, :direccion, :ocupacion, :cedula)";

    try {

        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindParam(':contrasena', $contrasena);
        $resultado->bindParam(':ciudad', $ciudad);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellidos', $apellidos);
        $resultado->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $resultado->bindParam(':celular', $celular);
        $resultado->bindParam(':fecha_registro',$fecha_registro);
        $resultado->bindParam(':direccion',$direccion);
        $resultado->bindParam(':ocupacion',$ocupacion);
        $resultado->bindParam(':cedula',$cedula);
        $comprobarexiste=$query->prepare($verificarexiste);
        $comprobarexiste->bindParam(':cedula',$cedula);
        $comprobarexiste->execute();

        if($comprobarexiste->fetchColumn() == 0){
          if ($resultado->execute()) {
              echo json_encode("Usuario registrado");
          } else {
              echo json_encode("Usuario no sepudo agregar");
          }
        }else {
          echo json_encode("Usuario ya existe");
        }

    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );

        return json_encode($errores);
    }
});

//Con este endpoint vamos consultar usuario para login
$app->post('/api/usuariologin', function (Request $request, Response $response) {
   
    //print_r($request->getParams()); die();
    // DELETE FROM `con_facturas` WHERE `con_facturas`.`id_factura` = 220
    
    $usuario  = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');
  
    $verificarexiste = "SELECT COUNT(*) as cuantos, usuario as User from usuarios where usuario  = :usuario AND contrasena = :contrasena ";
   

    try {
  
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($verificarexiste);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindParam(':contrasena', $contrasena);
  
        $resultado->execute();
  
        if($resultado->fetchColumn()>0){
        
        //   echo json_encode($resultado->execute());
          return try_catch_wrapper(function() use ($request){
            //throw new Exception('malo');
            $usuario  = $request->getParam('usuario');
            $contrasena = $request->getParam('contrasena');
            $dataUser = "SELECT usuario as User, CONCAT(nombre, ' ', apellidos) as nombre, celular as Telefono from usuarios where usuario  = '$usuario' AND contrasena = '$contrasena'";
            $dbConexion = new DBConexion(new Conexion());
            $resultado = $dbConexion->executeQuery($dataUser);
            return $resultado ?: [];
        }, $response);
  
        //   $_SESSION['id'] = "Administrador";
        }else {
          echo json_encode("02");
        }

    } catch (PDOException $error) {
  
        $errores =  array(
            "text" => $error->getMessage()
        );
  
        return json_encode($errores);
    }
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



?>