<?php

namespace gamringer\JSONPatch\Test\Resources;

use gamringer\JSONPointer\Pointer;
use gamringer\JSONPatch\Operation\Atomic;

class MockAtomic implements Atomic
{
    private $applyOperation;
    private $revertOperation;

    public function __construct(Callable $apply = null, Callable $revert = null)
    {
        $this->applyOperation = $apply;
        $this->revertOperation = $revert;
    }

    public function apply(Pointer $target)
    {
        $callable = $this->applyOperation;
        $callable($target);
    }

    public function revert(Pointer $target)
    {
        $callable = $this->revertOperation;
        $callable($target);
    }

    public function __toString()
    {
        return json_encode([]);
    }
}
