<?php

namespace Debuggertools\Formatter;

use Debuggertools\Config\InstanceConfig;
use Debuggertools\Converter\TypeConverter;
use Debuggertools\Enumerations\OptionForInstanceEnum;


class JSONformatter
{

    const OPEN_OBJECT = "{";
    const CLOSE_OBJECT = "}";
    const OPEN_ARRAY = "[";
    const CLOSE_ARRAY = "]";

    public function __construct()
    {
        $this->typeConverter = new TypeConverter();
        $this->instanceConfig = new InstanceConfig();
        $this->typeConverter->setQuote(true);
    }

    /**
     * Create a json if necessery
     *
     * @param mixed $data
     * @param integer $nbSpace
     * @return string
     */
    public  function createExpendedJson($data, $nbSpace = 0): string
    {
        $stringResponse = '';
        $type = gettype($data);
        //base indent
        $indent = self::createIndent($nbSpace);
        if (in_array($type, ['object', 'array'])) {
            if ($this->instanceConfig->get(OptionForInstanceEnum::EXPEND_OBJECT)) {
                $stringResponse = $this->generateExpendObject($data, $nbSpace);
            } else {
                $stringResponse = $indent . json_encode($data);
            }
        } else {
            $stringResponse = $this->typeConverter->convertArgToString($data);
        }
        return $stringResponse;
    }

    private function generateExpendObject($data, int $nbSpace): string
    {
        $indent = self::createIndent($nbSpace);

        $stringResponse = "\n";
        if (gettype($data) == 'object') { // object
            $srtCroche = self::OPEN_OBJECT;
            $endCroche = self::CLOSE_OBJECT;
        } elseif ($this->isAssociativeArray($data)) { // associative array
            $srtCroche =  self::OPEN_OBJECT;
            $endCroche = self::CLOSE_OBJECT;
            if (empty($data)) return $this->returnEmptyArray();
            $lastKey = end(array_keys($data));
        } else { // array
            $srtCroche = self::OPEN_ARRAY;
            $endCroche = self::CLOSE_ARRAY;
            if (empty($data)) return $this->returnEmptyArray();
            $lastKey = end(array_keys($data));
        }


        $stringResponse .= $indent . $srtCroche . "\n";
        foreach ($data as $key => $subData) {
            $index = $this->generageIndex($key);
            $stringResponse .= $indent . "  $index : ";
            $stringResponse .= $this->createExpendedJson($subData, $nbSpace + 2);
            if ($key !== $lastKey) $stringResponse .= ",";
            $stringResponse .= "\n";
        }
        $stringResponse .= $indent . $endCroche;
        return $stringResponse;
    }

    private static function returnEmptyArray(): string
    {
        return self::OPEN_ARRAY . self::CLOSE_ARRAY;
    }

    private function generageIndex($key): string
    {
        switch (gettype($key)) {
            case 'string':
                $index = "\"" . $key . "\"";
                break;
            case 'float':
            case 'integer':
            case 'double':
                $index = "" . (string) $key . "";
                break;
            default:
                $index = "\"" . $key . "\"";
                break;
        }
        return $index;
    }

    /**
     * check if the array associative or not
     *
     * @param array $array
     * @return boolean
     */
    private  function isAssociativeArray(array $array): bool
    {
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
    }

    /**
     * Create base to indentation
     *
     * @param integer $nbSpace
     * @return string
     */
    private static function createIndent(int $nbSpace): string
    {
        $indent = "";
        for ($i = 0; $i < $nbSpace; $i++) {
            $indent .= " ";
        }
        return $indent;
    }
}
