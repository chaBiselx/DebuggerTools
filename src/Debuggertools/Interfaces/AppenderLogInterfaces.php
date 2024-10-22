<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

interface AppenderLogInterfaces
{
    public function extractDataLog(mixed $obj): array;
}
