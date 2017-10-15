<?php

use Elastique\App\Book;
use Elastique\Core\Database;

function autoload($classname){
    $namespaces = explode('\\', $classname);
    $parent = $namespaces[count($namespaces) - 2];
    $filename = strtolower(array_pop($namespaces));
    if ($parent == 'Core'){
        include_once(__DIR__ . '/../core/' . $filename . '.php');
    }
    elseif ($parent == 'Exceptions'){
        include_once(__DIR__ . '/../core/exceptions.php');
    }
    else{
        include_once(__DIR__ . "/models/$filename.php");
    }
}

spl_autoload_register('autoload');

$book = new Book();
//$book->new('LOTRO');
//$book->save();
$b = $book->get(21);
var_dump($b->search('dominic'));
$a = $b->getAuthor();
//$b->title = 'sloenk';
//$b->save();
//var_dump($b);
//$data = ['title' => 'klak'];
//$book->new($data);
//var_dump($book);
//$book->save();
//var_dump($book->pluralize);
//$b->title = 'ploep';
//$b->save();


//$db = new PDO('sqlite:database.sqlite3');


?>
