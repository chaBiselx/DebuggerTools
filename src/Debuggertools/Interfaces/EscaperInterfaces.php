<?php

namespace Debuggertools\Interfaces;

interface EscaperInterfaces
{
    /**
     * Decomposition to process error for safety and escape special Char
     *
     * @param string $logMessage
     * @return string
     */
    public function escape(string $logMessage): string;
}
