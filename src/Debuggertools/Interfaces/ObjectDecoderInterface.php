<?php

namespace Debuggertools\Interfaces;

interface ObjectDecoderInterface
{
    public function decodeObject($obj): ?array;
}
