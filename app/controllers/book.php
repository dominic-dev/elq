<?php

namespace Elastique\App;

use Elastique\Core\Controller;

class BookController extends Controller{
    protected $model;

    protected function init(){
        $this->model = new Book();

    }

    public function search(string $query){
        // Split query by '-'
        $keywords = explode('-', $query);

        // Search for every keyword and combine results.
        $result = [];
        foreach ($keywords as $key => $value){
            $result += $this->model->search($value);
        }

        // Render
        $params = ['books' => $result];
        return $this->render('list', $params);
    }

    public function show(int $id){
        $book = $this->model->get($id);
        //return $this->view->loadTemplate('/book/show.twig')->render(['book' => $book]);
        $params = ['book' => $book];
        return $this->render('show', $params);
    }

    public function list(){
        $books = $this->model->getAll();
        $params = ['books' => $books];
        return $this->render('list', $params);
    }

}

?>
