<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../../src/config/config.php';
require '../../src/classes/dbconexion.php';

$app = new \Slim\App;


require '../../src/rutas/productos.php';




$app->run();
