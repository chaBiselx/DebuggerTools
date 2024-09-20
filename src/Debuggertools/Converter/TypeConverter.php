<?php

declare(strict_types=1);

namespace Debuggertools\Converter;

use Debuggertools\Converter\Type\IntConverter;
use Debuggertools\Converter\Type\ObjectConverter;
use Debuggertools\Converter\Type\StringConverter;
use Debuggertools\Converter\Type\BooleanConverter;
use Debuggertools\Converter\Type\DefaultConverter;
use Debuggertools\Interfaces\ConverterStrategyInterface;

class TypeConverter
{
    /**
     * Convert Arg to string for parameters of function
     *
     * @param $arg
     * @return string
     */
    public static function convertArgToString($arg): string
    {
        $type = gettype($arg);
        $converter = self::getConverterForType($type);
        return $converter->convert($arg);
    }

    private static function getConverterForType(string $type): ConverterStrategyInterface
    {
        switch ($type) {
            case 'integer':
            case 'float':
            case 'double':
                $return = new IntConverter();
                break;
            case 'string':
                $return = new StringConverter();
                break;
            case 'boolean':
                $return = new BooleanConverter();
                break;
            case 'object':
                $return = new ObjectConverter();
                break;
            default:
                $return = new DefaultConverter();
                break;
        }
        return $return;
    }
}
