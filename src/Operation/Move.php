<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;

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

    }

    public function revert(Pointer $target)
    {

    }

    public static function fromDecodedJSON($operationContent)
    {
        static::assertValidOperationContent($operationContent);

        return new static($operationContent->path, $operationContent->from);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!isset($operationContent->from)) {
            throw new Operation\Exception('"Move" Operations must contain a "from" member');
        }
    }
}
