<?php

namespace Elastique\App\Models;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Author extends Model{
    public $id;
    public $first_name;
    public $last_name;

    public function __construct() {
        parent::__construct(self::class);
    }

    public function get($id){
        $query = 'select * from authors where author_id = :id';
        $options['params'] = ['id', $id, PDO::PARAM_INT];
        return $this->fetch($query, $options);
    }

    public function getAll() : array {
        $query = <<<SQL
select * from authors 
SQL;
        return $this->fetchAll($query);
    }

    public function new($data){
        return $this->factory($data);
    }

    public function save(string $query=null, array $options=null){
        // Prepare data
        $options['values'] = array(
            ['first_name', $this->first_name],
            ['last_name', $this->last_name]
        );

        // Prepare query
        // Update
        if (isset($this->id)){
            array_push($options['values'], ['id', $this->id]);
            $query = <<<SQL
update authors set first_name = :first_name, last_name = :last_name where author_id = :id
SQL;
        }

        // Create
        else{
            $query = <<<SQL
insert into authors (first_name, last_name)
values (:first_name, :last_name)
SQL;
        }
        // Submit
        parent::save($query, $options);
    }

    protected function factory(array $data){
        $obj = new Author();
        $obj->id = $data['author_id'];
        $obj->first_name = $data['first_name'];
        $obj->last_name = $data['last_name'];
        return $obj;
    }

}
