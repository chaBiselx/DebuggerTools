<?php

declare(strict_types=1);

namespace Debuggertools\Interfaces;

interface ExtracterInterface
{
    public function extract($obj): self;
}
