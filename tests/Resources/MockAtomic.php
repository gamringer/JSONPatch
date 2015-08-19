<?php

namespace gamringer\JSONPatch\Test\Resources;

use gamringer\JSONPointer\Pointer;
use gamringer\JSONPatch\Operation\Atomic;

class MockAtomic implements Atomic
{
    public function apply(Pointer $target)
    {

    }

    public function revert(Pointer $target)
    {

    }

}
