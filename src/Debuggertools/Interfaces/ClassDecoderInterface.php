<?php

namespace Debuggertools\Interfaces;

interface ClassDecoderInterface
{
    public function decodeObject($obj): ?array;
}
