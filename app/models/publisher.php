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
        $sth = $this->db->prepare($query);
        $sth->bindParam('id', $id, PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch();
        if ($data == false){
            throw new NotFound('Publisher not found.');
        }
        return $this->factory($data);
    }

    public function getAll() : array {
        $query = <<<SQL
select * from publishers 
SQL;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $rows = $sth->fetchAll();
        $array = [];
        foreach ($rows as $key => $value){
            $obj = $this->factory($value);
            array_push($array, $obj);
        }
        return $array;
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
        $sth = $this->db->prepare($query);
        $sth->bindValue('name', $this->name);
        $sth->execute();
    }

    private function factory($data){
        $obj = new Publisher();
        $obj->id = $data['publisher_id'];
        $obj->name = $data['name'];
        return $obj;
    }

    private function update(){
        $query = <<<SQL
update publishers set name = :name where publisher_id = :id
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('name', $this->name);
        $sth->bindValue('id', $this->id);
        $sth->execute();    
    }


}
