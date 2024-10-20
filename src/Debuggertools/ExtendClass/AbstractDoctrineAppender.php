<?php

declare(strict_types=1);

namespace Debuggertools\ExtendClass;

use Debuggertools\Objects\SqlDecoder;
use Debuggertools\Decoder\ClassDecoder;
use Debuggertools\Converter\TypeConverter;
use Debuggertools\Interfaces\AppenderLogInterfaces;



class AbstractDoctrineAppender implements AppenderLogInterfaces
{

    public function __construct()
    {
        $this->ClassDecoder = new ClassDecoder;
        $this->SqlDecoder = new SqlDecoder;
        $this->TypeConverter = new TypeConverter;
    }

    /**
     * Undocumented function
     *
     * @param [type] $obj
     * @return array
     */
    public function extractDataLog($obj): array
    {
        return [];
    }

    /**
     * Convert value of parameters for sql value
     *
     * @param any $parameter
     * @return string
     */
    protected function decodeListObjetSpecialClassQueryBuilder($parameter): string
    {
        $value = 'TO_DEFINE';
        switch ($parameter->getType()) {
            case 'datetime':
                $value = "'" . $parameter->getValue()->format('Y-m-d H:i:s') . "'";
                break;
            case 'boolean':
            case 1: // boolean
            case '2': // string
            case 'integer': // string
            case 'string':
                $value = $this->TypeConverter->convertArgToString($parameter->getValue());
                break;

            case 102:
                $value = "";
                foreach ($parameter->getValue() as $k => $v) {
                    if ($k > 0) $value .= ", ";
                    $value .= "'" . $v . "'";
                }
                break;
            default:
                try {
                    $d = self::decodeObjetParameter($parameter);
                } catch (\Error $e) {
                    $d = get_class($parameter);
                }
                $value .= " : " . $d;
                break;
        }
        return $value;
    }

    /**
     * Analyse the object to give the classname, the content and if necesary log to append
     *
     * @param mixed $obj
     * @return string
     */
    protected  function decodeObjetParameter($parameter): string
    {
        $class = get_class($parameter); // get classname
        $decoded =  ' "' . $class . '" ' . $parameter->getType() . ' : ';
        $decoded .= json_decode(json_encode($parameter->getValue()), true);

        return $decoded;
    }

    /**
     * Decode object to convert in string
     *
     * @param [type] $parameter
     * @return string
     */
    private function decodeString($parameter): string
    {
        $stringReturn = null;

        try {
            $value = $parameter->getValue();
            switch (gettype($value)) {
                case 'integer':
                case 'float':
                case 'boolean':
                case 'string':
                    $stringReturn = "'" . $value . "'";
                    break;
                case 'array':
                    $stringReturn = json_encode($value);
                    break;
                case 'object':
                    $stringReturn = json_encode($this->ClassDecoder->decodeObject($value));
                    break;
                default:
                    $stringReturn = "{object:" . gettype($value) . "}";
                    break;
            }
        } catch (\Error $e) {
            $stringReturn = $e->getMessage();
        }
        return $stringReturn;
    }
}
