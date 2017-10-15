<?php
namespace Elastique\Core;

class Config{
    private static $data;

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
