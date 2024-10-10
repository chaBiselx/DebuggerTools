<?php

namespace Debuggertools\Interfaces;

use Generator;

interface TraceInterpretInterface
{
    public function decode(): Generator;
}
