<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Interfaces\ConverterStrategyInterface;

class ObjectConverter implements ConverterStrategyInterface
{

    /**
     * {@inheritdoc }
     */
    public function setQuoteIfNeccesary(bool $activeQuote = true): void {}

    public function convert($arg): string
    {
        return "'" . get_class($arg) . "'";
    }
}
