<?php

namespace Elastique\App\Controllers;

use Elastique\Core\Controller;
use Elastique\App\Models\Author;

class AuthorController extends Controller{
    protected $model;

    protected function init(){
        $this->model = new Author();

    }

    public function show(int $id){
        $author = $this->model->get($id);
        $params = ['author' => $author];
        return $this->render('show', $params);
    }

    public function list(){
        $authors = $this->model->getAll();
        $params = ['authors' => $authors];
        return $this->render('list', $params);
    }

}

?>
