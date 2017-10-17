<?php

namespace Elastique\Core;

use Elastique\Core\Database;
use Elastique\Core\Request;

use Twig_Environment;
use Twig_Loader_Filesystem;
require_once('Helpers.php');

abstract class Controller{
    public $request;
    public $db;

    // Name of the (child) class.
    protected $parsed_classname;

    public function __construct(Request $request){
        $this->request = $request;
        $this->parsed_classname = parse_classname(get_class($this));
        $this->db = Database::conn();

        // Init function
        if (method_exists($this, 'init')){
            $this->init();
        }
        // Load twig
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../App/views/');
        $this->view = new Twig_Environment($loader);
    }

    /**
     * Render view.
     *
     * @param $template (string) the template to render.
     * @param $params (array) the paramaters to pass to render. Optional.
     * @param directory (str) the directory to search in. Optional.
     *
     * @return string The output to render.
     */
    protected function render(string $template, array $params=[], $directory=null) : string {
        // Render searches by default in the views/<model name>/ directory
        if(!isset($directory)){
            $directory = str_replace('controller', '', $this->parsed_classname['short_lower']);
        }
        // Path to the template file.
        $path = $directory . '/' . $template . '.twig';
        return $this->view->loadTemplate($path)->render($params);
    }
    
    
}

?>
