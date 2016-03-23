<?php
declare(strict_types=1);

namespace gamringer\JSONPatch;

abstract class Operation
{

    const OP_TEST = 'test';
    const OP_ADD = 'add';
    const OP_REMOVE = 'remove';
    const OP_REPLACE = 'replace';
    const OP_MOVE = 'move';
    const OP_COPY = 'copy';

    protected $path = '';

    public static function fromDecodedJSON($operationContent)
    {
        self::assertValidOperationContent($operationContent);

        $operationClass = __NAMESPACE__.'\\Operation\\'.ucfirst($operationContent->op);

        return $operationClass::fromDecodedJSON($operationContent);
    }

    public function getPath(): string
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

        self::assertKnownOperation($operationContent->op);
    }

    private static function assertKnownOperation($operation)
    {
        $knownOperations = [
            self::OP_TEST,
            self::OP_ADD,
            self::OP_REMOVE,
            self::OP_REPLACE,
            self::OP_MOVE,
            self::OP_COPY
        ];
        
        if (!in_array($operation, $knownOperations)) {
            throw new Operation\Exception('Operation must be one of "'.implode('", "', $knownOperations).'"');
        }
    }
}
