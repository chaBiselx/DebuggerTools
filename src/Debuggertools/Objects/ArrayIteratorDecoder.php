<?php

declare(strict_types=1);

namespace Debuggertools\Objects;

use ReflectionFunction;
use Debuggertools\Interfaces\ClassDecoderInterface;

class ArrayIteratorDecoder implements ClassDecoderInterface
{

    public function decodeObject($it): ?array
    {
        $fakeData = [];

        while ($it->valid()) {
            $fakeData[$it->key()] = $this->convertArgToString($it->current());
            $it->next();
        }

        return $fakeData;
    }

    /**
     * Convert Arg to string for parameters of function
     *
     * @param $arg
     * @return string
     */
    protected function convertArgToString($arg): string
    {
        $text = "";
        //check type
        $type = gettype($arg);
        switch ($type) {
            case 'integer':
            case 'float':
            case 'double':
            case 'string':
                $text = $arg;
                break;
            case 'boolean':
                $text = $type . ' : ' . ($arg ? 'TRUE' : 'FALSE');
                break;
            case 'object':
                $text = "'" . get_class($arg) . "'";
                break;
            case 'array':
            default:
                $text = $type;
                break;
        }
        return $text;
    }
}
