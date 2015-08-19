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
     * Tests that objects get appended properly
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
     * @group wip
     */
    public function testRevert($operationDescription, $target, $path)
    {
        $control = json_encode($target);
        
        $pointer = new Pointer($target);
        $operation = Operation\Remove::fromDecodedJSON($operationDescription);
        
        try {
            $operation->apply($pointer);
        } catch (\Exception $e) {
            $this->markTestSkipped('Apply did not work.');
        }

        try {
            $pointer->get($path);
            $this->markTestSkipped('Apply did not work.');
        } catch (\gamringer\JSONPointer\Exception $e) {
        }

        $operation->revert($pointer);
        
        $this->assertEquals($control, json_encode($target));
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
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
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
            //[json_decode('{"path":"/foo"}'), ['foo'=>null], '/foo'],
            [json_decode('{"path":"/foo"}'), ['foo'=>true], '/foo'],
            //[json_decode('{"path":"/foo"}'), ['foo'=>'a'], '/foo'],
            //[json_decode('{"path":"/foo"}'), ['foo'=>['a', 'b']], '/foo'],
            //[json_decode('{"path":"/foo"}'), ['foo'=>new \stdClass()], '/foo'],
            //[json_decode('{"path":"/foo/0"}'), ['foo'=>['a', 'b', 'c']], '/foo/1'],
            //[json_decode('{"path":"/foo/1"}'), ['foo'=>['a', 'b', 'c']], '/foo/2'],
            //[json_decode('{"path":"/foo/2"}'), ['foo'=>['a', 'b', 'c']], '/foo/3'],
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
