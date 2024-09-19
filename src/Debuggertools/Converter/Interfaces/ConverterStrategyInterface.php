<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Interfaces;

interface ConverterStrategyInterface
{
    public function convert($arg): string;
}
