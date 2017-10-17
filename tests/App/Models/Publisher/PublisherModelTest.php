<?php 

namespace Elastique\Tests\App\Models;

use Elastique\App\Models\Publisher;
use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase {
    public function setUp(){
        $this->model = new Publisher();
    }
    public function testName(){
        $publisher = $this->model->get(1);
        $this->assertSame($publisher->name, 'Uitgever');
    }

}

?>
