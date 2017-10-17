<?php

// load Comopser
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Core/Helpers.php';

use Elastique\App\Models\Book;
use Elastique\Core\Database;
use Elastique\Core\Request;
use Elastique\Core\Router;
use Elastique\Core\Controller;

// Class autoloader.
function autoload($classname){
    $split_classname = split_class_name($classname);
    $parent = $split_classname[count($split_classname) - 2];
    $filename = strtolower(array_pop($split_classname));
    // Core
    if ($parent == 'Core'){
        require_once(__DIR__ . '/core/' . $filename . '.php');
    }
    // App controllers
    elseif ($parent == 'App' && strpos($filename, 'controller')){
        $filename =str_replace('controller', '', $filename);
        require_once(__DIR__ . '/app/controllers/' . $filename . '.php');
    }
    // Exceptions
    elseif ($parent == 'Exceptions'){
        require_once(__DIR__ . '/core/exceptions.php');
    }
    // App models
    else{
        require_once(__DIR__ . "/app/models/$filename.php");
    }
}

// Autoload classes.
//spl_autoload_register('autoload');

// Load twig.
$loader = new Twig_Loader_Filesystem(__DIR__ . '/App/views');
$twig = new Twig_Environment($loader);

$router = new Router();
$response = $router->route(new Request());
echo $response;

?>
