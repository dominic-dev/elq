<?php 

namespace Elastique\Tests\App\Models;

use Elastique\App\Models\Author;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase {
    public function setUp(){
        $this->model = new Author();
        $this->model->db->dbh = $this->model->db->conn('test');
    }
    public function testName(){
        $author = $this->model->get(1);
        $this->assertSame($author->first_name, 'Steven');
        $this->assertSame($author->last_name, 'Pietersen');
    }
}

?>
