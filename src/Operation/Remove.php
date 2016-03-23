<?php
declare(strict_types=1);

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;
use gamringer\JSONPointer;

class Remove extends Operation implements Atomic
{
    private $previousValue;
    
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function apply(Pointer $target)
    {
        try {
            $this->previousValue = $target->remove($this->path);
        } catch (JSONPointer\Exception $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    public function revert(Pointer $target)
    {
        $target->insert($this->path, $this->previousValue);
    }

    public static function fromDecodedJSON($operationContent): self
    {
        self::assertValidOperationContent($operationContent);

        return new self($operationContent->path);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!property_exists($operationContent, 'path')) {
            throw new Operation\Exception('"Remove" Operations must contain a "path" member');
        }
    }

    public function __toString(): string
    {
        return json_encode([
            'op' => Operation::OP_REMOVE,
            'path' => $this->path,
        ]);
    }
}
