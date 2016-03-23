<?php
declare(strict_types=1);

namespace gamringer\JSONPatch\Operation;

use gamringer\JSONPointer\Pointer;

interface Atomic extends Appliable, Revertable
{

}
