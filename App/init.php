<?php

use Elastique\App\Book;
use Elastique\Core\Database;

function autoload($classname){
    $namespaces = explode('\\', $classname);
    $parent = $namespaces[1];
    $filename = strtolower(array_pop($namespaces));
    if ($parent == 'Core'){
        include_once(__DIR__ . '/../core/' . $filename . '.php');
    }
    else{
        include_once(__DIR__ . "/models/$filename.php");
    }
}

spl_autoload_register('autoload');

$book = new Book(1, 'Lord of the Rings', 'Fantasy', 1, 0);

//$db = new PDO('sqlite:database.sqlite3');


?>
