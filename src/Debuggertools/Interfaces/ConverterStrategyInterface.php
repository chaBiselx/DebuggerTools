<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

interface ConverterStrategyInterface
{
    public function convert($arg): string;

    public function setQuoteIfNeccesary(bool $activeQuote = true): void;
}
