<?php

namespace Elastique\Core;

class Form{

    public function __construct(object $model, array $columns, string $foreign_key=null, string $relation=null){
        $html = '';

        foreach ($column as $name => $type){
            switch ($type) {
            case 'input':
                $html += '<input type="text" name="' . $name . '"><br>' . "\xA";
                break;
            case 'textarea':
                break;
            case 'select':
                break;
            case 'select_multiple':
                break;

            }

        }

    }

}

?>
