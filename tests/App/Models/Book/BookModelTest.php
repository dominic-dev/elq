<?php 

namespace Elastique\Tests\App\Models;

use Elastique\App\Models\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase {

    public function setUp(){
        $this->model = new Book();
    }
    public function testTitle(){
        $book = $this->model->get(1);
        $this->assertSame($book->title, 'Mice Of Sorrow');
    }

    public function testAuthor(){
        $book = $this->model->get(1);
        $this->assertSame($book->author->last_name, 'Pietersen');
    }

    public function testPublisher(){
        $book = $this->model->get(1);
        $this->assertSame($book->publisher->name, 'Uitgever');
    }

    public function testHighlighted(){
        $result = $this->model->getFeatured();
        $this->assertSame(count($result), 5);
    }

    public function testGetAll(){
        $result = $this->model->getAll();
        $this->assertSame(count($result), 17);
    }

    public function testSearch(){
        $result = $this->model->search('of');
        $this->assertSame(count($result), 9);
    }

}

?>
