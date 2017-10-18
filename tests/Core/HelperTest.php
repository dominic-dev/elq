<?php 

namespace Elastique\Tests\Core;

use Elastique\Core\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase {

    public function setUp(){
        $this->classname = 'Elastique\App\Models\Book';
    }

    public function testSplitClassname(){;
        $result = split_class_name($this->classname);
        $this->assertSame($result, ['Elastique', 'App', 'Models', 'Book']);
        
    }

    public function testParseClassname(){;
        $result = parse_classname($this->classname);
        $expected = array(
            'full_classname' => $this->classname,
            'split_classname' => split_class_name($this->classname),
            'short_classname' => 'Book',
            'short_lower' => 'book'
                );
        $this->assertSame($result, $expected);
        
    }

}

?>
