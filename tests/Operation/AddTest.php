<?php

namespace gamringer\JSONPatch\Test\Operation;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class AddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a valid Add Operation is constructed without errors
     */
    public function testCreation()
    {
        $operation = new Operation\Add('/foo', 'bar');

        return $operation;
    }

    /**
     * Tests that an invalid Add Operation is constructed with errors
     *
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidCreation()
    {
        $operation = new Operation\Add('/foo', tmpfile());
    }

    /**
     * Tests that a valid Add Operation is constructed without errors
     *
     * @dataProvider OperationProvider
     */
    public function testStaticCreation($operationDescription)
    {
        $operation = Operation\Add::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     *
     * @dataProvider InvalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidStaticCreation($operationDescription)
    {
        $operation = Operation\Add::fromDecodedJSON($operationDescription);
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
     * @dataProvider OperationApplyReplaceContentProvider
     */
    public function testApply($operationDescription, $target, $expected, $path)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Add::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);

        $actual = $pointer->get($path);

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /**
     * Tests that objects gets reverted properly
     *
     * @dataProvider OperationApplyReplaceContentProvider
     * @group wip
     */
    public function testRevert($operationDescription, $target, $expected, $path)
    {
        $control = json_encode($target);
        
        $pointer = new Pointer($target);
        $operation = Operation\Add::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
        $this->assertEquals($pointer->get($path), $operationDescription->value);
        $operation->revert($pointer);
        
        $this->assertEquals($control, json_encode($target));
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider OperationApplyInvalidStringContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidString($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider OperationApplyInvalidNumberContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidNumber($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider OperationApplyInvalidLiteralContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidLiteral($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider OperationApplyInvalidArrayContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidArray($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider OperationApplyInvalidObjectContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidObject($operationDescription, $target)
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

    public function InvalidOperationProvider()
    {
        return [
            [json_decode('{"path":"/foo"}')],
            [json_decode('{"value":"foo"}')],
        ];
    }

    public function OperationApplyReplaceContentProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"test1"}'), [], "test1", '/foo'],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>null], "test1", '/foo'],
            [json_decode('{"path":"/foo", "value":"1"}'), ['foo'=>null], "1", '/foo'],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>null], 1, '/foo'],
            [json_decode('{"path":"/foo", "value":0}'), ['foo'=>null], 0, '/foo'],
            [json_decode('{"path":"/foo", "value":0.23}'), ['foo'=>null], 0.23, '/foo'],
            [json_decode('{"path":"/foo", "value":null}'), ['foo'=>'foo'], null, '/foo'],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>null], true, '/foo'],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>null], false, '/foo'],
            [json_decode('{"path":"/foo", "value":[1, 2, 3]}'), ['foo'=>null], [1, 2, 3], '/foo'],
            [json_decode('{"path":"/foo", "value":{"bar": "baz"}}'), ['foo'=>null], json_decode('{"bar": "baz"}'), '/foo'],
            [json_decode('{"path":"/foo/1", "value":true}'), ['foo'=>['a','b']], true, '/foo/1'],
            [json_decode('{"path":"/foo/-", "value":true}'), ['foo'=>['a','b']], true, '/foo/2'],
        ];
    }

    public function OperationApplyAppendContentProvider()
    {
        return [
            [json_decode('{"path":"/foo/0", "value":"test1"}'), [], ['test1']],
            [json_decode('{"path":"/foo/0", "value":"test1"}'), [1], ['test1',1]],
            [json_decode('{"path":"/foo/1", "value":"test1"}'), [1], [1,'test1']],
            [json_decode('{"path":"/foo/1", "value":"test1"}'), [1,2], [1,'test1',2]],
            [json_decode('{"path":"/foo/-", "value":"test1"}'), [], ['test1']],
            [json_decode('{"path":"/foo/-", "value":"test1"}'), [1], [1,'test1']],
            [json_decode('{"path":"/foo/-", "value":"test1"}'), [1,2], [1,2,'test1']],
        ];
    }

    public function OperationApplyInsertContentProvider()
    {
        return [
            [json_decode('{"path":"/1", "value":"test1"}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":"1"}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":1}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":0}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":0.23}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":null}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":true}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":false}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":[1, 2, 3]}'), [1, 2, 3]],
            [json_decode('{"path":"/1", "value":{"bar": "baz"}}'), [1, 2, 3]],
        ];
    }

    public function OperationApplyInsertAfterContentProvider()
    {
        return [
            [json_decode('{"path":"/-", "value":"test1"}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":"1"}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":1}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":0}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":0.23}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":null}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":true}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":false}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":[1, 2, 3]}'), [1, 2, 3]],
            [json_decode('{"path":"/-", "value":{"bar": "baz"}}'), [1, 2, 3]],
        ];
    }

    public function OperationApplyInvalidStringContentProvider()
    {
        return [
            //  Same Type, different value
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>"test2"]],

            //  Different type
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>1]],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>json_decode('{"bar": "baz"}')]],
        ];
    }

    public function OperationApplyInvalidNumberContentProvider()
    {
        return [
            //  Same Type, different value
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>2]],

            //  Different type
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>'1']],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>json_decode('{"bar": "baz"}')]],
        ];
    }

    public function OperationApplyInvalidLiteralContentProvider()
    {
        return [
            //  Same Type, different value
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":null}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":null}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>null]],

            //  Different type
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>'1']],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>1]],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>'']],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>0]],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>json_decode('{"bar": "baz"}')]],
        ];
    }

    public function OperationApplyInvalidArrayContentProvider()
    {
        return [
            //  Same Type, different value
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>[1, 3]]],
            [json_decode('{"path":"/foo", "value":[1,2,3]}'), ['foo'=>[1, 3]]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>['1', '2']]],

            //  Different type
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>'1']],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>1]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":[1,2]}'), ['foo'=>json_decode('{"bar": "baz"}')]],
        ];
    }

    public function OperationApplyInvalidObjectContentProvider()
    {
        $obj = new \stdClass();
        $obj->foo = 'bar';

        $objBig = new \stdClass();
        $objBig->foo = 'bar';
        $objBig->baz = 'qux';
        return [
            //  Same Type, different value
            [json_decode('{"path":"/foo", "value":{"foo":"baz","bar":"qux"}}'), ['foo'=>$obj]],
            [json_decode('{"path":"/foo", "value":{"foo":"baz"}}'), ['foo'=>$obj]],
            [json_decode('{"path":"/foo", "value":{"foo":"baz"}}'), ['foo'=>$objBig]],
            [json_decode('{"path":"/foo", "value":{"baz":"bar"}}'), ['foo'=>$obj]],
            [json_decode('{"path":"/foo", "value":{"foo":["bar"]}}'), ['foo'=>$obj]],

            //  Different type
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>'foo']],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>'bar']],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>'1']],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>1]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>0]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":{"foo":"bar"}}'), ['foo'=>['bar']]],
        ];
    }
}
