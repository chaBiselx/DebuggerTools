<?php

declare(strict_types=1);

namespace Debuggertools\Objects;

use Debuggertools\Converter\TypeConverter;
use Debuggertools\Interfaces\ClassDecoderInterface;

class ArrayIteratorDecoder implements ClassDecoderInterface
{

    public function __construct()
    {
        $this->typeConverter = new TypeConverter();
    }

    public function decodeObject($it): ?array
    {
        $fakeData = [];

        while ($it->valid()) {
            $fakeData[$it->key()] = $this->typeConverter->convertArgToString($it->current());
            $it->next();
        }

        return $fakeData;
    }
}
