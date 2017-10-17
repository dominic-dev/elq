<?php
namespace Elastique\App\Models;

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

    public function save(string $query=null, array $options=null){
        // Prepare data
        $options['values'] = array(
            ['name', $this->name]
        );

        // Prepare query
        // Update
        if (isset($this->id)){
            array_push($options['values'], ['id', $this->id]);
            $query = <<<SQL
update publishers set name = :name where publisher_id = :id
SQL;
        }

        // Create
        else{
            $query = <<<SQL
insert into publishers (name)
values (:name)
SQL;
        }
        // Submit
        parent::save($query, $options);
    }

    protected function factory(array $data){
        $obj = new Publisher();
        $obj->id = $data['publisher_id'];
        $obj->name = $data['name'];
        return $obj;
    }
}
