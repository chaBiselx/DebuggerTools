<?php

namespace Debuggertools\Extractor;

use Debuggertools\Objects\ClassDecoder;
use Debuggertools\Objects\SymfonyQueryBuilder;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\ExtendClass\AbstractAdvancedExtracter;

class ClassExtracter extends AbstractAdvancedExtracter implements ExtracterInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->ClassDecoder = new ClassDecoder();
        $this->SymfonyQueryBuilder = new SymfonyQueryBuilder();
    }


    public function extract($obj): ExtracterInterface
    {
        $this->class = get_class($obj); // get classname
        $this->content = $this->ClassDecoder->decodeObject($obj);

        //check instance for more data
        $this->returnAppendLog = $this->getContentSpecialClass($obj);

        return $this;
    }

    /**
     * Get more content from spécial class
     *
     * @param object $obj
     * @return array
     */
    protected  function getContentSpecialClass($obj): array
    {
        $toAppendToLog = [];
        if (
            class_exists('Doctrine\\ORM\\QueryBuilder') &&
            $obj instanceof \Doctrine\ORM\QueryBuilder
        ) {
            $toAppendToLog = array_merge($toAppendToLog, $this->SymfonyQueryBuilder->returnForLog($obj));
        }
        return $toAppendToLog;
    }
}
