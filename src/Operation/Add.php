<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;
use gamringer\JSONPointer;
use gamringer\JSONPointer\VoidValue;

class Add extends Operation implements Atomic
{
    private $value;

    private $previousValueExists;
    private $previousValue;

    public function __construct($path, $value)
    {
        $this->assertValueAddability($value);

        $this->path = $path;
        $this->value = $value;
    }

    public function assertValueAddability($value)
    {
        if (!in_array(gettype($value), ['object', 'array', 'string', 'double', 'integer', 'boolean', 'NULL'])) {
            throw new Exception('Value is not a valid type');
        }
    }

    public function apply(Pointer $target)
    {
        try {
            $this->previousValue = $target->insert($this->path, $this->value);
        } catch (JSONPointer\Exception $e) {
            throw new Exception($e->getMessage(), null, $e);
        }
    }

    public function revert(Pointer $target)
    {
        if ($this->previousValue instanceof VoidValue) {
            $target->remove(preg_replace('/\/-$/', '/'.$this->previousValue->getTarget(), $this->path));
        } else {
            $target->set($this->path, $this->previousValue);
        }
    }

    public static function fromDecodedJSON($operationContent)
    {
        static::assertValidOperationContent($operationContent);

        return new static($operationContent->path, $operationContent->value);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!property_exists($operationContent, 'path') || !property_exists($operationContent, 'value')) {
            throw new Operation\Exception('"Add" Operations must contain a "path" and "value" member');
        }
    }

    public function __toString()
    {
        return json_encode([
            'op' => Operation::OP_ADD,
            'path' => $this->path,
            'value' => $this->value,
        ]);
    }
}
