<?php

use Elastique\App\Models\Book;
use Elastique\App\Models\Author;
use Elastique\App\Models\Publisher;
use Elastique\Core\Database;
use Elastique\Core\Request;

function autoload($classname){
    $namespaces = explode('\\', $classname);
    $parent = $namespaces[count($namespaces) - 2];
    $filename = array_pop($namespaces);
    if ($parent == 'Core'){
        include_once(__DIR__ . '/../Core/' . $filename . '.php');
    }
    elseif ($parent == 'Exceptions'){
        include_once(__DIR__ . '/../core/exceptions.php');
    }
    else{
        include_once(__DIR__ . "/Models/$filename.php");
    }
}

spl_autoload_register('autoload');

//$author = new Author();
//$author->first_name = 'piet';
//$author->last_name = 'hein';
//$author->save();
//$publisher = new Publisher();
//$publisher = $publisher->get(4);
//$publisher->name = 'Piet Wous';
//$publisher->save();
$b = new Book();
//$b = $book->get(1);
//$r = new Request();
//$p = $r->getParams();
//$a = $b->getAuthor();
$b->title = 'De Kast met Veren';
$b->author_id = 1;
$b->publisher_id = 2;
$b->save();
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
