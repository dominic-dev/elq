<?php

namespace Elastique\App\Controllers;

use Elastique\Core\Controller;
use Elastique\App\Models\Book;

class BookController extends Controller{
    protected $model;

    protected function init(){
        $this->model = new Book();
    }

    public function featured(){
        $books = $this->model->getFeatured();
        $params = ['books' => $books];
        return $this->render('list', $params);
        
    }

    public function search(string $query=null, int $offset=null, int $limit=null){
        // Render form.
        if ($query == null){
            return $this->render('search');
        }


        // Render results.
        // Split query by '-'
        $keywords = explode('-', $query);

        // Search for every keyword and combine results.
        $result = [];
        foreach ($keywords as $key => $value){
            $result += $this->model->search($value);
        }

        if(isset($offset)){
            $result = array_slice($result, $offset);
        }
        if(isset($limit)){
            $result = array_slice($result, 0, $limit);
        }

        $params = ['books' => $result];
        return $this->render('list', $params);
    }

    public function show(int $id){
        $book = $this->model->get($id);
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
