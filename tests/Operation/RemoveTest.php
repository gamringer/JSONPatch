<?php

namespace gamringer\JSONPatch\Test\Operation;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class RemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a valid Add Operation is constructed without errors
     */
    public function testCreation()
    {
        $operation = new Operation\Remove('/foo', 'bar');

        return $operation;
    }

    /**
     * Tests that a valid Add Operation is constructed without errors
     *
     * @dataProvider OperationProvider
     */
    public function testStaticCreation($operationDescription)
    {
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     *
     * @dataProvider InvalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidStaticCreation($operationDescription)
    {
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that invalid objects get properly fail on test
     *
     * @depends testCreation
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidPointer($operation)
    {
        $pointer = new Pointer();
        $operation->apply($pointer);
    }

    /**
     * Tests that objects get removed properly
     *
     * @expectedException \gamringer\JSONPointer\Exception
     * @dataProvider OperationApplyProvider
     */
    public function testApply($operationDescription, $target, $path)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);

        try {
            $operation->apply($pointer);
        } catch (\Exception $e) {
            $this->markTestSkipped('Apply did not work.');
        }


        $pointer->get($path);
    }

    /**
     * Tests that objects gets reverted properly
     *
     * @dataProvider OperationRevertProvider
     */
    public function testRevert($operationDescription, $target, $expected)
    {
        $control = json_encode($target);
        $pointer = new Pointer($target);
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);
        
        try {
            $operation->apply($pointer);
        } catch (\Exception $e) {
            $this->markTestSkipped('Apply did not work.');
        }

        $this->assertEquals(json_encode($pointer->getTarget()), $expected);
        
        $operation->revert($pointer);
        $this->assertEquals(json_encode($pointer->getTarget()), $control);
    }

    /**
     * Tests that non existant paths can be removed
     *
     * @dataProvider OperationApplyInvalidPathProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidPath($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    public function OperationProvider()
    {
        return [
            [json_decode('{"path":"/foo"}')],
        ];
    }

    public function OperationRevertProvider()
    {
        return [
            [json_decode('{"path":"/foo"}'), ['foo'=>null], json_encode([])],
            [json_decode('{"path":"/foo"}'), ['foo'=>true], json_encode([])],
            [json_decode('{"path":"/foo"}'), ['foo'=>'a'], json_encode([])],
            [json_decode('{"path":"/foo"}'), ['foo'=>['a', 'b']], json_encode([])],
            [json_decode('{"path":"/foo"}'), ['foo'=>new \stdClass()], json_encode([])],
            [json_decode('{"path":"/foo/0"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['b', 'c']])],
            [json_decode('{"path":"/foo/1"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'c']])],
            [json_decode('{"path":"/foo/2"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'b']])],
        ];
    }

    public function InvalidOperationProvider()
    {
        return [
            [json_decode('{}')],
        ];
    }

    public function OperationApplyProvider()
    {
        return [
            [json_decode('{"path":"/foo"}'), ['foo'=>true], '/foo'],
            [json_decode('{"path":""}'), ['foo'=>true], '/foo'],
            [json_decode('{"path":"/foo/1"}'), ['foo'=>['a', 'b', 'c']], '/foo/2'],
        ];
    }

    public function OperationApplyInvalidPathProvider()
    {
        return [
            [json_decode('{"path":"/foo"}'), ['bar'=>true]],
            [json_decode('{"path":"/foo"}'), []],
            [json_decode('{"path":"/foo/0"}'), ['foo'=>'allo']],
            [json_decode('{"path":"/foo/1"}'), ['foo'=>['a']]],
            [json_decode('{"path":"/foo/-"}'), ['foo'=>['a', 'b', 'c']]],
        ];
    }
}
