<?php

namespace Elastique\app;
use Elastique\app\Author;

use Elastique\Core\Model;
use Elastique\Core\DataMapper;
use Elastique\Core\Exceptions\NotFound;

use PDO;

class Book extends Model{
    public $author_id;
    public $publisher_id;
    public $id;
    public $title;
    public $featured;

    public function __construct() {
        parent::__construct(self::class);
    }

    public function get(int $id) : Book {
        $query = <<<SQL
select * from books
LEFT JOIN authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
where books.book_id = :id
SQL;
        $options = array(
            'params' => ['id', $id, PDO::PARAM_INT]
        );
        return $this->fetch($query, $options); 
    }

    public function getAll() : array {
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
SQL;
        return $this->fetchAll($query);
    }

    public function getFeatured() : array {
        $query = <<<SQL
select * from books
left join authors
on books.author_id = authors.author_id
LEFT JOIN publishers
on books.publisher_id = publishers.publisher_id
where books.featured = 1
SQL;
        return $this->fetchAll($query); 
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
        $options = array(
            'params' => ['search_string', $search_string, PDO::PARAM_STR],
            'limit' => $limit,
            'offset' => $offset
        
        );
        return $this->fetchAll($query, $options);
    }

    public function new(array $data) : array {
        return $this->factory($data);
    }

    public function save(){
        // Data
        $options['values'] = array(
            ['title', $this->title],
            ['author_id', $this->author_id],
            ['publisher_id', $this->publisher_id]
        );

        // Update
        if (isset($this->id)){
            $options['values']['id'] = $this->id;
            $query = <<<SQL
update books set title = :title, author_id = :author_id, publisher_id = :publisher_id where book_id = :id
SQL;
        }

        // Create
        else{
            print('craete');
            $query = <<<SQL
insert into books (title, author_id, publisher_id)
values (:title, :author_id, :publisher_id)
SQL;
        }

        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();
        $this->clearCache();
    }

    private function create(){
        $query = <<<SQL
insert into books (title)
values (:title)
SQL;
        $options['values'] = array(
            ['title', $this->title],
            ['author_id', $this->author_id],
            ['publisher_id', $this->publisher_id]
        );
        $sth = $this->db->prepareStatement($query, $options);
        $sth->execute();
    }

    protected function factory(array $data) : Book {
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

    private function update(){
        $query = <<<SQL
update books set title = :title where book_id = :id
SQL;
        $sth = $this->db->prepare($query);
        $sth->bindValue('title', $this->title);
        $sth->bindValue('author_id', $this->id);
        $sth->bindValue('publisher_id', $this->id);
        $sth->execute();    
    }


}
