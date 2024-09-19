<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Converter\Interfaces\ConverterStrategyInterface;

class StringConverter implements ConverterStrategyInterface
{
    public function convert($arg): string
    {
        return (string) $arg;
    }
}
