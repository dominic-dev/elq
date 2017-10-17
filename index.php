<?php

// load Comopser
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Core/Helpers.php';

use Elastique\Core\Router;
use Elastique\Core\Request;

// Load twig.
$loader = new Twig_Loader_Filesystem(__DIR__ . '/App/views');
$twig = new Twig_Environment($loader);

$router = new Router();
$response = $router->route(new Request());
echo $response;

?>
