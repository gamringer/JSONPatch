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

    public function apply(&$target)
    {
        $jsonPointer = new Pointer($target);
        
        try {
            foreach ($this->operations as $i => $operation) {
                $operation->apply($jsonPointer);
            }
        } catch (Operation\Exception $e) {
            $this->revert($jsonPointer, $i);

            throw new Exception('An Operation failed', 1, $e);
        }
    }

    private function revert(Pointer $jsonPointer, $index)
    {
        $operations = array_reverse(array_slice($this->operations, 0, $index));

        try {
            foreach ($operations as $operation) {
                $operation->revert($jsonPointer);
            }
        } catch (Operation\Exception $e) {
            throw new Exception('An Operation failed and the reverting process also failed', 2, $e);
        }
    }

    public function addOperation(Operation\Atomic $operation)
    {
        $this->operations[] = $operation;
    }

    public function __toString()
    {
        return '['.implode(',', $this->operations).']';
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
