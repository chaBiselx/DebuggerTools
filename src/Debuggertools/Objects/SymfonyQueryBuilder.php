<?php

namespace Debuggertools\Objects;

use Debuggertools\Interfaces\QueryBuilderInterface;
use Debuggertools\Objects\SqlDecoder;
use Debuggertools\Objects\ObjectDecoder;



class SymfonyQueryBuilder implements QueryBuilderInterface
{

    public function __construct()
    {
        $this->ObjectDecoder = new ObjectDecoder;
        $this->SqlDecoder = new SqlDecoder;
    }

    public function returnForLog($obj): array
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
            $retLog[] = $this->SqlDecoder->decodeSql($sql);
            $retLog[] = json_encode($listParam);
        }
        return $retLog;
    }

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
                    $stringReturn = json_encode($this->ObjectDecoder->decodeObject($value));
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
