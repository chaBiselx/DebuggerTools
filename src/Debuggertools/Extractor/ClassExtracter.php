<?php

declare(strict_types=1);

namespace Debuggertools\Extractor;

use Debuggertools\Decoder\ClassDecoder;
use Debuggertools\Decoder\ClosureDecoder;
use Debuggertools\Objects\SymfonyQueryBuilder;
use Debuggertools\Decoder\ArrayIteratorDecoder;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;
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
                $this->decodeContent($obj, $this->ClosureDecoder);
                break;
            case 'ArrayIterator':
                $this->decodeContent($obj, $this->ArrayIteratorDecoder);
                break;
            default:
                $this->decodeContent($obj, $this->ClassDecoder);
                break;
        }
        //check instance for more data
        $this->appendLog = $this->getContentSpecialClass($obj);

        return $this;
    }

    private function decodeContent($obj, ClassDecoderInterface $decoder): void
    {
        $this->content = $decoder->decodeObject($obj);
    }

    /**
     * Get more content from spécial class
     *
     * @param object $obj
     * @return array
     */
    private function getContentSpecialClass($obj): array
    {
        $toAppendToLog = [];
        $appender = null;
        if (
            class_exists('Doctrine\\ORM\\QueryBuilder') &&
            $obj instanceof \Doctrine\ORM\QueryBuilder
        ) {
            $appender =  $this->SymfonyQueryBuilder;
        }
        if ($appender) $toAppendToLog = array_merge($toAppendToLog, $this->extractMoreData($obj, $appender));
        return $toAppendToLog;
    }

    private function extractMoreData($obj, AppenderLogInterfaces $appender): array
    {
        return $appender->extractDataLog($obj);
    }
}
