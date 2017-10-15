<?php

namespace Elastique\Core;
use Elastique\Core\Config;
require('config.php');

use PDO;

class Database{
    private static $instance;

    /*************************
    *  Connect to database  *
    *************************/
    
    public static function conn(){
        $environment = Config::get('environment');
        $db_settings = Config::get("db-$environment");
        if ($db_settings['driver'] == 'sqlite'){
            $connection_string = $db_settings["driver"] . ':' . __DIR__ . '/../db/' .  $db_settings["database"];
        }
        return new PDO($connection_string);
    }
}
