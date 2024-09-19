<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Converter\Interfaces\ConverterStrategyInterface;

class DefaultConverter implements ConverterStrategyInterface
{
    public function convert($arg): string
    {
        return (string) gettype($arg);
    }
}
