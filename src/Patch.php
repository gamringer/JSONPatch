<?php

namespace gamringer\JSONPatch;

use gamringer\JSONPointer;
use gamringer\JSONPointer\Pointer;

class Patch
{
    protected $operations = [];

    public static function fromJSON($patchContent)
    {
        $patch = new static();

        $patchContent = json_decode($patchContent);
        static::assertValidPatchContent($patchContent);

        foreach ($patchContent as $operationContent) {
            $operation = Operation::fromDecodedJSON($operationContent);
            $patch->addOperation($operation);
        }

        return $patch;
    }

    public function apply($target)
    {
        try {
            $jsonPointer = new Pointer($target);
        } catch(JSONPointer\Exception $e) {
            throw new Exception('Could not initialize target', 0, $e);
        }

        try {
            foreach ($this->operations as $operation) {
                $operation->apply($jsonPointer);
            }
        } catch(Operation\Exception $e) {
            $this->revert($jsonPointer);

            throw new Exception('An Operation failed', 1, $e);
        }
    }

    private function revert(Pointer $jsonPointer)
    {
        $this->operations = array_reverse($this->operations);

        try {
            foreach ($this->operations as $operation) {
                $operation->revert($jsonPointer);
            }
        } catch(Operation\Exception $e) {
            throw new Exception('An Operation failed and the reverting process also failed', 2, $e);
        }
    }

    public function addOperation(Operation\Atomic $operation)
    {
        $this->operations[] = $operation;
    }

    public function __toString()
    {
        $operations = [];

        return json_encode($operations);
    }

    private static function assertValidPatchContent($patchContent)
    {
        if ($patchContent === null && strtolower($patchContent) != 'null') {
            throw new Exception('Content of source patch file could not be decoded', 3);
        }

        if (!is_array($patchContent)) {
            throw new Exception('Content of source patch file is not a collection', 4);
        }
    }
}
