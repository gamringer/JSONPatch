<?php

namespace gamringer\JSONPatch\Test;

use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that an empty patch is succesfully rendered to string
     */
    public function testEmptyPatch()
    {
        $patch = new Patch();

        $this->assertEquals('[]', (string)$patch);
    }

    /**
     * Tests that an operation can be successfully be added
     */
    public function testAddOperation()
    {
        $patch = new Patch();
        $op = new Resources\MockAtomic();
        $patch->addOperation($op);

        $this->assertNotEmpty(json_decode($patch));
    }

    /**
     * Tests that a patch applies properly
     */
    public function testApplyPatch()
    {
        $patch = new Patch();
        //$op = new Resources\MockModifies();
        //$patch->addOperation($op);

        //$patch->apply(['foo'=>'bar']);
        //$this->assertEquals('[]', (string)$patch);
    }

    /**
     * Tests that invalid operations are rejected
     *
     * @expectedException \gamringer\JSONPatch\Exception
     *//*
    public function testException()
    {
        throw new \gamringer\JSONPatch\Exception();
    }*/
}
