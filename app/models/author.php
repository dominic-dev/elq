<?php

namespace Elastique\app;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Author extends Model{
    private $id;
    public $first_name;
    public $last_name;

    public function __construct() {
        parent::__construct(self::class);
    }

    public function get($id){
        $query = 'select * from authors where author_id = :id';
        $sth = $this->db->prepare($query);
        $sth->bindParam('id', $id, PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch();
        if ($data == false){
            throw new NotFound('Author not found.');
        }
        return $this->factory($data);
    }

    public function getAll() : array {
        $query = <<<SQL
select * from authors 
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
insert into authors (first_name, last_name)
values (:first_name, :last_name)
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('first_name', $this->first_name);
        $sth->bindValue('last_name', $this->last_name);
        $sth->execute();
    }

    private function factory($data){
        $obj = new Author();
        $obj->id = $data['author_id'];
        $obj->first_name = $data['first_name'];
        $obj->last_name = $data['last_name'];
        return $obj;
    }

    private function update(){
        $query = <<<SQL
update authors set first_name = :first_name, last_name = :last_name where author_id = :id
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('first_name', $this->first_name);
        $sth->bindValue('last_name', $this->last_name);
        $sth->bindValue('id', $this->id);
        $sth->execute();    
    }


}
