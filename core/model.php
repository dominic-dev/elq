<?php

namespace Elastique\Core;

use Elastique\Core\Database;

use PDO;
use APCUIterator;

require_once('helpers.php');

abstract class Model{
    public $pluralize;

    protected $db;
    protected $full_classname;
    protected $short_classname;

    abstract protected function factory(array $data);

    public function __construct($full_classname){
        // Get last part of class name, and lowercase it.
        $this->full_classname = $full_classname;
        $split_class_name = split_class_name($full_classname);
        $short_classname = array_pop($split_class_name);
        $this->short_classname = strtolower($short_classname);

        // Plural name of model
        $this->pluralize = $this->short_classname . 's';
        // Connect to database
        $this->db = new Database();
    }

    /**
        * Take a query, return results.
        *
        * @param query (string) The query to execute.
        * @param options (array) Valid options are
        *   'limit' => (int),
        *   'offset' => (int),
        *   'params' => (array) Can be:
        *       1. a one dimensional array with one paramater
        *       containing (param_name, param_value, PDO::param_type)
        *       2. a two dimensiona array containing an array of paramaters
        *       as described in 1.
        *   'values' => (array) Can be.
        *       1. a one dimensional array with one value 
        *       containing (value_name, value_value)
        *       2. a two dimensiona array containing an array of values
        *       as described in 1.
        *
        * @return array
        * Returns an array of objects.
        *
     */
    
    public function fetchAll(string $query, array $options=null){
        $cache_key = $this->short_classname . md5($query);
        if (apcu_exists($cache_key)){
            return apcu_fetch($cache_key);
        }
        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();
        $rows = $sth->fetchAll();
        $result = $this->rowsToObjects($rows);
        apcu_store($cache_key, $result);

        return $result;

    }

    /**
     * Analogous to fetchAll, except it only returns one row as object.
     */
    
    public function fetch(string $query, array $options=null){
        $result = $this->fetchAll($query, $options);
        return $result[0];
    }

    protected function clearCache(){
        apcu_delete(new APCUIterator('#^' . $this->short_classname . '#'));
    }

    /**
     * Take rows from database, return objects from models.
     */
    
    protected function rowsToObjects(array $rows) : array{
        $array = [];
        foreach ($rows as $key => $value){
            $obj = $this->factory($value);
            array_push($array, $obj);
        }
        return $array;
    }
}

?>
