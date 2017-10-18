<?php 

namespace Elastique\Tests\App\Controllers;

use Elastique\App\Controllers\BookController;
use Elastique\App\Models\Book;
use Elastique\Core\Request;
use PHPUnit\Framework\TestCase;

class TestRequest extends Request{
    public function __construct(){;
        // Do nothing
    }
}

class BookControllerTest extends TestCase {

    public function setUp(){
        $this->request = new TestRequest;
        $this->controller = new BookController($this->request);
        $this->model = new Book();
        $this->model->db->dbh = $this->model->db->conn('test');
    }

    public function testFeatured()
    {
        $result = $this->controller->featured();
        $this->assertSame(substr_count($result, '/books'), 5);
    }

    public function testSearch(){;
        $result = $this->controller->search('of');
        $this->assertSame(substr_count($result, '/books'), 9);
    }
    
    public function testSearchLimitOffset(){;
        $result = $this->controller->search('of', 2, 2);
        $this->assertSame(substr_count($result, '/books'), 2);
        $this->assertRegExp('/Means Of The Stockades/', $result);
        $this->assertRegExp('/Serpent Of Freedom/', $result);
    }

    public function testShow(){;
        for($i=1; $i<18; $i++){;
            $result_controller = $this->controller->show($i);
            $result_model = $this->model->get($i);
            $this->assertRegExp('/' . $result_model->title . '/', $result_controller);
            $this->assertRegExp('/' . $result_model->author->first_name . '/', $result_controller);
            $this->assertRegExp('/' . $result_model->author->last_name . '/', $result_controller);
            $this->assertRegExp('/' . $result_model->publisher->name. '/', $result_controller);
        }
        
    }

    public function testList(){ ;
        $result = $this->controller->list();
        $this->assertSame(substr_count($result, '/books'), 17);

    }


}

?>
