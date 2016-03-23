<?php
declare(strict_types=1);

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPatch\Operation;
use gamringer\JSONPointer\Pointer;
use gamringer\JSONPointer\VoidValue;
use gamringer\JSONPointer;

class Copy extends Operation implements Atomic
{
    private $from = '';
    
    private $previousValue;

    public function __construct(string $path, string $from)
    {
        $this->path = $path;
        $this->from = $from;
    }

    public function apply(Pointer $target)
    {
        try {
            $copiedValue = $target->get($this->from);
            $this->previousValue = $target->insert($this->path, $copiedValue);
        } catch (JSONPointer\Exception $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    public function revert(Pointer $target)
    {
        $target->remove($this->path);
        if (!($this->previousValue instanceof VoidValue)) {
            $target->insert($this->path, $this->previousValue);
        }
    }

    public static function fromDecodedJSON($operationContent): self
    {
        self::assertValidOperationContent($operationContent);

        return new self($operationContent->path, $operationContent->from);
    }

    private static function assertValidOperationContent($operationContent)
    {
        if (!property_exists($operationContent, 'path') || !property_exists($operationContent, 'from')) {
            throw new Operation\Exception('"Copy" Operations must contain a "from" and "path" member');
        }

        // Validate that the "path" doesn't contain "from"
    }

    public function __toString(): string
    {
        return json_encode([
            'op' => Operation::OP_COPY,
            'path' => $this->path,
            'from' => $this->from,
        ]);
    }
}
