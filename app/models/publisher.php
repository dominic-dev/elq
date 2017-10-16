<?php

namespace Elastique\app;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Publisher extends Model{
    public $id;
    public $name;

    public function __construct() {
        parent::__construct(self::class);
    }

    public function get($id){
        $query = 'select * from publishers where publisher_id = :id';
        $options['params'] = ['id', $id, PDO::PARAM_INT];
        return $this->fetch($query, $options);
    }

    public function getAll() : array {
        $query = <<<SQL
select * from publishers 
SQL;
        return $this->fetchAll($query);
    }

    public function new($data){
        return $this->factory($data);
    }

    public function save(){
        if (isset($this->id)){
            $this->update();
        }
        else{
            $this->create();
        }
    }

    private function create(){
        $query = <<<SQL
insert into publishers (name)
values (:name)
SQL;
        $options['values'] = ['name', $this->name];
        $sth = $this->db->prepareStatement($query);
        $sth->execute();
    }

    private function update(){
        $query = <<<SQL
update publishers set name = :name where publisher_id = :id
SQL;
        $options['values'] = array(
            ['name', $this->name].
            ['id', $this->id]
        );
        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();    
    }

    protected function factory(array $data){
        $obj = new Publisher();
        $obj->id = $data['publisher_id'];
        $obj->name = $data['name'];
        return $obj;
    }
}
