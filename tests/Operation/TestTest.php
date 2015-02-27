<?php

namespace gamringer\JSONPatch\Test\Operation;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class TestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a valid Test Operation is constructed without errors
     */
    public function testCreation()
    {
        $operation = new Operation\Test('/foo', 'bar');

        return $operation;
    }

    /**
     * Tests that an invalid Test Operation is constructed with errors
     *
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidCreation()
    {
        $operation = new Operation\Test('/foo', tmpfile());
    }

    /**
     * Tests that a valid Test Operation is constructed without errors
     *
     * @dataProvider testOperationProvider
     */
    public function testStaticCreation($operationDescription)
    {
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     *
     * @dataProvider testInvalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testInvalidStaticCreation($operationDescription)
    {
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
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
     * Tests that objects get tested properly
     *
     * @dataProvider testOperationApplyContentProvider
     */
    public function testApply($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    /**
     * Tests that objects gets reverted properly
     *
     * @dataProvider testOperationApplyContentProvider
     */
    public function testRevert($operationDescription, $target)
    {
        $control = json_encode($target);
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
        $operation->revert($pointer);

        $this->assertEquals($control, json_encode($target));
    }

    /**
     * Tests that objects get tested properly
     *
     * @dataProvider testOperationApplyInvalidStringContentProvider
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
     * @dataProvider testOperationApplyInvalidNumberContentProvider
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
     * @dataProvider testOperationApplyInvalidLiteralContentProvider
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
     * @dataProvider testOperationApplyInvalidArrayContentProvider
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
     * @dataProvider testOperationApplyInvalidObjectContentProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testApplyInvalidObject($operationDescription, $target)
    {
        $pointer = new Pointer($target);
        $operation = Operation\Test::fromDecodedJSON($operationDescription);
        $operation->apply($pointer);
    }

    public function testOperationProvider()
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

    public function testInvalidOperationProvider()
    {
        return [
            [json_decode('{"path":"/foo"}')],
            [json_decode('{"value":"foo"}')],
        ];
    }

    public function testOperationApplyContentProvider()
    {
        return [
            [json_decode('{"path":"/foo", "value":"test1"}'), ['foo'=>"test1"]],
            [json_decode('{"path":"/foo", "value":"1"}'), ['foo'=>"1"]],
            [json_decode('{"path":"/foo", "value":1}'), ['foo'=>1]],
            [json_decode('{"path":"/foo", "value":0}'), ['foo'=>0]],
            [json_decode('{"path":"/foo", "value":0.23}'), ['foo'=>0.23]],
            [json_decode('{"path":"/foo", "value":null}'), ['foo'=>null]],
            [json_decode('{"path":"/foo", "value":true}'), ['foo'=>true]],
            [json_decode('{"path":"/foo", "value":false}'), ['foo'=>false]],
            [json_decode('{"path":"/foo", "value":[1, 2, 3]}'), ['foo'=>[1, 2, 3]]],
            [json_decode('{"path":"/foo", "value":{"bar": "baz"}}'), ['foo'=>json_decode('{"bar": "baz"}')]],
        ];
    }

    public function testOperationApplyInvalidStringContentProvider()
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

    public function testOperationApplyInvalidNumberContentProvider()
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

    public function testOperationApplyInvalidLiteralContentProvider()
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

    public function testOperationApplyInvalidArrayContentProvider()
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

    public function testOperationApplyInvalidObjectContentProvider()
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
