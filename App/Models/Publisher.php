<?php

namespace Elastique\App\Models;

use Elastique\Core\Model;

class Publisher extends Model{

    public function __construct() {
        $this->columns = array(
            'name' => ['type' => 'varchar(55)', 'not_null' => true]
        );

        parent::__construct(self::class);
    }

}
