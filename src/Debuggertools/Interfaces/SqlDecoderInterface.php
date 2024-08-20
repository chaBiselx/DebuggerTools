<?php

namespace Debuggertools\Interfaces;

interface SqlDecoderInterface
{
    public function serialize(string $obj): string;
}
