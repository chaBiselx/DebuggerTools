<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use Debuggertools\Converter\TypeConverter;
use Debuggertools\Interfaces\ClassDecoderInterface;

class ArrayIteratorDecoder implements ClassDecoderInterface
{

    public function __construct()
    {
        $this->typeConverter = new TypeConverter();
    }

    /**
     * {@inheritDoc}
     */
    public function decodeObject($obj): ?array
    {
        $fakeData = [];

        while ($obj->valid()) {
            $fakeData[$obj->key()] = $this->typeConverter->convertArgToString($obj->current());
            $obj->next();
        }

        return $fakeData;
    }
}
