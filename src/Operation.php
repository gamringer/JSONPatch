<?php

namespace gamringer\JSONPatch;

abstract class Operation
{

    const OP_TEST = 'test';
    const OP_ADD = 'add';
    const OP_REMOVE = 'remove';
    const OP_REPLACE = 'replace';
    const OP_MOVE = 'move';
    const OP_COPY = 'copy';

    protected $path;

    public static function fromDecodedJSON($operationContent)
    {
        static::assertValidOperationContent($operationContent);

        $operationClass = __NAMESPACE__.'\\Operation\\'.ucfirst($operationContent->op);

        return $operationClass::fromDecodedJSON($operationContent);
    }

    public function getPath()
    {
        return $this->path;
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!($operationContent instanceof \stdClass)) {
            throw new Operation\Exception('Operation Content is not an object');
        }

        if (!isset($operationContent->op)) {
            throw new Operation\Exception('All Operations must contain exactly one "op" member');
        }

        $possibleOperations = [
            static::OP_TEST,
            static::OP_ADD,
            static::OP_REMOVE,
            static::OP_REPLACE,
            static::OP_MOVE,
            static::OP_COPY
        ];
        
        if (!in_array($operationContent->op, $possibleOperations)) {
            throw new Operation\Exception('Operation must be one of "'.implode('", "', $possibleOperations).'"');
        }
    }
}
