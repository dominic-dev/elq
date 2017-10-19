<?php

namespace Elastique\App\Models;

use Elastique\Core\Model;

class Author extends Model{

    public function __construct() {
        $this->columns = array(
            'first_name' => ['type' => 'varchar(55)', 'not_null' => true],
            'last_name' => ['type' => 'varchar(55)', 'not_null' => true]
        );

        parent::__construct(self::class);
    }

}
