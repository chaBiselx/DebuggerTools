<?php

namespace Debuggertools\Interfaces;

interface ExtracterInterface
{
    public function extract($obj): self;
}
