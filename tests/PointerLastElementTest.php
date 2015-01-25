<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;

class PointerLastElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that setting the /- value does indeed create a new element
     */
    public function testCanSetNew()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->set('/-', $value)->get('/3');

        $this->assertEquals($value, $result);

        return $pointer;
    }

    /**
     * Tests that cannot get the /- value
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testCannotGet()
    {
        $target = [];
        $pointer = new Pointer($target);

        $nextElement = $pointer->get('/-');
    }

    /**
     * Tests that cannot unset the /- value
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testCannotUnset()
    {
        $target = [];
        $pointer = new Pointer($target);

        $nextElement = $pointer->remove('/-');
    }
}
