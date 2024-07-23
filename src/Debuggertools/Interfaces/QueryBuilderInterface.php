<?php

namespace Debuggertools\Interfaces;

interface QueryBuilderInterface
{
    public function returnForLog(mixed $obj): array;
}
