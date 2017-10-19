<?php

namespace Elastique\Core;

use Elastique\Core\Config;

use PDO;

require('Config.php');


class Database{
    public $dbh;

    public function __construct(){
        $this->dbh = $this->conn();
    }

    /**
     * Connect to database.
     */
    public function conn(string $environment=null){
        if(!isset($environment)){
            $environment = Config::get('environment');
        }
        $db_settings = Config::get("db-$environment");
        if ($db_settings['driver'] == 'sqlite'){
            $connection_string = $db_settings["driver"] . ':' . __DIR__ . '/../db/' .  $db_settings["database"];
        }
        return new PDO($connection_string);
    }

    /**
     * Take a query and prepare it. Return statement handler.
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
     * @return PDOStatement 
     *
     */
    
    public function prepareStatement(string $query, array $options=null){
        // Limit
        if (isset($options['limit'])){
            $options['values'] = isset($options['values']) ? $options['values'] : [];
            $query .= ' LIMIT :limit'; 
            array_push($options['values'], ['limit', $options['limit'], PDO::PARAM_INT]);

            // Offset
            if (isset($options['offset'])){
                $query .= ' OFFSET :offset';
                array_push($options['values'], ['offset', $options['offset'], PDO::PARAM_INT]);
            }
        }

        $sth = $this->dbh->prepare($query);

        // bindParam
        if (isset($options['params'])){
            // A single param may be passed, wrap it in an array.
            $options['params'] = is_array($options['params'][0]) ? $options['params'] : array($options['params']);
            foreach ($options['params'] as $key => $array){
                list($name, $value, $type) = $array;
                $sth ->bindParam($name, $value, $type);
            }
            
        }
        // bindValue
        if (isset($options['values'])){
            // A single value may be passed, wrap it in an array.
            $options['values'] = is_array($options['values'][0]) ? $options['values'] : array($options['values']);
            foreach ($options['values'] as $key => $array){
                list($name, $value, $type) = $array;
                $sth->bindValue($name, $value, $type);
            }
        }
        return $sth;
    }
}
