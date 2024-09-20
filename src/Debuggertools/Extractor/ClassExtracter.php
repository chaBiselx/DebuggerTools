<?php

declare(strict_types=1);

namespace Debuggertools\Extractor;

use Debuggertools\Objects\ClassDecoder;
use Debuggertools\Objects\ClosureDecoder;
use Debuggertools\Objects\SymfonyQueryBuilder;
use Debuggertools\Objects\ArrayIteratorDecoder;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\ExtendClass\AbstractAdvancedExtracter;

class ClassExtracter extends AbstractAdvancedExtracter implements ExtracterInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->ClassDecoder = new ClassDecoder();
        $this->ClosureDecoder = new ClosureDecoder();
        $this->ArrayIteratorDecoder = new ArrayIteratorDecoder();
        $this->SymfonyQueryBuilder = new SymfonyQueryBuilder();
    }


    public function extract($obj): ExtracterInterface
    {
        $this->class = get_class($obj); // get classname
        $this->type = 'class'; //type
        switch ($this->class) {
            case 'Closure':
                $this->content = $this->ClosureDecoder->decodeObject($obj);
                break;
            case 'ArrayIterator':
                $this->content = $this->ArrayIteratorDecoder->decodeObject($obj);
                break;
            default:
                $this->content = $this->ClassDecoder->decodeObject($obj);
                break;
        }
        //check instance for more data
        $this->appendLog = $this->getContentSpecialClass($obj);

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
            $toAppendToLog = array_merge($toAppendToLog, $this->SymfonyQueryBuilder->extractDataLog($obj));
        }
        return $toAppendToLog;
    }
}
