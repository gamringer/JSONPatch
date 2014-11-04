<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;

class PointerArrayAccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getting value from Object as array
     */
    public function testGetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);
        
        $this->assertEquals($attributes['foo'], $pointer->get('/foo'));
        $this->assertEquals($target->qux, $pointer->get('/qux'));
    }

    /**
     * Test setting value from Object as array
     */
    public function testSetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);
        
        $this->assertEquals($attributes['qux'], $pointer->get('/qux'));
        $pointer->set('/qux', 'corge');
        $this->assertEquals('corge', $pointer->get('/qux'));
        $this->assertEquals('corge', $target->qux);
    }

    /**
     * Test unsetting value from Object as array
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testUnsetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);
        
        $this->assertEquals($attributes['qux'], $pointer->get('/qux'));
        $pointer->remove('/qux');
        $pointer->get('/qux');
    }

    /**
     * Tests getting an unset path
     *     
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetUnsetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $pointer->get('/corge');
    }

    /**
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testUnsetRootValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $this->assertEquals($pointer->getTarget(), $target);
        $pointer->remove('');
        $this->assertEquals(null, $target);
    }

}
