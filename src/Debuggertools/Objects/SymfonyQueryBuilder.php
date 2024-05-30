<?php

namespace Debuggertools\Objects;



class SymfonyQueryBuilder
{
    public static function returnForLog(\Doctrine\ORM\QueryBuilder $obj): array
    {
        $retLog = [];
        $sql = $obj->getQuery()->getSql();
        $listKeys = $obj->getQuery()->getParameters()->getKeys();
        $listValues = $obj->getQuery()->getParameters()->getValues();
        $listParam = [];
        foreach ($listKeys as $key) {
            $parameter = $listValues[$key];
            $listParam[$key] = self::decodeListObjetSpecialClassQueryBuilder($parameter);
        }
        $retLog[] = $sql;
        $retLog[] = json_encode($listParam);

        return $retLog;
    }

    private static function decodeListObjetSpecialClassQueryBuilder($parameter): string
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
        $value = null;
        if (
            class_exists('Doctrine\\ORM\\PersistentCollection') &&
            $parameter instanceof \Doctrine\ORM\PersistentCollection
        ) {
            $value = "{Object: PersistentCollection}";
        } else {
            try {
                $value = "'" . gettype($parameter) . "'";
                $value .= "'" . get_class($parameter) . "'";
            } catch (\Error $e) {
                $value = $e->getMessage();
            }
        }
        return $value;
    }
}
