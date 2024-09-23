<?php

namespace Debuggertools\Formatter;

use Debuggertools\Config\InstanceConfig;
use Debuggertools\Converter\TypeConverter;


class JSONformatter
{

    public function __construct()
    {
        $this->typeConverter = new TypeConverter();
        $this->instanceConfig = new InstanceConfig();
    }

    /**
     * Create a json if necessery
     *
     * @param mixed $data
     * @param boolean $expendObject if true expend the object
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
            if ($this->instanceConfig->get('expendObject')) {
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
            $srtCroche = '{';
            $endCroche = '}';
        } elseif ($this->hasStringKeys($data)) { // associative array
            $srtCroche = '{';
            $endCroche = '}';
            if (empty($data)) return '[]';
            $lastKey = end(array_keys($data));
        } else { // array
            $srtCroche = '[';
            $endCroche = ']';
            if (empty($data)) return '[]';
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
    private  function hasStringKeys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
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
