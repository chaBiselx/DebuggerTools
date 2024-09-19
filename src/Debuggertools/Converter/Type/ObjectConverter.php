<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Converter\Interfaces\ConverterStrategyInterface;

class ObjectConverter implements ConverterStrategyInterface
{
    public function convert($arg): string
    {
        return "'" . get_class($arg) . "'";
    }
}
