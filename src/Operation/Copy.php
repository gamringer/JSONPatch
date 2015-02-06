<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;

class Copy extends Operation implements Modifies
{
    public function apply()
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
            throw new Operation\Exception('"Copy" Operations must contain a "from" member');
        }
    }
}
