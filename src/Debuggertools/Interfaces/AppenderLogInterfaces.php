<?php

namespace Debuggertools\Interfaces;

interface AppenderLogInterfaces
{
    public function extractDataLog(mixed $obj): array;
}
