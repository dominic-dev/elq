<?php 

namespace Elastique\Tests\App\Controllers;

use Elastique\App\Controllers\PublisherController;
use Elastique\App\Models\Publisher;
use Elastique\Core\Request;
use PHPUnit\Framework\TestCase;

class PublisherControllerTest extends TestCase {

    public function setUp(){
        $this->request = new TestRequest;
        $this->controller = new PublisherController($this->request);
        $this->model = new Publisher();
        $this->model->_db->dbh = $this->model->_db->conn('test');
    }

    public function testShow(){;
        for($i=1; $i<4; $i++){;
            $result_controller = $this->controller->show($i);
            $result_model = $this->model->get($i);
            $this->assertRegExp('/' . $result_model->name . '/', $result_controller);
        }
        
    }

    public function testList(){ ;
        $result = $this->controller->list();
        $this->assertSame(substr_count($result, 'data-model="publisher"'), 3);

    }


}

?>
