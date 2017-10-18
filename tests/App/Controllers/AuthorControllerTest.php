<?php 

namespace Elastique\Tests\App\Controllers;

use Elastique\App\Controllers\AuthorController;
use Elastique\App\Models\Author;
use Elastique\Core\Request;
use PHPUnit\Framework\TestCase;

class AuthorControllerTest extends TestCase {

    public function setUp(){
        $this->request = new TestRequest;
        $this->controller = new AuthorController($this->request);
        $this->model = new Author();
        $this->model->db->dbh = $this->model->db->conn('test');
    }

    public function testShow(){;
        for($i=1; $i<6; $i++){;
            $result_controller = $this->controller->show($i);
            $result_model = $this->model->get($i);
            $this->assertRegExp('/' . $result_model->first_name . '/', $result_controller);
            $this->assertRegExp('/' . $result_model->last_name . '/', $result_controller);
        }
        
    }

    public function testList(){ ;
        $result = $this->controller->list();
        $this->assertSame(substr_count($result, 'data-model="author"'), 5);

    }


}

?>
