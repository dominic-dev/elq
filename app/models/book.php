<?php

namespace Elastique\app;
use Elastique\app\Author;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Book extends Model{
    private $author_id;
    private $publisher_id;

    public $id;
    public $title;
    public $featured;

    public function __construct() {
        parent::__construct(self::class);
    }

    public function get(int $id) : Book {
     /*   $query = <<<SQL
select books.book_id, books.title, authors.author_id, authors.first_name, authors.last_name
from books
inner join  where book_id = :id
SQL;*/
        $query = <<<SQL
select * from books
LEFT JOIN authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
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
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
SQL;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $rows = $sth->fetchAll();
        return $this->rowsToObjects($rows);
    }

    /*
    public function getAuthor() {
        if (isset($this->author_id)){
            $author = new Author();
            return $author->get($this->author_id);
        }
    }
     */

    public function getFeatured() : array {
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
where books.featured = 1
SQL;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $rows = $sth->fetchAll();
        return $this->rowsToObjects($rows);
    }

    public function search($search_string, int $offset=null, int $limit=null) : array {
        $search_string = '%' . $search_string . '%';
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
where books.title like :search_string or
authors.first_name like :search_string or
authors.last_name like :search_string or
publishers.name like :search_string
SQL;
        $sth = $this->db->prepare($query);
        $sth ->bindParam('search_string', $search_string, PDO::PARAM_STR);
        $sth->execute();
        $rows = $sth->fetchAll();

        return $this->rowsToObjects($rows);
    }

    public function new(array $data) : array {
        return $this->factory($data);
    }

    public function save() : void {
        if (isset($this->id)){
            $this->update();
        }
        else{
            $this->create();
        }
    }

    private function create() : void {
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

    private function factory(array $data) : Book {
        $obj = new Book();
        $obj->id = $data['book_id'];
        $obj->title = $data['title'];
        $obj->author_id = $data['author_id'];
        $obj->publisher_id = $data['publisher_id'];
        $obj->featured = $data['featured'];
        if (isset($obj->author_id)){
            $author = new Author();
            $obj->author = $author->get($data['author_id']);
        }
        if (isset($obj->publisher_id)){
            $publisher = new Publisher();
            $obj->publisher = $publisher->get($data['publisher_id']);

        }
        return $obj;
    }

    private function update() : void {
        $query = <<<SQL
update books set title = :title where book_id = :id
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('title', $this->title);
        $sth->bindValue('id', $this->id);
        $sth->execute();    
    }


}
