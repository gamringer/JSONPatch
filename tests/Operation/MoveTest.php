<?php

namespace gamringer\JSONPatch\Test\Operation;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a valid Add Operation is constructed without errors
     */
    public function testCreation()
    {
        $operation = new Operation\Move('/foo', '/bar');

        return $operation;
    }

    /**
     * Tests that a valid Add Operation is constructed without errors
     *
     * @dataProvider OperationProvider
     */
    public function testStaticCreation($operationDescription)
    {
        $operation = Operation\Move::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     *
     * @dataProvider InvalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidStaticCreation($operationDescription)
    {
        $operation = Operation\Move::fromDecodedJSON($operationDescription);
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
        $operation = Operation\Move::fromDecodedJSON($operationDescription);

        $operation->apply($pointer);

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
        $operation = Operation\Move::fromDecodedJSON($operationDescription);
        
        try {
            $operation->apply($pointer);
        } catch (\Exception $e) {
            $this->markTestSkipped('Apply did not work.');
        }

        $this->assertEquals(json_encode($pointer->getTarget()), $expected);
        $operation->revert($pointer);


        $this->assertEquals($control, json_encode($pointer->getTarget()));
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
        $operation = Operation\Move::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    public function OperationProvider()
    {
        return [
            [json_decode('{"path":"/bar","from":"/foo"}')],
        ];
    }

    public function OperationRevertProvider()
    {
        return [
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['foo'=>null], json_encode(['bar'=>null])],
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['foo'=>true], json_encode(['bar'=>true])],
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['foo'=>'a'], json_encode(['bar'=>'a'])],
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['foo'=>['a', 'b']], json_encode(['bar'=>['a','b']])],
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['foo'=>new \stdClass()], json_encode(['bar'=>new \stdClass()])],
            [json_decode('{"from":"/foo/0", "path":"/bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['b', 'c'],'bar'=>'a'])],
            [json_decode('{"from":"/foo/1", "path":"/bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'c'],'bar'=>'b'])],
            [json_decode('{"from":"/foo/2", "path":"/bar"}'), ['foo'=>['a', 'b', 'c']], json_encode(['foo'=>['a', 'b'],'bar'=>'c'])],
            [json_decode('{"from":"/foo", "path":""}'), ['foo'=>null], json_encode(null)],
            [json_decode('{"from":"/foo", "path":""}'), ['foo'=>true], json_encode(true)],
            [json_decode('{"from":"/foo", "path":""}'), ['foo'=>'a'], json_encode('a')],
            [json_decode('{"from":"/foo", "path":""}'), ['foo'=>['a', 'b']], json_encode(['a', 'b'])],
            [json_decode('{"from":"/foo", "path":""}'), ['foo'=>new \stdClass()], json_encode(new \stdClass())],
            [json_decode('{"from":"/foo/0", "path":""}'), ['foo'=>['a', 'b', 'c']], json_encode('a')],
            [json_decode('{"from":"/foo/1", "path":""}'), ['foo'=>['a', 'b', 'c']], json_encode('b')],
            [json_decode('{"from":"/foo/2", "path":""}'), ['foo'=>['a', 'b', 'c']], json_encode('c')],
        ];
    }

    public function InvalidOperationProvider()
    {
        return [
            [json_decode('{}')],
            [json_decode('{"from":"/foo"}')],
            [json_decode('{"path":"/foo"}')],
        ];
    }

    public function OperationApplyProvider()
    {
        return [
            [json_decode('{"path":"/bar","from":"/foo"}'), ['foo'=>true], json_encode(['bar'=>true])],
            [json_decode('{"path":"","from":"/foo"}'), ['foo'=>true], json_encode(true)],
            [json_decode('{"path":"/bar","from":"/foo"}'), ['foo'=>['a', 'b', 'c']], json_encode(['bar'=>['a', 'b', 'c']])],
        ];
    }

    public function OperationApplyInvalidPathProvider()
    {
        return [
            [json_decode('{"from":"/foo", "path":"/bar"}'), ['bar'=>true]],
            [json_decode('{"from":"/foo", "path":"/bar"}'), []],
            [json_decode('{"from":"/foo/0", "path":"/bar"}'), ['foo'=>'allo']],
            [json_decode('{"from":"/foo/1", "path":"/bar"}'), ['foo'=>['a']]],
            [json_decode('{"from":"/foo/-", "path":"/bar"}'), ['foo'=>['a', 'b', 'c']]],
        ];
    }
}
