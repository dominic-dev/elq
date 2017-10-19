<?php

namespace Elastique\Core;

use Elastique\Core\Database;
use Elastique\Core\Config;
use Elastique\Core\Cache;

use PDO;

require_once('Helpers.php');

abstract class Model{
    public $id;

    public $_db;
    public $_plural;

    protected $_full_classname;
    protected $_parsed_classname;
    protected $_class_namespace;
    protected $_table_name;

    public function __construct($full_classname){
        $this->_full_classname = $full_classname;
        $this->_parsed_classname = parse_classname($full_classname);
        $this->_class_namespace= str_replace($this->_parsed_classname['short_classname'], '', $this->_full_classname);
        $this->_plural = $this->_parsed_classname['short_lower']. 's';
        $this->_table_name = $this->_plural;

        // Connect to database
        $this->_db = new Database();
    }


    /**
     * Prepare object for serialization for cache.
     */
    
    public function __sleep(){
        $properties = get_object_vars($this);
        // Unset database connection.
        unset($properties['_db']);
        return array_keys($properties);
    }

    /**
     * Restore object after deserialization from cache.
     */
    
    public function __wakeup(){
        // Restore connection.
        $this->_db = new Database();
    }

    /**
     * Take a data row from the database and return it as object.
     *
     * @param data (array) The data row from the database.
     *
     * @return mixed The data row as object.
     */
    
    protected function factory(array $data) {
        // Instantiate the model
        $obj =  new $this->_full_classname;

        // The convention for keys in the database is <model name>_id,
        // set it as property 'id'
        $obj->id = $data["{$this->_parsed_classname['short_lower']}_id"];

        // Set the data columns as object properties.
        foreach ($this->columns as $name => $info){
           $obj->$name = $data[$name];
        }

        // Belongs to relationships
        if (isset($this->belongs_to)){
            $this->belongs_to = is_array($this->belongs_to) ? $this->belongs_to : array($this->belongs_to);

            foreach ($this->belongs_to as $key => $model_name ){
                $model_name_full = $this->_class_namespace . $model_name;
                // Instantiate foreign model.
                $model = new $model_name_full;
                // Load the data as an object and store it as a property of the main model.
                $obj->{$model->_parsed_classname['short_lower']} = $model->factory($data);
            }
            
        }
        return $obj;
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
    
    public function fetchAll(string $query, array $options=null, bool $cache=true) : array {
        $cache_key = $this->getCacheKey('all', $query, $options);
        if ($cache == true && Cache::isEnabled() && Cache::exists($cache_key)){
            return Cache::fetch($cache_key);
        }

        $sth = $this->_db->prepareStatement($query, $options);
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
        $cache_key = $this->getCacheKey('row', $query, $options);
        if (Cache::isEnabled() && Cache::exists($cache_key)){
            return Cache::fetch($cache_key);
        }

        $result = $this->fetchAll($query, $options, false);
        $result = $result[0];
        Cache::store($cache_key, $result);

        return $result;
    }

    /**
     * Take an id. Return the row from the database with that id, as object.
     *
     * @param id (int) The id of the row.
     *
     * @return object The object from the database.
     */
    
    public function get(int $id){
        $query = $this->getAllQuery();
        $query .= " WHERE {$this->_parsed_classname['short_lower']}_id = :id";
        $options = array(
            'params' => ['id', $id, PDO::PARAM_INT]
        );
        return $this->fetch($query, $options); 

    }

    /**
     * Return all rows from the database as objects.
     *
     * @return array The array with all the rows as objects from the database.
     */

    public function getAll(){;
        $query = $this->getAllQuery();
        return $this->fetchAll($query); 

    }
    
    /**
     * Create the query string to get all rows, with relationships.
     *
     * @return string The query.
     */
    
    public function getAllQuery() : string {
        $query = 'SELECT * FROM ' . $this->_table_name;

        // Belongs to relationship
        if(isset($this->belongs_to)){
            $this->belongs_to = is_array($this->belongs_to) ? $this->belongs_to : array($this->belongs_to);
            foreach ($this->belongs_to as $key => $model_name){
                $full_model_name = $this->_class_namespace . $model_name;
                $model = new $full_model_name;
                $foreign_key = strtolower($model_name) . '_id';
                $table_name = strtolower($model_name) . 's';
                    $query .= " LEFT JOIN $table_name ON $this->_table_name.$foreign_key = $model->_table_name.$foreign_key";
            }
        }
        return $query;
    }

    /**
     * Take an identifier, a query and its options and return a string
     * to be used as a key for caching.
     *
     * @param identifier (string) A string to organize keys by, e.g. 'row' or 'all'.
     * @param query (string) The query.
     * @param options (array) The options that were passed with the query.
     *
     * @return string The key to be used for caching.
     */
    
    public function getCacheKey(string $identifier, string $query, array $options=null) : string {;
        return $this->_parsed_classname['short_lower'] . '_' . $identifier . '_' . md5(json_encode([$query, $options]));
    }

    /**
     * Store model in database.
     * Delete affected queries from cache.
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
        if (isset($this->id)){
            $this->update($query, $options);
        }
        else{
            $this->create($query, $options);
        }
        $this->clearCache($query, $options);
    }

    /**
     * Take a string, and search the model in the database for soft matches.
     *
     * @return array The array of: rows that match the search string, as objects.
     *
     */
    
    public function search($search_string, int $offset=null, int $limit=null) : array {
        // Wildcards
        $search_string = '%' . $search_string . '%';
        $query = $this->getAllQuery();

        $query .= ' WHERE ';
        foreach ($this->columns as $name=> $info){
            $query .= " $this->_table_name.$name LIKE :search_string OR ";
        }
        // Belongs to relationship
        if(isset($this->belongs_to)){
            $this->belongs_to = is_array($this->belongs_to) ? $this->belongs_to : array($this->belongs_to);
            foreach ($this->belongs_to as $key => $model_name){
                $full_model_name = $this->_class_namespace . $model_name;
                $model = new $full_model_name;
                foreach ( $model->columns as $name=> $info){
                    $query .= " $model->_table_name.$name LIKE :search_string OR ";
                }
            }
        }

        // Remove the last or if necessary.
        if (substr($query, -3) == 'OR '){
            $query = substr($query, 0, -3);
        }

        $options = array(
            'params' => ['search_string', $search_string, PDO::PARAM_STR],
            'limit' => $limit,
            'offset' => $offset
        
        );

        return $this->fetchAll($query, $options);
    }



    /**
     * Clear the cache for the data that is affected by query.
     *
     * @param query (string) The query.
     * @param options (array) The options that were passed with the query.
     */
    
    protected function clearCache(string $query, array $options){
        Cache::deletePattern($this->short_classname . '_all_');
        Cache::deleteKey($this->getCacheKey('row', $query, $options));
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
