<?php

declare(strict_types=1);

namespace Debuggertools\ExtendClass;

use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Objects\SqlDecoder;
use Debuggertools\Decoder\ClassDecoder;



class AbstractDoctrineAppender implements AppenderLogInterfaces
{

    public function __construct()
    {
        $this->ClassDecoder = new ClassDecoder;
        $this->SqlDecoder = new SqlDecoder;
    }

    /**
     * Undocumented function
     *
     * @param [type] $obj
     * @return array
     */
    public function extractDataLog($obj): array
    {
        $retLog = [];
        if ($obj instanceof \Doctrine\ORM\QueryBuilder) {
            $sql = $obj->getQuery()->getSql();
            $listKeys = $obj->getQuery()->getParameters()->getKeys();
            $listValues = $obj->getQuery()->getParameters()->getValues();
            $listParam = [];
            foreach ($listKeys as $key) {
                $parameter = $listValues[$key];
                $listParam[$key] = $this->decodeListObjetSpecialClassQueryBuilder($parameter);
            }
            $retLog[] = $this->SqlDecoder->serialize($sql);
            if(!empty($listParam)){
                $retLog[] = json_encode($listParam);
            }
        }elseif ($obj instanceof \Doctrine\ORM\Query) {
            $sql = $obj->getSql();
            $retLog[] = $this->SqlDecoder->serialize($sql);
        }
        return $retLog;
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
                $value = " " . ($parameter->getValue() ? 1 : 0) . " ";
                break;
            case '2': // string
            case 'string':
                $value = self::decodeString($parameter);


                break;
            case 'integer': // string
                $value =  $parameter->getValue();
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
