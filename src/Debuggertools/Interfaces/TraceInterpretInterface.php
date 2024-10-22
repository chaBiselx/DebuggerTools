<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

use Generator;

interface TraceInterpretInterface
{
    public function decode(): Generator;
}
