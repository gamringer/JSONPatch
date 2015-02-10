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

        switch (gettype($this->value)) {
            case 'array':
                $this->assertArraysEquals($this->value, $targetValue);
                break;

            case 'string':
                $this->assertStringsEquals($this->value, $targetValue);
                break;

            case 'double':
            case 'integer':
                $this->assertNumbersEquals($this->value, $targetValue);
                break;

            case 'bool':
                $this->assertBoolEquals($this->value, $targetValue);
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
        if (!isset($operationContent->value)) {
            throw new Operation\Exception('"Test" Operations must contain a "value" member');
        }
    }
}
