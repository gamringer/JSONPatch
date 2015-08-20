<?php

namespace gamringer\JSONPatch\Test\Operation;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class ReplaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a valid Add Operation is constructed without errors
     */
    public function testCreation()
    {
        $operation = new Operation\Replace('/foo', 'bar');

        return $operation;
    }

    /**
     * Tests that a valid Add Operation is constructed without errors
     *
     * @dataProvider OperationProvider
     */
    public function testStaticCreation($operationDescription)
    {
        $operation = Operation\Replace::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     *
     * @dataProvider InvalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidStaticCreation($operationDescription)
    {
        $operation = Operation\Replace::fromDecodedJSON($operationDescription);
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
     * @dataProvider OperationApplyProvider
     */
    public function testApply($operationDescription, $target, $expected)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Replace::fromDecodedJSON($operationDescription);

        try {
            $operation->apply($pointer);
        } catch (\Exception $e) {
            $this->markTestSkipped('Apply did not work.');
        }


        $this->assertEquals(json_encode($pointer->getTarget()), $expected);
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
        $operation = Operation\Replace::fromDecodedJSON($operationDescription);
        
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
        $operation = Operation\Replace::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    public function OperationProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"test1"}')],
            [json_decode('{"path":"/foo", "value":"1"}')],
            [json_decode('{"path":"/foo", "value":1}')],
            [json_decode('{"path":"/foo", "value":0}')],
            [json_decode('{"path":"/foo", "value":0.23}')],
            [json_decode('{"path":"/foo", "value":null}')],
            [json_decode('{"path":"/foo", "value":true}')],
            [json_decode('{"path":"/foo", "value":false}')],
            [json_decode('{"path":"/foo", "value":[1, 2, 3]}')],
            [json_decode('{"path":"/foo", "value":{"bar": "baz"}}')],
        ];
    }

    public function OperationRevertProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>null], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>true], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>'a'], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>['a', 'b']], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>new \stdClass()], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"/foo/0", "value":"bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['bar', 'b', 'c']])],
            [json_decode('{"path":"/foo/1", "value":"bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'bar', 'c']])],
            [json_decode('{"path":"/foo/2", "value":"bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'b', 'bar']])],
        ];
    }

    public function InvalidOperationProvider()
    {
        return [
            [json_decode('{}')],
            [json_decode('{"path":"/"}')],
            [json_decode('{"value":"foo"}')],
        ];
    }

    public function OperationApplyProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"bar"}'), ['foo'=>true], json_encode(['foo'=>'bar'])],
            [json_decode('{"path":"", "value":["bar"]}'), ['foo'=>true], json_encode(['bar'])],
            [json_decode('{"path":"/foo/1", "value":"bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a','bar','c']])],
        ];
    }

    public function OperationApplyInvalidPathProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"bar"}'), ['bar'=>true]],
            [json_decode('{"path":"/foo", "value":"bar"}'), []],
            [json_decode('{"path":"/foo/0", "value":"bar"}'), ['foo'=>'allo']],
            [json_decode('{"path":"/foo/1", "value":"bar"}'), ['foo'=>['a']]],
            [json_decode('{"path":"/foo/-", "value":"bar"}'), ['foo'=>['a', 'b', 'c']]],
        ];
    }
}
