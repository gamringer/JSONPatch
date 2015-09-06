<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;
use gamringer\JSONPointer\VoidValue;
use gamringer\JSONPointer;

class Move extends Operation implements Atomic
{
    private $from;

    public function __construct($path, $from)
    {
        $this->path = $path;
        $this->from = $from;
    }

    public function apply(Pointer $target)
    {
        try {
            $movedValue = $target->remove($this->from);
            $this->previousValue = $target->insert($this->path, $movedValue);
        } catch (JSONPointer\Exception $e) {
            throw new Exception($e->getMessage(), null, $e);
        }
    }

    public function revert(Pointer $target)
    {
        $movedValue = $target->remove($this->path);

        if (!($this->previousValue instanceof VoidValue)) {
            $target->insert($this->path, $this->previousValue);
        }

        $target->insert($this->from, $movedValue);
    }

    public static function fromDecodedJSON($operationContent)
    {
        static::assertValidOperationContent($operationContent);

        return new static($operationContent->path, $operationContent->from);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!property_exists($operationContent, 'path') || !property_exists($operationContent, 'from')) {
            throw new Operation\Exception('"Move" Operations must contain a "from" and "path" member');
        }
    }

    public function __toString()
    {
        return json_encode([
            'op' => Operation::OP_MOVE,
            'path' => $this->path,
            'from' => $this->from,
        ]);
    }
}
