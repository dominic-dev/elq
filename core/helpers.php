<?php 

/**
 * Take class name. Return class name split by \
 */

function split_class_name($class_name){
    return explode('\\', $class_name);
}

/**
 * Take classname, return array.
 *
 * @param classname (string) The classname to parse.
 *
 * @return array
 *
 * Return array with full_classname, split_classname
 * short_classname, short_lower.
 */
function parse_classname(string $classname) : array {
    $array = [];
    $array['full_classname'] = $classname;
    $array['split_classname'] = explode('\\', $classname);
    $array['short_classname'] = end($array['split_classname']);
    $array['short_lower'] = strtolower($array['short_classname']);
    return $array;
}
