<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


$app->post('/api/administracion/login', function (Request $request, Response $response) {

  $correo  = $request->getParam('correo');
  $password = $request->getParam('password');

  $verificarexiste = "SELECT COUNT(*) as cuantos from administracion where correo  = :correo AND clave  = :password ";




  try {

      $cnx = new Conexion();
      $query = $cnx->Conectar();
      $resultado = $query->prepare($verificarexiste);
      $resultado->bindParam(':correo', $correo);
      $resultado->bindParam(':password', $password);

      $resultado->execute();

      if($resultado->fetchColumn()>0){

        echo json_encode("01");

        $_SESSION['id'] = "Administrador";
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
