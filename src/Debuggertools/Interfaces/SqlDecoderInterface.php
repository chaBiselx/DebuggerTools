<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

interface SqlDecoderInterface
{
    public function serialize(string $obj): string;
}
