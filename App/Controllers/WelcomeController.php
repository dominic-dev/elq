<?php

namespace Elastique\App\Controllers;

use Elastique\Core\Controller;

class WelcomeController extends Controller{

    public function index(){
        return $this->render('index');
    }

}
