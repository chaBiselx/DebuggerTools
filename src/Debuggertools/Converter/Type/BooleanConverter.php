<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Interfaces\ConverterStrategyInterface;

class BooleanConverter implements ConverterStrategyInterface
{
    public function convert($arg): string
    {
        return $arg ? 'true' : 'false';
    }
}
