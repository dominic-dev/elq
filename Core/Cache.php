<?php

namespace Elastique\Core;

use Elastique\Core\Config;
use APCUIterator;

class Cache{
    /**
     * Take a key, return value from cache.
     *
     * @param key (string) The key to search for.
     */

    public static function fetch(string $key){
        echo('fetching from cache key ' . $key . '<br />');
        return apcu_fetch($key);
    }

    /**
     * Take a key and value, store it in cache.
     *
     * @param key (string) The key to store value by.
     * @param value (mixed) The data to store.
     */
    
    public static function store(string $key, $value){
        apcu_store($key, $value);
    }

    /**
     * Take a key, return boolean true if it exists, false if it does not.
     *
     * @param key (string) The key to search for.
     *
     */
    
    public static function exists(string $key) : bool {
        if (apcu_exists($key)){
            return true;
        }
        return false;
    }

    /**
     * Take a key, delete it and its value from cache.
     *
     * @param key (string) They key to delete.
     */
    
    public static function deleteKey(string $key){
        apcu_delete($key);
    }

    /**
     * Take a pattern. Delete cached items by key that match pattern.
     *
     * @param pattern (string) The pattern to match.
     */
    
    public static function deletePattern(string $pattern){
        apcu_delete(new APCUIterator('#^' . $pattern . '#'));
    }
    
    /**
     * @return boolean True if caching is enabled, false if it is not.
     */
    
    public static function isEnabled() : bool {
        if (Config::get('cache') == 1){
            return true;
        }
        return false;
    }
}

?>
