<?php

declare(strict_types=1);

namespace Debuggertools\Converter\Type;

use Debuggertools\Interfaces\ConverterStrategyInterface;

class StringConverter implements ConverterStrategyInterface
{
    private $activeQuote = false;
    public function setQuoteIfNeccesary(bool $activeQuote = true): void
    {
        $this->activeQuote = $activeQuote;
    }

    public function convert($arg): string
    {
        if ($this->activeQuote) {
            return "\"" . (string) str_replace('"', '\\"', $arg) . "\"";
        } else {
            return (string) $arg;
        }
    }
}
