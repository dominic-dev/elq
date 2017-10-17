<?php

namespace Elastique\Core;

use Elastique\Core\Database;
use Elastique\Core\Config;
use Elastique\Core\Cache;

use PDO;
use APCUIterator;

require_once('Helpers.php');

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
     * Store model in database.
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
     */
    
    public function save(string $query, array $options){
        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();
        $this->clearCache($query, $options);
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
      * @return array Return an array of objects.
      *
      */
    
    public function fetchAll(string $query, array $options=null, bool $cache=true){
        $cache_key = $this->short_classname . '_all_' . md5(json_encode([$query, $options]));
        if ($cache == true && Cache::isEnabled() && Cache::exists($cache_key)){
            return Cache::fetch($cache_key);
        }
        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();
        $rows = $sth->fetchAll();
        $result = $this->rowsToObjects($rows);
        Cache::store($cache_key, $result);

        return $result;

    }

    /**
     * Analogous to fetchAll, except it only returns one row as object.
     *
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
     * @return object
     *
     */
    public function fetch(string $query, array $options=null){
        $cache_key = $this->short_classname . '_row_' . md5(json_encode([$query, $options]));
        if (Cache::isEnabled() && Cache::exists($cache_key)){
            return Cache::fetch($cache_key);
        }
        $result = $this->fetchAll($query, $options, false);
        $result = $result[0];
        Cache::store($cache_key, $result);
        return $result;
    }

    /**
     * Clear the cache for the data that is affected by query.
     *
     * @param query (string) The query.
     * @param options (array) The options that were passed with the query.
     */
    
    protected function clearCache($query, $options){
        Cache::deletePattern($this->short_classname . '_all_');
        Cache::deleteKey(md5(json_encode([$query, $options])));
    }

    /**
     * Take rows from database, return objects from models.
     *
     * @param rows (array) The rows to process.
     *
     * @return array Returns an array of objects.
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
