<?php

namespace Elastique\app;
use Elastique\app\Author;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Book extends Model{
    private $id;
    private $author_id;
    public $title;



    public function __construct() {
        parent::__construct(self::class);
    }

    public function get($id){
     /*   $query = <<<SQL
select books.book_id, books.title, authors.author_id, authors.first_name, authors.last_name
from books
inner join  where book_id = :id
SQL;*/
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
where books.book_id = :id
SQL;
        //$query = 'select * from books where book_id = :id';
        $sth = $this->db->prepare($query);
        $sth->bindParam('id', $id, PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch();
        if ($data == false){
            throw new NotFound('Book not found');
        }
        return $this->factory($data);
    }

    public function getAll() : array {
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
SQL;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $rows = $sth->fetchAll();
        return $this->rowsToObjects($rows);
    }

    public function getAuthor() {
        if (isset($this->author_id)){
            $author = new Author();
            return $author->get($this->author_id);
        }
    }

    public function search($search_string){
        $search_string = '%' . $search_string . '%';
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
where books.title like :search_string or
authors.first_name like :search_string or
authors.last_name like :search_string
SQL;
        $sth = $this->db->prepare($query);
        $sth ->bindParam('search_string', $search_string, PDO::PARAM_STR);
        $sth->execute();
        $rows = $sth->fetchAll();
        return $this->rowsToObjects($rows);
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
insert into books (title)
values (:title)
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('title', $this->title);
        $sth->execute();
    }

    private function rowsToObjects(array $rows) : array{
        $array = [];
        foreach ($rows as $key => $value){
            $book = $this->factory($value);
            array_push($array, $book);
        }
        return $array;
    }

    private function factory($data) : Book {
        $obj = new Book();
        $obj->id = $data['book_id'];
        $obj->title = $data['title'];
        $obj->author_id = $data['author_id'];
        if (isset($obj->author_id)){
            $author = new Author();
            $obj->author = $author->get($data['author_id']);
        }
        return $obj;
    }

    private function update(){
        $query = <<<SQL
update books set title = :title where book_id = :id
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('title', $this->title);
        $sth->bindValue('id', $this->id);
        $sth->execute();    
    }


}
