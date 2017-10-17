<?php

// load Comopser
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Core/Helpers.php';

use Elastique\App\Models\Book;
use Elastique\Core\Database;
use Elastique\Core\Request;
use Elastique\Core\Router;
use Elastique\Core\Controller;

// Load twig.
$loader = new Twig_Loader_Filesystem(__DIR__ . '/App/views');
$twig = new Twig_Environment($loader);

$router = new Router();
$response = $router->route(new Request());
echo $response;

?>
