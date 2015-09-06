<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;
use gamringer\JSONPointer;

class Test extends Operation implements Atomic
{
    private $value;

    public function __construct($path, $value)
    {
        $this->assertValueTestability($value);

        $this->path = $path;
        $this->value = $value;
    }

    public function assertValueTestability($value)
    {
        if (!in_array(gettype($value), ['object', 'array', 'string', 'double', 'integer', 'boolean', 'NULL'])) {
            throw new Exception('Value is not a valid type');
        }
    }

    public function apply(Pointer $target)
    {
        try {
            $targetValue = $target->get($this->path);
        } catch (JSONPointer\Exception $e) {
            throw new Exception($e->getMessage(), null, $e);
        }

        $this->assertEquals($this->value, $targetValue);
    }

    private function assertEquals($expected, $actual)
    {
        switch (gettype($expected)) {
            case 'object':
                $this->assertObjectsEquals($expected, $actual);
                break;

            case 'array':
                $this->assertArraysEquals($expected, $actual);
                break;

            case 'string':
                $this->assertStringsEquals($expected, $actual);
                break;

            case 'double':
            case 'integer':
                $this->assertNumbersEquals($expected, $actual);
                break;

            case 'boolean':
            case 'NULL':
                $this->assertLiteralEquals($expected, $actual);
                break;
        }
    }

    private function assertStringsEquals($expected, $actual)
    {
        if (gettype($actual) !== 'string') {
            throw new Exception('Target value is not a string');
        }

        if ($expected !== $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    private function assertNumbersEquals($expected, $actual)
    {
        if (!in_array(gettype($actual), ['integer', 'double'])) {
            throw new Exception('Target value is not a number');
        }

        if ($expected != $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    private function assertLiteralEquals($expected, $actual)
    {
        if (!in_array(gettype($actual), ['boolean', 'NULL'])) {
            throw new Exception('Target value is not a literal (true, false, null)');
        }

        if ($expected !== $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    private function assertArraysEquals($expected, $actual)
    {
        if (gettype($actual) !== 'array') {
            throw new Exception('Target value is not an array');
        }

        if (sizeof($expected) !== sizeof($actual)) {
            throw new Exception('Target value size does not match expected value size');
        }

        foreach ($expected as $i => $expectedValue) {
            $this->assertEquals($expectedValue, $actual[$i]);
        }
    }

    private function assertObjectsEquals($expected, $actual)
    {
        if (gettype($actual) !== 'object') {
            throw new Exception('Target value is not an object');
        }

        if (sizeof(get_object_vars($expected)) !== sizeof(get_object_vars($actual))) {
            throw new Exception('Target value size does not match expected value size');
        }

        foreach ($expected as $i => $expectedValue) {
            $this->assertObjectHas($i, $actual);
            $this->assertEquals($expectedValue, $actual->{$i});
        }
    }

    private function assertObjectHas($property, $object)
    {
        if (!property_exists($object, $property)) {
            throw new Exception('Property missing from the target: '.$property);
        }
    }

    public function revert(Pointer $target)
    {

    }

    public static function fromDecodedJSON($operationContent)
    {
        static::assertValidOperationContent($operationContent);

        return new static($operationContent->path, $operationContent->value);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!property_exists($operationContent, 'path') || !property_exists($operationContent, 'value')) {
            throw new Operation\Exception('"Test" Operations must contain a "path" and "value" member');
        }
    }

    public function __toString()
    {
        return json_encode([
            'op' => Operation::OP_TEST,
            'path' => $this->path,
            'value' => $this->value,
        ]);
    }
}
