<?php
declare(strict_types=1);

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPointer\Pointer;

interface Revertable
{
    public function revert(Pointer $target);
}
