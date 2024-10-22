<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use Debuggertools\Converter\TypeConverter;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;

class ArrayIteratorDecoder implements ClassDecoderInterface
{

    final public function __construct()
    {
        $this->typeConverter = new TypeConverter();
    }

    /**
     * {@inheritDoc}
     */
    final public function decodeObject($obj): ?array
    {
        $fakeData = [];

        while ($obj->valid()) {
            $fakeData[$obj->key()] = $this->typeConverter->convertArgToString($obj->current());
            $obj->next();
        }

        return $fakeData;
    }

    
    public function getAppender($obj): ?AppenderLogInterfaces {
        return null;
    }
}
