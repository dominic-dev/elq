<?php

namespace Elastique\Core;

use Elastique\Core\Database;

require_once('helpers.php');

abstract class Model{
    public $pluralize;

    protected $db;
    protected $full_classname;
    protected $short_classname;

    public function __construct($full_classname){
        // Get last part of class name, and lowercase it.
        $this->full_classname = $full_classname;
        $split_class_name = split_class_name($full_classname);
        $short_classname = array_pop($split_class_name);
        $this->short_classname = strtolower($short_classname);

        // Plural name of model
        $this->pluralize = $this->short_classname . 's';
        // Connect to database
        $this->db = Database::conn();
    }
}

?>
