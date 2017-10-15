<?php 

function split_class_name($class_name){
    return explode('\\', $class_name);
}
function parse_classname($classname){
    $array = [];
    $array['full_classname'] = $classname;
    $array['split_classname'] = explode('\\', $classname);
    $array['short_classname'] = end($array['split_classname']);
    $array['short_lower'] = strtolower($array['short_classname']);
    return $array;
}
