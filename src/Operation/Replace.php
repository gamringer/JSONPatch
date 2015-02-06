<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;

class Replace extends Operation implements Modifies
{
    public function apply()
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
            throw new Operation\Exception('"Replace" Operations must contain a "value" member');
        }
    }
}
