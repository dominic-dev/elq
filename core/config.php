<?php
namespace Elastique\Core;

use Elastique\Core\Exceptions\NotFound;

/**
 * Config. Singleton.
 */

class Config{
    private static $data;

    /**
     * Take a key, return data from config/aspp.json with the same key.
     *
     * @param key (string) The key to search for.
     * @return mixed 
     */
    public static function get($key){
        if (self::$data == null){
            $app_json = file_get_contents(__DIR__ . '/../config/app.json');
            self::$data = json_decode($app_json, true);
        }

        if (!isset(self::$data[$key])){
                throw new NotFound("Data key not found in config.");
        }
        return self::$data[$key];
    }
}

?>
