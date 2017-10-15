<?php

namespace Elastique\App;

use Elastique\Core\Controller;

class PublisherController extends Controller{
    protected $model;

    protected function init(){
        $this->model = new Publisher();
    }

    public function show(int $id){
        $publisher = $this->model->get($id);
        $params = ['publisher' => $publisher];
        return $this->render('show', $params);
    }

    public function list(){
        $publishers = $this->model->getAll();
        $params = ['publishers' => $publishers];
        return $this->render('list', $params);
    }

}

?>
