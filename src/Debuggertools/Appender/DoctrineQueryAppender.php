<?php

declare(strict_types=1);

namespace Debuggertools\Appender;

use Debuggertools\Objects\SqlDecoder;
use Debuggertools\Decoder\ClassDecoder;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\ExtendClass\AbstractDoctrineAppender;



class DoctrineQueryAppender extends AbstractDoctrineAppender implements AppenderLogInterfaces
{


    /**
     * Undocumented function
     *
     * @param [type] $obj
     * @return array
     */
    public function extractDataLog($obj): array
    {
        $retLog = [];
        if ($obj instanceof \Doctrine\ORM\Query) {
            $sql = $obj->getSql();
            $retLog[] = $this->SqlDecoder->serialize($sql);

            $listKeys = $obj->getParameters()->getKeys();
            $listValues = $obj->getParameters()->getValues();
            $listParam = [];
            foreach ($listKeys as $key) {
                $parameter = $listValues[$key];
                $listParam[$key] = $this->decodeListObjetSpecialClassQueryBuilder($parameter);
                if (gettype($parameter->getValue()) == "object") {
                    try {
                        $listObject[$key] = $listParam[$key] . " => " . json_encode($this->ClassDecoder->decodeObject($parameter->getValue()));
                    } catch (\Throwable $th) {
                        //ignore
                    }
                }
            }
            if (!empty($listParam)) {
                $retLog[] = json_encode($listParam);
            }
            if (!empty($listObject)) {
                $retLog = array_merge($retLog, array_values($listObject));
            }
        }
        return $retLog;
    }
}
