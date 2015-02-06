<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;

class Remove extends Operation implements Atomic
{
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function apply(Pointer $target)
    {

    }

    public function revert(Pointer $target)
    {

    }

    public static function fromDecodedJSON($operationContent)
    {
        return new static($operationContent->path);
    }
}
