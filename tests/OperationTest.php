<?php

namespace gamringer\JSONPatch\Test;

use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;

class OperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that an empty patch is succesfully rendered to string
     * @dataProvider operationProvider
     */
    public function testStaticCreation($opDescription)
    {
        $operation = Operation::fromDecodedJSON($opDescription);

        $this->assertEquals($opDescription->path, $operation->getPath());
    }

    /**
     * Tests that an empty patch is succesfully rendered to string
     * @dataProvider invalidOperationProvider
     * @expectedException \gamringer\JSONPatch\Operation\Exception
     */
    public function testinvalidStaticCreation($opDescription)
    {
        $operation = Operation::fromDecodedJSON($opDescription);
    }

    public function operationProvider()
    {
        return [
            [json_decode('{"op":"test","path":"/foo","value":"bar"}')],
            [json_decode('{"op":"add","path":"/foo","value":"bar"}')],
            [json_decode('{"op":"remove","path":"/foo"}')],
            [json_decode('{"op":"replace","path":"/foo","value":"bar"}')],
            [json_decode('{"op":"move","path":"/foo","from":"bar"}')],
            [json_decode('{"op":"copy","path":"/foo","from":"bar"}')],
        ];
    }

    public function invalidOperationProvider()
    {
        return [
            [null],
            [true],
            [false],
            [1],
            [2],
            ['foo'],
            [[]],
            [json_decode('{"path":"/foo","value":"bar"}')],
            [json_decode('{"path":"/foo"}')],
            [json_decode('{"path":"/foo","from":"bar"}')],
            [json_decode('{"op":"foo","path":"/foo"}')],
        ];
    }
}
