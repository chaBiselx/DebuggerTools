<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;
use Debuggertools\Appender\DoctrineQueryBuilderAppender;

class DoctrineQueryBuilderDecoder  implements ClassDecoderInterface
{

    public function __construct()
    {
        $this->appender = new DoctrineQueryBuilderAppender();
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
