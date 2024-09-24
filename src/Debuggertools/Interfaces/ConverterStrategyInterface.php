<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

interface ConverterStrategyInterface
{
    public function convert($arg): string;

    /**
     * Add quote if neccesary, fox exemple a string don't need a quote for a simple log but in a JSON the string need a quote to be valid
     *
     * @param bool $activeQuote
     *
     * @return void
     */
    public function setQuoteIfNeccesary(bool $activeQuote = true): void;
}
