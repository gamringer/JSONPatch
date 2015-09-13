<?php

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPointer\Pointer;

interface Revertable
{
    public function revert(Pointer $target);
}
