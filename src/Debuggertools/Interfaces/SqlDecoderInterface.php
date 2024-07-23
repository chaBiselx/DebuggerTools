<?php

namespace Debuggertools\Interfaces;

interface SqlDecoderInterface
{
    public function decodeSql(string $obj): string;
}
