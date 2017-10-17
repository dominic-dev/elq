<?php 

namespace Elastique\Tests\App\Models;

use Elastique\App\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase {
    public function testSomething(){
        $b = new Book();
        $this->assertSame(1, 1);
    }
}

?>
