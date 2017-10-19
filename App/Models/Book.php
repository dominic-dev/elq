<?php

namespace Elastique\App\Models;

use Elastique\Core\Model;

class Book extends Model{

    public function __construct() {
        $this->columns = array(
            'title' => [
                'type' => 'varchar(55)',
                'not_null' => true
            ],
            'featured' => [
                'type' => 'BIT',
                'not_null' => true,
                'default' => 0
            ]
        );
        $this->belongs_to = array('Author', 'Publisher');

        parent::__construct(self::class);
    }

    public function getFeatured() : array {
        $query = $this->getAllQuery();
        $query .= " WHERE $this->_table_name.featured = 1";
        return $this->fetchAll($query); 
    }

}
