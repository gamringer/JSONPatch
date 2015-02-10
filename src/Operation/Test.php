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
        $this->path = $path;
        $this->value = $value;
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

    public function assertStringsEquals($expected, $actual)
    {
        if (gettype($actual) !== 'string') {
            throw new Exception('Target value is not a string');
        }

        if ($expected !== $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    public function assertNumbersEquals($expected, $actual)
    {
        if (!in_array(gettype($actual), ['integer', 'double'])) {
            throw new Exception('Target value is not a number');
        }

        if ($expected != $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    public function assertLiteralEquals($expected, $actual)
    {
        if (!in_array(gettype($actual), ['boolean', 'NULL'])) {
            throw new Exception('Target value is not a literal (true, false, null)');
        }

        if ($expected !== $actual) {
            throw new Exception('Target value does not match expected value');
        }
    }

    public function assertArraysEquals($expected, $actual)
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

    public function assertObjectsEquals($expected, $actual)
    {
        if (gettype($actual) !== 'object') {
            throw new Exception('Target value is not an object');
        }

        if (sizeof($expected) !== sizeof($actual)) {
            throw new Exception('Target value size does not match expected value size');
        }

        foreach ($expected as $i => $expectedValue) {
            $this->assertEquals($expectedValue, $actual->{$i});
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
        if (!property_exists($operationContent, 'value')) {
            throw new Operation\Exception('"Test" Operations must contain a "value" member');
        }
    }
}
