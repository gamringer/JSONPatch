<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;

class Remove extends Operation implements Modifies
{
    public function apply()
    {

    }

    public static function fromDecodedJSON($operationContent)
    {
        return new static($operationContent->path);
    }
}
