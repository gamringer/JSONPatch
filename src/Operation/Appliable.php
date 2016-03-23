<?php
declare(strict_types=1);

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPointer\Pointer;

interface Appliable
{
    public function apply(Pointer $target);
}
