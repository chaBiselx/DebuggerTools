<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use Debuggertools\Appender\DoctrineQueryAppender;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;

class DoctrineQueryDecoder implements ClassDecoderInterface
{

    public function __construct()
    {
        $this->appender = new DoctrineQueryAppender();
    }

    /**
     * {@inheritDoc}
     */
    public function decodeObject($obj): ?array
    {
        return [];
    }

    public function getAppender($obj): ?AppenderLogInterfaces {
        return $this->appender;
    }
}
