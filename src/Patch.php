<?php

namespace gamringer\JSONPatch;

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
        $jsonPointer = new Pointer($target);
        foreach ($this->operations as $operation) {
            var_dump($operation->getTarget());
        }
    }

    public function addOperation(Operation\Modifies $operation)
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
            throw new Exception('Content of source patch file could not be decoded');
        }

        if (!is_array($patchContent)) {
            throw new Exception('Content of source patch file is not a collection');
        }
    }
}
